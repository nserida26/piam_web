@extends('layouts.admin')
@section('title')
    @lang('localite.show')
@endsection
@section('contentheader')
    @lang('localite.show')
@endsection
@section('contentheaderlink')
    <a href="{{ url('admin/localites') }}"> @lang('localite.title') </a>
@endsection
@section('contentheaderactive')
    @lang('localite.show')
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="card-title">@lang('localite.show')</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ url('/admin/localites') }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-arrow-left"></i> @lang('localite.back')
                                </a>
                                <a href="{{ url('/admin/localites/' . $localite->id . '/edit') }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> @lang('localite.edit_action')
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>@lang('localite.code'):</strong></label>
                                    <p>{{ $localite->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>@lang('localite.code_ansade'):</strong></label>
                                    <p>{{ $localite->code_ansade ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>@lang('localite.intitule'):</strong></label>
                                    <p>{{ $localite->intitule }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>@lang('localite.intitule_fr'):</strong></label>
                                    <p>{{ $localite->intitule_fr }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>@lang('localite.wilaya'):</strong></label>
                                    <p>{{ $localite->wilaya->intitule ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>@lang('localite.moughata'):</strong></label>
                                    <p>{{ $localite->moughata->intitule ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>@lang('localite.commune'):</strong></label>
                                    <p>{{ $localite->commune->intitule ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>@lang('localite.nb_pop_ansade'):</strong></label>
                                    <p>{{ $localite->nb_pop_ansade ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>@lang('localite.lat_loc'):</strong></label>
                                    <p>{{ $localite->lat_loc ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>@lang('localite.long_loc'):</strong></label>
                                    <p>{{ $localite->long_loc ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>@lang('localite.valider'):</strong></label>
                                    <p>
                                        @if($localite->valider)
                                            <span class="badge badge-success">@lang('Oui')</span>
                                        @else
                                            <span class="badge badge-danger">@lang('Non')</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection