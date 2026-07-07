<div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
    <label for="code" class="control-label">@lang('crud.code')</label>
    <input class="form-control" name="code" type="text" id="code"
        value="{{ isset($projet->code) ? $projet->code : '' }}">
    {!! $errors->first('code', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule_resume') ? 'has-error' : '' }}">
    <label for="intitule_resume" class="control-label">@lang('crud.intitule_resume')</label>
    <input class="form-control" name="intitule_resume" type="text" id="intitule_resume"
        value="{{ isset($projet->intitule_resume) ? $projet->intitule_resume : '' }}">
    {!! $errors->first('intitule_resume', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule_resume_fr') ? 'has-error' : '' }}">
    <label for="intitule_resume_fr" class="control-label">@lang('crud.intitule_resume_fr')</label>
    <input class="form-control" name="intitule_resume_fr" type="text" id="intitule_resume_fr"
        value="{{ isset($projet->intitule_resume_fr) ? $projet->intitule_resume_fr : '' }}">
    {!! $errors->first('intitule_resume_fr', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule_complet') ? 'has-error' : '' }}">
    <label for="intitule_complet" class="control-label">@lang('crud.intitule_complet')</label>
    <input class="form-control" name="intitule_complet" type="text" id="intitule_complet"
        value="{{ isset($projet->intitule_complet) ? $projet->intitule_complet : '' }}">
    {!! $errors->first('intitule_complet', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule_complet_fr') ? 'has-error' : '' }}">
    <label for="intitule_complet_fr" class="control-label">@lang('crud.intitule_complet_fr')</label>
    <input class="form-control" name="intitule_complet_fr" type="text" id="intitule_complet_fr"
        value="{{ isset($projet->intitule_complet_fr) ? $projet->intitule_complet_fr : '' }}">
    {!! $errors->first('intitule_complet_fr', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('milieu_cible') ? 'has-error' : '' }}">
    <label for="milieu_cible" class="control-label">@lang('crud.milieu_cible')</label>
    <select name="milieu_cible" class="form-control" id="milieu_cible">
        <option value="urbaine" {{ isset($projet->milieu_cible) && $projet->milieu_cible === 'urbaine' ? 'selected' : '' }}>
            @lang('crud.urbaine')</option>
        <option value="rural" {{ isset($projet->milieu_cible) && $projet->milieu_cible === 'rural' ? 'selected' : '' }}>
            @lang('crud.rural')</option>
        <option value="semi_rural" {{ isset($projet->milieu_cible) && $projet->milieu_cible === 'semi_rural' ? 'selected' : '' }}>
            @lang('crud.semi_rural')</option>
        <option value="autre" {{ isset($projet->milieu_cible) && $projet->milieu_cible === 'autre' ? 'selected' : '' }}>
            @lang('crud.autre')</option>
    </select>
    {!! $errors->first('milieu_cible', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('periode') ? 'has-error' : '' }}">
    <label for="periode" class="control-label">@lang('crud.periode')</label>
    <input class="form-control" name="periode" type="number" id="periode"
        value="{{ isset($projet->periode) ? $projet->periode : '' }}">
    {!! $errors->first('periode', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
