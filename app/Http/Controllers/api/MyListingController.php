<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\RealestateInformationRequest;
use App\Models\BusinessImage;
use App\Models\BusinessInformation;
use App\Models\ListingType;
use App\Models\SellerFinancialDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Self-service listing management for badge-active professionals/sellers.
// Ported deliberately close to the proven, already-working admin-portal
// controllers (portal\BusinessController / RealestateController) — same
// validation classes, same draft-vs-publish branching, same file-storage
// conventions — with ownership scoping and the badge gate layered on top.
class MyListingController extends Controller
{
    // Buyers and non-active-badge professionals get this on every mutating
    // action — checked in the controller body (not FormRequest::authorize())
    // so the response is the app's normal makeResponse() envelope, matching
    // how ForumController::createForum() gates forum posting.
    private function ensureCanManageListings()
    {
        if (!auth()->user()->canManageListings()) {
            return makeResponse(ABORT_CODE, 'Get your badge to create and manage listings.');
        }
        return null;
    }

    public function index()
    {
        $listings = BusinessInformation::where('user_id', auth()->id())
            ->with(['business_images', 'country', 'state', 'county'])
            ->orderBy('id', 'desc')
            ->get();

        return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $listings);
    }

    public function show($id)
    {
        $listing = BusinessInformation::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['business_images', 'financial_documents', 'country', 'state', 'county', 'category', 'subcategory'])
            ->first();

        if (!$listing) {
            return makeResponse(FAILURE_CODE, NOT_FOUND);
        }

        return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $listing);
    }

    public function storeBusiness(Request $request)
    {
        if ($blocked = $this->ensureCanManageListings()) {
            return $blocked;
        }

        $this->prepareForOwnership($request);
        $validated = $this->validateOrDraft($request, BusinessInformationRequest::class);

        $validated['business_type'] = 'business';
        if (empty($validated['business_name'])) {
            $validated['business_name'] = $validated['listing_heading'] ?? '';
        }

        $business = BusinessInformation::create($validated);
        $this->resolveListingType($business, $validated);
        $this->storeImages($request, $business);
        $this->storeFinancialDocuments($request, $business);
        $this->storeNda($request, $business);

        return makeResponse(SUCCESS_CODE, CREATE_SUCCESS, $business->fresh(['business_images', 'financial_documents']));
    }

    public function updateBusiness(Request $request, $id)
    {
        if ($blocked = $this->ensureCanManageListings()) {
            return $blocked;
        }

        $business = BusinessInformation::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$business) {
            return makeResponse(FAILURE_CODE, NOT_FOUND);
        }

        $request->merge(['business_id' => $business->id]);
        if (!$request->filled('listing_number')) {
            $request->merge(['listing_number' => $business->listing_number]);
        }
        $this->prepareForOwnership($request);
        $validated = $this->validateOrDraft($request, BusinessInformationRequest::class);

        $validated['business_type'] = 'business';
        if (empty($validated['business_name'])) {
            $validated['business_name'] = $validated['listing_heading'] ?? $business->listing_heading;
        }

        $business->update($validated);
        $this->resolveListingType($business, $validated);
        $this->replaceImages($request, $business);
        $this->replaceFinancialDocuments($request, $business);
        $this->storeNda($request, $business);

        return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS, $business->fresh(['business_images', 'financial_documents']));
    }

    public function storeRealEstate(Request $request)
    {
        if ($blocked = $this->ensureCanManageListings()) {
            return $blocked;
        }

        $this->prepareForOwnership($request);
        $validated = $this->validateOrDraft($request, RealestateInformationRequest::class);

        $validated['business_type'] = 'real-estate';
        if (empty($validated['business_name'])) {
            $validated['business_name'] = $validated['listing_heading'] ?? '';
        }

        $business = BusinessInformation::create($validated);
        $this->resolveListingType($business, $validated);
        $this->storeImages($request, $business);
        $this->storeFinancialDocuments($request, $business);

        return makeResponse(SUCCESS_CODE, CREATE_SUCCESS, $business->fresh(['business_images', 'financial_documents']));
    }

    public function updateRealEstate(Request $request, $id)
    {
        if ($blocked = $this->ensureCanManageListings()) {
            return $blocked;
        }

        $business = BusinessInformation::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$business) {
            return makeResponse(FAILURE_CODE, NOT_FOUND);
        }

        $request->merge(['business_id' => $business->id]);
        if (!$request->filled('listing_number')) {
            $request->merge(['listing_number' => $business->listing_number]);
        }
        $this->prepareForOwnership($request);
        $validated = $this->validateOrDraft($request, RealestateInformationRequest::class);

        $validated['business_type'] = 'real-estate';
        if (empty($validated['business_name'])) {
            $validated['business_name'] = $validated['listing_heading'] ?? $business->listing_heading;
        }

        $business->update($validated);
        $this->resolveListingType($business, $validated);
        $this->replaceImages($request, $business);
        $this->replaceFinancialDocuments($request, $business);

        return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS, $business->fresh(['business_images', 'financial_documents']));
    }

    public function destroy($id)
    {
        if ($blocked = $this->ensureCanManageListings()) {
            return $blocked;
        }

        $business = BusinessInformation::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['business_images', 'financial_documents'])
            ->first();

        if (!$business) {
            return makeResponse(FAILURE_CODE, NOT_FOUND);
        }

        foreach ($business->business_images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }
        foreach ($business->financial_documents as $doc) {
            Storage::delete('public/' . $doc->document_path);
            $doc->delete();
        }
        if ($business->nda_document_path) {
            Storage::disk('public')->delete($business->nda_document_path);
        }

        $business->delete();

        return makeResponse(SUCCESS_CODE, DELETE_SUCCESS);
    }

    // ── shared helpers ──────────────────────────────────────────────────

    // user_id/contact_user_id are never trusted from client input — always
    // derived from the authenticated user, merged in before validation runs.
    private function prepareForOwnership(Request $request): void
    {
        $request->merge([
            'user_id' => auth()->id(),
            'contact_user_id' => $request->input('contact_user_id') ?: auth()->id(),
        ]);
        if (!$request->filled('listing_number')) {
            $request->merge(['listing_number' => $this->generateListingNumber()]);
        }
    }

    // Full FormRequest validation only when actually publishing — matches
    // portal\BusinessController/RealestateController exactly, so a draft
    // save doesn't force every field to be filled in yet.
    private function validateOrDraft(Request $request, string $formRequestClass): array
    {
        if ((int) $request->input('is_publish') === 1) {
            return app($formRequestClass)->validated();
        }
        return $request->all();
    }

    private function generateListingNumber(): string
    {
        do {
            $number = 'LST-' . strtoupper(Str::random(8));
        } while (BusinessInformation::where('listing_number', $number)->exists());
        return $number;
    }

    private function resolveListingType(BusinessInformation $business, array $validated): void
    {
        if (!empty($validated['listing_type'])) {
            $listingType = ListingType::where('listing_type', $validated['listing_type'])->first();
            if ($listingType) {
                $business->listing_type_id = $listingType->id;
                $business->listing_type = $listingType->listing_type;
                $business->save();
            }
        }
    }

    private function storeImages(Request $request, BusinessInformation $business): void
    {
        if (!$request->hasFile('images')) {
            return;
        }
        foreach ($request->file('images') as $image) {
            $path = $image->store('business_images', 'public');
            BusinessImage::create([
                'business_information_id' => $business->id,
                'image_path' => $path,
            ]);
        }
    }

    private function replaceImages(Request $request, BusinessInformation $business): void
    {
        if (!$request->hasFile('images')) {
            return;
        }
        foreach ($business->business_images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
        $this->storeImages($request, $business);
    }

    private function storeFinancialDocuments(Request $request, BusinessInformation $business): void
    {
        if (!$request->hasFile('seller_financial_documents')) {
            return;
        }
        foreach ($request->file('seller_financial_documents') as $doc) {
            $filename = 'doc_' . time() . '_' . Str::random(8) . '.' . $doc->getClientOriginalExtension();
            $doc->storeAs('public/documents', $filename);
            SellerFinancialDocument::create([
                'business_information_id' => $business->id,
                'document_path' => 'documents/' . $filename,
            ]);
        }
    }

    private function replaceFinancialDocuments(Request $request, BusinessInformation $business): void
    {
        if (!$request->hasFile('seller_financial_documents')) {
            return;
        }
        foreach ($business->financial_documents as $doc) {
            if (Storage::exists('public/' . $doc->document_path)) {
                Storage::delete('public/' . $doc->document_path);
            }
            $doc->delete();
        }
        $this->storeFinancialDocuments($request, $business);
    }

    private function storeNda(Request $request, BusinessInformation $business): void
    {
        if (!$request->hasFile('nda_document')) {
            return;
        }
        if ($business->nda_document_path && Storage::disk('public')->exists($business->nda_document_path)) {
            Storage::disk('public')->delete($business->nda_document_path);
        }
        $ndaFile = $request->file('nda_document');
        $filename = 'nda_' . time() . '_' . Str::random(8) . '.' . $ndaFile->getClientOriginalExtension();
        $ndaFile->storeAs('nda_documents', $filename, 'public');
        $business->nda_document_path = 'nda_documents/' . $filename;
        $business->save();
    }
}
