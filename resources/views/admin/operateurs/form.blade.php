<div class="form-group {{ $errors->has('intitule') ? 'has-error' : ''}}">
    <label for="intitule" class="control-label">@lang('crud.intitule')</label>
    <input class="form-control" name="intitule" type="text" id="intitule" value="{{ isset($operateur->intitule) ? $operateur->intitule : ''}}">
    {!! $errors->first('intitule', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule_fr') ? 'has-error' : ''}}">
    <label for="intitule_fr" class="control-label">@lang('crud.intitule_fr')</label>
    <input class="form-control" name="intitule_fr" type="text" id="intitule_fr" value="{{ isset($operateur->intitule_fr) ? $operateur->intitule_fr : ''}}">
    {!! $errors->first('intitule_fr', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('type_struct') ? 'has-error' : ''}}">
    <label for="type_struct" class="control-label">@lang('crud.type_struct')</label>
    <input class="form-control" name="type_struct" type="text" id="type_struct" value="{{ isset($operateur->type_struct) ? $operateur->type_struct : ''}}">
    {!! $errors->first('type_struct', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
