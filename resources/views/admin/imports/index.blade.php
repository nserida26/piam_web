@extends('layouts.admin')
@section('title')
    @lang('sidebar.injection')
@endsection
@section('contentheader')
    @lang('sidebar.injection')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.imports.administrations.index') }}"> @lang('sidebar.injection') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.injection')
@endsection
@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            @yield('import')
        </div>
    </div>
    <!-- /.row -->
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
