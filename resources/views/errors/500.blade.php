@extends('layouts.admin')
@section('title')
    500
@endsection


@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="error-page">
        <h2 class="headline text-danger">500</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> Oops! Something went wrong.</h3>

            <p>
                <a href="{{route('admin.dashboard')}}">@lang('sidebar.dashboard')</a>
            </p>
        </div>
    </div>
    <!-- /.error-page -->
@endsection

@push('script')
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- dropzonejs -->
    <!-- Page specific script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

        });
    </script>
    <script type="text/javascript"></script>
@endpush
