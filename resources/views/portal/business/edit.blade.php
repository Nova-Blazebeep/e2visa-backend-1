@extends('portal.layout.app')
@section('content')
    <style>
        .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }

        .existing-image-thumb {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        /* Consistent form layout */
        .form fieldset legend.col-form-label {
            font-weight: 500;
        }

        .required-legend::after {
            content: " *";
            color: #dc3545;
        }

        .form .input-group {
            margin-bottom: 0;
        }
    </style>
    <div id="fullscreen-loader" class="fullscreen-loader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
        </div>
    </div>
    <main class="px-4 py-4">
        <div class="bg-white">
            <form class="p-3 form" id="businessForm" enctype="multipart/form-data">
                <input type="hidden" name="business_id" value="{{ $business->id }}">
                <h5 class="mt-2">Business information</h5>
                <hr>

                {{-- Listing Heading at top — this is what users see on the front end --}}
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">
                        Listing Heading :
                        <br><small class="text-success fw-semibold">(this title will appear to users)</small>
                    </legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="Listing Heading"
                                value="{{ old('listing_heading', $business->listing_heading) }}"
                                id="listing_heading" name="listing_heading" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 d-none">
                    <legend class="col-form-label col-sm-3 pt-0">Seller :</legend>
                    <div class="col-sm-9">
                        <div class="form-check">
                            <div class="col-md-8 d-flex">
                                <select id="user_id" name="user_id" class="form-select" required>
                                    <option value="{{ old('user_id', $business->user_id) }}"
                                        {{ old('user_id', $business->user_id) ? 'selected' : '' }}>
                                        {{ $business->user->name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Listing Is :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                             {{-- First 2 rows --}}
                                @foreach ($listing_types->take(2) as $listing_type)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="listing_type"
                                            id="listingType{{ $listing_type->id }}"
                                            value="{{ $listing_type->listing_type }}"
                                            {{ old('listing_type', $business->listing_type) == $listing_type->listing_type ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label" for="listingType{{ $listing_type->id }}">
                                            {{ $listing_type->listing_type }}
                                        </label>
                                    </div>
                                @endforeach

                                {{-- Last row --}}
                                @if ($listing_types->count() > 2)
                                    @php $last = $listing_types->last(); @endphp
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="listing_type"
                                            id="listingType{{ $last->id }}"
                                            value="{{ $last->listing_type }}"
                                            {{ old('listing_type', $business->listing_type) == $last->listing_type ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label" for="listingType{{ $last->id }}">
                                            {{ $last->listing_type }}
                                        </label>
                                    </div>
                                @endif
                                <input type="hidden" name="listing_type_id" id="listing_type_id"
                                    value="{{ old('listing_type_id', $business->listing_type_id) }}">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Primary Business Category :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $business->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Business Subcategory :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="subcategory_id" name="sub_category_id" class="form-select" required>
                                <option value="">Select subcategory</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}"
                                        {{ old('sub_category_id', $business->sub_category_id) == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>

                {{-- Business is: optional, multi-select --}}
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Details :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8 inner-type">
                            @php $savedIds = old('business_type_ids', $business->business_type_ids ?? []); @endphp
                            @foreach ($business_types as $business_type)
                                <div class="form-check">
                                    <input class="form-check-input" name="business_type_ids[]"
                                        value="{{ $business_type->id }}" type="checkbox"
                                        id="businessType{{ $business_type->id }}"
                                        {{ in_array($business_type->id, (array) $savedIds) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="businessType{{ $business_type->id }}">
                                        {{ $business_type->business_type }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-12 inner-border">
                        <h5>Business Location</h5>
                    </div>
                </div>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Country :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="country_id" name="country_id" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('country_id', $business->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">State :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="state_id" name="state_id" class="form-select" required>
                                <option value="">Select State</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">County :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="county_id" name="county_id" class="form-select" required>
                                <option value="">Select County</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">City :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="City"
                                value="{{ old('zip_code', $business->zip_code) }}" id="zip_code" name="zip_code">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Street Address :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <input type="text" class="form-control pac-target-input" placeholder="Street Address"
                                value="{{ old('street_address', $business->street_address) }}" id="street_address"
                                name="street_address" autocomplete="off">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Make Confidential :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8 inner-type">
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_state" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_state"
                                    id="is_confidential_state" value="1"
                                    {{ old('is_confidential_state', $business->is_confidential_state) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_state">State</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_country" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_country"
                                    id="is_confidential_country" value="1"
                                    {{ old('is_confidential_country', $business->is_confidential_country) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_country">Country</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_city" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_city"
                                    id="is_confidential_city" value="1"
                                    {{ old('is_confidential_city', $business->is_confidential_city) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_city">City</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_address" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_address"
                                    id="is_confidential_address" value="1"
                                    {{ old('is_confidential_address', $business->is_confidential_address) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_address">Address</label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-12 inner-border">
                        <h5>Contact and Listing Reference Information</h5>
                    </div>
                </div>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Contact :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                                    <select id="contact_id" name="contact_id" class="form-select" required>
                                        <option value="">Select Contact</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                data-name="{{ $user->userInformation->name ?? $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->userInformation->phone_number ?? 'NA' }}"
                                                {{ old('contact_id', $business->contact_user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                {{ $user->userInformation->name ?? $user->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                <div class="contactInfoDiv mt-3">
                                    <p><a href="#" class="text-primary"
                                            id="display_contact_email">{{ old('contact_email', $business->contact_email ?? '') }}</a>
                                    </p>
                                    <p><a href="#" class="text-primary"
                                            id="display_contact_phone">{{ old('contact_phone', $business->contact_phone ?? '') }}</a>
                                    </p>
                                </div>

                                <input type="hidden" name="contact_name" id="hidden_contact_name"
                                    value="{{ old('contact_name', $business->contact_name ?? '') }}">
                                <input type="hidden" name="contact_email" id="hidden_contact_email"
                                    value="{{ old('contact_email', $business->contact_email ?? '') }}">
                                <input type="hidden" name="contact_phone" id="hidden_contact_phone"
                                    value="{{ old('contact_phone', $business->contact_phone ?? '') }}">
                                <input type="hidden" name="contact_user_id" id="hidden_contact_user_id"
                                    value="{{ old('contact_user_id', $business->contact_user_id ?? '') }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">
                        Listing Number :
                        <br><span class="text-danger fw-semibold">(Not shown on listing)</span>
                    </legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            {{-- text type allows letters, special characters, and numbers --}}
                            <input type="text" class="form-control" placeholder=""
                                id="listing_number" name="listing_number"
                                value="{{ old('listing_number', $business->listing_number) }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Non-Disclosure Agreement :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="form-check">
                                <input class="form-check-input use_ndaClass" type="radio"
                                    name="is_non_disclosure_agreement" id="nda_no" value="0"
                                    {{ old('is_non_disclosure_agreement', $business->is_non_disclosure_agreement) == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="nda_no">
                                    Do Not Use a Non-Disclosure Agreement
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input use_ndaClass" type="radio"
                                    name="is_non_disclosure_agreement" id="nda_yes" value="1"
                                    {{ old('is_non_disclosure_agreement', $business->is_non_disclosure_agreement) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="nda_yes">
                                    Upload Your Own Non-Disclosure Agreement
                                </label>
                            </div>
                            <div id="nda_upload_section" class="mt-2"
                                style="{{ old('is_non_disclosure_agreement', $business->is_non_disclosure_agreement) == 1 ? '' : 'display:none;' }}">
                                @if($business->nda_document_path)
                                    <p class="text-sm mb-1">
                                        Current NDA:
                                        <a href="{{ Storage::url($business->nda_document_path) }}" target="_blank" class="text-primary">
                                            View uploaded document
                                        </a>
                                    </p>
                                @endif
                                <input type="file" class="form-control" name="nda_document" id="nda_document"
                                    accept=".pdf,.doc,.docx">
                                <small class="text-muted">Accepted formats: PDF, DOC, DOCX (max 10MB){{ $business->nda_document_path ? ' — upload a new file to replace the existing one' : '' }}</small>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <h5 class="mt-2">Listing Summary and Description</h5>
                <hr>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Listing Summary :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <textarea class="form-control" id="listing_summary" name="listing_summary" placeholder="Listing Summary"
                                rows="3">{{ old('listing_summary', $business->listing_summary) }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-12 inner-border">
                        <h5>Financial Information</h5>
                    </div>
                </div>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Listing Price :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text" id="asking_price_label">$</span>
                                <input type="text" class="form-control" name="asking_price"
                                    id="asking_price" placeholder="Listing Price"
                                    value="{{ old('asking_price', $business->asking_price) }}"
                                    aria-label="Listing Price" aria-describedby="asking_price_label">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Cash Flow :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text" id="cash_flow_label">$</span>
                                <input type="text" class="form-control"
                                    value="{{ old('cash_flow', $business->cash_flow) }}" name="cash_flow"
                                    id="cash_flow" placeholder="Cash Flow" aria-label="Cash Flow"
                                    aria-describedby="cash_flow_label">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">EBITDA :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text" id="ebitdas_label">$</span>
                                <input type="text" class="form-control"
                                    value="{{ old('ebitdas', $business->ebitdas) }}" name="ebitdas" id="ebitdas"
                                    placeholder="EBITDA" aria-label="EBITDA" aria-describedby="ebitdas_label">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Gross Revenue :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text" id="gross_revenue_label">$</span>
                                <input type="text" class="form-control"
                                    value="{{ old('gross_revenue', $business->gross_revenue) }}" name="gross_revenue"
                                    id="gross_revenue" placeholder="Gross Revenue" aria-label="Gross Revenue"
                                    aria-describedby="gross_revenue_label">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Inventory :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="inventory_label">$</span>
                                <input type="text" class="form-control"
                                    value="{{ old('inventory', $business->inventory) }}" name="inventory"
                                    id="inventory" placeholder="Inventory" aria-label="Inventory"
                                    aria-describedby="inventory_label">
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="inventory_included_in_price" value="0">
                                <input class="form-check-input" type="checkbox" name="inventory_included_in_price"
                                    id="inventory_included_in_price" value="1"
                                    {{ old('inventory_included_in_price', $business->inventory_included_in_price) ? 'checked' : '' }}>
                                <label class="form-check-label" for="inventory_included_in_price">
                                    Included in Listing Price
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">FF&amp;E :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="ffe_label">$</span>
                                <input type="text" class="form-control" placeholder="FF&E"
                                    value="{{ old('ffe', $business->ffe) }}" name="ffe" id="ffe"
                                    aria-label="FFE" aria-describedby="ffe_label">
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="ffe_included_in_price" value="0">
                                <input class="form-check-input" type="checkbox" name="ffe_included_in_price"
                                    id="ffe_included_in_price" value="1"
                                    {{ old('ffe_included_in_price', $business->ffe_included_in_price) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ffe_included_in_price">
                                    Included in Listing Price
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Total Building Size :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="number" class="form-control" placeholder="Total Building Size"
                                    value="{{ old('building_size', $business->building_size) }}" name="building_size"
                                    id="building_size" aria-label="Building Size"
                                    aria-describedby="building_size_label">
                                <span class="input-group-text" id="building_size_label">sqft</span>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Seller Financing :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="form-check mb-2">
                                <input type="hidden" name="is_seller_financing_available" value="0">
                                <input class="form-check-input" type="checkbox"
                                    {{ old('is_seller_financing_available', $business->is_seller_financing_available) ? 'checked' : '' }}
                                    name="is_seller_financing_available" id="is_seller_financing_available"
                                    value="1">
                                <label class="form-check-label" for="is_seller_financing_available">
                                    Seller Financing Available
                                </label>
                            </div>
                            <input type="text" class="form-control" id="seller_note"
                                value="{{ old('seller_note', $business->seller_note) }}" name="seller_note"
                                placeholder="Notes About Seller Financing">
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-12 inner-border">
                        <h5>Detailed Business Information</h5>
                    </div>
                </div>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Number of Employees :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            {{-- text type allows entries like "12 full time, 2 part-time" --}}
                            <input type="text" class="form-control"
                                placeholder="e.g. 12 full time, 2 part-time"
                                value="{{ old('number_of_employees', $business->number_of_employees) }}"
                                id="number_of_employees" name="number_of_employees">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Year Established :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="year_established" name="year_established" class="form-select">
                                <option value="">Select Year</option>
                                @foreach ($established_years as $established_year)
                                    <option value="{{ $established_year->year }}"
                                        {{ old('year_established', $business->year_established) == $established_year->year ? 'selected' : '' }}>
                                        {{ $established_year->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Site/Infrastructure :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <textarea class="form-control" id="facilities" name="facilities" rows="3" placeholder="Site/Infrastructure">{{ old('facilities', $business->facilities) }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Training/Onboarding :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <textarea class="form-control" id="support_and_training" name="support_and_training" rows="3"
                                placeholder="Training/Onboarding">{{ old('support_and_training', $business->support_and_training) }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Motivation for Sale :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="Motivation for Sale"
                                id="reason_for_selling" name="reason_for_selling"
                                value="{{ old('reason_for_selling', $business->reason_for_selling) }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Competitive Landscape/Market Dynamics :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <textarea class="form-control" placeholder="Competitive Landscape/Market Dynamics" id="competition_market_pros_and_cons"
                                name="competition_market_pros_and_cons" rows="3">{{ old('competition_market_pros_and_cons', $business->competition_market_pros_and_cons) }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Opportunities/Development :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <textarea class="form-control" placeholder="Opportunities/Development"
                                id="growth_and_expansion_pros_and_cons" name="growth_and_expansion_pros_and_cons" rows="3">{{ old('growth_and_expansion_pros_and_cons', $business->growth_and_expansion_pros_and_cons) }}</textarea>
                        </div>
                    </div>
                </fieldset>

                {{-- Real Estate Section --}}
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Real Estate :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="real_estate_leased"
                                    id="real_estate_leased" value="1"
                                    {{ old('real_estate_leased', $business->real_estate_leased) ? 'checked' : '' }}>
                                <label class="form-check-label" for="real_estate_leased">Real Estate Leased</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="real_estate_available"
                                    id="real_estate_available" value="1"
                                    {{ old('real_estate_available', $business->real_estate_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="real_estate_available">Real Estate Available</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="real_estate_included"
                                    id="real_estate_included" value="1"
                                    {{ old('real_estate_included', $business->real_estate_included) ? 'checked' : '' }}>
                                <label class="form-check-label" for="real_estate_included">Real Estate Included</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="home_based_business"
                                    id="home_based_business" value="1"
                                    {{ old('home_based_business', $business->home_based_business) ? 'checked' : '' }}>
                                <label class="form-check-label" for="home_based_business">Home-Based Business</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Rent</span>
                                <input type="text" class="form-control" placeholder="Monthly Rent Amount"
                                    name="real_estate_rent" id="real_estate_rent"
                                    value="{{ old('real_estate_rent', $business->real_estate_rent) }}">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-12 inner-border">
                        <h5>Images</h5>
                    </div>
                </div>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Image :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            @include('portal.partials.image-dropzone', [
                                'currentImageUrl' => $business->business_images->first()
                                    ? asset('storage/' . $business->business_images->first()->image_path)
                                    : '',
                            ])
                            @if($business->business_images->count() == 0)
                                <p class="text-muted small mt-1">An image is required.</p>
                            @endif
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Document Upload :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            @include('portal.partials.document-dropzone', [
                                'currentDocUrl' => $business->financial_documents->first()
                                    ? asset('storage/' . $business->financial_documents->first()->document_path)
                                    : '',
                            ])
                            <p class="text-muted small mt-1">Uploading a new document will replace the current one.</p>
                        </div>
                    </div>
                </fieldset>
                <p class="mt-4">Please note that requesting more information typically lowers the number of contacts you
                    will receive</p>

                <input type="hidden" id="existing_images_count" value="{{ $existingImagesCount }}">
                <input type="hidden" id="existing_docs_count" value="{{ $existingDocsCount }}">
                <input type="hidden" id="is_publish" name="is_publish" value="0">
                <div class="d-flex justify-content-end">
                    <button type="button" onclick="publish(event)" class="btn btn-primary mt-2 mx-1">
                        Publish My Listing
                    </button>
                    <button type="button" onclick="saveDraft(event)" class="btn btn-secondary mt-2 mx-1">
                        Save &amp; Finish Later
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        const subcategoriesByCategory = {
            @foreach ($categories as $category)
                "{{ $category->id }}": [
                    @foreach ($category->subcategories as $sub)
                        {
                            id: "{{ $sub->id }}",
                            name: "{{ $sub->name }}"
                        },
                    @endforeach
                ],
            @endforeach
        };

        const categorySelect = document.getElementById('category_id');
        const subcategorySelect = document.getElementById('subcategory_id');

        function populateSubcategories(selectedCategoryId, selectedSubcategoryId = null) {
            subcategorySelect.innerHTML = '<option value="">Select subcategory</option>';

            if (subcategoriesByCategory[selectedCategoryId]) {
                subcategoriesByCategory[selectedCategoryId].forEach(subcat => {
                    const option = document.createElement('option');
                    option.value = subcat.id;
                    option.textContent = subcat.name;

                    if (selectedSubcategoryId && subcat.id == selectedSubcategoryId) {
                        option.selected = true;
                    }

                    subcategorySelect.appendChild(option);
                });
            }
        }

        categorySelect.addEventListener('change', function() {
            populateSubcategories(this.value);
        });

        window.addEventListener('DOMContentLoaded', () => {
            const oldCategory = "{{ old('category_id', $business->category_id) }}";
            const oldSubcategory = "{{ old('sub_category_id', $business->sub_category_id) }}";

            if (oldCategory) {
                categorySelect.value = oldCategory;
                populateSubcategories(oldCategory, oldSubcategory);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const countries = @json($countries);
            const states = @json($states);
            const counties = @json($counties);

            const countrySelect = document.getElementById('country_id');
            const stateSelect = document.getElementById('state_id');
            const countySelect = document.getElementById('county_id');

            const selectedCountryId = "{{ old('country_id', $business->country_id) }}";
            const selectedStateId = "{{ old('state_id', $business->state_id) }}";
            const selectedCountyId = "{{ old('county_id', $business->county_id) }}";

            function populateStates(countryId, selectedStateId = null) {
                stateSelect.innerHTML = '<option value="">Select State</option>';
                countySelect.innerHTML = '<option value="">Select County</option>';

                const filteredStates = states.filter(state => state.country_id == countryId);
                filteredStates.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    if (state.id == selectedStateId) {
                        option.selected = true;
                    }
                    stateSelect.appendChild(option);
                });

                if (selectedStateId) {
                    populateCounties(selectedStateId, selectedCountyId);
                }
            }

            function populateCounties(stateId, selectedCountyId = null) {
                countySelect.innerHTML = '<option value="">Select County</option>';

                const filteredCounties = counties.filter(county => county.state_id == stateId);
                filteredCounties.forEach(county => {
                    const option = document.createElement('option');
                    option.value = county.id;
                    option.textContent = county.name;
                    if (county.id == selectedCountyId) {
                        option.selected = true;
                    }
                    countySelect.appendChild(option);
                });
            }

            countrySelect.addEventListener('change', function() {
                populateStates(this.value);
            });

            stateSelect.addEventListener('change', function() {
                populateCounties(this.value);
            });

            if (selectedCountryId) {
                countrySelect.value = selectedCountryId;
                populateStates(selectedCountryId, selectedStateId);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            @if (session(SUCCESS_CODE))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session(SUCCESS_CODE) }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @elseif (session(FAILURE_CODE))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session(FAILURE_CODE) }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif
        });

        function publish(e) {
            e.preventDefault();

            const errors = [];
            const existingImagesCount = parseInt(document.getElementById('existing_images_count').value) || 0;

            // Only the required fields per client spec
            const requiredFields = [
                { id: 'category_id', name: 'Primary Business Category' },
                { id: 'subcategory_id', name: 'Business Subcategory' },
                { id: 'country_id', name: 'Country' },
                { id: 'state_id', name: 'State' },
                { id: 'county_id', name: 'County' },
                { id: 'contact_id', name: 'Contact' },
                { id: 'listing_number', name: 'Listing Number' },
                { id: 'listing_heading', name: 'Listing Heading' },
                { id: 'listing_summary', name: 'Listing Summary' },
                { id: 'asking_price', name: 'Listing Price' },
                { id: 'year_established', name: 'Year Established' },
            ];

            requiredFields.forEach(field => {
                const value = document.getElementById(field.id)?.value.trim();
                if (!value) errors.push(`Please enter ${field.name}.`);
            });

            const radios = [
                { name: 'listing_type', label: 'Listing Type' },
                { name: 'is_non_disclosure_agreement', label: 'NDA Option' }
            ];

            radios.forEach(radio => {
                const checked = document.querySelector(`input[name="${radio.name}"]:checked`);
                if (!checked) errors.push(`Please select ${radio.label}.`);
            });

            // Images required only if no existing images saved
            const images = document.getElementById('images');
            if (!images.files.length && existingImagesCount === 0) {
                errors.push("Please upload an image.");
            }

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: errors[0],
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }

            const formData = new FormData(document.getElementById('businessForm'));
            formData.set('is_publish', 1);
            formData.set('_token', document.querySelector('input[name="_token"]').value);

            document.getElementById('fullscreen-loader').style.display = 'block';
            fetch("/business/update", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(res => {
                    document.getElementById('fullscreen-loader').style.display = 'none';
                    const contentType = res.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return res.json();
                    } else {
                        throw new Error("Server returned non-JSON response.");
                    }
                })
                .then(data => {
                    if (data.code === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Listing published successfully!',
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        window.location.href = "/business";
                    } else {
                        throw new Error(data.message || "Unexpected error");
                    }
                })
                .catch(err => {
                    document.getElementById('fullscreen-loader').style.display = 'none';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message,
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
        }

        function saveDraft(e) {
            e.preventDefault();

            const formData = new FormData(document.getElementById('businessForm'));
            formData.set('is_publish', 0);
            formData.set('_token', document.querySelector('input[name="_token"]').value);

            document.getElementById('fullscreen-loader').style.display = 'block';
            fetch("/business/update", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(res => {
                    document.getElementById('fullscreen-loader').style.display = 'none';
                    const contentType = res.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return res.json();
                    } else {
                        throw new Error("Server returned non-JSON response.");
                    }
                })
                .then(data => {
                    if (data.code === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Draft saved successfully!',
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        window.location.href = "/business";
                    } else {
                        throw new Error(data.message || "Unexpected error");
                    }
                })
                .catch(err => {
                    document.getElementById('fullscreen-loader').style.display = 'none';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message,
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
        }
    </script>
    <script>
        const usersData = @json($usersData);

        document.getElementById('contact_id').addEventListener('change', function() {
            const userId = this.value;
            if (usersData[userId]) {
                document.getElementById('display_contact_email').textContent = usersData[userId].email;
                document.getElementById('display_contact_email').href = "mailto:" + usersData[userId].email;

                document.getElementById('display_contact_phone').textContent = usersData[userId].phone;
                document.getElementById('display_contact_phone').href = "tel:" + usersData[userId].phone;

                document.getElementById('hidden_contact_name').value = usersData[userId].name;
                document.getElementById('hidden_contact_email').value = usersData[userId].email;
                document.getElementById('hidden_contact_phone').value = usersData[userId].phone;
                document.getElementById('hidden_contact_user_id').value = userId;
            }
        });

        // Show/hide NDA upload section based on radio selection
        document.querySelectorAll('.use_ndaClass').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('nda_upload_section').style.display =
                    this.value === '1' ? '' : 'none';
            });
        });
    </script>
@endsection
