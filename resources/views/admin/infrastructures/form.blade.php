<div class="form-group {{ $errors->has('infra_publ') ? 'has-error' : '' }}">
    <label for="infra_publ" class="control-label">@lang('crud.infra_publ')</label>
    <input class="form-control" name="infra_publ" type="text" id="infra_publ"
        value="{{ isset($infrastructure->infra_publ) ? $infrastructure->infra_publ : '' }}">
    {!! $errors->first('infra_publ', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('code_infra_publ') ? 'has-error' : '' }}">
    <label for="code_infra_publ" class="control-label">@lang('crud.code_infra_publ')</label>
    <input class="form-control" name="code_infra_publ" type="text" id="code_infra_publ"
        value="{{ isset($infrastructure->code_infra_publ) ? $infrastructure->code_infra_publ : '' }}">
    {!! $errors->first('code_infra_publ', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('code_men') ? 'has-error' : '' }}">
    <label for="code_men" class="control-label">@lang('crud.code_men')</label>
    <input class="form-control" name="code_men" type="text" id="code_men"
        value="{{ isset($infrastructure->code_men) ? $infrastructure->code_men : '' }}">
    {!! $errors->first('code_men', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('intitule_infra_publ') ? 'has-error' : '' }}">
    <label for="intitule_infra_publ" class="control-label">@lang('crud.intitule_infra_publ')</label>
    <input class="form-control" name="intitule_infra_publ" type="text" id="intitule_infra_publ"
        value="{{ isset($infrastructure->intitule_infra_publ) ? $infrastructure->intitule_infra_publ : '' }}">
    {!! $errors->first('intitule_infra_publ', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('long_infra_publ') ? 'has-error' : '' }}">
    <label for="long_infra_publ" class="control-label">@lang('crud.long_infra_publ')</label>
    <input class="form-control" name="long_infra_publ" type="number" id="long_infra_publ"
        value="{{ isset($infrastructure->long_infra_publ) ? $infrastructure->long_infra_publ : '' }}">
    {!! $errors->first('long_infra_publ', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('lat_infra_publ') ? 'has-error' : '' }}">
    <label for="lat_infra_publ" class="control-label">@lang('crud.lat_infra_publ')</label>
    <input class="form-control" name="lat_infra_publ" type="number" id="lat_infra_publ"
        value="{{ isset($infrastructure->lat_infra_publ) ? $infrastructure->lat_infra_publ : '' }}">
    {!! $errors->first('lat_infra_publ', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('wilaya_id') ? 'has-error' : '' }}">
    <select name="wilaya_id" id="wilaya" class="form-control wilaya" runat="server">
        <option value="">@lang('crud.select_wilaya')</option>
        @foreach ($wilayas as $item)
            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
        @endforeach
    </select>
</div>
<div class="form-group {{ $errors->has('moughata_id') ? 'has-error' : '' }}">
    <select name="moughata_id" id="moughata" class="form-control moughata">
    </select>
</div>
<div class="form-group {{ $errors->has('commune_id') ? 'has-error' : '' }}">
    <select name="commune_id" id="commune" class="form-control commune">
    </select>
</div>
<div class="form-group {{ $errors->has('localite_id') ? 'has-error' : '' }}">
    <select name="localite_id" id="localite" class="form-control localite">
    </select>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
