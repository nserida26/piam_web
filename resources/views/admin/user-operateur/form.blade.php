<div class="form-group {{ $errors->has('date_valid') ? 'has-error' : '' }}">
    <label for="date_valid" class="control-label">@lang('crud.date_valid')</label>
    <input class="form-control" name="date_valid" type="date" id="date_valid"
        value="{{ isset($useroperateur->date_valid) ? $useroperateur->date_valid : '' }}">
    {!! $errors->first('date_valid', '<p class="help-block">:message</p>') !!}
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
    <select name="operateur_id" id="operateur" class="form-control operateur">
    </select>
</div>
<div class="form-group">
    <select data-placeholder="{{ __('crud.select_operation') }}" name="operation_id[]" id="operation" class="operation"
        multiple="multiple" runat="server" style="width: 100%;">
        @foreach ($operations as $item)
            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <select data-placeholder="{{ __('crud.select_user') }}" name="user_id[]" id="user" class="user" multiple="multiple"
        runat="server" style="width: 100%;">
        @foreach ($users as $item)
            <option value="{{ $item->id }}">{{ $item->username }} ({{ $item->email }})</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? __('crud.update') : __('crud.create') }}">
</div>
