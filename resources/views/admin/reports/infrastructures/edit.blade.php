@extends('layouts.admin')
@section('title')
    @lang('sidebar.infrastructure') - @lang('sidebar.edit')
@endsection
@section('contentheader')
    @lang('sidebar.infrastructure') - @lang('sidebar.edit')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.reports.infrastructures') }}"> @lang('sidebar.infrastructure') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.edit')
@endsection
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_title_center">@lang('infrastructure.edit_report')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ url('/admin/reports/infrastructures/update') }}" method="post">
                        @csrf
                        
                        <input type="hidden" id="id" name="id" value="{{ $report->id }}">
                        <!-- Location Information (Read-only) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="wilaya">@lang('infrastructure.wilaya') </label>
                                    <input type="text" id="wilaya" class="form-control" value="{{ $report->wilaya->intitule_fr ?? $report->wilaya_id }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="moughata">@lang('infrastructure.moughata') </label>
                                    <input type="text" id="moughata" class="form-control" value="{{ $report->moughata->intitule_fr ?? $report->moughata_id }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commune">@lang('infrastructure.commune') </label>
                                    <input type="text" id="commune" class="form-control" value="{{ $report->commune->intitule_fr ?? $report->commune_id }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="localite">@lang('infrastructure.localite') </label>
                                    <input type="text" id="localite" class="form-control" value="{{ $report->localite->intitule_fr ?? $report->localite_id }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Project Information (Read-only) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="operation">@lang('infrastructure.operation') </label>
                                    <input type="text" id="operation" class="form-control" value="{{ $report->operation->intitule_fr ?? $report->operation_id }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="projet">@lang('infrastructure.projet') </label>
                                    <input type="text" id="projet" class="form-control" value="{{ $report->projet->intitule_fr ?? $report->projet_id }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="operateur">@lang('infrastructure.operateur') </label>
                                    <input type="text" id="operateur" class="form-control" value="{{ $report->operateur->intitule_fr ?? $report->operateur_id }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_op">@lang('infrastructure.date_op') </label>
                                    <input type="date" id="date_op" name="date_op" class="form-control" value="{{ $report->date_op ? $report->date_op->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Infrastructure Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="infrastructure_id">@lang('infrastructure.type') </label>
                                    <input type="text" id="infrastructure_id" class="form-control" value="{{ $report->infrastructure->intitule_fr ?? $report->infrastructure_id }}" readonly>
                                </div>
                            </div>
                            
                            @if($report->hasValue('effectif') || $report->hasValue('effectif_garcon') || $report->hasValue('effectif_fille'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effectif">@lang('infrastructure.effectif_total') </label>
                                    <input type="number" id="effectif" name="effectif" class="form-control" value="{{ $report->effectif }}" min="0">
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($report->hasValue('effectif_garcon') || $report->hasValue('effectif_fille'))
                        <div class="row">
                            @if($report->hasValue('effectif_garcon'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effectif_garcon">@lang('infrastructure.effectif_garcon') </label>
                                    <input type="number" id="effectif_garcon" name="effectif_garcon" class="form-control" value="{{ $report->effectif_garcon }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('effectif_fille'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effectif_fille">@lang('infrastructure.effectif_fille') </label>
                                    <input type="number" id="effectif_fille" name="effectif_fille" class="form-control" value="{{ $report->effectif_fille }}" min="0">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Water Supply Information -->
                        @if($report->hasValue('approv_eau') || $report->hasValue('dist_approv_eau'))
                        <div class="row">
                            @if($report->hasValue('approv_eau'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="approv_eau">@lang('infrastructure.approv_eau') </label>
                                    <select id="approv_eau" name="approv_eau" class="form-control custom-select">
                                        <option value="1" {{ $report->approv_eau == 1 ? 'selected' : '' }}>@lang('general.yes')</option>
                                        <option value="0" {{ $report->approv_eau == 0 ? 'selected' : '' }}>@lang('general.no')</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('dist_approv_eau'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dist_approv_eau">@lang('infrastructure.dist_approv_eau') </label>
                                    <input type="number" step="0.01" id="dist_approv_eau" name="dist_approv_eau" class="form-control" value="{{ $report->dist_approv_eau }}" min="0">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Latrines Information -->
                        @if($report->hasValue('nbr_bloc') || $report->hasValue('nb_dlm') || $report->hasValue('type_latr_publ'))
                        <div class="row">
                            @if($report->hasValue('nbr_bloc'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nbr_bloc">@lang('infrastructure.nbr_bloc') </label>
                                    <input type="number" id="nbr_bloc" name="nbr_bloc" class="form-control" value="{{ $report->nbr_bloc }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('nb_dlm'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nb_dlm">@lang('infrastructure.nb_dlm') </label>
                                    <input type="number" id="nb_dlm" name="nb_dlm" class="form-control" value="{{ $report->nb_dlm }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('type_latr_publ'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type_latr_publ">@lang('infrastructure.type_latr_publ') </label>
                                    <select id="type_latr_publ" name="type_latr_publ" class="form-control custom-select">
                                        <option value="">-- @lang('general.select') --</option>
                                        <option value="moderne" {{ $report->type_latr_publ == 'moderne' ? 'selected' : '' }}>@lang('infrastructure.moderne')</option>
                                        <option value="traditionnel" {{ $report->type_latr_publ == 'traditionnel' ? 'selected' : '' }}>@lang('infrastructure.traditionnel')</option>
                                        <option value="mixte" {{ $report->type_latr_publ == 'mixte' ? 'selected' : '' }}>@lang('infrastructure.mixte')</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($report->hasValue('nb_dlm_complet') || $report->hasValue('nb_dlm_incomplet'))
                        <div class="row">
                            @if($report->hasValue('nb_dlm_complet'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nb_dlm_complet">@lang('infrastructure.nb_dlm_complet') </label>
                                    <input type="number" id="nb_dlm_complet" name="nb_dlm_complet" class="form-control" value="{{ $report->nb_dlm_complet }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('nb_dlm_incomplet'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nb_dlm_incomplet">@lang('infrastructure.nb_dlm_incomplet') </label>
                                    <input type="number" id="nb_dlm_incomplet" name="nb_dlm_incomplet" class="form-control" value="{{ $report->nb_dlm_incomplet }}" min="0">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($report->hasValue('nb_sans_dlm'))
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nb_sans_dlm">@lang('infrastructure.nb_sans_dlm') </label>
                                    <input type="number" id="nb_sans_dlm" name="nb_sans_dlm" class="form-control" value="{{ $report->nb_sans_dlm }}" min="0">
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Cabins Information -->
                        @if($report->hasValue('nbr_cabine_exist') || $report->hasValue('nbr_cabine_fonct') || $report->hasValue('dist_cab_hom_fem'))
                        <div class="row">
                            @if($report->hasValue('nbr_cabine_exist'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nbr_cabine_exist">@lang('infrastructure.nbr_cabine_exist') </label>
                                    <input type="number" id="nbr_cabine_exist" name="nbr_cabine_exist" class="form-control" value="{{ $report->nbr_cabine_exist }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('nbr_cabine_fonct'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nbr_cabine_fonct">@lang('infrastructure.nbr_cabine_fonct') </label>
                                    <input type="number" id="nbr_cabine_fonct" name="nbr_cabine_fonct" class="form-control" value="{{ $report->nbr_cabine_fonct }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('dist_cab_hom_fem'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dist_cab_hom_fem">@lang('infrastructure.dist_cab_hom_fem') </label>
                                    <input type="text" id="dist_cab_hom_fem" name="dist_cab_hom_fem" class="form-control" value="{{ $report->dist_cab_hom_fem }}">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($report->hasValue('nbr_cabine_fem_hyg') || $report->hasValue('nbr_cabine_pmr') || $report->hasValue('nbr_cabine_a_rehab'))
                        <div class="row">
                            @if($report->hasValue('nbr_cabine_fem_hyg'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nbr_cabine_fem_hyg">@lang('infrastructure.nbr_cabine_fem_hyg') </label>
                                    <input type="number" id="nbr_cabine_fem_hyg" name="nbr_cabine_fem_hyg" class="form-control" value="{{ $report->nbr_cabine_fem_hyg }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('nbr_cabine_pmr'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nbr_cabine_pmr">@lang('infrastructure.nbr_cabine_pmr') </label>
                                    <input type="number" id="nbr_cabine_pmr" name="nbr_cabine_pmr" class="form-control" value="{{ $report->nbr_cabine_pmr }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('nbr_cabine_a_rehab'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nbr_cabine_a_rehab">@lang('infrastructure.nbr_cabine_a_rehab') </label>
                                    <input type="number" id="nbr_cabine_a_rehab" name="nbr_cabine_a_rehab" class="form-control" value="{{ $report->nbr_cabine_a_rehab }}" min="0">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Construction Needs -->
                        @if($report->hasValue('nbr_bloc_a_const') || $report->hasValue('nbr_cabine_a_const'))
                        <div class="row">
                            @if($report->hasValue('nbr_bloc_a_const'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nbr_bloc_a_const">@lang('infrastructure.nbr_bloc_a_const') </label>
                                    <input type="number" id="nbr_bloc_a_const" name="nbr_bloc_a_const" class="form-control" value="{{ $report->nbr_bloc_a_const }}" min="0">
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('nbr_cabine_a_const'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nbr_cabine_a_const">@lang('infrastructure.nbr_cabine_a_const') </label>
                                    <input type="number" id="nbr_cabine_a_const" name="nbr_cabine_a_const" class="form-control" value="{{ $report->nbr_cabine_a_const }}" min="0">
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Waste Management -->
                        @if($report->hasValue('gest_dechet') || $report->hasValue('gest_dechet_biomed'))
                        <div class="row">
                            @if($report->hasValue('gest_dechet'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gest_dechet">@lang('infrastructure.gest_dechet') </label>
                                    <select id="gest_dechet" name="gest_dechet" class="form-control custom-select">
                                        <option value="1" {{ $report->gest_dechet == 1 ? 'selected' : '' }}>@lang('general.yes')</option>
                                        <option value="0" {{ $report->gest_dechet == 0 ? 'selected' : '' }}>@lang('general.no')</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                            
                            @if($report->hasValue('gest_dechet_biomed'))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gest_dechet_biomed">@lang('infrastructure.gest_dechet_biomed') </label>
                                    <select id="gest_dechet_biomed" name="gest_dechet_biomed" class="form-control custom-select">
                                        <option value="1" {{ $report->gest_dechet_biomed == 1 ? 'selected' : '' }}>@lang('general.yes')</option>
                                        <option value="0" {{ $report->gest_dechet_biomed == 0 ? 'selected' : '' }}>@lang('general.no')</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Remarks -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="remarques">@lang('infrastructure.remarques') </label>
                                    <textarea id="remarques" name="remarques" class="form-control" rows="4">{{ $report->remarque }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Status (always show status) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">@lang('infrastructure.status')</label>
                                    <select id="status" name="status" class="form-control custom-select">
                                        <option value="1" {{ $report->status == 1 ? 'selected' : '' }}>@lang('general.ok')</option>
                                        <option value="0" {{ $report->status == 0 ? 'selected' : '' }}>@lang('general.no')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg">@lang('general.update')</button>
                            <a href="{{ route('admin.reports.infrastructures') }}" class="btn btn-secondary btn-lg">@lang('general.cancel')</a>
                        </div>

                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@push('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
@endpush

@push('custom')
    <script>
        $(document).ready(function() {
            // Initialize Select2 Elements
            $('.select2').select2();
            
            // Auto-calculate total effectif when garcon or fille changes
            $('#effectif_garcon, #effectif_fille').on('keyup change', function() {
                var garcon = parseInt($('#effectif_garcon').val()) || 0;
                var fille = parseInt($('#effectif_fille').val()) || 0;
                $('#effectif').val(garcon + fille);
            });
        });
    </script>
@endpush