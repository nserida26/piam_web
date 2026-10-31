
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('sidebar.validation'); ?> - <?php echo app('translator')->get('sidebar.family'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> 
    <?php echo app('translator')->get('sidebar.validation'); ?> - <?php echo app('translator')->get('sidebar.family'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('admin.reports.localites')); ?>"> <?php echo app('translator')->get('sidebar.validation'); ?> - <?php echo app('translator')->get('sidebar.family'); ?> </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-scroller/css/scroller.bootstrap4.min.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/select2/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/daterangepicker/daterangepicker.css')); ?>">

    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="<?php echo e(asset('assets/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')); ?>">
    <style>
        #loading-spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            position: fixed;
            top: 50%;
            left: 50%;
            margin-top: -25px;
            margin-left: -25px;
            z-index: 999;
        }

        .hidden {
            display: none;
        }

        @keyframes  spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title card_title_center"><?php echo app('translator')->get('general.filter'); ?>
            </h2>

        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="wilaya" id="wilaya" class="form-control wilaya">
                            <option value="">-- Select Wilaya --</option>
                            <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->intitule); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="moughata" id="moughata" class="form-control moughata">
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="commune" id="commune" class="form-control commune">
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="localite" id="localite" class="form-control localite">
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">

                        <select name="operation" id="operation" class="form-control operation">
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <select name="projet" id="projet" class="form-control projet">

                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">

                        <select name="operateur" id="operateur" class="form-control operateur">


                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control float-right" id="from_to">
                        </div>

                        <!-- /.input group -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loading-spinner" class="hidden"></div>


    <div class="row" id="content">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_title_center"><?php echo app('translator')->get('sidebar.validation'); ?></h3>
                </div>
                <!-- /.card-header -->
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
                    <th>Moughata</th>
                    <th>Commune</th>
                    <th>Localite</th>
                    <th>Operation</th>
                    <th>Projet</th>
                    <th>Operateur</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th id="actions">#</th>
                </tr>
            </thead>
            <tbody id="localite_reports">
            </tbody>
            <tfoot>
                <tr>
                    <th>#</th>
                    <th>Wilaya</th>
                    <th>Moughata</th>
                    <th>Commune</th>
                    <th>Localite</th>
                    <th>Operation</th>
                    <th>Projet</th>
                    <th>Operateur</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>#</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <div class="modal fade" id="bulkModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bulk-modal-title"><?php echo app('translator')->get('general.validate_selection'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><span id="bulk-selected-count">0</span> <?php echo app('translator')->get('general.rows_selected'); ?></p>
                    <form id="bulk-form">
                        <input type="hidden" id="bulk-status" name="status">
                        <div class="form-group">
                            <label for="bulk-remarque"><?php echo app('translator')->get('validate.remarques'); ?></label>
                            <textarea class="form-control" name="remarque" id="bulk-remarque"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('general.submit'); ?></button>
                    </form>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('script'); ?>
    <!-- DataTables  & Plugins -->
    <script src="<?php echo e(asset('assets/admin/plugins/moment/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>

    <script src="<?php echo e(asset('assets/admin/plugins/datatables-scroller/js/scroller.bootstrap4.min.js')); ?>"></script>

    <!-- Select2 -->
    <script src="<?php echo e(asset('assets/admin/plugins/select2/js/select2.min.js')); ?>"></script>
    <!-- date-range-picker -->
    <script src="<?php echo e(asset('assets/admin/plugins/daterangepicker/daterangepicker.js')); ?>"></script>

    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo e(asset('assets/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')); ?>">
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('custom'); ?>
    <script>
    $(document).ready(function() {
        // Initialisation de DataTable en mode serveur
        var data = $('#data').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(route('admin.reports.localites.data')); ?>",
                type: "POST",
                data: function(d) {
                    d._token = '<?php echo e(csrf_token()); ?>';
                    d.wilaya_id = $('#wilaya').val();
                    d.moughata_id = $('#moughata').val();
                    d.commune_id = $('#commune').val();
                    d.localite_id = $('#localite').val();
                    d.operation_id = $('#operation').val();
                    d.projet_id = $('#projet').val();
                    d.operateur_id = $('#operateur').val();
                    
                    // Dates
                    var fromTo = $('#from_to').val();
                    if (fromTo) {
                        var dates = fromTo.split(' - ');
                        d.from = dates[0];
                        d.to = dates[1];
                    }
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'wilaya', name: 'wilayas.intitule' },
                { data: 'moughata', name: 'moughatas.intitule' },
                { data: 'commune', name: 'communes.intitule' },
                { data: 'localite', name: 'localites.intitule' },
                { data: 'operation', name: 'operations.intitule' },
                { data: 'projet', name: 'projets.intitule_resume' },
                { data: 'operateur', name: 'operateurs.intitule' },
                { data: 'date_op', name: 'localite_operations.date_op' },
                { 
                    data: 'confirmed', 
                    name: 'confirmed',
                    render: function(data, type, row) {
                        if (data) {
                            return '<span class="badge badge-success"><?php echo app('translator')->get("general.valid"); ?></span>';
                        }
                        // Étape coordinateur d'ONG (avant la validation admin)
                        var st = row.statut_coordinateur;
                        var extra = st ? '<br><small class="badge badge-' + (st === 'valide' ? 'primary' : 'warning') + '">' +
                            (st === 'valide' ? 'Validé coordinateur' : 'Rejeté coordinateur') + '</small>' : '';
                        return '<span class="badge badge-danger"><?php echo app('translator')->get("general.invalid"); ?></span>' + extra;
                    }
                },
                { 
                    data: 'action', 
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[8, 'desc']], // Trier par date par d�faut
            createdRow: function(row, data, dataIndex) {
                // Ajouter une classe bas�e sur le statut
                if (data.confirmed == 1 || data.confirmed === true) {
                    $(row).addClass('table-success'); // Vert pour valid�
                } else {
                    $(row).addClass('table-danger'); // Rouge pour non valid�
                }
            },
            drawCallback: function() {
                $('#check-all').prop('checked', false);
                updateBulkButtons();
            }
        });

        function updateBulkButtons() {
            var count = $('#data tbody .row-checkbox:checked').length;
            $('#bulk-validate-btn, #bulk-reject-btn').prop('disabled', count === 0);
            $('#selected-count').text(count > 0 ? count + ' <?php echo app('translator')->get("general.rows_selected"); ?>' : '');
        }

        $('#check-all').on('change', function() {
            $('#data tbody .row-checkbox').prop('checked', this.checked);
            updateBulkButtons();
        });

        $('#data tbody').on('change', '.row-checkbox', function() {
            if (!this.checked) {
                $('#check-all').prop('checked', false);
            }
            updateBulkButtons();
        });

        function openBulkModal(status) {
            var ids = $('#data tbody .row-checkbox:checked').map(function() { return this.value; }).get();
            if (ids.length === 0) { return; }
            $('#bulk-status').val(status);
            $('#bulk-selected-count').text(ids.length);
            $('#bulk-modal-title').text(status == 1 ? '<?php echo app('translator')->get("general.validate_selection"); ?>' : '<?php echo app('translator')->get("general.reject_selection"); ?>');
            $('#bulk-remarque').val('');
            $('#bulkModal').modal('show');
        }

        $('#bulk-validate-btn').on('click', function() { openBulkModal(1); });
        $('#bulk-reject-btn').on('click', function() { openBulkModal(0); });

        $('#bulk-form').on('submit', function(e) {
            e.preventDefault();
            var ids = $('#data tbody .row-checkbox:checked').map(function() { return this.value; }).get();
            if (ids.length === 0) { $('#bulkModal').modal('hide'); return; }
            $.ajax({
                url: "<?php echo e(url('admin/reports/localites/bulk-update')); ?>",
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    ids: ids,
                    status: $('#bulk-status').val(),
                    remarque: $('#bulk-remarque').val()
                },
                dataType: 'json',
                success: function(res) {
                    $('#bulkModal').modal('hide');
                    data.ajax.reload(null, false);
                    if (res && res.message) { alert(res.message); }
                }
            });
        });

        // Per-column search inputs (footer), excluding the actions column
        $('#data tfoot th').each(function() {
            var title = $(this).text().trim();
            if (title === '#') {
                $(this).html('');
                return;
            }
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />');
        });

        data.columns().every(function() {
            var column = this;
            $('input', column.footer()).on('keyup change clear', function() {
                if (column.search() !== this.value) {
                    column.search(this.value).draw();
                }
            });
        });

        // Initialize Select2
        $('.wilaya, .moughata, .commune, .localite, .operation, .projet, .operateur').select2();

        // Disable all filters initially
        $('#moughata, #commune, #localite, #projet, #operation, #operateur, #from_to').prop('disabled', true);

        // Date range picker
        $('#from_to').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false
        });

        $('#from_to').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            data.ajax.reload();
        });

        $('#from_to').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            data.ajax.reload();
        });

        // Wilaya change
        $('#wilaya').on('change', function() {
            var idWilaya = this.value;
            
            if (idWilaya) {
                // Enable filters
                $('#moughata, #commune, #localite, #projet, #operation, #operateur, #from_to').prop('disabled', false);
                
                // Load moughatas
                $.ajax({
                    url: "<?php echo e(url('admin/api/fetch-moughatas')); ?>",
                    type: "POST",
                    data: {
                        wilaya_id: idWilaya,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#moughata').html('<option value="">-- Select Moughata --</option>');
                        $.each(result.moughatas, function(key, value) {
                            $("#moughata").append('<option value="' + value.id + '">' + value.intitule_fr + '</option>');
                        });
                        $('#moughata').trigger('change.select2');
                    }
                });

                // Load operations
                $.ajax({
                    url: "<?php echo e(url('admin/api/fetch-operations')); ?>",
                    type: "POST",
                    data: {
                        wilaya_id: idWilaya,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#operation').html('<option value="">-- Select Operation --</option>');
                        $.each(res.operations, function(key, value) {
                            $("#operation").append('<option value="' + value.id + '">' + value.intitule + '</option>');
                        });
                        $('#operation').trigger('change.select2');
                    }
                });

                // Load projets
                $.ajax({
                    url: "<?php echo e(url('admin/api/fetch-projets')); ?>",
                    type: "POST",
                    data: {
                        wilaya_id: idWilaya,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#projet').html('<option value="">-- Select Projet --</option>');
                        $.each(res.projets, function(key, value) {
                            $("#projet").append('<option value="' + value.id + '">' + value.intitule_resume + '</option>');
                        });
                        $('#projet').trigger('change.select2');
                    }
                });

                // Reload DataTable
                data.ajax.reload();
            } else {
                $('#moughata, #commune, #localite, #projet, #operation, #operateur, #from_to').prop('disabled', true);
                $('#moughata, #commune, #localite, #projet, #operation, #operateur').empty().trigger('change.select2');
                data.ajax.reload();
            }
        });

        // Moughata change
        $('#moughata').on('change', function() {
            var idWilaya = $('#wilaya').val();
            var idMoughata = this.value;

            if (idMoughata) {
                // Load communes
                $.ajax({
                    url: "<?php echo e(url('admin/api/fetch-communes')); ?>",
                    type: "POST",
                    data: {
                        moughata_id: idMoughata,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#commune').html('<option value="">-- Select Commune --</option>');
                        $.each(res.communes, function(key, value) {
                            $("#commune").append('<option value="' + value.id + '">' + value.intitule_fr + '</option>');
                        });
                        $('#commune').trigger('change.select2');
                    }
                });
            } else {
                $('#commune, #localite').empty().trigger('change.select2');
            }
            
            data.ajax.reload();
        });

        // Commune change
        $('#commune').on('change', function() {
            var idWilaya = $('#wilaya').val();
            var idMoughata = $('#moughata').val();
            var idCommune = this.value;

            if (idCommune) {
                // Load localites
                $.ajax({
                    url: "<?php echo e(url('admin/api/fetch-localites')); ?>",
                    type: "POST",
                    data: {
                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        commune_id: idCommune,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#localite').html('<option value="">-- Select Localite --</option>');
                        $.each(res.localites, function(key, value) {
                            $("#localite").append('<option value="' + value.id + '">' + value.intitule_fr + '</option>');
                        });
                        $('#localite').trigger('change.select2');
                    }
                });
            } else {
                $('#localite').empty().trigger('change.select2');
            }
            
            data.ajax.reload();
        });

        // Localite change
        $('#localite').on('change', function() {
            data.ajax.reload();
        });

        // Operation change
        $('#operation').on('change', function() {
            data.ajax.reload();
        });

        // Projet change
        $('#projet').on('change', function() {
            var idWilaya = $('#wilaya').val();
            var idProjet = this.value;

            if (idProjet) {
                // Load operateurs
                $.ajax({
                    url: "<?php echo e(url('admin/api/fetch-operateurs')); ?>",
                    type: "POST",
                    data: {
                        wilaya_id: idWilaya,
                        projet_id: idProjet,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#operateur').html('<option value="">-- Select Operateur --</option>');
                        $.each(res.operateurs, function(key, value) {
                            $("#operateur").append('<option value="' + value.id + '">' + value.intitule + '</option>');
                        });
                        $('#operateur').trigger('change.select2');
                    }
                });
            } else {
                $('#operateur').empty().trigger('change.select2');
            }
            
            data.ajax.reload();
        });

        // Operateur change
        $('#operateur').on('change', function() {
            data.ajax.reload();
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/reports/localites/index.blade.php ENDPATH**/ ?>