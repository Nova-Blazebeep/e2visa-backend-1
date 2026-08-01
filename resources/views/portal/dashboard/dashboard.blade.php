@extends('portal.layout.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h4 class="page-title">Dashboard</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Overview</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-4">

        {{-- Users --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-primary">
                        <i class="ti ti-users"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Users</p>
                        <h4 class="stat-value mb-0">{{ $usercount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Buyers --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-success">
                        <i class="ti ti-user-dollar"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Buyers</p>
                        <h4 class="stat-value mb-0">{{ $buyerCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sellers --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-warning">
                        <i class="ti ti-trending-up"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Sellers</p>
                        <h4 class="stat-value mb-0">{{ $sellerCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Brokers --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-info">
                        <i class="ti ti-briefcase"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Brokers</p>
                        <h4 class="stat-value mb-0">{{ $brokerCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attorneys --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-danger">
                        <i class="ti ti-scale"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Attorneys</p>
                        <h4 class="stat-value mb-0">{{ $attorneyCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Businesses --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-primary">
                        <i class="ti ti-chart-bar"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Businesses</p>
                        <h4 class="stat-value mb-0">{{ $businessCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-secondary">
                        <i class="ti ti-building-bank"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Categories</p>
                        <h4 class="stat-value mb-0">{{ $categorycount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Subscribers --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-success">
                        <i class="ti ti-mail"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Subscribers</p>
                        <h4 class="stat-value mb-0">{{ $subscriberCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contacts --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-warning">
                        <i class="ti ti-address-book"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Contacts</p>
                        <h4 class="stat-value mb-0">{{ $contactCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pages --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-info">
                        <i class="ti ti-book"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Pages</p>
                        <h4 class="stat-value mb-0">{{ $pagecount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Subscriptions --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-danger">
                        <i class="ti ti-credit-card"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Subscriptions</p>
                        <h4 class="stat-value mb-0">{{ $subscriptionCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Testimonials --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-circle bg-label-secondary">
                        <i class="ti ti-quote"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-1">Testimonials</p>
                        <h4 class="stat-value mb-0">{{ $testimonialCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /.row --}}
</div>
@endsection
