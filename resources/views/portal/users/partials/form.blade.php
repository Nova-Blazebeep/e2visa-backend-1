<style>
    .margin-left-10 {
        margin-left: -10px !important;
    }

    .swal2-container {
        z-index: 10999 !important;
    }

    .swal-toast-zindex {
        z-index: 11000 !important;
    }
</style>

<form id="usermodalForm" action="{{ route('portal.users.storeOrUpdate', $user->id ?? null) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @php $info = optional($user->userInformation); @endphp

    <div class="row">

        <div class="col-md-6 mb-3">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ $user->name ?? '' }}">
        </div>

        <div class="col-md-6 mb-3">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ $user->email ?? '' }}">
        </div>

        <div class="col-md-6 mb-3">
            <label>Password @if (empty($user->id))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" name="password" class="form-control"
                placeholder="{{ empty($user->id) ? '' : 'Leave blank to keep current password' }}">
        </div>

        <div class="col-md-6 mb-3">
            <label>Role <span class="text-danger">*</span></label>
            <select name="role" class="form-control">
                <option value="">Select Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label>Phone Number <span class="text-danger">*</span></label>
            <input type="text" name="phone_number" class="form-control" value="{{ $info->phone_number }}">
        </div>

        <div class="col-md-6 mb-3">
            <label>Address <span class="text-danger">*</span></label>
            <input type="text" name="address" class="form-control" value="{{ $info->address }}">
        </div>

        <div class="col-md-4 mb-3">
            <label>Country <span class="text-danger">*</span></label>
            <select name="country_id" id="country" class="form-control">
                <option value="">Select Country</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ $info->country_id == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label>State <span class="text-danger">*</span></label>
            <select name="state_id" id="state" class="form-control">
                <option value="">Select State</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label>County</label>
            <select name="county_id" id="county" class="form-control">
                <option value="">Select County</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label>City <span class="text-danger">*</span></label>
            <input type="text" name="city" class="form-control" value="{{ $info->city }}">
        </div>

        <div class="col-md-4 mb-3">
            <label>Zipcode <span class="text-danger">*</span></label>
            <input type="text" name="zipcode" class="form-control" value="{{ $info->zipcode }}">
        </div>

        <div class="col-md-4 mb-3 form-check d-flex align-items-center" style="padding-top: 30px;">
            <input type="checkbox" class="form-check-input margin-left-10" name="subscribe_for_newsletter"
                value="1" {{ $info->subscribe_for_newsletter ? 'checked' : '' }}>
            <label class="form-check-label ms-2">Subscribe to Newsletter?</label>
        </div>

        <div class="col-md-12 mb-3">
            <label>About</label>
            <textarea name="about" id="about" cols="15" rows="5" class="form-control">{{ trim($user->userInformation->about ?? '') }}</textarea>
        </div>

        <div class="col-md-12 mb-3">
            <label>Licensed States <small class="text-muted">(select up to 3 states where this professional is licensed)</small></label>
            <select name="licensed_states[]" id="licensedStates" multiple class="form-control" size="6">
                <option value="">Loading states…</option>
            </select>
            <small class="text-muted">Hold Ctrl / Cmd to select multiple. Maximum 3 states.</small>
        </div>

        <div class="col-md-6 mb-3">
            <label>Upload Image</label>
            <input type="file" name="image" class="form-control">
        </div>
    </div>

   <button type="submit" class="btn btn-primary" id="submitBtn">
        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
        Submit
    </button>
</form>

<script>
    $(function() {
        let userCountry = "{{ $info->country_id }}";
        let userState = "{{ $info->state_id }}";
        let userCounty = "{{ $info->county_id }}";

        function loadStates(country_id, selectedStateId = null) {
            if (!country_id) {
                $('#state').html('<option value="">Select State</option>');
                $('#county').html('<option value="">Select County</option>');
                return;
            }
            $('#state').html('<option value="">Loading...</option>');
            $.get(`/states/${country_id}`, function(states) {
                let options = '<option value="">Select State</option>';
                states.forEach(state => {
                    let selected = state.id == selectedStateId ? 'selected' : '';
                    options += `<option value="${state.id}" ${selected}>${state.name}</option>`;
                });
                $('#state').html(options);
            });
        }

        function loadCounties(state_id, selectedCountyId = null) {
            if (!state_id) {
                $('#county').html('<option value="">Select County</option>');
                return;
            }
            $('#county').html('<option value="">Loading...</option>');
            $.get(`/counties/${state_id}`, function(counties) {
                let options = '<option value="">Select County</option>';
                counties.forEach(county => {
                    let selected = county.id == selectedCountyId ? 'selected' : '';
                    options +=
                        `<option value="${county.id}" ${selected}>${county.name}</option>`;
                });
                $('#county').html(options);
            });
        }

        $('#country').on('change', function() {
            loadStates($(this).val());
            $('#county').html('<option value="">Select County</option>');
        });

        $('#state').on('change', function() {
            loadCounties($(this).val());
        });

        if (userCountry) loadStates(userCountry, userState);
        if (userState) loadCounties(userState, userCounty);

        // Licensed states — load all states for the selected country
        let currentLicensedStates = @json($info->licensed_states ?? []);

        function loadLicensedStates(country_id) {
            if (!country_id) {
                $('#licensedStates').html('<option value="">Select a country first</option>');
                return;
            }
            $.get(`/states/${country_id}`, function(states) {
                let options = '';
                states.forEach(function(state) {
                    let selected = currentLicensedStates.includes(state.name) ? 'selected' : '';
                    options += `<option value="${state.name}" ${selected}>${state.name}</option>`;
                });
                $('#licensedStates').html(options);
            });
        }

        // Load on page open if country already set
        if (userCountry) loadLicensedStates(userCountry);

        // Reload when country changes
        $('#country').on('change', function() {
            currentLicensedStates = [];
            loadLicensedStates($(this).val());
        });

        // Enforce max 3 selections
        $('#licensedStates').on('change', function() {
            let selected = $(this).val() || [];
            if (selected.length > 3) {
                let keep = selected.slice(0, 3);
                $(this).find('option').each(function() {
                    $(this).prop('selected', keep.includes($(this).val()));
                });
            }
        });

        function toast(message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    container: 'swal2-container',
                    popup: 'swal-toast-zindex'
                }
            });
        }

        $('#usermodalForm').on('submit', function(e) {
            const requiredFields = {
                'name': 'Name',
                'email': 'Email',
                'role': 'Role',
                'phone_number': 'Phone Number',
                'address': 'Address',
                'country_id': 'Country',
                'state_id': 'State',
                'city': 'City',
                'zipcode': 'Zipcode',
            };

            let isValid = true;

            $.each(requiredFields, function(field, label) {
                let value = $(`[name="${field}"]`).val();
                if (!value || value.trim() === '') {
                    e.preventDefault();
                    toast(label + ' is required.');
                    isValid = false;
                    return false;
                }
            });

            if (!isValid) return false;

            const email = $.trim($('input[name="email"]').val());
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                e.preventDefault();
                toast('Please enter a valid email address.');
                return false;
            }

            const password = $('input[name="password"]').val();
            const userId = "{{ $user->id ?? '' }}";

            console.log("User ID:", userId);
            console.log("Password entered:", password);

            if (!userId) {
                if (!password || password.length < 8) {
                    e.preventDefault();
                    toast('Password is required and must be at least 8 characters.');
                    console.log("Password validation failed for new user");
                    return false;
                }
            } else {
                if (password && password.length > 0 && password.length < 8) {
                    e.preventDefault();
                    toast('Password must be at least 8 characters.');
                    console.log("Password validation failed for edit user");
                    return false;
                }
            }

            return true;
        });
    });
</script>
