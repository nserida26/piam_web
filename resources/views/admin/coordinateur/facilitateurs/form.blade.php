@extends('layouts.admin')
@php $editing = $facilitateur->exists; @endphp
@section('title') @lang('coordinateur.facilitateurs') @endsection
@section('contentheader') {{ $editing ? __('coordinateur.edit_facilitateur') : __('coordinateur.new_facilitateur') }} @endsection
@section('contentheaderlink')
    <a href="{{ route('coordinateur.facilitateurs.index') }}">@lang('coordinateur.facilitateurs')</a>
@endsection
@section('contentheaderactive') {{ $editing ? __('coordinateur.edit_facilitateur') : __('coordinateur.new_facilitateur') }} @endsection

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post"
                    action="{{ $editing ? route('coordinateur.facilitateurs.update', $facilitateur->id) : route('coordinateur.facilitateurs.store') }}">
                    @csrf
                    @if ($editing) @method('PUT') @endif

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>@lang('coordinateur.name') <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $facilitateur->username) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('coordinateur.email') <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $facilitateur->email) }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>@lang('coordinateur.password') @unless($editing)<span class="text-danger">*</span>@endunless</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password"
                                @if($editing) placeholder="{{ __('coordinateur.password_keep') }}" @else required @endif>
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('coordinateur.password_confirm')</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('coordinateur.projets')</label>
                        <div>
                            @foreach ($projets as $projet)
                                <label class="mr-3 font-weight-normal">
                                    <input type="checkbox" name="projet_ids[]" value="{{ $projet->id }}"
                                        {{ in_array($projet->id, $assignedProjets) ? 'checked' : '' }}>
                                    {{ $projet->intitule_resume }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label>@lang('coordinateur.operateurs')</label>
                        <div>
                            @foreach ($operateurs as $operateur)
                                <label class="mr-3 font-weight-normal">
                                    <input type="checkbox" name="operateur_ids[]" value="{{ $operateur->id }}"
                                        {{ in_array($operateur->id, $assignedOperateurs) ? 'checked' : '' }}>
                                    {{ $operateur->intitule }}
                                </label>
                            @endforeach
                        </div>
                        <small class="text-muted">@lang('coordinateur.assign_help')</small>
                    </div>

                    @include('admin.users._localites')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> @lang('coordinateur.save')</button>
                        <a href="{{ route('coordinateur.facilitateurs.index') }}" class="btn btn-secondary">@lang('coordinateur.cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
