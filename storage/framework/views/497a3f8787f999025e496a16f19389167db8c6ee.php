<?php $__env->startSection('title'); ?> Réorganisation territoriale — Localités <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> Réorganisation territoriale <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(url('admin/reorganisation/localites')); ?>">Localités → Commune</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> Localités → Commune <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Affecter des localités à une commune</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Recherchez des localités (par wilaya/moughataa/commune actuels et/ou par nom), sélectionnez-en une ou
                    plusieurs, choisissez la commune de destination, puis validez. Utile notamment pour les localités
                    laissées "non affectées" par la migration de nomenclature.
                </p>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Wilaya</label>
                        <select id="f_wilaya" class="form-control">
                            <option value="">-- Toutes --</option>
                            <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($w->id); ?>"><?php echo e($w->intitule); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Moughataa</label>
                        <select id="f_moughata" class="form-control">
                            <option value="">-- Toutes --</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Commune actuelle</label>
                        <select id="f_commune" class="form-control">
                            <option value="">-- Toutes --</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Nom / code ansade</label>
                        <input type="text" id="f_q" class="form-control" placeholder="Rechercher...">
                    </div>
                </div>
                <button type="button" id="btn-search" class="btn btn-primary btn-sm mb-3">
                    <i class="fas fa-search"></i> Rechercher
                </button>
                <span id="search-hint" class="text-muted ml-2"></span>

                <form method="POST" action="<?php echo e(route('admin.reorganisation.localites.assign')); ?>" id="assign-form">
                    <?php echo csrf_field(); ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="results-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:30px"><input type="checkbox" id="check-all"></th>
                                    <th>Localité</th>
                                    <th>Code ansade</th>
                                    <th>Wilaya</th>
                                    <th>Moughataa</th>
                                    <th>Commune actuelle</th>
                                </tr>
                            </thead>
                            <tbody id="results-body">
                                <tr><td colspan="6" class="text-center text-muted">Lancez une recherche pour afficher des localités.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <h5>Commune de destination</h5>
                    <div class="row align-items-end">
                        <div class="col-md-3 form-group">
                            <label>Wilaya</label>
                            <select id="t_wilaya" class="form-control">
                                <option value="">-- Choisir --</option>
                                <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($w->id); ?>"><?php echo e($w->intitule); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Moughataa</label>
                            <select id="t_moughata" class="form-control">
                                <option value="">-- Choisir --</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Commune</label>
                            <select name="commune_id" id="t_commune" class="form-control" required>
                                <option value="">-- Choisir --</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Affecter la sélection (<span id="selected-count">0</span>)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom'); ?>
<script>
$(function () {
    function loadMoughatas(wilayaId, target, onDone) {
        $(target).html('<option value="">-- Toutes --</option>');
        if (!wilayaId) { if (onDone) onDone(); return; }
        $.get("<?php echo e(url('admin/localites/get-moughatas')); ?>/" + wilayaId, function (result) {
            $.each(result.moughatas, function (k, v) {
                $(target).append('<option value="' + v.id + '">' + v.intitule + '</option>');
            });
            if (onDone) onDone();
        }, 'json');
    }

    function loadCommunes(moughataId, target, onDone) {
        $(target).html('<option value="">-- Toutes --</option>');
        if (!moughataId) { if (onDone) onDone(); return; }
        $.get("<?php echo e(url('admin/localites/get-communes')); ?>/" + moughataId, function (result) {
            $.each(result.communes, function (k, v) {
                $(target).append('<option value="' + v.id + '">' + v.intitule + '</option>');
            });
            if (onDone) onDone();
        }, 'json');
    }

    $('#f_wilaya').on('change', function () { loadMoughatas(this.value, '#f_moughata', function () { $('#f_commune').html('<option value="">-- Toutes --</option>'); }); });
    $('#f_moughata').on('change', function () { loadCommunes(this.value, '#f_commune'); });

    $('#t_wilaya').on('change', function () { loadMoughatas(this.value, '#t_moughata', function () { $('#t_commune').html('<option value="">-- Choisir --</option>'); }); });
    $('#t_moughata').on('change', function () { loadCommunes(this.value, '#t_commune'); });

    function updateSelectedCount() {
        $('#selected-count').text($('#results-body .row-checkbox:checked').length);
    }

    $('#check-all').on('change', function () {
        $('#results-body .row-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    $('#results-body').on('change', '.row-checkbox', function () {
        if (!this.checked) { $('#check-all').prop('checked', false); }
        updateSelectedCount();
    });

    $('#btn-search').on('click', function () {
        $.get("<?php echo e(route('admin.reorganisation.localites.search')); ?>", {
            wilaya_id: $('#f_wilaya').val(),
            moughata_id: $('#f_moughata').val(),
            commune_id: $('#f_commune').val(),
            q: $('#f_q').val(),
        }, function (res) {
            $('#check-all').prop('checked', false);
            $('#search-hint').text(res.message || (res.data.length + ' résultat(s)'));
            var rows = '';
            $.each(res.data, function (i, l) {
                rows += '<tr>'
                    + '<td><input type="checkbox" class="row-checkbox" name="localite_ids[]" value="' + l.id + '" form="assign-form"></td>'
                    + '<td>' + l.intitule + '</td>'
                    + '<td>' + (l.code_ansade || '') + '</td>'
                    + '<td>' + l.wilaya + '</td>'
                    + '<td>' + l.moughata + '</td>'
                    + '<td>' + l.commune + '</td>'
                    + '</tr>';
            });
            $('#results-body').html(rows || '<tr><td colspan="6" class="text-center text-muted">Aucun résultat.</td></tr>');
            updateSelectedCount();
        }, 'json');
    });

    $('#assign-form').on('submit', function (e) {
        if ($('#results-body .row-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Sélectionnez au moins une localité.');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/reorganisation/localites.blade.php ENDPATH**/ ?>