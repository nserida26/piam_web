@extends('layouts.admin')
@section('title') @lang('coordinateur.space') @endsection
@section('contentheader') @lang('coordinateur.dashboard') — {{ $ong->intitule ?? '' }} @endsection
@section('contentheaderlink')
    <a href="{{ route('coordinateur.dashboard') }}">@lang('coordinateur.space')</a>
@endsection
@section('contentheaderactive') @lang('coordinateur.dashboard') @endsection

@section('content')
<div class="row">
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('coordinateur.facilitateurs.index') }}" class="text-dark">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('coordinateur.nb_facilitateurs')</span>
                    <span class="info-box-number">{{ $nbFacilitateurs }}</span>
                </div>
            </div>
        </a>
    </div>
</div>

@php
    $boxes = [
        'attente' => ['bg-warning', 'fa-hourglass-half'],
        'valide_coord' => ['bg-primary', 'fa-user-check'],
        'rejete' => ['bg-danger', 'fa-times-circle'],
        'valide_admin' => ['bg-success', 'fa-check-double'],
    ];
@endphp

@foreach ($stats as $type => $counts)
    <h5 class="mt-3">@lang('coordinateur.type_' . $type)</h5>
    <div class="row">
        @foreach ($boxes as $statut => [$bg, $icon])
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('coordinateur.validation', ['type' => $type, 'statut' => $statut]) }}" class="text-dark">
                    <div class="info-box">
                        <span class="info-box-icon {{ $bg }}"><i class="fas {{ $icon }}"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-wrap">@lang('coordinateur.statut_' . $statut)</span>
                            <span class="info-box-number">{{ $counts[$statut] }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endforeach

<p class="text-muted small mt-2"><i class="fas fa-info-circle"></i> @lang('coordinateur.help_validation')</p>
@endsection
