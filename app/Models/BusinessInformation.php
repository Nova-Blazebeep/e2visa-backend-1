<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessInformation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id',
        'country_id',
        'state_id',
        'county_id',
        'business_type_id',
        'business_type_ids',
        'listing_type',
        'business_name',
        'zip_code',
        'street_address',
        'is_confidential_state',
        'is_confidential_country',
        'is_confidential_city',
        'is_confidential_address',
        'is_seller_financing_available',
        'confidential_fields',
        'contact_name',
        'contact_email',
        'contact_phone',
        'listing_number',
        'is_non_disclosure_agreement',
        'nda_option',
        'nda_document_path',
        'listing_heading',
        'business_website',
        'is_website_confidential',
        'listing_summary',
        'asking_price',
        'sale_price',
        'monthly_rent',
        'cash_flow',
        'ebitdas',
        'gross_revenue',
        'inventory',
        'inventory_included_in_price',
        'ffe',
        'ffe_included_in_price',
        'real_estate_status',
        'building_size',
        'seller_financing_available',
        'seller_note',
        'number_of_employees',
        'year_established',
        'facilities',
        'support_and_training',
        'reason_for_selling',
        'competition_market_pros_and_cons',
        'growth_and_expansion_pros_and_cons',
        'real_estate_leased',
        'real_estate_available',
        'real_estate_included',
        'home_based_business',
        'real_estate_rent',
        'is_required_buyer_telephone_number',
        'is_required_buyer_zip_code',
        'is_required_buyer_available_funds',
        'is_required_buyer_timeframe',
        'is_publish',
        'buyer_email_response_options',
        'listing_type_id',
        'contact_user_id',
        'property_type_id',
        'property_type',
        'rooms',
        'baths',
        'business_type',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'business_type_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business_images()
    {
        return $this->hasMany(BusinessImage::class, 'business_information_id');
    }

    public function financial_documents()
    {
        return $this->hasMany(SellerFinancialDocument::class, 'business_information_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'sub_category_id');
    }
}
