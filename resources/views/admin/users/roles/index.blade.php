@extends('layouts.admin')
@section('title') @lang('sidebar.roles') @endsection
@section('contentheader') @lang('sidebar.roles') @endsection
@section('contentheaderlink')
    <a href="{{ route('admin.users.roles') }}"> @lang('sidebar.roles') </a>
@endsection
@section('contentheaderactive') @lang('sidebar.roles') @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold">@lang('sidebar.roles')</h3>
                    <a href="{{ url('/admin/users/roles/create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> @lang('crud.add_new')
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>@lang('crud.no')</th>
                                    <th>@lang('crud.role_name')</th>
                                    <th class="text-center">@lang('crud.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center text-nowrap">
                                            <a href="{{ url('/admin/users/roles/' . $item->id . '/edit') }}"
                                                class="btn btn-primary btn-xs">
                                                <i class="fa fa-pencil-square-o"></i> @lang('crud.edit')
                                            </a>
                                            <form method="POST"
                                                action="{{ url('/admin/users/roles/' . $item->id . '/destroy') }}"
                                                accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-xs"
                                                    onclick="return confirm('{{ __('crud.confirm_delete') }}')">
                                                    <i class="fa fa-trash-o"></i> @lang('crud.delete')
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper">{!! $roles->render() !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
