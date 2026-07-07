@extends('layouts.admin')
@section('title') @lang('sidebar.wilayas') @endsection
@section('contentheader') @lang('sidebar.wilayas') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/wilayas') }}">@lang('sidebar.wilayas')</a>
@endsection
@section('contentheaderactive') @lang('sidebar.wilayas') @endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold">@lang('sidebar.wilayas')</h3>
                <a href="{{ url('/admin/wilayas/create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> @lang('crud.add_new')
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dt-wilayas" class="table table-bordered table-striped table-hover w-100">
                        <thead class="thead-dark">
                            <tr>
                                <th>@lang('crud.no')</th>
                                <th>@lang('crud.code')</th>
                                <th>@lang('crud.intitule')</th>
                                <th>@lang('crud.intitule_fr')</th>
                                <th class="text-center">@lang('crud.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wilayas as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge badge-secondary">{{ $item->code }}</span></td>
                                <td>{{ $item->intitule }}</td>
                                <td>{{ $item->intitule_fr }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ url('/admin/wilayas/' . $item->id) }}" class="btn btn-info btn-xs" title="@lang('crud.view')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ url('/admin/wilayas/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="@lang('crud.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ url('/admin/wilayas/' . $item->id) }}" style="display:inline">
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
    $('#dt-wilayas').DataTable({
        language: dtLang,
        responsive: true,
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        pageLength: 25
    });
});
</script>
@endpush
