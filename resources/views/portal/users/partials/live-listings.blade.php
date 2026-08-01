@php
    $businessCount = $listings->where('business_type', 'business')->count();
    $realEstateCount = $listings->where('business_type', 'real-estate')->count();
@endphp

<div class="row">
    <div class="col-12 mb-3">
        <h6 class="text-uppercase text-muted border-bottom pb-2 mb-3" style="font-size: 12px; letter-spacing: .5px;">
            Published Listings — {{ $record->name ?? 'N/A' }}</h6>
        <p class="text-muted mb-3">
            {{ $businessCount }} business{{ $businessCount === 1 ? '' : 'es' }},
            {{ $realEstateCount }} real estate listing{{ $realEstateCount === 1 ? '' : 's' }}.
        </p>
    </div>

    <div class="col-12">
        @forelse ($listings as $listing)
            <a href="{{ $listing->business_type === 'real-estate' ? route('real-estate.show', $listing->id) : route('business.show', $listing->id) }}"
                target="_blank" rel="noopener"
                class="d-flex align-items-center justify-content-between text-decoration-none px-3 py-2 mb-2 rounded"
                style="border: 1px solid #e5e7eb; color: #40433F;">
                <span>{{ $listing->listing_heading ?: 'Untitled listing' }}</span>
                <span class="badge {{ $listing->business_type === 'real-estate' ? 'bg-label-success' : 'bg-label-primary' }} border">
                    {{ $listing->business_type === 'real-estate' ? 'Real Estate' : 'Business' }}
                </span>
            </a>
        @empty
            <p class="text-muted">No published listings.</p>
        @endforelse
    </div>
</div>
