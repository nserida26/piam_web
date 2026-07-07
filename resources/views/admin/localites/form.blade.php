@if($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-ban"></i> @lang('Erreur')!</h5>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="code" class="control-label">@lang('localite.code') *</label>
            <input class="form-control" name="code" type="text" id="code"
                   value="{{ old('code', isset($localite->code) ? $localite->code : '') }}"
                   required>
            @error('code')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <label for="code_ansade" class="control-label">@lang('localite.code_ansade')</label>
            <input class="form-control" name="code_ansade" type="text" id="code_ansade"
                   value="{{ old('code_ansade', isset($localite->code_ansade) ? $localite->code_ansade : '') }}">
            @error('code_ansade')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="intitule" class="control-label">@lang('localite.intitule') *</label>
            <input class="form-control" name="intitule" type="text" id="intitule"
                   value="{{ old('intitule', isset($localite->intitule) ? $localite->intitule : '') }}"
                   required>
            @error('intitule')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <label for="intitule_fr" class="control-label">@lang('localite.intitule_fr') *</label>
            <input class="form-control" name="intitule_fr" type="text" id="intitule_fr"
                   value="{{ old('intitule_fr', isset($localite->intitule_fr) ? $localite->intitule_fr : '') }}"
                   required>
            @error('intitule_fr')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="wilaya_id" class="control-label">@lang('localite.wilaya') *</label>
            <select name="wilaya_id" id="wilaya" class="form-control wilaya" required>
                <option value="">@lang('localite.select_wilaya')</option>
                @foreach ($wilayas as $item)
                    <option value="{{ $item->id }}" 
                        {{ old('wilaya_id', isset($localite->wilaya_id) ? $localite->wilaya_id : '') == $item->id ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'ar' ? $item->intitule : $item->intitule_fr }}
                    </option>
                @endforeach
            </select>
            @error('wilaya_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            <label for="moughata_id" class="control-label">@lang('localite.moughata') *</label>
            <select name="moughata_id" id="moughata" class="form-control moughata" required>
                <option value="">@lang('localite.select_moughata')</option>
                @if(isset($localite->moughata_id) && $localite->moughata)
                    <option value="{{ $localite->moughata_id }}" selected>
                        {{ app()->getLocale() == 'ar' ? $localite->moughata->intitule : $localite->moughata->intitule_fr }}
                    </option>
                @endif
            </select>
            @error('moughata_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            <label for="commune_id" class="control-label">@lang('localite.commune') *</label>
            <select name="commune_id" id="commune" class="form-control commune" required>
                <option value="">@lang('localite.select_commune')</option>
                @if(isset($localite->commune_id) && $localite->commune)
                    <option value="{{ $localite->commune_id }}" selected>
                        {{ app()->getLocale() == 'ar' ? $localite->commune->intitule : $localite->commune->intitule_fr }}
                    </option>
                @endif
            </select>
            @error('commune_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="nb_pop_ansade" class="control-label">@lang('localite.nb_pop_ansade')</label>
            <input class="form-control" name="nb_pop_ansade" type="number" id="nb_pop_ansade" min="0"
                   value="{{ old('nb_pop_ansade', isset($localite->nb_pop_ansade) ? $localite->nb_pop_ansade : '') }}">
            @error('nb_pop_ansade')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            <label for="lat_loc" class="control-label">@lang('localite.lat_loc')</label>
            <input class="form-control" name="lat_loc" type="text" id="lat_loc"
                   value="{{ old('lat_loc', isset($localite->lat_loc) ? $localite->lat_loc : '') }}">
            @error('lat_loc')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            <label for="long_loc" class="control-label">@lang('localite.long_loc')</label>
            <input class="form-control" name="long_loc" type="text" id="long_loc"
                   value="{{ old('long_loc', isset($localite->long_loc) ? $localite->long_loc : '') }}">
            @error('long_loc')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input" type="checkbox" id="valider" name="valider" value="1"
                    {{ old('valider', isset($localite->valider) ? $localite->valider : 0) ? 'checked' : '' }}>
                <label for="valider" class="custom-control-label">@lang('localite.valider')</label>
            </div>
            @error('valider')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> {{ $formMode === 'edit' ? __('localite.update') : __('localite.save') }}
    </button>
    <a href="{{ url('/admin/localites') }}" class="btn btn-default">
        <i class="fa fa-times"></i> @lang('Annuler')
    </a>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        //Initialize Select2 Elements
        $('.wilaya').select2({
            placeholder: "@lang('localite.select_wilaya')",
            allowClear: true
        });
        $('.moughata').select2({
            placeholder: "@lang('localite.select_moughata')",
            allowClear: true
        });
        $('.commune').select2({
            placeholder: "@lang('localite.select_commune')",
            allowClear: true
        });

        // Load moughatas when wilaya changes
        $('#wilaya').on('change', function() {
            var idWilaya = this.value;
            $("#moughata").html('<option value="">@lang('localite.select_moughata')</option>');
            $("#commune").html('<option value="">@lang('localite.select_commune')</option>');

            if(idWilaya) {
                $.ajax({
                    url: "{{ url('admin/localites/get-moughatas') }}/" + idWilaya,
                    type: "GET",
                    dataType: 'json',
                    success: function(result) {
                        $.each(result.moughatas, function(key, value) {
                            $("#moughata").append('<option value="' + value.id + '">' + 
                                (@if(app()->getLocale() == 'ar') value.intitule @else value.intitule_fr @endif) + 
                                '</option>');
                        });
                    }
                });
            }
        });

        // Load communes when moughata changes
        $('#moughata').on('change', function() {
            var idMoughata = this.value;
            $("#commune").html('<option value="">@lang('localite.select_commune')</option>');

            if(idMoughata) {
                $.ajax({
                    url: "{{ url('admin/localites/get-communes') }}/" + idMoughata,
                    type: "GET",
                    dataType: 'json',
                    success: function(result) {
                        $.each(result.communes, function(key, value) {
                            $("#commune").append('<option value="' + value.id + '">' + 
                                (@if(app()->getLocale() == 'ar') value.intitule @else value.intitule_fr @endif) + 
                                '</option>');
                        });
                    }
                });
            }
        });

        // Load moughatas and communes on edit if wilaya is already selected
        @if(isset($localite->wilaya_id) && $localite->wilaya_id)
            $('#wilaya').trigger('change');
            setTimeout(function() {
                $('#moughata').val('{{ $localite->moughata_id }}').trigger('change');
            }, 500);
            
            setTimeout(function() {
                $('#commune').val('{{ $localite->commune_id }}').trigger('change');
            }, 1000);
        @endif
    });
</script>
@endpush