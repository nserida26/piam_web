@extends('layouts.admin')
@section('title') @lang('sidebar.operators') @endsection
@section('contentheader') @lang('sidebar.operators') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/operateurs') }}">@lang('sidebar.operators')</a>
@endsection
@section('contentheaderactive') @lang('sidebar.operators') @endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold">@lang('sidebar.operators')</h3>
                <a href="{{ url('admin/operateurs/create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> @lang('crud.add_new')
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dt-operateurs" class="table table-bordered table-striped table-hover w-100">
                        <thead class="thead-dark">
                            <tr>
                                <th>@lang('crud.no')</th>
                                <th>@lang('crud.intitule')</th>
                                <th>@lang('crud.intitule_fr')</th>
                                <th>@lang('crud.type_struct')</th>
                                <th>@lang('crud.projet')</th>
                                <th class="text-center">@lang('crud.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operateur as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->operateur_nom }}</strong></td>
                                <td>{{ $item->operateur_nom_fr }}</td>
                                <td><span class="badge badge-info">{{ $item->operateur_type }}</span></td>
                                <td>{{ $item->projet_resume }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ url('admin/operateurs/' . $item->operateur_id . '/' . $item->projet_id) }}"
                                        class="btn btn-info btn-xs" title="@lang('crud.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ url('admin/operateurs/' . $item->operateur_id . '/edit') }}"
                                        class="btn btn-primary btn-xs" title="@lang('crud.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
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
    $('#dt-operateurs').DataTable({
        language: dtLang,
        responsive: true,
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        pageLength: 25
    });
});
</script>
@endpush
