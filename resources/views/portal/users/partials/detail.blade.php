<div class="row">
    <div class="col-12 text-center mb-4">
        @if ($user_image)
            <img src="{{ $record->userInformation && $record->userInformation->image ? asset('storage/' . $record->userInformation->image) : asset('portal/assets/img/avatars/default-avatar.png') }}" alt="User Image" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;">
        @else
            <div class="rounded-circle bg-secondary d-inline-block"
                style="width: 120px; height: 120px; line-height: 120px; text-align: center; color: white;">
                N/A
            </div>
        @endif
        <h4 class="mt-3">{{ $record->name ?? 'N/A' }}</h4>
        <p class="text-muted">{{ $role ?? 'N/A' }}</p>
        @if ($role_badge)
            <div>{!! $role_badge !!}</div>
        @endif
    </div>

    <div class="col-md-6">
        <p><strong>Email:</strong> {{ $record->email ?? 'N/A' }}</p>
        <p><strong>Phone Number:</strong> {{ optional($record->userInformation)->phone_number ?? 'N/A' }}</p>
        <p><strong>Address:</strong> {{ optional($record->userInformation)->address ?? 'N/A' }}</p>
        <p><strong>Country:</strong> {{ optional($record->userInformation)->country_name ?? 'N/A' }}</p>
        <p><strong>State:</strong> {{ optional($record->userInformation)->state_name ?? 'N/A' }}</p>
        <p><strong>County:</strong> {{ optional($record->userInformation)->county_name ?? 'N/A' }}</p>
        <p><strong>City:</strong> {{ optional($record->userInformation)->city ?? 'N/A' }}</p>
        <p><strong>Zipcode:</strong> {{ optional($record->userInformation)->zipcode ?? 'N/A' }}</p>
    </div>

    <div class="col-md-6">
        <p><strong>Subscribe to Newsletter:</strong>
            {{ optional($record->userInformation)->subscribe_for_newsletter ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-12">
        <p class="mb-0"><strong>About:</strong></p>
          {{ optional($record->userInformation)->about ?? 'N/A' }}
    </div>
</div>
