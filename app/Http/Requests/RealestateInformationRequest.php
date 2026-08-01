<?php

namespace App\Http\Requests;

use App\Models\PropertyType;
use Illuminate\Foundation\Http\FormRequest;

class RealestateInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Basic sanity check only — the actual badge-eligibility/ownership
        // rules live in the controller, so a rejection returns the app's
        // normal makeResponse() envelope instead of a bare 403.
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('property_type') && is_numeric($this->property_type)) {
            $propertyType = PropertyType::find($this->property_type);
            if ($propertyType) {
                $this->merge([
                    'property_type_id' => $propertyType->id,
                    'property_type' => $propertyType->name,
                ]);
            }
        }

        if ($this->has('bath') && !$this->has('baths')) {
            $this->merge(['baths' => $this->bath]);
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->has('business_id') && !empty($this->business_id);

        return [
            'business_name'    => 'nullable|string',
            'user_id'          => 'required|exists:users,id',
            'category_id'      => 'nullable|exists:categories,id',
            'sub_category_id'  => 'nullable|exists:sub_categories,id',
            'country_id'       => 'required|exists:countries,id',
            'state_id'         => 'required|exists:states,id',
            'county_id'        => 'required|exists:counties,id',
            'listing_type_id'  => 'nullable|exists:listing_types,id',
            'contact_user_id'  => 'nullable|exists:users,id',
            'listing_type'     => 'required|string',
            'business_type_id' => 'nullable|exists:business_types,id',
            'zip_code'         => 'nullable|string',
            'street_address'   => 'nullable|string',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',

            'is_confidential_state'   => 'boolean',
            'is_confidential_country' => 'boolean',
            'is_confidential_city'    => 'boolean',
            'is_confidential_address' => 'boolean',

            'contact_name'   => 'required|string',
            'contact_email'  => 'required|email',
            'contact_phone'  => 'required|string',
            'listing_number' => 'required|string',

            'is_non_disclosure_agreement' => 'boolean',

            'listing_heading'       => 'required|string',
            'business_website'      => 'nullable|string',
            'is_website_confidential' => 'boolean',
            'listing_summary'       => 'nullable|string',

            'asking_price'              => 'nullable|numeric',
            'sale_price'                => 'nullable|numeric',
            'monthly_rent'              => 'nullable|numeric',
            'cash_flow'                 => 'nullable|numeric',
            'ebitdas'                   => 'nullable|numeric',
            'gross_revenue'             => 'nullable|numeric',
            'inventory'                 => 'nullable|numeric',
            'inventory_included_in_price' => 'boolean',
            'ffe'                       => 'nullable|numeric',

            'real_estate_status'          => 'nullable|string',
            'building_size'               => 'nullable|string',
            'is_seller_financing_available' => 'boolean',
            'seller_note'                 => 'nullable|string',
            'year_established'            => 'nullable|string',

            'facilities'                       => 'nullable|string',
            'support_and_training'             => 'nullable|string',
            'reason_for_selling'               => 'nullable|string',
            'competition_market_pros_and_cons' => 'nullable|string',
            'growth_and_expansion_pros_and_cons' => 'nullable|string',
            'business_type'                    => 'nullable|string',

            'property_type_id' => 'required|exists:property_types,id',
            'property_type'    => 'required|string',
            // rooms/baths allow "any", "1"–"9", "10+" so string not integer
            'rooms' => 'required|string',
            'baths' => 'required|string',

            'is_required_buyer_telephone_number' => 'boolean',
            'is_required_buyer_zip_code'         => 'boolean',
            'is_required_buyer_available_funds'  => 'boolean',
            'is_required_buyer_timeframe'        => 'boolean',
            'is_publish' => 'boolean',

            'images'    => $isUpdate ? 'nullable|array' : 'nullable|array',
            'images.*'  => 'nullable|mimes:jpeg,jpg,png|max:5120',
            'seller_financial_documents'   => 'nullable|array',
            'seller_financial_documents.*' => 'nullable|mimes:pdf,doc,docx,xlsx|max:10240',
        ];
    }
}
