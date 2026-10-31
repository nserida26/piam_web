@extends('layouts.admin')
@section('title') @lang('user.users') @endsection
@section('contentheader') @lang('user.users') @endsection
@section('contentheaderlink')
    <a href="{{ route('admin.users.index') }}"> @lang('sidebar.users') </a>
@endsection
@section('contentheaderactive') @lang('user.users') @endsection

@section('content')
<div class="row">
    <div class="col-md-10 col-sm-offset-1">
        <div class="card">
            <div class="card-header">@lang('user.create_new')</div>
            <div class="card-body">

                <form class="form-horizontal" action="{{ route('admin.users.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="control-label">@lang('user.name') <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('user.name') }}"
                            value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label for="email" class="control-label">@lang('user.email') <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="{{ __('user.email') }}"
                            value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="password" class="control-label">@lang('user.password') <span class="text-danger">*</span></label>
                        <input id="password" type="password" class="form-control" name="password"
                            autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="control-label">@lang('user.confirm_password') <span class="text-danger">*</span></label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="operateur_id" class="control-label">@lang('coordinateur.ong')</label>
                        <select name="operateur_id" id="operateur_id" class="form-control">
                            <option value="">—</option>
                            @foreach ($operateurs as $operateur)
                                <option value="{{ $operateur->id }}" {{ (string) old('operateur_id', '') === (string) $operateur->id ? 'selected' : '' }}>
                                    {{ $operateur->intitule }}{{ $operateur->type_struct ? ' — ' . $operateur->type_struct : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Obligatoire pour un coordinateur : il ne verra que les données de cette ONG.</small>
                        @error('operateur_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group" style="margin-top:10px">
                        <label for="role_id" class="col-sm-2 control-label">@lang('user.roles_label') <span class="text-danger">*</span></label>
                        <div class="col-sm-offset-2 col-sm-10" style="margin-top:10px">
                            @foreach ($roles as $role)
                                <label>
                                    <input type="checkbox" name="role_id[]" class="minimal" value="{{ $role->id }}"
                                        {{ in_array($role->id, old('role_id', [])) ? 'checked' : '' }}>
                                    <span style="display:inline-block;margin-right:15px">{{ ucfirst($role->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    @include('admin.users._localites')

                    <div class="form-group">
                        <label for="image" class="col-sm-2 control-label">@lang('user.photo')</label>
                        <div class="col-sm-10">
                            <input type="file" name="image" class="form-control-file" id="image">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success" style="margin-top:10px">@lang('user.add_new')</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
