<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Basic sanity check only — the actual badge-eligibility/ownership
        // rules live in the controller, so a rejection returns the app's
        // normal makeResponse() envelope instead of a bare 403.
        return auth()->check();
    }

    public function rules(): array
    {
        $isUpdate = $this->has('business_id') && !empty($this->business_id);

        return [
            // business_name is auto-set from listing_heading, not in form
            'business_name' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'county_id' => 'required|exists:counties,id',
            'listing_type_id' => 'nullable|exists:listing_types,id',
            'contact_user_id' => 'nullable|exists:users,id',
            'listing_type' => 'required|string',
            'business_type_ids' => 'nullable|array',
            'business_type_ids.*' => 'exists:business_types,id',
            // zip_code (City) and street_address are now optional
            'zip_code' => 'nullable|string',
            'street_address' => 'nullable|string',

            'is_confidential_state' => 'boolean',
            'is_confidential_country' => 'boolean',
            'is_confidential_city' => 'boolean',
            'is_confidential_address' => 'boolean',

            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'listing_number' => 'required|string',

            'is_non_disclosure_agreement' => 'boolean',

            'listing_heading' => 'required|string',
            'business_website' => 'nullable|string',
            'is_website_confidential' => 'boolean',
            'listing_summary' => 'required|string',

            'asking_price' => 'required|string|max:100',
            'cash_flow' => 'nullable|string|max:100',
            'ebitdas' => 'nullable|string|max:100',
            'gross_revenue' => 'nullable|string|max:100',
            'inventory' => 'nullable|string|max:100',
            'inventory_included_in_price' => 'boolean',
            'ffe' => 'nullable|string|max:100',
            'ffe_included_in_price' => 'boolean',

            'building_size' => 'nullable|string',
            'is_seller_financing_available' => 'boolean',
            'seller_note' => 'nullable|string',
            // number_of_employees changed to string to allow "12 full time, 2 part-time"
            'number_of_employees' => 'nullable|string',
            'year_established' => 'required|string',

            'facilities' => 'nullable|string',
            'support_and_training' => 'nullable|string',
            'reason_for_selling' => 'nullable|string',
            'competition_market_pros_and_cons' => 'nullable|string',
            'growth_and_expansion_pros_and_cons' => 'nullable|string',
            'real_estate_leased' => 'boolean',
            'real_estate_available' => 'boolean',
            'real_estate_included' => 'boolean',
            'home_based_business' => 'boolean',
            'real_estate_rent' => 'nullable|string|max:100',
            'business_type' => 'nullable|string',

            'is_required_buyer_telephone_number' => 'boolean',
            'is_required_buyer_zip_code' => 'boolean',
            'is_required_buyer_available_funds' => 'boolean',
            'is_required_buyer_timeframe' => 'boolean',
            'is_publish' => 'boolean',

            // On create, at least one image required; on update, existing images are kept
            'images' => $isUpdate ? 'nullable|array' : 'required|array|min:1',
            'images.*' => 'nullable|mimes:jpeg,jpg,png|max:5120',
            'nda_document' => 'nullable|mimes:pdf,doc,docx|max:20480',
            'seller_financial_documents' => 'nullable|array',
            'seller_financial_documents.*' => 'nullable|mimes:pdf,doc,docx,xlsx|max:10240',
        ];
    }
}
