@extends('layouts.admin')
@section('title') @lang('sidebar.infrastructures') @endsection
@section('contentheader') @lang('sidebar.infrastructures') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/infrastructures') }}">@lang('sidebar.infrastructures')</a>
@endsection
@section('contentheaderactive') @lang('sidebar.infrastructures') @endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold">@lang('sidebar.infrastructures')</h3>
                <a href="{{ url('/admin/infrastructures/create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> @lang('crud.add_new')
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dt-infrastructures" class="table table-bordered table-striped table-hover w-100">
                        <thead class="thead-dark">
                            <tr>
                                <th>@lang('crud.no')</th>
                                <th>@lang('crud.intitule')</th>
                                <th>@lang('crud.code')</th>
                                <th>@lang('crud.type_struct')</th>
                                <th class="text-center">@lang('crud.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($infrastructures as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->infra_publ }}</td>
                                <td><span class="badge badge-secondary">{{ $item->code_infra_publ }}</span></td>
                                <td>{{ $item->code_men }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ url('/admin/infrastructures/' . $item->id) }}" class="btn btn-info btn-xs" title="@lang('crud.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ url('/admin/infrastructures/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="@lang('crud.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ url('/admin/infrastructures/' . $item->id) }}" style="display:inline">
                                        @method('DELETE') @csrf
                                        <button type="submit" class="btn btn-danger btn-xs" title="@lang('crud.delete')"
                                            onclick="return confirm('@lang('crud.confirm_delete')')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
    $('#dt-infrastructures').DataTable({
        language: dtLang,
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        pageLength: 25
    });
});
</script>
@endpush
