<div class="form-group">
    <select name="wilaya_id" id="wilaya" class="form-control wilaya" runat="server">
        <option value="">@lang('crud.select_wilaya')</option>
        @foreach ($wilayas as $item)
            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <select name="user_id" id="user" class="user" runat="server" style="width: 100%;">
        <option value="">@lang('crud.select_user')</option>
        @foreach ($users as $item)
            <option value="{{ $item->id }}">{{ $item->username }} ({{ $item->email }})</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
