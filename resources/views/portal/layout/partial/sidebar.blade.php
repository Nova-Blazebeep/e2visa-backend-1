<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <img style="width: 22px" src="{{ url('/portal/logo/logo.png') }}" alt="logo">
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-3">E2VISA</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active open' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-dashboard"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <li class="menu-item dropdown {{ request()->routeIs('portal.users.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-user-cog"></i>
                <div data-i18n="Manage Users">Manage Users</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('portal.users.index') ? 'active' : '' }}">
                    <a href="{{ route('portal.users.index') }}" class="menu-link">
                        <div data-i18n="List Users">List Users</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item dropdown {{ request()->routeIs('portal.badges.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-award"></i>
                <div data-i18n="Manage Badges">Manage Badges</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('portal.badges.index') ? 'active' : '' }}">
                    <a href="{{ route('portal.badges.index') }}" class="menu-link">
                        <div data-i18n="List Badges">List Badges</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item dropdown {{ request()->routeIs('business.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-briefcase"></i>
                <div data-i18n="Manage Business">Manage Business</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('business.show.form') ? 'active' : '' }}">
                    <a href="{{ route('business.show.form') }}" class="menu-link">
                        <div data-i18n="Add Business">Add Business</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('business.index') ? 'active' : '' }}">
                    <a href="{{ route('business.index') }}" class="menu-link">
                        <div data-i18n="List Business">List Business</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item dropdown {{ request()->routeIs('real-estate.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-building-estate"></i>
                <div data-i18n="Manage Real-Estate">Manage Real-Estate</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('real-estate.show.form') ? 'active' : '' }}">
                    <a href="{{ route('real-estate.show.form') }}" class="menu-link">
                        <div data-i18n="Add Real-Estate">Add Real-Estate</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('real-estate.index') ? 'active' : '' }}">
                    <a href="{{ route('real-estate.index') }}" class="menu-link">
                        <div data-i18n="List Real-Estate">List Real-Estate</div>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="menu-item dropdown {{ request()->routeIs('portal.categories.*') || request()->routeIs('portal.subcategories.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-category"></i>
                <div data-i18n="Manage Categories">Manage Categories</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('portal.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('portal.categories.index') }}" class="menu-link">
                        <div data-i18n="Categories">Categories</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal.subcategories.*') ? 'active' : '' }}">
                    <a href="{{ route('portal.subcategories.index') }}" class="menu-link">
                        <div data-i18n="Sub Categories">Sub Categories</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- <li class="menu-item dropdown {{ request()->is('packages*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-package"></i>
                <div data-i18n="Manage Packages">Manage Packages</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div data-i18n="All Packages">All Packages</div>
                    </a>
                </li>
            </ul>
        </li> --}}

        {{-- <li class="menu-item dropdown {{ request()->routeIs('portal.pages.*') ? 'active open' : '' }} ">
				<a href="javascript:void(0);" class="menu-link menu-toggle">
					<i class="menu-icon tf-icons ti ti-clipboard"></i>
					<div data-i18n="Manage Pages">Manage Pages</div>
				</a>
				<ul class="menu-sub">
						<li class="menu-item ">
							<a href="{{route('portal.pages.index')}}" class="menu-link">
								<div data-i18n="List Pages">List Pages</div>
							</a>
						</li>
				</ul>
			</li> --}}

        <li class="menu-item dropdown {{ request()->routeIs('portal.blog.*') || request()->routeIs('portal.blog-categories.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-bookmark"></i>
                <div data-i18n="Manage Blog">Manage Blog</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('portal.blog.index') ? 'active' : '' }}">
                    <a href="{{ route('portal.blog.index') }}" class="menu-link">
                        <div data-i18n="List Blog">List Blog</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal.blog-categories.index') ? 'active' : '' }}">
                    <a href="{{ route('portal.blog-categories.index') }}" class="menu-link">
                        <div data-i18n="Blog categories">Blog categories</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal.blog.comments.index') ? 'active' : '' }}">
                    <a href="{{ route('portal.blog.comments.index') }}" class="menu-link">
                        <div data-i18n="Manage Comments">Manage Comments</div>
                    </a>
                </li>
            </ul>
        </li>




        {{-- Manage Forum --}}
        <li class="menu-item dropdown {{ request()->routeIs('portal.forums.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-message-circle"></i>
                <div data-i18n="Manage Forum">Manage Forum</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('portal.forums.index') ? 'active' : '' }}">
                    <a href="{{ route('portal.forums.index') }}" class="menu-link">
                        <div data-i18n="List Forums">List Forums</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Media Gallery -->
		<li class="menu-item {{ request()->routeIs('media_gallery.index*') ? 'active open' : '' }}">
			<a href="{{route('media_gallery.index')}}" class="menu-link">
				<i class="menu-icon tf-icons ti ti-checkbox"></i>
				<div data-i18n="Media Gallery">Media Gallery</div>
			</a>
		</li>

        <li class="menu-item {{ request()->routeIs('contact.index*') ? 'active open' : '' }}">
			<a href="{{route('contact.index')}}" class="menu-link">
				 <i class="menu-icon tf-icons ti ti-phone"></i>
				<div data-i18n="Contact Us">Contact Us</div>
			</a>
		</li>
         <li class="menu-item {{ request()->routeIs('subscriber.index*') ? 'active open' : '' }}">
			<a href="{{route('subscriber.index')}}" class="menu-link">
				  <i class="menu-icon tf-icons ti ti-users"></i>
				<div data-i18n="Subscribers">Subscribers</div>
			</a>
		</li>
    </ul>
</aside>
