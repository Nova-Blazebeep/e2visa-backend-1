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

    .form-section-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #8a919a;
        border-bottom: 1px solid #e4e6e8;
        padding-bottom: 6px;
        margin-bottom: 0;
    }

    #usermodalForm .form-label {
        font-weight: 500;
        margin-bottom: 4px;
    }

    .image-dropzone {
        border: 2px dashed #c4c9d0;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        background: #fafbfc;
        transition: border-color .2s, background .2s;
    }

    .image-dropzone:hover,
    .image-dropzone.dz-dragover {
        border-color: #0d6efd;
        background: #f0f6ff;
    }

    .image-dropzone .dz-placeholder {
        color: #8a919a;
        font-size: 13px;
        padding: 10px 0;
    }

    .image-dropzone .dz-placeholder svg {
        display: block;
        margin: 0 auto 8px;
    }

    .image-dropzone .dz-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
    }

    .image-dropzone .dz-preview img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        flex-shrink: 0;
    }

    .image-dropzone .dz-preview .dz-label {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-bottom: 4px;
        word-break: break-all;
    }
</style>

<form id="usermodalForm" action="{{ route('portal.users.storeOrUpdate', $user->id ?? null) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @php
        $info = optional($user->userInformation);
        $badge = optional($user->userBadge ?? null);
        $badgeExemptRoles = ['Buyer', 'Admin', 'Moderator'];
    @endphp

    <div class="row g-3">

        <div class="col-12">
            <h6 class="form-section-title">Account</h6>
        </div>

        <div class="col-md-6">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Full name"
                value="{{ $user->name ?? '' }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" placeholder="name@example.com"
                value="{{ $user->email ?? '' }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Password @if (empty($user->id))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" name="password" class="form-control"
                placeholder="{{ empty($user->id) ? 'Minimum 8 characters' : 'Leave blank to keep current password' }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-select">
                <option value="">Select Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <h6 class="form-section-title">Contact &amp; Location</h6>
        </div>

        <div class="col-md-6">
            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
            <input type="text" name="phone_number" class="form-control" value="{{ $info->phone_number }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Address <span class="text-danger">*</span></label>
            <input type="text" name="address" class="form-control" value="{{ $info->address }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Country <span class="text-danger">*</span></label>
            <select name="country_id" id="country" class="form-select">
                <option value="">Select Country</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ $info->country_id == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">State <span class="text-danger">*</span></label>
            <select name="state_id" id="state" class="form-select">
                <option value="">Select State</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">County</label>
            <select name="county_id" id="county" class="form-select">
                <option value="">Select County</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">City <span class="text-danger">*</span></label>
            <input type="text" name="city" class="form-control" value="{{ $info->city }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Zipcode <span class="text-danger">*</span></label>
            <input type="text" name="zipcode" class="form-control" value="{{ $info->zipcode }}">
        </div>

        <div class="col-12">
            <h6 class="form-section-title">Profile</h6>
        </div>

        <div class="col-12">
            <label class="form-label">About <small class="text-muted">(max 150 characters)</small></label>
            <textarea name="about" id="about" rows="4" maxlength="150" class="form-control"
                placeholder="A short bio shown on the public profile">{{ trim($user->userInformation->about ?? '') }}</textarea>
            <small class="d-block text-end mt-1" id="aboutCharCount">0/150 characters</small>
        </div>

        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <label class="form-label mb-1">Licensed States
                    <small class="text-muted">(states where this professional is licensed)</small></label>
                <div class="form-check mb-1">
                    <input type="checkbox" class="form-check-input" id="selectAllStates">
                    <label class="form-check-label" for="selectAllStates">Select all states</label>
                </div>
            </div>
            <select name="licensed_states[]" id="licensedStates" multiple class="form-select" size="6">
                <option value="">Select a country first</option>
            </select>
            <small class="text-muted">Hold Ctrl / Cmd to select multiple.</small>
        </div>

        <div class="col-12">
            <h6 class="form-section-title">Badge</h6>
            <small class="text-muted d-block mb-2">Not applicable for Buyer / Admin / Moderator accounts — leave as-is for those roles.</small>
        </div>

        <div class="col-md-6">
            <label class="form-label">Badge Status</label>
            <select name="badge_status" class="form-select">
                <option value="pending" {{ ($badge->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ ($badge->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="revoked" {{ ($badge->status ?? '') == 'revoked' ? 'selected' : '' }}>Revoked</option>
            </select>
            <small class="text-muted">Setting this to Active sends the user a confirmation email and unlocks forum posting.</small>
        </div>

        <div class="col-md-6">
            <label class="form-label">Payment Reference / Note</label>
            <input type="text" name="payment_reference" class="form-control" maxlength="255"
                placeholder="e.g. invoice # or note from Mike" value="{{ $badge->payment_reference ?? '' }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Profile Image</label>
            <div id="imageDropzone" class="image-dropzone">
                <input type="file" name="image" id="userImageInput" accept="image/*" class="d-none">

                <div id="dzPlaceholder" class="dz-placeholder">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#8a919a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Drag &amp; drop an image here, or <u>click to browse</u>
                </div>

                <div id="dzPreview" class="dz-preview d-none">
                    <img id="dzPreviewImg" src="" alt="Preview">
                    <div>
                        <span id="dzPreviewLabel" class="dz-label"></span>
                        <button type="button" id="dzChangeBtn" class="btn btn-sm btn-outline-primary me-1">Change</button>
                        <button type="button" id="dzRemoveBtn" class="btn btn-sm btn-outline-danger d-none">Remove</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check mb-2">
                <input type="checkbox" class="form-check-input" id="subscribeNewsletter" name="subscribe_for_newsletter"
                    value="1" {{ $info->subscribe_for_newsletter ? 'checked' : '' }}>
                <label class="form-check-label" for="subscribeNewsletter">Subscribe to Newsletter</label>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="submitBtn">
            <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
            {{ empty($user->id) ? 'Create User' : 'Save Changes' }}
        </button>
    </div>
</form>

<script>
    $(function() {
        // Live character counter for the About field (mirrors the frontend limit)
        const $aboutField = $('#about');
        const $aboutCount = $('#aboutCharCount');
        const ABOUT_MAX = 150;

        function updateAboutCount() {
            const len = $aboutField.val().length;
            $aboutCount.text(len + '/' + ABOUT_MAX + ' characters');
            $aboutCount.toggleClass('text-danger fw-semibold', len >= ABOUT_MAX);
            $aboutCount.toggleClass('text-muted', len < ABOUT_MAX);
        }
        $aboutField.on('input', updateAboutCount);
        updateAboutCount();

        let userCountry = "{{ $info->country_id }}";
        let userState = "{{ $info->state_id }}";
        let userCounty = "{{ $info->county_id }}";

        // The states/counties tables may contain duplicate rows — keep the first of each name
        function uniqueByName(items) {
            const seen = new Set();
            return items.filter(item => {
                const key = (item.name || '').trim().toLowerCase();
                if (!key || seen.has(key)) return false;
                seen.add(key);
                return true;
            });
        }

        function loadStates(country_id, selectedStateId = null) {
            if (!country_id) {
                $('#state').html('<option value="">Select State</option>');
                $('#county').html('<option value="">Select County</option>');
                return;
            }
            $('#state').html('<option value="">Loading...</option>');
            $.get(`/states/${country_id}`, function(states) {
                states = uniqueByName(states);
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
                counties = uniqueByName(counties);
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
                $('#selectAllStates').prop('checked', false);
                return;
            }
            $.get(`/states/${country_id}`, function(states) {
                states = uniqueByName(states);
                let options = '';
                states.forEach(function(state) {
                    let selected = currentLicensedStates.includes(state.name) ? 'selected' : '';
                    options += `<option value="${state.name}" ${selected}>${state.name}</option>`;
                });
                $('#licensedStates').html(options);
                syncSelectAllCheckbox();
            });
        }

        // Load on page open if country already set
        if (userCountry) loadLicensedStates(userCountry);

        // Reload when country changes
        $('#country').on('change', function() {
            currentLicensedStates = [];
            loadLicensedStates($(this).val());
        });

        // "Select all states" checkbox
        function syncSelectAllCheckbox() {
            const $options = $('#licensedStates option');
            const total = $options.length;
            const selected = $options.filter(':selected').length;
            $('#selectAllStates').prop('checked', total > 0 && selected === total);
        }

        $('#selectAllStates').on('change', function() {
            $('#licensedStates option').prop('selected', this.checked);
        });

        $('#licensedStates').on('change', syncSelectAllCheckbox);

        // ── Image dropzone with preview ──
        const existingImageUrl = "{{ $info->image ? asset('storage/' . $info->image) : '' }}";
        const $dz = $('#imageDropzone');
        const $input = $('#userImageInput');

        function showPreview(src, label, isNew) {
            $('#dzPreviewImg').attr('src', src);
            $('#dzPreviewLabel').text(label);
            $('#dzPlaceholder').addClass('d-none');
            $('#dzPreview').removeClass('d-none');
            // "Remove" only makes sense for a newly picked file (reverts the selection)
            $('#dzRemoveBtn').toggleClass('d-none', !isNew);
        }

        function resetToInitial() {
            $input.val('');
            if (existingImageUrl) {
                showPreview(existingImageUrl, 'Current image', false);
            } else {
                $('#dzPreview').addClass('d-none');
                $('#dzPlaceholder').removeClass('d-none');
            }
        }

        function handleFile(file) {
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                toast('Please select an image file.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                toast('Image must be smaller than 5 MB.');
                return;
            }
            showPreview(URL.createObjectURL(file),
                existingImageUrl ? file.name + ' (will replace current image)' : file.name,
                true);
        }

        $dz.on('click', function(e) {
            if ($(e.target).closest('#dzRemoveBtn').length) return;
            $input.trigger('click');
        });

        $input.on('click', function(e) {
            e.stopPropagation();
        });

        $input.on('change', function() {
            handleFile(this.files[0]);
        });

        $dz.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $dz.addClass('dz-dragover');
        });

        $dz.on('dragleave dragend', function(e) {
            e.preventDefault();
            $dz.removeClass('dz-dragover');
        });

        $dz.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $dz.removeClass('dz-dragover');
            const files = e.originalEvent.dataTransfer.files;
            if (files.length) {
                // Put the dropped file into the input so it submits with the form
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                $input[0].files = dt.files;
                handleFile(files[0]);
            }
        });

        $('#dzRemoveBtn').on('click', function(e) {
            e.stopPropagation();
            resetToInitial();
        });

        // On edit, show the already-uploaded image right away
        resetToInitial();

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
