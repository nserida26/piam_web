@extends('layouts.admin')
@section('title') @lang('sidebar.wilaya_user') @endsection
@section('contentheader') @lang('sidebar.wilaya_user') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/wilaya-user') }}"> @lang('sidebar.wilaya_user') </a>
@endsection
@section('contentheaderactive') @lang('sidebar.wilaya_user') @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">@lang('sidebar.wilaya_user') #{{ $wilayauser->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/wilaya-user') }}">
                        <button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> @lang('crud.back')</button>
                    </a>
                    <a href="{{ url('/admin/wilaya-user/' . $wilayauser->id . '/edit') }}">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> @lang('crud.edit')</button>
                    </a>
                    <form method="POST" action="{{ url('admin/wilayauser/' . $wilayauser->id) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('{{ __('crud.confirm_delete') }}')">
                            <i class="fa fa-trash-o"></i> @lang('crud.delete')
                        </button>
                    </form>
                    <br><br>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr><th>ID</th><td>{{ $wilayauser->id }}</td></tr>
                                <tr><th>@lang('crud.wilaya')</th><td>{{ $wilayauser->intitule }}</td></tr>
                                <tr><th>@lang('crud.user')</th><td>{{ $wilayauser->username }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
