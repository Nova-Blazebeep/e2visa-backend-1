@php
    $info = optional($record->userInformation);
    $licensedStates = is_array($info->licensed_states) ? array_values(array_unique($info->licensed_states)) : [];
    $licensedAll = count($licensedStates) >= 50;
@endphp

<div class="row">
    {{-- ── Header: avatar, name, role, badge ── --}}
    <div class="col-12 text-center mb-4">
        @if ($info->image)
            <img src="{{ asset('storage/' . $info->image) }}" alt="{{ $record->name }}"
                class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;">
        @else
            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center shadow"
                style="width: 120px; height: 120px; color: white; font-size: 40px; font-weight: 600;">
                {{ strtoupper(substr($record->name ?? '?', 0, 1)) }}
            </div>
        @endif
        <h4 class="mt-3 mb-1">{{ $record->name ?? 'N/A' }}</h4>
        <div class="d-flex align-items-center justify-content-center gap-2">
            @if ($role_badge)
                {!! $role_badge !!}
            @endif
            <span class="text-muted">{{ $role ?? 'N/A' }}</span>
        </div>
    </div>

    {{-- ── Contact & Location ── --}}
    <div class="col-12 mb-3">
        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3" style="font-size: 12px; letter-spacing: .5px;">
            Contact &amp; Location</h6>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Email:</strong> {{ $record->email ?? 'N/A' }}</p>
                <p><strong>Phone Number:</strong> {{ $info->phone_number ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $info->address ?? 'N/A' }}</p>
                <p><strong>City:</strong> {{ $info->city ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Country:</strong> {{ $info->country_name ?? 'N/A' }}</p>
                <p><strong>State:</strong> {{ $info->state_name ?? 'N/A' }}</p>
                <p><strong>County:</strong> {{ $info->county_name ?? 'N/A' }}</p>
                <p><strong>Zipcode:</strong> {{ $info->zipcode ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- ── Professional Details ── --}}
    <div class="col-12 mb-3">
        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3" style="font-size: 12px; letter-spacing: .5px;">
            Professional Details</h6>
        <div class="row">
            <div class="col-12">
                <p class="mb-2"><strong>Licensed States:</strong></p>
                @if ($licensedAll)
                    <p><span class="badge bg-label-primary border me-1 mb-1">All 50 states</span></p>
                @elseif (count($licensedStates))
                    <p class="d-flex flex-wrap gap-1 mb-2">
                        @foreach ($licensedStates as $state)
                            <span class="badge bg-label-primary border">{{ $state }}</span>
                        @endforeach
                    </p>
                @else
                    <p class="text-muted">N/A</p>
                @endif
            </div>
            @if (!is_null($info->broker_license) || !is_null($info->attorney_license) || !empty($info->time_frame_for_immigration))
                <div class="col-12">
                    @if (!is_null($info->broker_license))
                        <p><strong>Broker License:</strong> {{ $info->broker_license ?: 'N/A' }}</p>
                    @endif
                    @if (!is_null($info->attorney_license))
                        <p><strong>Attorney License:</strong> {{ $info->attorney_license ?: 'N/A' }}</p>
                    @endif
                    @if (!empty($info->time_frame_for_immigration))
                        <p><strong>Immigration Time Frame:</strong> {{ $info->time_frame_for_immigration }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── About ── --}}
    <div class="col-12 mb-2">
        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3" style="font-size: 12px; letter-spacing: .5px;">
            About</h6>
        <p class="mb-0" style="white-space: pre-line;">{{ trim($info->about ?? '') ?: 'N/A' }}</p>
    </div>

    {{-- ── Account Info ── --}}
    <div class="col-12">
        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3" style="font-size: 12px; letter-spacing: .5px;">
            Account</h6>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Member Since:</strong>
                    {{ $record->created_at ? $record->created_at->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Last Updated:</strong>
                    {{ $record->updated_at ? $record->updated_at->format('M d, Y') : 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
