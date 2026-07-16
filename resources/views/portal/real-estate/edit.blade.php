@extends('portal.layout.app')
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .btn-primary { background-color: #0d6efd !important; border-color: #0d6efd !important; }
        .text-primary { color: #0d6efd !important; }
        .existing-image-thumb { width:100px; height:75px; object-fit:cover; border:1px solid #dee2e6; border-radius:4px; }
        #location_picker_map { height:320px; border-radius:8px; border:1px solid #dee2e6; cursor:crosshair; }

        /* Consistent form layout */
        .form fieldset legend.col-form-label { font-weight: 500; }
        .required-legend::after { content: " *"; color: #dc3545; }
        .form .input-group { margin-bottom: 0; }
    </style>
    <div id="fullscreen-loader" class="fullscreen-loader">
        <div class="loader-content"><div class="loader-spinner"></div></div>
    </div>
    <main class="px-4 py-4">
        <div class="bg-white">
            <form class="p-3 form" id="businessForm" enctype="multipart/form-data">
                <input type="hidden" name="business_id" value="{{ $business->id }}">
                <h5 class="mt-2">Real Estate information</h5>
                <hr>

                {{-- Listing Title --}}
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">
                        Listing Title :
                        <br><small class="text-success fw-semibold">(this title will appear to users)</small>
                    </legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="Listing Title"
                                value="{{ old('listing_heading', $business->listing_heading) }}"
                                id="listing_heading" name="listing_heading" required autocomplete="off">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Property Type :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select class="form-select" id="property_type" name="property_type" required>
                                <option value="">Select Property Type</option>
                                <optgroup label="── For Sale ──">
                                    @foreach ($property_types->filter(fn($t) => str_contains($t->name, '[for sale]')) as $pt)
                                        <option value="{{ $pt->id }}" {{ (old('property_type', $business->property_type_id) == $pt->id) ? 'selected' : '' }}>{{ $pt->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="── For Rent/Lease ──">
                                    @foreach ($property_types->filter(fn($t) => str_contains($t->name, '[for rent/lease]')) as $pt)
                                        <option value="{{ $pt->id }}" {{ (old('property_type', $business->property_type_id) == $pt->id) ? 'selected' : '' }}>{{ $pt->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3" id="sale_price_group" style="display:none">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Sale Price :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" placeholder="e.g. 450000"
                                    value="{{ old('sale_price', $business->sale_price) }}" id="sale_price" name="sale_price" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3" id="monthly_rent_group" style="display:none">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Monthly Rent :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" placeholder="e.g. 1500"
                                    value="{{ old('monthly_rent', $business->monthly_rent) }}" id="monthly_rent" name="monthly_rent" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Rooms :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select class="form-select" id="rooms" name="rooms" required>
                                <option value="">Select Rooms</option>
                                @foreach(['1'=>'1+','2'=>'2+','3'=>'3+','4'=>'4+','5'=>'5+','6'=>'6+'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('rooms', $business->rooms) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Baths :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select class="form-select" id="bath" name="bath" required>
                                <option value="">Select Bath</option>
                                @foreach(['1'=>'1+','2'=>'2+','3'=>'3+','4'=>'4+','5'=>'5+','6'=>'6+'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('bath', $business->baths) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Square Footage :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select class="form-select" id="building_size" name="building_size" required>
                                <option value="">Select Square Footage</option>
                                <option value="0"    {{ old('building_size', $business->building_size) == '0'    ? 'selected' : '' }}>Under 1,000 sq ft</option>
                                <option value="1000" {{ old('building_size', $business->building_size) == '1000' ? 'selected' : '' }}>1,000 – 2,000 sq ft</option>
                                <option value="2000" {{ old('building_size', $business->building_size) == '2000' ? 'selected' : '' }}>2,000 – 3,500 sq ft</option>
                                <option value="3500" {{ old('building_size', $business->building_size) == '3500' ? 'selected' : '' }}>3,500 – 5,000 sq ft</option>
                                <option value="5001" {{ old('building_size', $business->building_size) == '5001' ? 'selected' : '' }}>Above 5,000 sq ft</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 d-none">
                    <legend class="col-form-label col-sm-3 pt-0">Seller :</legend>
                    <div class="col-sm-9">
                        <select id="user_id" name="user_id" class="form-select" required>
                            <option value="{{ old('user_id', $business->user_id) }}" selected>{{ $business->user->name }}</option>
                        </select>
                    </div>
                </fieldset>

                {{-- Listing Type — Residential first --}}
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Listing Type :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            @php
                                $reTypes = $listing_types->skip(2)->sortBy(function($t) {
                                    $n = strtolower($t->listing_type);
                                    if (str_contains($n, 'residential') && str_contains($n, 'sale'))  return '1';
                                    if (str_contains($n, 'residential') && str_contains($n, 'lease')) return '2';
                                    if (str_contains($n, 'commercial')  && str_contains($n, 'sale'))  return '3';
                                    if (str_contains($n, 'commercial')  && str_contains($n, 'lease')) return '4';
                                    return '5';
                                });
                            @endphp
                            @foreach ($reTypes as $listing_type)
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
                            <input type="hidden" name="listing_type_id" id="listing_type_id" value="{{ old('listing_type_id', $business->listing_type_id) }}">
                        </div>
                    </div>
                </fieldset>

                <div class="row"><div class="col-12 inner-border"><h5>Real Estate Location</h5></div></div>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Real Estate Country :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="country_id" name="country_id" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $business->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Real Estate State :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <select id="state_id" name="state_id" class="form-select" required>
                                <option value="">Select State</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">Real Estate County :</legend>
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
                            <input type="text" class="form-control" placeholder="City" value="{{ old('zip_code', $business->zip_code) }}" id="zip_code" name="zip_code">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Street Address :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="Street Address" value="{{ old('street_address', $business->street_address) }}" id="street_address" name="street_address" autocomplete="off">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Pin Location on Map :</legend>
                    <div class="col-sm-9">
                        <div class="input-group mb-2">
                            <input type="text" id="address_search" class="form-control" placeholder="Search address (e.g. 123 Main St, Sarasota, FL)..." onkeydown="if(event.key==='Enter'){event.preventDefault();searchAddress();}">
                            <button type="button" class="btn btn-primary" onclick="searchAddress()">Search</button>
                        </div>
                        <div id="location_picker_map"></div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-5">
                                <label class="form-label small text-muted mb-1">Latitude</label>
                                <input type="number" step="any" class="form-control form-control-sm" id="latitude" name="latitude" value="{{ old('latitude', $business->latitude) }}" placeholder="Auto-filled from map">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small text-muted mb-1">Longitude</label>
                                <input type="number" step="any" class="form-control form-control-sm" id="longitude" name="longitude" value="{{ old('longitude', $business->longitude) }}" placeholder="Auto-filled from map">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="clearPin()">Clear Pin</button>
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">Search for an address, or click anywhere on the map to drop a pin. Drag the pin to fine-tune the location.</small>
                    </div>
                </fieldset>
                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Make Confidential :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8 inner-type">
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_state" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_state" id="is_confidential_state" value="1" {{ old('is_confidential_state', $business->is_confidential_state) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_state">State</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_country" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_country" id="is_confidential_country" value="1" {{ old('is_confidential_country', $business->is_confidential_country) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_country">Country</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_city" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_city" id="is_confidential_city" value="1" {{ old('is_confidential_city', $business->is_confidential_city) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_city">City</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="is_confidential_address" value="0">
                                <input class="form-check-input" type="checkbox" name="is_confidential_address" id="is_confidential_address" value="1" {{ old('is_confidential_address', $business->is_confidential_address) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_confidential_address">Address</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="row"><div class="col-12 inner-border"><h5>Contact and Listing Reference Information</h5></div></div>

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
                                <p><a href="#" class="text-primary" id="display_contact_email">{{ old('contact_email', $business->contact_email ?? '') }}</a></p>
                                <p><a href="#" class="text-primary" id="display_contact_phone">{{ old('contact_phone', $business->contact_phone ?? '') }}</a></p>
                            </div>
                            <input type="hidden" name="contact_name" id="hidden_contact_name" value="{{ old('contact_name', $business->contact_name ?? '') }}">
                            <input type="hidden" name="contact_email" id="hidden_contact_email" value="{{ old('contact_email', $business->contact_email ?? '') }}">
                            <input type="hidden" name="contact_phone" id="hidden_contact_phone" value="{{ old('contact_phone', $business->contact_phone ?? '') }}">
                            <input type="hidden" name="contact_user_id" id="hidden_contact_user_id" value="{{ old('contact_user_id', $business->contact_user_id ?? '') }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0 required-legend">
                        Listing Number :<br><span class="inner-coler">(Not shown on listing)</span>
                    </legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="Office Reference ID"
                                id="listing_number" name="listing_number"
                                value="{{ old('listing_number', $business->listing_number) }}">
                        </div>
                    </div>
                </fieldset>

                <h5 class="mt-2">Listing Summary and Description</h5><hr>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Listing Summary/Description :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            <textarea class="form-control" id="listing_summary" name="listing_summary"
                                placeholder="Listing Summary/Description — include all features, details, and amenities"
                                rows="8">{{ old('listing_summary', $business->listing_summary) }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <div class="row"><div class="col-12 inner-border"><h5>Buyer Email Response Options</h5></div></div>
                <fieldset class="row mb-3">
                    <legend class="col-form-label col-sm-3 pt-0">Options :</legend>
                    <div class="col-sm-9">
                        <div class="form-check">
                            <input type="hidden" name="is_required_buyer_telephone_number" value="0">
                            <input class="form-check-input" type="checkbox" name="is_required_buyer_telephone_number" id="is_required_buyer_telephone_number" value="1" {{ old('is_required_buyer_telephone_number', $business->is_required_buyer_telephone_number) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required_buyer_telephone_number">Require the buyer's telephone number</label>
                        </div>
                        <div class="form-check">
                            <input type="hidden" name="is_required_buyer_available_funds" value="0">
                            <input class="form-check-input" type="checkbox" name="is_required_buyer_available_funds" id="is_required_buyer_available_funds" value="1" {{ old('is_required_buyer_available_funds', $business->is_required_buyer_available_funds) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required_buyer_available_funds">Ask for the available funds a buyer has</label>
                        </div>
                        <div class="form-check">
                            <input type="hidden" name="is_required_buyer_timeframe" value="0">
                            <input class="form-check-input" type="checkbox" name="is_required_buyer_timeframe" id="is_required_buyer_timeframe" value="1" {{ old('is_required_buyer_timeframe', $business->is_required_buyer_timeframe) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required_buyer_timeframe">Ask what timeframe the buyer has</label>
                        </div>
                    </div>
                </fieldset>

                <div class="row"><div class="col-12 inner-border"><h5>Images</h5></div></div>

                <fieldset class="row mb-3 mt-3">
                    <legend class="col-form-label col-sm-3 pt-0">Image :</legend>
                    <div class="col-sm-9">
                        <div class="col-md-8">
                            @include('portal.partials.image-dropzone', [
                                'currentImageUrl' => $business->business_images->first()
                                    ? asset('storage/' . $business->business_images->first()->image_path)
                                    : '',
                            ])
                        </div>
                    </div>
                </fieldset>

                <input type="hidden" id="existing_images_count" value="{{ $existingImagesCount }}">
                <input type="hidden" id="is_publish" name="is_publish" value="0">
                <div class="d-flex justify-content-end">
                    <button type="button" onclick="publish(event)" class="btn btn-primary mt-2 mx-1">Publish My Listing</button>
                    <button type="button" onclick="saveDraft(event)" class="btn btn-secondary mt-2 mx-1">Save &amp; Finish Later</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // ── Location map picker ───────────────────────────────────────────────
        var _locMap, _locMarker;

        function initLocationPicker(defaultLat, defaultLng) {
            _locMap = L.map('location_picker_map', {
                center: defaultLat ? [defaultLat, defaultLng] : [39.5, -98.35],
                zoom:   defaultLat ? 15 : 4,
                scrollWheelZoom: true,
            });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(_locMap);

            if (defaultLat && defaultLng) {
                _locMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(_locMap);
                _locMarker.on('dragend', _onMarkerDrag);
            }

            _locMap.on('click', function (e) {
                var lat = e.latlng.lat, lng = e.latlng.lng;
                if (_locMarker) { _locMarker.setLatLng([lat, lng]); }
                else { _locMarker = L.marker([lat, lng], { draggable: true }).addTo(_locMap); _locMarker.on('dragend', _onMarkerDrag); }
                document.getElementById('latitude').value  = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
            });
        }

        function _onMarkerDrag(e) {
            var pos = e.target.getLatLng();
            document.getElementById('latitude').value  = pos.lat.toFixed(7);
            document.getElementById('longitude').value = pos.lng.toFixed(7);
        }

        function searchAddress() {
            var q = document.getElementById('address_search').value.trim();
            if (!q) return;
            fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q))
                .then(function(r){ return r.json(); })
                .then(function(data) {
                    if (!data || data.length === 0) { alert('Address not found. Try a more specific search.'); return; }
                    var lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                    _locMap.setView([lat, lng], 16);
                    if (_locMarker) { _locMarker.setLatLng([lat, lng]); }
                    else { _locMarker = L.marker([lat, lng], { draggable: true }).addTo(_locMap); _locMarker.on('dragend', _onMarkerDrag); }
                    document.getElementById('latitude').value  = lat.toFixed(7);
                    document.getElementById('longitude').value = lng.toFixed(7);
                })
                .catch(function(){ alert('Search failed. Check your internet connection.'); });
        }

        function clearPin() {
            if (_locMarker) { _locMarker.remove(); _locMarker = null; }
            document.getElementById('latitude').value  = '';
            document.getElementById('longitude').value = '';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const countries = @json($countries);
            const states    = @json($states);
            const counties  = @json($counties);
            const countrySelect = document.getElementById('country_id');
            const stateSelect   = document.getElementById('state_id');
            const countySelect  = document.getElementById('county_id');
            const selectedCountryId = "{{ old('country_id', $business->country_id) }}";
            const selectedStateId   = "{{ old('state_id',   $business->state_id) }}";
            const selectedCountyId  = "{{ old('county_id',  $business->county_id) }}";

            function populateStates(countryId, selectedStateId = null) {
                stateSelect.innerHTML  = '<option value="">Select State</option>';
                countySelect.innerHTML = '<option value="">Select County</option>';
                const filteredStates = states.filter(s => s.country_id == countryId);
                filteredStates.forEach(state => {
                    const o = document.createElement('option');
                    o.value = state.id; o.textContent = state.name;
                    if (state.id == selectedStateId) o.selected = true;
                    stateSelect.appendChild(o);
                });
                if (selectedStateId) populateCounties(selectedStateId, selectedCountyId);
            }
            function populateCounties(stateId, selectedCountyId = null) {
                countySelect.innerHTML = '<option value="">Select County</option>';
                const filteredCounties = counties.filter(c => c.state_id == stateId);
                filteredCounties.forEach(county => {
                    const o = document.createElement('option');
                    o.value = county.id; o.textContent = county.name;
                    if (county.id == selectedCountyId) o.selected = true;
                    countySelect.appendChild(o);
                });
            }
            countrySelect.addEventListener('change', function() { populateStates(this.value); });
            stateSelect.addEventListener('change',   function() { populateCounties(this.value); });
            if (selectedCountryId) {
                countrySelect.value = selectedCountryId;
                populateStates(selectedCountryId, selectedStateId);
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            @if (session(SUCCESS_CODE))
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session(SUCCESS_CODE) }}', showConfirmButton: false, timer: 3000 });
            @elseif (session(FAILURE_CODE))
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: '{{ session(FAILURE_CODE) }}', showConfirmButton: false, timer: 3000 });
            @endif
        });

        const usersData = @json($usersData);
        document.getElementById('contact_id').addEventListener('change', function() {
            const userId = this.value;
            if (usersData[userId]) {
                document.getElementById('display_contact_email').textContent = usersData[userId].email;
                document.getElementById('display_contact_email').href = "mailto:" + usersData[userId].email;
                document.getElementById('display_contact_phone').textContent = usersData[userId].phone;
                document.getElementById('display_contact_phone').href = "tel:" + usersData[userId].phone;
                document.getElementById('hidden_contact_name').value  = usersData[userId].name;
                document.getElementById('hidden_contact_email').value = usersData[userId].email;
                document.getElementById('hidden_contact_phone').value = usersData[userId].phone;
                document.getElementById('hidden_contact_user_id').value = userId;
            }
        });

        function publish(e) {
            e.preventDefault();
            const errors = [];
            const existingImagesCount = parseInt(document.getElementById('existing_images_count').value) || 0;

            const requiredFields = [
                { id: 'listing_heading', name: 'Listing Title' },
                { id: 'country_id',     name: 'Country' },
                { id: 'state_id',       name: 'State' },
                { id: 'county_id',      name: 'County' },
                { id: 'contact_id',     name: 'Contact' },
                { id: 'listing_number', name: 'Listing Number' },
            ];
            requiredFields.forEach(field => {
                const value = document.getElementById(field.id)?.value.trim();
                if (!value) errors.push(`Please enter ${field.name}.`);
            });

            const rooms = document.getElementById('rooms')?.value;
            const bath  = document.getElementById('bath')?.value;
            if (!rooms) errors.push('Please select Rooms.');
            if (!bath)  errors.push('Please select Bath.');

            const propertyTypeSelect = document.getElementById('property_type');
            const propertyType = propertyTypeSelect?.value;
            if (!propertyType) errors.push('Please select Property Type.');

            if (propertyType) {
                const isRent = _isRentPropertyType(propertyTypeSelect);
                if (isRent) {
                    if (!document.getElementById('monthly_rent')?.value) errors.push('Please enter Monthly Rent.');
                } else {
                    if (!document.getElementById('sale_price')?.value) errors.push('Please enter Sale Price.');
                }
            }

            const radios = [{ name: 'listing_type', label: 'Listing Type' }];
            radios.forEach(radio => {
                const checked = document.querySelector(`input[name="${radio.name}"]:checked`);
                if (!checked) errors.push(`Please select ${radio.label}.`);
            });

            const images = document.getElementById('images');
            if (!images.files.length && existingImagesCount === 0) errors.push("Please upload at least one gallery image.");

            if (errors.length > 0) {
                Swal.fire({ icon: 'error', title: errors[0], toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
                return;
            }

            const formData = new FormData(document.getElementById('businessForm'));
            formData.set('is_publish', 1);
            formData.set('_token', document.querySelector('input[name="_token"]').value);
            document.getElementById('fullscreen-loader').style.display = 'block';
            fetch("/real-estate/update", {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => { document.getElementById('fullscreen-loader').style.display = 'none'; if (res.headers.get("content-type")?.includes("application/json")) return res.json(); throw new Error("Server returned non-JSON response."); })
            .then(data => { if (data.code === 200) { Swal.fire({ icon: 'success', title: 'Listing published!', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false }); window.location.href = "/real-estate"; } else throw new Error(data.message || "Unexpected error"); })
            .catch(err => { document.getElementById('fullscreen-loader').style.display = 'none'; Swal.fire({ icon: 'error', title: 'Error', text: err.message, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false }); });
        }

        function saveDraft(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('businessForm'));
            formData.set('is_publish', 0);
            formData.set('_token', document.querySelector('input[name="_token"]').value);
            document.getElementById('fullscreen-loader').style.display = 'block';
            fetch("/real-estate/update", {
                method: "POST",
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => { document.getElementById('fullscreen-loader').style.display = 'none'; if (res.headers.get("content-type")?.includes("application/json")) return res.json(); throw new Error("Server returned non-JSON response."); })
            .then(data => { if (data.code === 200) { Swal.fire({ icon: 'success', title: 'Draft saved!', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false }); window.location.href = "/real-estate"; } else throw new Error(data.message || "Unexpected error"); })
            .catch(err => { document.getElementById('fullscreen-loader').style.display = 'none'; Swal.fire({ icon: 'error', title: 'Error', text: err.message, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false }); });
        }

        // ── Show Sale Price or Monthly Rent based on selected Property Type ──
        function _isRentPropertyType(selectEl) {
            const text = selectEl.options[selectEl.selectedIndex] ? selectEl.options[selectEl.selectedIndex].text : '';
            return text.toLowerCase().includes('[for rent/lease]');
        }

        function togglePriceFields(clearOther = true) {
            const propertyTypeSelect = document.getElementById('property_type');
            const saleGroup  = document.getElementById('sale_price_group');
            const rentGroup  = document.getElementById('monthly_rent_group');
            const hasType    = !!propertyTypeSelect.value;
            const isRent     = hasType && _isRentPropertyType(propertyTypeSelect);

            if (isRent) {
                saleGroup.style.display = 'none';
                rentGroup.style.display = '';
                if (clearOther) document.getElementById('sale_price').value = '';
            } else {
                rentGroup.style.display = 'none';
                saleGroup.style.display = hasType ? '' : 'none';
                if (clearOther) document.getElementById('monthly_rent').value = '';
            }
        }

        document.getElementById('property_type').addEventListener('change', () => togglePriceFields(true));
        // Initialize on page load — reveal the correct field without wiping the stored value.
        togglePriceFields(false);
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var _existingLat = parseFloat("{{ old('latitude', $business->latitude ?? '') }}") || null;
        var _existingLng = parseFloat("{{ old('longitude', $business->longitude ?? '') }}") || null;
        initLocationPicker(_existingLat, _existingLng);
    </script>
@endsection
