@extends('layouts.admin')
@section('title') @lang('sidebar.infrastructures') @endsection
@section('contentheader') @lang('sidebar.infrastructures') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/infrastructures') }}"> @lang('sidebar.infrastructures') </a>
@endsection
@section('contentheaderactive') @lang('sidebar.infrastructures') @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">@lang('sidebar.infrastructures') #{{ $infrastructure->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/infrastructures') }}">
                        <button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> @lang('crud.back')</button>
                    </a>
                    <a href="{{ url('/admin/infrastructures/' . $infrastructure->id . '/edit') }}">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> @lang('crud.edit')</button>
                    </a>
                    <form method="POST" action="{{ url('admin/infrastructures/' . $infrastructure->id) }}" accept-charset="UTF-8" style="display:inline">
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
                                <tr><th>ID</th><td>{{ $infrastructure->id }}</td></tr>
                                <tr><th>@lang('crud.infra_publ')</th><td>{{ $infrastructure->infra_publ }}</td></tr>
                                <tr><th>@lang('crud.code_infra_publ')</th><td>{{ $infrastructure->code_infra_publ }}</td></tr>
                                <tr><th>@lang('crud.code_men')</th><td>{{ $infrastructure->code_men }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
