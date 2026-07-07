<div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
    <label for="code" class="control-label">@lang('crud.code')</label>
    <input class="form-control" name="code" type="text" id="code"
        value="{{ isset($moughatum->code) ? $moughatum->code : '' }}">
    {!! $errors->first('code', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule') ? 'has-error' : '' }}">
    <label for="intitule" class="control-label">@lang('crud.intitule')</label>
    <input class="form-control" name="intitule" type="text" id="intitule"
        value="{{ isset($moughatum->intitule) ? $moughatum->intitule : '' }}">
    {!! $errors->first('intitule', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule_fr') ? 'has-error' : '' }}">
    <label for="intitule_fr" class="control-label">@lang('crud.intitule_fr')</label>
    <input class="form-control" name="intitule_fr" type="text" id="intitule_fr"
        value="{{ isset($moughatum->intitule_fr) ? $moughatum->intitule_fr : '' }}">
    {!! $errors->first('intitule_fr', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('wilaya_id') ? 'has-error' : '' }}">
    <select name="wilaya_id" id="wilaya" class="form-control wilaya" runat="server">
        <option value="">@lang('crud.select_wilaya')</option>
        @foreach ($wilayas as $item)
            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
