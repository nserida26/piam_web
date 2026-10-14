{{--
    Localités assignées à un facilitateur. Visible uniquement quand le rôle
    « Facilitateur » est coché. Les localités choisies sont envoyées dans
    localite_ids[] ; l'application mobile n'affichera que celles-ci.
    Variables : $wilayas, $assignedLocalites (collection, peut être vide).
--}}
<div class="form-group" id="localites-section" style="display:none;margin-top:10px">
    <label class="control-label">@lang('user.localites_label') <span class="text-danger">*</span></label>
    <p class="text-muted small mb-2">@lang('user.localites_help')</p>

    @error('localite_ids')
        <div class="alert alert-danger py-1">{{ $message }}</div>
    @enderror

    <div id="localites-selected" class="mb-2"></div>
    <p id="localites-empty" class="text-muted small">@lang('user.localites_none')</p>

    <div class="card card-body bg-light p-2">
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <select id="loc-wilaya" class="form-control form-control-sm">
                    <option value="">@lang('user.wilaya') — @lang('user.all')</option>
                    @foreach ($wilayas as $wilaya)
                        <option value="{{ $wilaya->id }}">{{ $wilaya->intitule }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select id="loc-moughata" class="form-control form-control-sm" disabled>
                    <option value="">@lang('user.moughataa') — @lang('user.all')</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select id="loc-commune" class="form-control form-control-sm" disabled>
                    <option value="">@lang('user.commune') — @lang('user.all')</option>
                </select>
            </div>
        </div>
        <input type="text" id="loc-q" class="form-control form-control-sm mb-2"
            placeholder="{{ __('user.localites_search') }}">
        <div id="loc-results" style="max-height:240px;overflow-y:auto"></div>
    </div>
</div>

@push('custom')
<script>
(function () {
    var urls = {
        moughatas: "{{ url('admin/localites/get-moughatas') }}/",
        communes: "{{ url('admin/localites/get-communes') }}/",
        search: "{{ route('admin.reorganisation.localites.search') }}"
    };
    var txt = {
        add: @json(__('user.localites_add')),
        remove: @json(__('user.localites_remove')),
        chooseWilaya: @json(__('user.localites_choose_wilaya')),
        noResult: @json(__('user.localites_no_result')),
        all: @json(__('user.all')),
        moughataa: @json(__('user.moughataa')),
        commune: @json(__('user.commune'))
    };
    var selected = {}; // id -> libellé
    @foreach ($assignedLocalites as $loc)
        selected[{{ $loc->id }}] = @json($loc->intitule . ($loc->code_ansade ? ' (' . $loc->code_ansade . ')' : ''));
    @endforeach

    function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }

    // Section visible seulement si le rôle Facilitateur est coché.
    function toggleSection() {
        var on = $('input[name="role_id[]"][data-facilitateur="1"]:checked').length > 0;
        $('#localites-section').toggle(on);
        $('#localites-section input[name="localite_ids[]"]').prop('disabled', !on);
    }

    function renderSelected() {
        var box = $('#localites-selected').empty();
        var ids = Object.keys(selected);
        $('#localites-empty').toggle(ids.length === 0);
        ids.forEach(function (id) {
            box.append(
                '<span class="badge badge-success p-2 mr-1 mb-1" style="font-size:90%">' +
                esc(selected[id]) +
                ' <a href="#" class="text-white ml-1 loc-remove" data-id="' + id + '" title="' + esc(txt.remove) + '">&times;</a>' +
                '<input type="hidden" name="localite_ids[]" value="' + id + '"></span>'
            );
        });
        toggleSection();
        search(); // met à jour les boutons Ajouter
    }

    var timer = null;
    function search() {
        var params = {
            wilaya_id: $('#loc-wilaya').val(),
            moughata_id: $('#loc-moughata').val(),
            commune_id: $('#loc-commune').val(),
            q: $('#loc-q').val()
        };
        if (!params.wilaya_id && !params.q) {
            $('#loc-results').html('<p class="text-muted small m-1">' + esc(txt.chooseWilaya) + '</p>');
            return;
        }
        $.getJSON(urls.search, params, function (res) {
            var rows = res.data || [];
            if (!rows.length) {
                $('#loc-results').html('<p class="text-muted small m-1">' + esc(txt.noResult) + '</p>');
                return;
            }
            var html = '<table class="table table-sm table-hover mb-0"><tbody>';
            rows.forEach(function (l) {
                var label = l.intitule + (l.code_ansade ? ' (' + l.code_ansade + ')' : '');
                var already = selected[l.id] !== undefined;
                html += '<tr><td>' + esc(label) +
                    '<br><small class="text-muted">' + esc(l.wilaya + ' / ' + l.moughata + ' / ' + l.commune) + '</small></td>' +
                    '<td class="text-right align-middle">' +
                    (already ? '<i class="fas fa-check text-success"></i>'
                             : '<button type="button" class="btn btn-xs btn-outline-success loc-add" data-id="' + l.id +
                               '" data-label="' + esc(label) + '">' + esc(txt.add) + '</button>') +
                    '</td></tr>';
            });
            $('#loc-results').html(html + '</tbody></table>');
        });
    }

    function fillSelect(sel, items, placeholder) {
        sel.empty().append('<option value="">' + esc(placeholder + ' — ' + txt.all) + '</option>');
        items.forEach(function (i) { sel.append('<option value="' + i.id + '">' + esc(i.intitule) + '</option>'); });
        sel.prop('disabled', items.length === 0);
    }

    $(function () {
        // Repère la case du rôle Facilitateur par son nom (« Faciltateur » en base).
        $('input[name="role_id[]"]').each(function () {
            var name = $(this).closest('label').text().toLowerCase();
            if (name.indexOf('facil') !== -1) $(this).attr('data-facilitateur', '1');
        });
        $(document).on('change', 'input[name="role_id[]"]', toggleSection);

        $('#loc-wilaya').on('change', function () {
            fillSelect($('#loc-commune'), [], txt.commune);
            if (!this.value) { fillSelect($('#loc-moughata'), [], txt.moughataa); search(); return; }
            $.getJSON(urls.moughatas + this.value, function (res) {
                fillSelect($('#loc-moughata'), res.moughatas || [], txt.moughataa);
            });
            search();
        });
        $('#loc-moughata').on('change', function () {
            if (!this.value) { fillSelect($('#loc-commune'), [], txt.commune); search(); return; }
            $.getJSON(urls.communes + this.value, function (res) {
                fillSelect($('#loc-commune'), res.communes || [], txt.commune);
            });
            search();
        });
        $('#loc-commune').on('change', search);
        $('#loc-q').on('input', function () { clearTimeout(timer); timer = setTimeout(search, 300); });

        $(document).on('click', '.loc-add', function () {
            selected[$(this).data('id')] = $(this).data('label');
            renderSelected();
        });
        $(document).on('click', '.loc-remove', function (e) {
            e.preventDefault();
            delete selected[$(this).data('id')];
            renderSelected();
        });

        renderSelected();
    });
})();
</script>
@endpush
