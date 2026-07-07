<!DOCTYPE html>
<!--
   This is a starter template page. Use this page to start your new project from
   scratch. This page gets rid of all links and provides the needed markup only.
   -->
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title') | PIAM</title>
    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">
    <!-- Cairo Font (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    @stack('css')
    <!-- AdminLTE Theme -->
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/modern-theme.css') }}">
    <!-- Source Sans Pro (fallback) -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}">
    @if(LaravelLocalization::getCurrentLocale() == 'fr')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-4.0.0-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/mycustomstyle.css') }}">
    @endif
    @if(LaravelLocalization::getCurrentLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/custom_rtl.css') }}">
    @endif
    <!-- PIAM Theme — palette officielle + Cairo (chargé en dernier pour priorité) -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/piam-theme.css') }}">
</head>

<body class="fixed">

    <div class="wrapper">
        <!-- Navbar -->
        @include('admin.includes.navbar')
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        @include('admin.includes.sidebar')
        <!--  End Main Sidebar Container -->
        <!-- Content Wrapper. Contains page content -->
        @include('admin.includes.content')
        <!-- /.content-wrapper -->
        @include('admin.includes.footer')
        <!-- Main Footer -->
    </div>
    <!-- ./wrapper -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('auth.logout')</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">@lang('auth.logout_confirm')</div>
                <div class="modal-footer">
                    <button class="btn btn-link" type="button" data-dismiss="modal">@lang('general.cancel')</button>
                    <a href="{{ route('admin.logout') }}" class="btn btn-danger">@lang('auth.logout')</a>
                </div>
            </div>
        </div>
    </div>
    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @stack('script')
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/general.js') }}"></script>
    @stack('custom')

</body>

</html>
