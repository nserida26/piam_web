<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">

        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">@lang('sidebar.dashboard')</a>
        </li>


    </ul>


    <!-- Right navbar links -->

    <ul  class="{{ LaravelLocalization::getCurrentLocale() == 'fr' ? 'navbar-nav ml-auto' : 'navbar-nav' }}" style="{{ LaravelLocalization::getCurrentLocale() == 'ar' ? 'margin-right: auto' : '' }}">

        @php
            $otherLocale = LaravelLocalization::getCurrentLocale() === 'fr' ? 'ar' : 'fr';
            $otherLocaleNative = LaravelLocalization::getSupportedLocales()[$otherLocale]['native'];
        @endphp
        <li class="nav-item d-flex align-items-center mr-2">
            <a class="nav-link lang-switch-btn" rel="alternate" hreflang="{{ $otherLocale }}"
                href="{{ LaravelLocalization::getLocalizedURL($otherLocale, null, [], true) }}">
                <i class="fas fa-globe"></i> {{ $otherLocaleNative }}
            </a>
        </li>
        {{-- Notifications (rapports rejetés…) --}}
        @php($nbNotifs = \App\Services\Notifier::unreadCount(Auth::id()))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.notifications') }}" title="Notifications">
                <i class="far fa-bell"></i>
                @if ($nbNotifs)
                    <span class="badge badge-danger navbar-badge">{{ $nbNotifs }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">

                <img src="{{asset('/assets/admin/imgs/'.Auth::user()->photo )}}"
                    class="img-profile rounded-circle avatar user-image" width="32px" height="32px"
                    alt="User Image" />

            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ url('admin/profile') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    @lang('user.profile')
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    @lang('auth.logout')
                </a>
            </div>
        </li>
    </ul>
</nav>
