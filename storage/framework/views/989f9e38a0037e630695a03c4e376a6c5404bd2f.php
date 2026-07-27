<?php $__env->startSection('title'); ?> Réorganisation territoriale — Moughataas <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> Réorganisation territoriale <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(url('admin/reorganisation/moughatas')); ?>">Moughataa → Wilaya</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> Moughataa → Wilaya <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Affecter des moughataas à une wilaya</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Recherchez des moughataas (par wilaya actuelle et/ou par nom), sélectionnez-en une ou
                    plusieurs, choisissez la wilaya de destination, puis validez. Les communes et localités des
                    moughataas déplacées suivent automatiquement.
                </p>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Wilaya actuelle</label>
                        <select id="f_wilaya" class="form-control">
                            <option value="">-- Toutes --</option>
                            <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($w->id); ?>"><?php echo e($w->intitule); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nom de la moughataa</label>
                        <input type="text" id="f_q" class="form-control" placeholder="Rechercher...">
                    </div>
                </div>
                <button type="button" id="btn-search" class="btn btn-primary btn-sm mb-3">
                    <i class="fas fa-search"></i> Rechercher
                </button>
                <span id="search-hint" class="text-muted ml-2"></span>

                <form method="POST" action="<?php echo e(route('admin.reorganisation.moughatas.assign')); ?>" id="assign-form">
                    <?php echo csrf_field(); ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="results-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:30px"><input type="checkbox" id="check-all"></th>
                                    <th>Moughataa</th>
                                    <th>Code</th>
                                    <th>Wilaya actuelle</th>
                                </tr>
                            </thead>
                            <tbody id="results-body">
                                <tr><td colspan="4" class="text-center text-muted">Lancez une recherche pour afficher des moughataas.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <h5>Wilaya de destination</h5>
                    <div class="row align-items-end">
                        <div class="col-md-8 form-group">
                            <label>Wilaya</label>
                            <select name="wilaya_id" id="t_wilaya" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($w->id); ?>"><?php echo e($w->intitule); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
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
        $.get("<?php echo e(route('admin.reorganisation.moughatas.search')); ?>", {
            wilaya_id: $('#f_wilaya').val(),
            q: $('#f_q').val(),
        }, function (res) {
            $('#check-all').prop('checked', false);
            $('#search-hint').text(res.message || (res.data.length + ' résultat(s)'));
            var rows = '';
            $.each(res.data, function (i, m) {
                rows += '<tr>'
                    + '<td><input type="checkbox" class="row-checkbox" name="moughata_ids[]" value="' + m.id + '"></td>'
                    + '<td>' + m.intitule + '</td>'
                    + '<td>' + m.code + '</td>'
                    + '<td>' + m.wilaya + '</td>'
                    + '</tr>';
            });
            $('#results-body').html(rows || '<tr><td colspan="4" class="text-center text-muted">Aucun résultat.</td></tr>');
            updateSelectedCount();
        }, 'json');
    });

    $('#assign-form').on('submit', function (e) {
        if ($('#results-body .row-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Sélectionnez au moins une moughataa.');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/reorganisation/moughatas.blade.php ENDPATH**/ ?>