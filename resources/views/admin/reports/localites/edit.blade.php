@extends('layouts.admin')
@section('title')
    @lang('sidebar.validation') - @lang('sidebar.family')
@endsection
@section('contentheader')
    @lang('sidebar.validation') - @lang('sidebar.family')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.reports.localites') }}"> @lang('sidebar.validation') - @lang('sidebar.family') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.family')
@endsection
@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <style>
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #e9ecef;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            font-weight: 600;
            color: #495057;
        }
        .form-control:disabled, .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_title_center">
                        <i class="fas fa-edit"></i> @lang('general.edit_report') - {{ $report->localite }}
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ url('/admin/reports/localites/update') }}" method="post">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{ $report->id }}">

                        <!-- Informations générales (tous désactivés sauf date) -->
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i> @lang('general.general_info')
                                <span class="badge-info">@lang('general.read_only')</span>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="wilaya"><i class="fas fa-map-marker-alt"></i> @lang('validate.wilaya')</label>
                                        <input type="text" id="wilaya" class="form-control" value="{{ $report->wilaya }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="moughata">@lang('validate.moughata')</label>
                                        <input type="text" id="moughata" class="form-control" value="{{ $report->moughata }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="commune">@lang('validate.commune')</label>
                                        <input type="text" id="commune" class="form-control" value="{{ $report->commune }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="localite">@lang('validate.localite')</label>
                                        <input type="text" id="localite" class="form-control" value="{{ $report->localite }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="operation">@lang('validate.operation')</label>
                                        <input type="text" id="operation" class="form-control" value="{{ $report->operation }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="projet">@lang('validate.projet')</label>
                                        <input type="text" id="projet" class="form-control" value="{{ $report->projet }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="operateur">@lang('validate.operateur')</label>
                                        <input type="text" id="operateur" class="form-control" value="{{ $report->operateur }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_op"><i class="fas fa-calendar-alt"></i> @lang('validate.date_op')</label>
                                        <input type="date" id="date_op" name="date_op" class="form-control" value="{{ $report->date_op }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Population et ménages -->
                        @if(!is_null($report->nb_tot_pop) || !is_null($report->nbr_h_c) || !is_null($report->nbr_f_c) || !is_null($report->nbr_enfant_inf_5_ans) || !is_null($report->nb_menages_comptabilises))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-users"></i> @lang('general.population_households')
                            </div>
                            <div class="row">
                                @if(!is_null($report->nb_tot_pop))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.total_population')</label>
                                        <input type="number" class="form-control" value="{{ $report->nb_tot_pop }}" disabled>
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_h_c))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.men_count')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_h_c }}" disabled>
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_f_c))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.women_count')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_f_c }}" disabled>
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_enfant_inf_5_ans))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.children_under_5')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_enfant_inf_5_ans }}" disabled>
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nb_menages_comptabilises))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.households_counted')</label>
                                        <input type="number" class="form-control" value="{{ $report->nb_menages_comptabilises }}" disabled>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Approvisionnement en eau -->
                        @if(!is_null($report->approv_eau))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-water"></i> @lang('general.water_supply')
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('general.water_source')</label>
                                        <input type="text" class="form-control" value="{{ $report->approv_eau }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Latrines familiales -->
                        @if(!is_null($report->nb_tot_latr_fam) || !is_null($report->nb_latrine_amelior) || !is_null($report->nb_latrine_non_amelior))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-toilet"></i> @lang('general.family_latrines')
                            </div>
                            <div class="row">
                                @if(!is_null($report->nb_tot_latr_fam))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.total_family_latrines')</label>
                                        <input type="number" class="form-control" value="{{ $report->nb_tot_latr_fam }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nb_latrine_amelior))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.improved_latrines')</label>
                                        <input type="number" class="form-control" value="{{ $report->nb_latrine_amelior }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nb_latrine_non_amelior))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.unimproved_latrines')</label>
                                        <input type="number" class="form-control" value="{{ $report->nb_latrine_non_amelior }}" >
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Statut des latrines -->
                        @if(!is_null($report->nbr_menages_latr_amel_inadeq) || !is_null($report->nbr_menages_latr_amel_adeq) || !is_null($report->nbr_menages_latr_amel_vid_adeq_part) || !is_null($report->nbr_menages_latr_amel_inadeq_part))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-chart-pie"></i> @lang('general.latrine_status')
                            </div>
                            <div class="row">
                                @if(!is_null($report->nbr_menages_latr_amel_inadeq))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.hh_improved_inadequate')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_latr_amel_inadeq }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_latr_amel_adeq))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.hh_improved_adequate')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_latr_amel_adeq }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_latr_amel_vid_adeq_part))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.hh_improved_adequate_emptying')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_latr_amel_vid_adeq_part }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_latr_amel_inadeq_part))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.hh_improved_inadequate_partial')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_latr_amel_inadeq_part }}" >
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Types de latrines -->
                        @if(!is_null($report->nbr_menages_ltr_end_phm) || !is_null($report->nbr_menages_ltr_vsn))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-list"></i> @lang('general.latrine_types')
                            </div>
                            <div class="row">
                                @if(!is_null($report->nbr_menages_ltr_end_phm))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('general.hh_end_phm_latrines')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_ltr_end_phm }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_ltr_vsn))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('general.hh_vsn_latrines')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_ltr_vsn }}" >
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Statut DAL/DLM -->
                        @if(!is_null($report->nbr_menages_dal) || !is_null($report->nbr_menages_dlm) || !is_null($report->nbr_menages_dlm_complet) || !is_null($report->nbr_menages_dlm_incomplet) || !is_null($report->nbr_menages_sans_dlm))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-check-circle"></i> @lang('general.dal_dlm_status')
                            </div>
                            <div class="row">
                                @if(!is_null($report->nbr_menages_dal))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.hh_dal')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_dal }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_dlm))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.hh_dlm')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_dlm }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_dlm_complet))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.dlm_complete')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_dlm_complet }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_dlm_incomplet))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.dlm_incomplete')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_dlm_incomplet }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_menages_sans_dlm))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('general.without_dlm')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_menages_sans_dlm }}" >
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Réalisation des latrines -->
                        @if(!is_null($report->nbr_latrine_realise) || !is_null($report->nbr_latrine_autoconst) || !is_null($report->nbr_latrine_realise_macon) || !is_null($report->nbr_latrine_entraid) || !is_null($report->nbr_latrine_subv) || !is_null($report->nbr_latrine_autofin) || !is_null($report->nbr_latrine_en_constr))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-hammer"></i> @lang('general.latrine_construction')
                            </div>
                            <div class="row">
                                @if(!is_null($report->nbr_latrine_realise))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.latrines_built')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_latrine_realise }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_latrine_autoconst))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.self_built')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_latrine_autoconst }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_latrine_realise_macon))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.built_by_mason')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_latrine_realise_macon }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_latrine_entraid))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.community_built')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_latrine_entraid }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_latrine_subv))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.subsidized_latrines')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_latrine_subv }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_latrine_autofin))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.self_financed')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_latrine_autofin }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nbr_latrine_en_constr))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('general.under_construction')</label>
                                        <input type="number" class="form-control" value="{{ $report->nbr_latrine_en_constr }}" >
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Investissement ménages -->
                        @if(!is_null($report->mont_invest_men) || !is_null($report->nb_latr_invest_men_dispo))
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-coins"></i> @lang('general.household_investment')
                            </div>
                            <div class="row">
                                @if(!is_null($report->mont_invest_men))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('general.investment_amount') (MRU)</label>
                                        <input type="number" step="0.01" class="form-control" value="{{ number_format($report->mont_invest_men, 2) }}" >
                                    </div>
                                </div>
                                @endif
                                @if(!is_null($report->nb_latr_invest_men_dispo))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('general.available_latrines')</label>
                                        <input type="number" class="form-control" value="{{ $report->nb_latr_invest_men_dispo }}" >
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Validation et remarques -->
                        <div class="info-section">
                            <div class="section-title">
                                <i class="fas fa-check-circle"></i> @lang('general.validation')
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="remarques"><i class="fas fa-comment"></i> @lang('validate.remarques')</label>
                                        <textarea id="remarques" name="remarques" class="form-control" rows="4">{{ $report->remarque }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status"><i class="fas fa-flag"></i> @lang('validate.status')</label>
                                        <select id="status" name="status" class="form-control custom-select">
                                            <option value="1" {{ $report->confirmed ? 'selected' : '' }}>@lang('general.valid')</option>
                                            <option value="0" {{ !$report->confirmed ? 'selected' : '' }}>@lang('general.invalid')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> @lang('general.save')
                            </button>
                            <a href="{{ route('admin.reports.localites') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> @lang('general.cancel')
                            </a>
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
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();
        });
    </script>
@endpush