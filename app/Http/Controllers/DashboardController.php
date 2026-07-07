<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\InfrastructureReport;
use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\Moughata;
use App\Models\Wilaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $wilaya_reports = LocaliteReport::pluck('wilaya_id');
        $wilayas = Wilaya::whereIn('id', $wilaya_reports)->orderBy('intitule_fr')->get();

        return view('admin.dashboard', array_merge(
            ['wilayas' => $wilayas],
            $this->dashboardData()
        ));
    }

    public function fetch_general_wilaya(Request $request)
    {
        return response()->json($this->dashboardAjaxData($this->filtersFromRequest($request)));
    }

    private function dashboardData(array $filters = []): array
    {
        $latestEdlIds = $this->latestLocaliteReportIds(2, $filters);
        $latestSuiviIds = $this->latestLocaliteReportIds(4, $filters);
        $firstEdlIds = $this->firstLocaliteReportIds(2, $filters);
        $edlBeforeSuiviIds = $this->latestEdlBeforeLatestSuiviIds($filters);

        $nb_loc_cod_ANSADE = $this->scopedLocalites($filters)->whereNotNull('code_ansade')->count();
        $nb_loc_non_cod_ANSADE = $this->scopedLocalites($filters)->whereNull('code_ansade')->count();
        $nb_tot_loc = $nb_loc_cod_ANSADE + $nb_loc_non_cod_ANSADE;

        $pop1 = $this->scopedLocalites($filters)
            ->whereNotIn('id', LocaliteReport::whereIn('id', $latestEdlIds)->pluck('localite_id'))
            ->sum('nb_pop_ansade');
        $pop2 = LocaliteReport::whereIn('id', $latestEdlIds)->sum('nb_tot_pop');

        $nb_ecole_fond = $this->scopedInfrastructures($filters)
            ->where('infra_publ', 'LIKE', '%ecole')
            ->count();
        $nb_struct_sante = $this->scopedInfrastructures($filters)
            ->where('infra_publ', 'LIKE', '%sant%')
            ->count();

        $nb_loc_declench = $this->operationLocalitesCount(1, $filters);
        $nb_loc_declench_cod_ANSADE = $this->operationLocalitesCount(1, $filters, true);
        $nb_loc_declench_non_cod_ANSADE = $this->operationLocalitesCount(1, $filters, false);

        $nb_loc = $this->scopedLocaliteReports($filters)
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(
                DB::raw('SUM(CASE WHEN operations.code = 3 THEN 1 ELSE 0 END) as localites_3'),
                DB::raw('SUM(CASE WHEN operations.code = 1 THEN 1 ELSE 0 END) as localites_1'),
                DB::raw('YEAR(date_op) as year')
            )
            ->groupBy('year')
            ->whereIn('operations.code', [1, 3])
            ->whereNotNull('date_op')
            ->get();

        $access_hygiene = $this->zeroObject([
            'nbr_menages_dlm_complet',
            'nbr_menages_dlm_incomplet',
            'nbr_menages_sans_dlm',
            'nbr_menages_dlm',
        ], LocaliteReport::whereIn('localite_operations.id', $latestSuiviIds)
            ->select(
                DB::raw('SUM(localite_operations.nbr_menages_dlm) as nbr_menages_dlm'),
                DB::raw('SUM(localite_operations.nbr_menages_dlm_complet) as nbr_menages_dlm_complet'),
                DB::raw('SUM(localite_operations.nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet'),
                DB::raw('SUM(localite_operations.nbr_menages_sans_dlm) as nbr_menages_sans_dlm')
            )
            ->first());

        $base = $this->sumMaxLocaliteValue('nb_latrine_amelior', 4, $filters, 'nb_latrine_amelior');
        $non_ameliores = $this->sumMaxLocaliteValue('nb_tot_latr_fam - nb_latrine_amelior', 4, $filters, 'nb_latrine_amelior');
        $men_dal = $this->sumMaxLocaliteValue('nbr_menages_dal', 4, $filters);
        $limite = $this->sumMaxLocaliteValue(
            'nbr_menages_latr_amel_vid_adeq_part + nbr_menages_latr_amel_inadeq_part + nbr_menages_ltr_vsn',
            4,
            $filters
        );

        $securise = $this->scopedLocaliteReports($filters)
            ->select(DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise'))
            ->where('operation_id', 4)
            ->value('securise') ?? 0;

        $couvertures_EDL = LocaliteReport::whereIn('localite_operations.id', $edlBeforeSuiviIds)
            ->sum('localite_operations.nb_tot_latr_fam');
        $couvertures_dernier = LocaliteReport::whereIn('localite_operations.id', $latestSuiviIds)
            ->sum('localite_operations.nb_tot_latr_fam');
        $nb_nouv_latr_construit = LocaliteReport::whereIn('localite_operations.id', $latestSuiviIds)
            ->sum('localite_operations.nbr_latrine_realise');

        $invest_tot_men_latr = $this->scopedLocaliteReports($filters)
            ->where('operation_id', 4)
            ->sum('localite_operations.mont_invest_men');
        $dispo = $this->scopedLocaliteReports($filters)
            ->where('operation_id', 4)
            ->sum('localite_operations.nb_latr_invest_men_dispo');
        $investi_menages = $this->safeDivide($invest_tot_men_latr, $dispo);

        $ecoles = $this->infrastructureLatrineStats($filters, true);
        $santes = $this->infrastructureLatrineStats($filters, false);

        $nb_loc_certif_FDAL = $this->operationLocalitesCount(3, $filters);
        $nb_loc_redeclench_cod_ANSADE = $this->operationLocalitesCount(1, $filters, true, true);
        $nb_loc_redeclench_non_cod_ANSADE = $this->operationLocalitesCount(1, $filters, false, true);
        $nb_loc_benefic_appui_post_FDAL = $this->operationLocalitesCount(4, $filters, null, true);

        $nb_tot_men_cible_ATPC = LocaliteReport::whereIn('id', $latestEdlIds)->sum('nb_menages_comptabilises');
        $pop_tot_ciblee_ATPC = LocaliteReport::whereIn('id', $latestEdlIds)->sum('nb_tot_pop');

        $menages_EDL = LocaliteReport::whereIn('localite_operations.id', $firstEdlIds)->sum('localite_operations.nb_menages_comptabilises');
        $menages_dernier = LocaliteReport::whereIn('localite_operations.id', $edlBeforeSuiviIds)->sum('localite_operations.nb_menages_comptabilises');

        return [
            'nb_wilaya' => $this->wilayaCount($filters),
            'nb_moughataa' => $this->moughataCount($filters),
            'nb_commune' => $this->communeCount($filters),
            'nb_loc_cod_ANSADE' => $nb_loc_cod_ANSADE,
            'nb_loc_non_cod_ANSADE' => $nb_loc_non_cod_ANSADE,
            'nb_tot_loc' => $nb_tot_loc,
            'nb_pop_rgph' => $pop1 + $pop2,
            'nb_ecole_fond' => $nb_ecole_fond,
            'nb_eleves' => $this->scopedInfrastructureReports($filters)
                ->join('infrastructures', 'infrastructures.id', 'infrastructure_operations.infrastructure_id')
                ->sum('effectif'),
            'nb_struct_sante' => $nb_struct_sante,
            'nb_loc_declench' => $nb_loc_declench,
            'nb_loc_declench_cod_ANSADE' => $nb_loc_declench_cod_ANSADE,
            'nb_loc_declench_non_cod_ANSADE' => $nb_loc_declench_non_cod_ANSADE,
            'nb_loc' => $nb_loc,
            'access_hygiene' => $access_hygiene,
            'menages_EDL' => $menages_EDL,
            'menages_dernier' => $menages_dernier,
            'menages_EDL_max' => LocaliteReport::whereIn('localite_operations.id', $edlBeforeSuiviIds)->sum('localite_operations.nb_menages_comptabilises'),
            'base' => $base,
            'limite' => $limite,
            'non_ameliores' => $non_ameliores,
            'men_dal' => $men_dal,
            'securise' => $securise,
            'couvertures_EDL' => $couvertures_EDL,
            'couvertures_dernier' => $couvertures_dernier,
            'nouv_latr_const' => $couvertures_dernier - $couvertures_EDL,
            'nb_nouv_latr_construit' => $nb_nouv_latr_construit,
            'invest_tot_men_latr' => $invest_tot_men_latr,
            'dispo' => $dispo,
            'investi_menages' => $investi_menages,
            'nb_ecole_besoin_latr_satisf' => $ecoles->nb_ecole_besoin_latr_satisf,
            'nb_ecole_besoin_latr_non_satisf' => $ecoles->nb_ecole_besoin_latr_non_satisf,
            'nb_ecole_sans_latr' => $ecoles->nb_ecole_sans_latr,
            'nbr_cabine_a_const' => $ecoles->nbr_cabine_a_const,
            'nb_sante_besoin_latr_satisf' => $santes->nb_sante_besoin_latr_satisf,
            'nb_sante_besoin_latr_non_satisf' => $santes->nb_sante_besoin_latr_non_satisf,
            'nb_sante_sans_latr' => $santes->nb_sante_sans_latr,
            'sante_nbr_cabine_a_const' => $santes->nbr_cabine_a_const,
            'nb_loc_certif_FDAL' => $nb_loc_certif_FDAL,
            'nb_loc_redeclench_cod_ANSADE' => $nb_loc_redeclench_cod_ANSADE,
            'nb_loc_redeclench_non_cod_ANSADE' => $nb_loc_redeclench_non_cod_ANSADE,
            'nb_loc_benefic_appui_post_FDAL' => $nb_loc_benefic_appui_post_FDAL,
            'nb_tot_men_cible_ATPC' => $nb_tot_men_cible_ATPC,
            'pop_tot_ciblee_ATPC' => $pop_tot_ciblee_ATPC,
        ];
    }

    private function dashboardAjaxData(array $filters = []): array
    {
        $data = $this->dashboardData($filters);

        $data['nb_ecole_besoin_latr_satisf'] = (object) [
            'nb_ecole_besoin_latr_satisf' => $data['nb_ecole_besoin_latr_satisf'],
            'nb_ecole_besoin_latr_non_satisf' => $data['nb_ecole_besoin_latr_non_satisf'],
            'nb_ecole_sans_latr' => $data['nb_ecole_sans_latr'],
            'nbr_cabine_a_const' => $data['nbr_cabine_a_const'],
        ];
        $data['nb_sante_besoin_latr_satisf'] = (object) [
            'nb_sante_besoin_latr_satisf' => $data['nb_sante_besoin_latr_satisf'],
            'nb_sante_besoin_latr_non_satisf' => $data['nb_sante_besoin_latr_non_satisf'],
            'nb_sante_sans_latr' => $data['nb_sante_sans_latr'],
            'nbr_cabine_a_const' => $data['sante_nbr_cabine_a_const'],
        ];

        return $data;
    }

    private function filtersFromRequest(Request $request): array
    {
        return array_filter([
            'wilaya_id' => $request->wilaya_id !== 'all' ? $request->wilaya_id : null,
            'moughata_id' => $request->moughata_id,
            'commune_id' => $request->commune_id,
        ], function ($value) {
            return $value !== null && $value !== '';
        });
    }

    private function scopedLocalites(array $filters)
    {
        $query = Localite::query();
        return $this->applyScope($query, $filters);
    }

    private function scopedInfrastructures(array $filters)
    {
        $query = Infrastructure::query();
        return $this->applyScope($query, $filters);
    }

    private function scopedLocaliteReports(array $filters)
    {
        $query = LocaliteReport::query();
        return $this->applyScope($query, $filters, 'localite_operations');
    }

    private function scopedInfrastructureReports(array $filters)
    {
        $query = InfrastructureReport::query();
        return $this->applyScope($query, $filters, 'infrastructure_operations');
    }

    private function applyScope($query, array $filters, ?string $table = null)
    {
        foreach (['wilaya_id', 'moughata_id', 'commune_id'] as $column) {
            if (isset($filters[$column])) {
                $query->where($table ? "{$table}.{$column}" : $column, $filters[$column]);
            }
        }

        return $query;
    }

    private function latestLocaliteReportIds(int $operationId, array $filters)
    {
        return $this->scopedLocaliteReports($filters)
            ->where('operation_id', $operationId)
            ->selectRaw('MAX(id) as id')
            ->groupBy('localite_id')
            ->pluck('id');
    }

    private function firstLocaliteReportIds(int $operationId, array $filters)
    {
        return $this->scopedLocaliteReports($filters)
            ->where('operation_id', $operationId)
            ->selectRaw('MIN(id) as id')
            ->groupBy('localite_id')
            ->pluck('id');
    }

    private function latestEdlBeforeLatestSuiviIds(array $filters)
    {
        $latestSuivis = $this->scopedLocaliteReports($filters)
            ->where('operation_id', 4)
            ->whereNotNull('date_op')
            ->groupBy('localite_id')
            ->selectRaw('MAX(date_op) as max_date, localite_id')
            ->get();

        if ($latestSuivis->isEmpty()) {
            return collect();
        }

        $query = $this->scopedLocaliteReports($filters)
            ->where('operation_id', 2)
            ->whereNotNull('date_op')
            ->where(function ($query) use ($latestSuivis) {
                foreach ($latestSuivis as $suivi) {
                    $query->orWhere(function ($query) use ($suivi) {
                        $query->where('localite_id', $suivi->localite_id)
                            ->where('date_op', '<=', $suivi->max_date);
                    });
                }
            })
            ->groupBy('localite_id')
            ->selectRaw('MAX(localite_operations.id) as id');

        return $query->pluck('id');
    }

    private function sumMaxLocaliteValue(string $expression, int $operationId, array $filters, ?string $notNullColumn = null): float
    {
        $value = DB::query()
            ->fromSub(function ($query) use ($expression, $operationId, $filters, $notNullColumn) {
                $query->from('localite_operations')
                    ->selectRaw("MAX({$expression}) as max_value")
                    ->where('operation_id', $operationId)
                    ->groupBy('localite_id');

                foreach (['wilaya_id', 'moughata_id', 'commune_id'] as $column) {
                    if (isset($filters[$column])) {
                        $query->where($column, $filters[$column]);
                    }
                }

                if ($notNullColumn) {
                    $query->whereNotNull($notNullColumn);
                }
            }, 'values_by_localite')
            ->sum('max_value');

        return (float) ($value ?? 0);
    }

    private function operationLocalitesCount(int $operationCode, array $filters, ?bool $coded = null, bool $redeclenche = false): int
    {
        $query = $this->scopedLocaliteReports($filters)
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->where('operations.code', $operationCode)
            ->groupBy('localites.id');

        if ($coded === true) {
            $query->whereNotNull('localites.code_ansade');
        } elseif ($coded === false) {
            $query->whereNull('localites.code_ansade');
        }

        if ($redeclenche) {
            $query->havingRaw('COUNT(localites.id) > 1');
        }

        return $query->get()->count();
    }

    private function infrastructureLatrineStats(array $filters, bool $isEcole)
    {
        $prefix = $isEcole ? 'ecole' : 'sante';
        $query = $this->scopedInfrastructureReports($filters)
            ->join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->where('operations.code', 5)
            ->whereNotNull('nbr_bloc_a_const')
            ->whereNotNull('nbr_cabine_a_const');

        $isEcole
            ? $query->where('infrastructures.infra_publ', 'Ecole')
            : $query->where('infrastructures.infra_publ', '<>', 'Ecole');

        return $this->zeroObject([
            "nb_{$prefix}_besoin_latr_satisf",
            "nb_{$prefix}_besoin_latr_non_satisf",
            "nb_{$prefix}_sans_latr",
            'nbr_cabine_a_const',
        ], $query->select(
            DB::raw("SUM(CASE WHEN infrastructure_operations.nbr_bloc_a_const = 0 AND infrastructure_operations.nbr_cabine_a_const = 0 THEN 1 ELSE 0 END) as nb_{$prefix}_besoin_latr_satisf"),
            DB::raw("SUM(CASE WHEN infrastructure_operations.nbr_bloc_a_const = 0 AND infrastructure_operations.nbr_cabine_a_const = 0 THEN 0 ELSE 1 END) as nb_{$prefix}_besoin_latr_non_satisf"),
            DB::raw("SUM(CASE WHEN infrastructure_operations.nbr_cabine_exist = 0 THEN 1 ELSE 0 END) as nb_{$prefix}_sans_latr"),
            DB::raw('SUM(infrastructure_operations.nbr_cabine_a_const) as nbr_cabine_a_const')
        )->first());
    }

    private function zeroObject(array $fields, $row)
    {
        $object = new \stdClass();

        foreach ($fields as $field) {
            $object->{$field} = (float) ($row->{$field} ?? 0);
        }

        return $object;
    }

    private function wilayaCount(array $filters): int
    {
        return isset($filters['wilaya_id']) ? Wilaya::where('id', $filters['wilaya_id'])->count() : Wilaya::count();
    }

    private function moughataCount(array $filters): int
    {
        if (isset($filters['moughata_id'])) {
            return Moughata::where('id', $filters['moughata_id'])->count();
        }

        return $this->applyScope(Moughata::query(), $filters)->count();
    }

    private function communeCount(array $filters): int
    {
        if (isset($filters['commune_id'])) {
            return Commune::where('id', $filters['commune_id'])->count();
        }

        return $this->applyScope(Commune::query(), $filters)->count();
    }

    private function safeDivide($numerator, $denominator): float
    {
        $denominator = (float) ($denominator ?? 0);

        return $denominator == 0.0 ? 0.0 : (float) $numerator / $denominator;
    }

    public function fech_infrastructures()
    {
        $data = array();

        $data['acces_eau_loc'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
            ->groupBy('infrastructure_operations.approv_eau')
            ->where('operations.code', 5)
            ->get();
        $data['couvert_ecole_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructures.infra_publ')
            ->groupBy('infrastructures.infra_publ')
            ->where('operations.code', 5)
            ->where('nbr_cabine_fonct', '>', 0)
            ->get();

        $data['acces_asst_eleve'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->select(DB::raw('SUM(infrastructure_operations.effectif) as effectif'), 'infrastructures.infra_publ')
            ->groupBy('infrastructures.infra_publ')
            ->where('operations.code', 5)
            ->where('nbr_cabine_fonct', '>', 0)
            ->get();

        $data['acces_asst_garcon'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->select(DB::raw('SUM(infrastructure_operations.effectif_garcon) as effectif_garcon'), 'infrastructures.infra_publ')
            ->groupBy('infrastructures.infra_publ')
            ->where('operations.code', 5)
            ->where('nbr_cabine_fonct', '>', 0)
            ->get();

        $data['acces_asst_fille'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->select(DB::raw('SUM(infrastructure_operations.effectif_fille) as effectif_fille'), 'infrastructures.infra_publ')
            ->groupBy('infrastructures.infra_publ')
            ->where('operations.code', 5)
            ->where('nbr_cabine_fonct', '>', 0)
            ->get();

        $data['nb_estim_cab_latr_a_construire_ecole'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('SUM(infrastructure_operations.nbr_cabine_a_const) as nbr_cabine_a_const'), 'infrastructures.infra_publ')
            ->groupBy('infrastructures.infra_publ')
            ->get();
        $data['nb_ecole_cab_fille_distinct'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(DISTINCT infrastructures.id) as infrastructures'), 'infrastructures.infra_publ')
            ->groupBy('infrastructures.infra_publ')
            ->where('dist_cab_hom_fem', '<>', 0)
            ->get();
        $data['acces_ecole_serv_asst_limite'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(DISTINCT infrastructures.id) as infrastructures'), 'infrastructures.infra_publ')
            ->groupBy('infrastructures.infra_publ')
            ->where('dist_cab_hom_fem', 0)
            ->orWhere('nbr_cabine_fonct', '<', 'nbr_cabine_exist')
            ->get();
        return response()->json($data);
    }
}
