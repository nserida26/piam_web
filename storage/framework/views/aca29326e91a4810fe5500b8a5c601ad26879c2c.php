<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('sidebar.validation'); ?> - <?php echo app('translator')->get('sidebar.menages'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?>
    <?php echo app('translator')->get('sidebar.validation'); ?> - <?php echo app('translator')->get('sidebar.menages'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('admin.reports.menages')); ?>"> <?php echo app('translator')->get('sidebar.validation'); ?> - <?php echo app('translator')->get('sidebar.menages'); ?> </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/select2/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/daterangepicker/daterangepicker.css')); ?>">
    <style>
        #detail-table th { width: 45%; font-weight: 600; }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title card_title_center"><?php echo app('translator')->get('general.filter'); ?></h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="wilaya" class="form-control filtre">
                            <option value="">-- Wilaya --</option>
                            <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->intitule); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="moughata" class="form-control filtre" disabled></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="commune" class="form-control filtre" disabled></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="localite" class="form-control filtre" disabled></select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <select id="operation" class="form-control filtre">
                            <option value="">-- Opération --</option>
                            <option value="2">Etat des lieux</option>
                            <option value="4">Dernier suivi</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select id="statut" class="form-control filtre">
                            <option value="">-- Statut --</option>
                            <option value="a_valider"><?php echo app('translator')->get('general.invalid'); ?></option>
                            <option value="valide"><?php echo app('translator')->get('general.valid'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" id="from_to" placeholder="Période" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title card_title_center"><?php echo app('translator')->get('sidebar.validation'); ?> - <?php echo app('translator')->get('sidebar.menages'); ?></h3>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <button type="button" class="btn btn-success" id="bulk-validate-btn" disabled>
                    <i class="fas fa-check"></i> <?php echo app('translator')->get('general.validate_selection'); ?>
                </button>
                <button type="button" class="btn btn-danger" id="bulk-reject-btn" disabled>
                    <i class="fas fa-times"></i> <?php echo app('translator')->get('general.reject_selection'); ?>
                </button>
                <span id="selected-count" class="ml-2 text-muted"></span>
            </div>
            <div class="table-responsive">
                <table id="data" class="table table-bordered table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="check-all"></th>
                            <th>Wilaya</th>
                            <th>Moughataa</th>
                            <th>Commune</th>
                            <th>Localité</th>
                            <th>Opération</th>
                            <th>Agent</th>
                            <th>Personnes</th>
                            <th>Latrine</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="bulkModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bulk-modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="bulk-form">
                    <div class="modal-body">
                        <p><span id="bulk-selected-count">0</span> <?php echo app('translator')->get('general.rows_selected'); ?></p>
                        <input type="hidden" id="bulk-status">
                        <div class="form-group">
                            <label for="bulk-remarque"><?php echo app('translator')->get('validate.remarques'); ?></label>
                            <textarea class="form-control" id="bulk-remarque"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('general.submit'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="detailModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Fiche ménage <span id="detail-id"></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered" id="detail-table"><tbody></tbody></table>
                    <div class="form-group">
                        <label for="detail-remarque"><?php echo app('translator')->get('validate.remarques'); ?></label>
                        <textarea class="form-control" id="detail-remarque"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <div>
                        <button type="button" class="btn btn-danger detail-action" data-status="0">
                            <i class="fas fa-times"></i> Rejeter
                        </button>
                        <button type="button" class="btn btn-success detail-action" data-status="1">
                            <i class="fas fa-check"></i> Valider
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('assets/admin/plugins/moment/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/daterangepicker/daterangepicker.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('custom'); ?>
<script>
$(function () {
    var csrf = '<?php echo e(csrf_token()); ?>';
    var bulkUrl = "<?php echo e(route('admin.reports.menages.bulkUpdate')); ?>";
    var showUrl = "<?php echo e(url('admin/reports/menages')); ?>/";

    var table = $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?php echo e(route('admin.reports.menages.data')); ?>",
            type: 'POST',
            data: function (d) {
                d._token = csrf;
                d.wilaya_id = $('#wilaya').val();
                d.moughata_id = $('#moughata').val();
                d.commune_id = $('#commune').val();
                d.localite_id = $('#localite').val();
                d.operation_id = $('#operation').val();
                d.statut = $('#statut').val();
                var fromTo = $('#from_to').val();
                if (fromTo) {
                    var dates = fromTo.split(' - ');
                    d.from = dates[0];
                    d.to = dates[1];
                }
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'wilaya' },
            { data: 'moughata' },
            { data: 'commune' },
            { data: 'localite' },
            { data: 'operation' },
            { data: 'agent' },
            { data: 'personnes', orderable: false },
            { data: 'latrine', orderable: false },
            { data: 'date' },
            {
                data: 'confirmed',
                render: function (val, type, row) {
                    var badge = val
                        ? '<span class="badge badge-success"><?php echo app('translator')->get("general.valid"); ?></span>'
                        : '<span class="badge badge-danger"><?php echo app('translator')->get("general.invalid"); ?></span>';
                    return row.remarque ? badge + '<br><small class="text-muted">' + row.remarque + '</small>' : badge;
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        pageLength: 50,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Tous']],
        order: [[9, 'desc']],
        createdRow: function (row, data) {
            $(row).addClass(data.confirmed ? 'table-success' : 'table-danger');
        },
        drawCallback: function () {
            $('#check-all').prop('checked', false);
            updateBulkButtons();
        }
    });

    function selectedIds() {
        return $('#data tbody .row-checkbox:checked').map(function () { return this.value; }).get();
    }

    function updateBulkButtons() {
        var count = selectedIds().length;
        $('#bulk-validate-btn, #bulk-reject-btn').prop('disabled', count === 0);
        $('#selected-count').text(count > 0 ? count + ' <?php echo app('translator')->get("general.rows_selected"); ?>' : '');
    }

    function sendStatus(ids, status, remarque, done) {
        $.post(bulkUrl, { _token: csrf, ids: ids, status: status, remarque: remarque }, function () {
            table.ajax.reload(null, false);
            if (done) done();
        }, 'json').fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Erreur');
        });
    }

    $('#check-all').on('change', function () {
        $('#data tbody .row-checkbox').prop('checked', this.checked);
        updateBulkButtons();
    });
    $('#data tbody').on('change', '.row-checkbox', function () {
        if (!this.checked) $('#check-all').prop('checked', false);
        updateBulkButtons();
    });

    function openBulkModal(status) {
        var ids = selectedIds();
        if (!ids.length) return;
        $('#bulk-status').val(status);
        $('#bulk-selected-count').text(ids.length);
        $('#bulk-modal-title').text(status == 1 ? '<?php echo app('translator')->get("general.validate_selection"); ?>' : '<?php echo app('translator')->get("general.reject_selection"); ?>');
        $('#bulk-remarque').val('');
        $('#bulkModal').modal('show');
    }
    $('#bulk-validate-btn').on('click', function () { openBulkModal(1); });
    $('#bulk-reject-btn').on('click', function () { openBulkModal(0); });

    $('#bulk-form').on('submit', function (e) {
        e.preventDefault();
        sendStatus(selectedIds(), $('#bulk-status').val(), $('#bulk-remarque').val(), function () {
            $('#bulkModal').modal('hide');
        });
    });

    // Détail d'une fiche + validation unitaire
    var currentId = null;
    $('#data tbody').on('click', '.btn-detail', function () {
        currentId = $(this).data('id');
        $.getJSON(showUrl + currentId, function (res) {
            var esc = function (v) { return $('<div>').text(v === null || v === undefined ? '' : v).html(); };
            var html = '';
            $.each(res.entete, function (label, value) {
                html += '<tr class="table-light"><th>' + esc(label) + '</th><td>' + esc(value) + '</td></tr>';
            });
            $.each(res.fields, function (i, f) {
                html += '<tr><th>' + esc(f.label) + '</th><td>' + esc(f.value) + '</td></tr>';
            });
            html += '<tr><th>Statut</th><td>' + (res.confirmed
                ? '<span class="badge badge-success"><?php echo app('translator')->get("general.valid"); ?></span>'
                : '<span class="badge badge-danger"><?php echo app('translator')->get("general.invalid"); ?></span>') + '</td></tr>';
            $('#detail-id').text('#' + res.id);
            $('#detail-table tbody').html(html);
            $('#detail-remarque').val(res.remarque || '');
            $('#detailModal').modal('show');
        });
    });
    $('.detail-action').on('click', function () {
        if (!currentId) return;
        sendStatus([currentId], $(this).data('status'), $('#detail-remarque').val(), function () {
            $('#detailModal').modal('hide');
        });
    });

    // Filtres
    $('.filtre').select2({ width: '100%' });
    $('#from_to').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, autoUpdateInput: false });
    $('#from_to').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        table.ajax.reload();
    }).on('cancel.daterangepicker', function () {
        $(this).val('');
        table.ajax.reload();
    });

    function fill($select, placeholder, items, labelKey) {
        $select.html('<option value="">' + placeholder + '</option>');
        $.each(items || [], function (i, v) {
            $select.append($('<option>').val(v.id).text(v[labelKey] || v.intitule));
        });
        $select.prop('disabled', false).trigger('change.select2');
    }
    function reset($selects) {
        $selects.empty().prop('disabled', true).trigger('change.select2');
    }

    $('#wilaya').on('change', function () {
        reset($('#moughata, #commune, #localite'));
        if (this.value) {
            $.post("<?php echo e(url('admin/api/fetch-moughatas')); ?>", { _token: csrf, wilaya_id: this.value }, function (res) {
                fill($('#moughata'), '-- Moughataa --', res.moughatas, 'intitule_fr');
            }, 'json');
        }
        table.ajax.reload();
    });
    $('#moughata').on('change', function () {
        reset($('#commune, #localite'));
        if (this.value) {
            $.post("<?php echo e(url('admin/api/fetch-communes')); ?>", { _token: csrf, moughata_id: this.value }, function (res) {
                fill($('#commune'), '-- Commune --', res.communes, 'intitule_fr');
            }, 'json');
        }
        table.ajax.reload();
    });
    $('#commune').on('change', function () {
        reset($('#localite'));
        if (this.value) {
            $.post("<?php echo e(url('admin/api/fetch-localites')); ?>", {
                _token: csrf, wilaya_id: $('#wilaya').val(), moughata_id: $('#moughata').val(), commune_id: this.value
            }, function (res) {
                fill($('#localite'), '-- Localité --', res.localites, 'intitule_fr');
            }, 'json');
        }
        table.ajax.reload();
    });
    $('#localite, #operation, #statut').on('change', function () { table.ajax.reload(); });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/reports/menages/index.blade.php ENDPATH**/ ?>