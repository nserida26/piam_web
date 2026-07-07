@extends('layouts.admin')
@section('title')
    @lang('localite.title')
@endsection
@section('contentheader')
    @lang('localite.title')
@endsection
@section('contentheaderlink')
    <a href="{{ url('admin/localites') }}"> @lang('localite.title') </a>
@endsection
@section('contentheaderactive')
    @lang('localite.title')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="card-title">@lang('localite.title')</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ url('/admin/localites/create') }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus"></i> @lang('localite.add_new')
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="localites-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('localite.code')</th>
                                    <th>@lang('localite.intitule')</th>
                                    <th>@lang('localite.intitule_fr')</th>
                                    <th>@lang('localite.wilaya')</th>
                                    <th>@lang('localite.moughata')</th>
                                    <th>@lang('localite.commune')</th>
                                    <th>@lang('localite.valider')</th>
                                    <th>@lang('localite.actions')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
@endpush

@push('custom')
<script>
$(function () {
    @include('admin.includes.datatable-lang')
    $('#localites-table').DataTable({
        language: dtLang,
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: {
            url: '{{ route('admin.localites.datatable') }}',
            type: 'GET'
        },
        columns: [
            { data: 0, orderable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4, orderable: false },
            { data: 5, orderable: false },
            { data: 6, orderable: false },
            { data: 7, orderable: false },
            { data: 8, orderable: false }
        ]
    });
});
</script>
@endpush
