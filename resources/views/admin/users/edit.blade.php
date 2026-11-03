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
            <div class="card-header">@lang('user.update_user')</div>
            <div class="card-body">

                <form class="form-horizontal" action="{{ route('admin.users.update', $user->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">@lang('user.name') <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" placeholder="{{ __('user.name') }}"
                                value="{{ $user->username }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">@lang('user.email') <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="email" name="email" class="form-control" placeholder="{{ __('user.email') }}"
                                value="{{ $user->email }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="operateur_id" class="control-label">@lang('coordinateur.ong')</label>
                        <select name="operateur_id" id="operateur_id" class="form-control">
                            <option value="">—</option>
                            @foreach ($operateurs as $operateur)
                                <option value="{{ $operateur->id }}" {{ (string) old('operateur_id', $user->operateur_id) === (string) $operateur->id ? 'selected' : '' }}>
                                    {{ $operateur->intitule }}{{ $operateur->type_struct ? ' — ' . $operateur->type_struct : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Obligatoire pour un coordinateur : il ne verra que les données de cette ONG.</small>
                        @error('operateur_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="control-label">Wilaya(s)</label>
                        <div>
                            @foreach ($wilayas as $wilaya)
                                <label class="mr-3 font-weight-normal">
                                    <input type="checkbox" name="wilaya_ids[]" value="{{ $wilaya->id }}"
                                        {{ in_array($wilaya->id, array_map('intval', (array) old('wilaya_ids', $user->wilayas()->pluck('wilayas.id')->all()))) ? 'checked' : '' }}>
                                    {{ $wilaya->intitule }}
                                </label>
                            @endforeach
                        </div>
                        <small class="text-muted">Obligatoire pour un coordinateur. L'application mobile n'affichera que les données de ces wilayas.</small>
                        @error('wilaya_ids') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="control-label">@lang('coordinateur.projets')</label>
                        <div>
                            @foreach ($projets as $projet)
                                <label class="mr-3 font-weight-normal">
                                    <input type="checkbox" name="projet_ids[]" value="{{ $projet->id }}"
                                        {{ in_array($projet->id, array_map('intval', (array) old('projet_ids', $user->projets()->pluck('projets.id')->all()))) ? 'checked' : '' }}>
                                    {{ $projet->intitule_resume }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">@lang('coordinateur.operateurs')</label>
                        <div>
                            @foreach ($operateurs as $operateur)
                                <label class="mr-3 font-weight-normal">
                                    <input type="checkbox" name="operateur_ids[]" value="{{ $operateur->id }}"
                                        {{ in_array($operateur->id, array_map('intval', (array) old('operateur_ids', $user->operateursAutorises()->pluck('operateurs.id')->all()))) ? 'checked' : '' }}>
                                    {{ $operateur->intitule }}
                                </label>
                            @endforeach
                        </div>
                        <small class="text-muted">@lang('coordinateur.assign_help')</small>
                    </div>

                    <div class="form-group" style="margin-top:10px">
                        <label for="role_id" class="col-sm-2 control-label">@lang('user.roles_label') <span class="text-danger">*</span></label>
                        <div class="col-sm-offset-2 col-sm-10" style="margin-top:10px">
                            @php
                                $userRoles = DB::table('role_user')->where('user_id', $user->id)->get();
                                $userRolesArray = $userRoles->pluck('role_id')->toArray();
                            @endphp
                            @foreach ($roles as $role)
                                <label>
                                    <input type="checkbox" name="role_id[]" class="minimal" value="{{ $role->id }}"
                                        @if(in_array($role->id, old('role_id', $userRolesArray))) checked @endif>
                                    <span style="display:inline-block;margin-right:15px">{{ ucfirst($role->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    @include('admin.users._localites')

                    <div class="form-group">
                        <label for="image" class="col-sm-2 control-label">@lang('user.photo')</label>
                        <div class="col-sm-10">
                            <img src="{{ asset('assets/admin/imgs/' . $user->photo) }}" id="photo"
                                style="margin-bottom: 8px;height:100px;border-radius:5px"><br>
                            <input type="file" name="image" class="form-control-file" id="image">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success" style="margin-top:10px">@lang('user.update_user')</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('custom')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#photo').attr('src', e.target.result).height(80).width(80);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#image").change(function() { readURL(this); });
</script>
@endpush
