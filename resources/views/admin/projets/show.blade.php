@extends('layouts.admin')
@section('title') @lang('sidebar.projects') @endsection
@section('contentheader') @lang('sidebar.projects') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/projets') }}"> @lang('sidebar.projects') </a>
@endsection
@section('contentheaderactive') @lang('sidebar.projects') @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('sidebar.projects') #{{ $projet->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/projets') }}">
                        <button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> @lang('crud.back')</button>
                    </a>
                    <a href="{{ url('/admin/projets/' . $projet->id . '/edit') }}">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> @lang('crud.edit')</button>
                    </a>
                    <br><br>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr><th>ID</th><td>{{ $projet->id }}</td></tr>
                                <tr><th>@lang('crud.code')</th><td>{{ $projet->code }}</td></tr>
                                <tr><th>@lang('crud.intitule_resume')</th><td>{{ $projet->intitule_resume }}</td></tr>
                                <tr><th>@lang('crud.intitule_resume_fr')</th><td>{{ $projet->intitule_resume_fr }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
