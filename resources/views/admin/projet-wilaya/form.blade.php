
<div class="form-group">
    <select name="wilaya_id" id="wilaya" class="form-control wilaya" runat="server">
        <option value="">@lang('crud.select_wilaya')</option>
        @foreach ($wilayas as $item)
            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <select name="projet_id" id="projet" class="form-control projet" runat="server">
        <option value="">@lang('crud.select_projet')</option>
        @foreach ($projets as $item)
            <option value="{{ $item->id }}">{{ $item->intitule_resume }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
