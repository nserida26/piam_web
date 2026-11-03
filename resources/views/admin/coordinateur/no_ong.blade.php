@extends('layouts.admin')
@section('title') @lang('coordinateur.space') @endsection
@section('contentheader') @lang('coordinateur.space') @endsection
@section('contentheaderlink')
    <a href="{{ route('coordinateur.dashboard') }}">@lang('coordinateur.space')</a>
@endsection
@section('contentheaderactive') @lang('coordinateur.dashboard') @endsection

@section('content')
<div class="alert alert-warning">
    <h5><i class="fas fa-exclamation-triangle"></i> @lang('coordinateur.no_ong_title')</h5>
    <p class="mb-0">@lang('coordinateur.no_ong_text')</p>
</div>
@endsection
