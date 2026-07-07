<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">@lang('crud.role_name')</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset($role->name) ? $role->name : ''}}">
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
