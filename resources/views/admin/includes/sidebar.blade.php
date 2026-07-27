<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('/assets/admin/imgs/logo.png') }}" alt="MRT Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">PIAM</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('/assets/admin/imgs/' . Auth::user()->photo) }}"
                    class="img-profile rounded-circle avatar user-image" width="160px" height="160px"
                    alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->username }}</a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            @lang('sidebar.dashboard')

                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.maps.index') }}"
                        class="nav-link {{ request()->is('maps') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map"></i>
                        <p>
                            @lang('sidebar.maps')

                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>

                            @lang('sidebar.reporting')

                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/reporting') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.reporting_territoire')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reporting.fetch_reporting_annuel') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.reporting_annuel')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reporting.fetch_reporting_projet') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.reporting_projet')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reporting.fetch_reporting_localite') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.reporting_localite')</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>
                            @lang('sidebar.injection')
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.imports.administrations.index') }}" class="nav-link">
                                <i class="fas fa-school"></i>
                                <p>@lang('sidebar.admins')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.imports.reports.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.operations')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>
                            @lang('sidebar.validation')
                            <i class="fas fa-angle-left right"></i>

                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin/reports/localites') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.family')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/reports/infrastructures') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.institution')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>@lang('sidebar.musers')

                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.users')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.roles') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.roles')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/wilaya-user') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.wilaya_user')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/user-operateur') }}" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.user_operateur')</p>
                            </a>
                        </li>
                    </ul>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/projets') }}" class="nav-link">
                                        <i class="nav-icon far fa-calendar-alt"></i>
                                        <p>@lang('sidebar.projects')</p>
                                    </a>
                                    
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/admin/operateurs') }}" class="nav-link">
                                        <i class="fas fa-business-time nav-icon"></i>
                                        <p>@lang('sidebar.operators')</p>
                                    </a>
                                </li>
                                {{--<li class="nav-item">
                                    <a href="{{ url('/admin/marches') }}" class="nav-link">
                                        <i class="fas fa-wallet nav-icon"></i>
                                        <p>@lang('sidebar.marches')</p>
                                    </a>
                                    
                                </li>--}}
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            @lang('sidebar.settings')
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                
                        <li class="nav-item">
                            <a href="{{ url('/admin/wilayas') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.wilayas')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/moughatas') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.moughatas')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/communes') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.communes')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/localites') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.localites')</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ url('/admin/reorganisation/localites') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.reorganisation_localites')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/reorganisation/communes') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.reorganisation_communes')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/reorganisation/moughatas') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.reorganisation_moughatas')</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ url('/admin/infrastructures') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>@lang('sidebar.infrastructures')</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-language"></i>
                        <p>
                            @lang('sidebar.languages')
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li class="nav-item">

                                <a class="nav-link" rel="alternate" hreflang="{{ $localeCode }}"
                                    href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    {{ $properties['native'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
