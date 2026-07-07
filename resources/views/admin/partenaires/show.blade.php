@extends('layouts.admin')
@section('title') @lang('sidebar.partners') @endsection
@section('contentheader') @lang('sidebar.partners') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/partenaires') }}"> @lang('sidebar.partners') </a>
@endsection
@section('contentheaderactive') @lang('sidebar.partners') @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">@lang('sidebar.partners') #{{ $partenaire->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/partenaires') }}">
                        <button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> @lang('crud.back')</button>
                    </a>
                    <a href="{{ url('/admin/partenaires/' . $partenaire->id . '/edit') }}">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> @lang('crud.edit')</button>
                    </a>
                    <form method="POST" action="{{ url('admin/partenaires/' . $partenaire->id) }}" accept-charset="UTF-8" style="display:inline">
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
                                <tr><th>ID</th><td>{{ $partenaire->id }}</td></tr>
                                <tr><th>@lang('crud.code')</th><td>{{ $partenaire->code }}</td></tr>
                                <tr><th>@lang('crud.intitule')</th><td>{{ $partenaire->intitule }}</td></tr>
                                <tr><th>@lang('crud.intitule_fr')</th><td>{{ $partenaire->intitule_fr }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
