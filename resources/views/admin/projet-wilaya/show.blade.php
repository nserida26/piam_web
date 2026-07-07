@extends('layouts.admin')
@section('title') @lang('sidebar.projet_wilaya') @endsection
@section('contentheader') @lang('sidebar.projet_wilaya') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/projet-wilaya') }}"> @lang('sidebar.projet_wilaya') </a>
@endsection
@section('contentheaderactive') @lang('sidebar.projet_wilaya') @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">@lang('sidebar.projet_wilaya') #{{ $projetwilaya->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/projet-wilaya') }}">
                        <button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> @lang('crud.back')</button>
                    </a>
                    <a href="{{ url('/admin/projet-wilaya/' . $projetwilaya->id . '/edit') }}">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> @lang('crud.edit')</button>
                    </a>
                    <form method="POST" action="{{ url('admin/projetwilaya/' . $projetwilaya->id) }}" accept-charset="UTF-8" style="display:inline">
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
                                <tr><th>ID</th><td>{{ $projetwilaya->id }}</td></tr>
                                <tr><th>@lang('crud.wilaya')</th><td>{{ $projetwilaya->intitule }}</td></tr>
                                <tr><th>@lang('crud.projet')</th><td>{{ $projetwilaya->intitule_resume }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
