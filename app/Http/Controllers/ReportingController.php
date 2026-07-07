<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\Projet;
use Illuminate\Database\Eloquent\Builder;

use App\Models\InfrastructureReport;
use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\Moughata;
use Illuminate\Http\Request;
use App\Models\Wilaya;
use Illuminate\Support\Facades\DB;


class ReportingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }



    public function index()
    {



        $maxs = LocaliteReport::where('operation_id', 2)->selectRaw('max(id) as id')->groupBy('localite_id')->get()->pluck('id');

        $ids = LocaliteReport::where('operation_id', 2)->selectRaw('DISTINCT localite_id')->orderBy('localite_id')->get()->pluck('localite_id');
        $pop1 = Localite::whereNotIn('id', $ids)->sum('nb_pop_ansade');
        $pop2 = LocaliteReport::whereIn('id', $maxs)->sum('nb_tot_pop');
        $report['donnees_generales']['population'] = $pop1 + $pop2;

        $report['donnees_generales']['nb_localites'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites'))

            ->value('nb_localites');


        $report['donnees_generales']['nb_localites_codifiees'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites_codifiees'))
            ->whereNotNull('localites.code_ansade')

            ->value('nb_localites_codifiees');

        $report['donnees_generales']['nb_localites_non_codifiees'] = Localite::select(DB::raw('COUNT(DISTINCT localites.id) as nb_localites_non_codifiees'))
            ->whereNull('localites.code_ansade')

            ->value('nb_localites_non_codifiees');

        $report['donnees_generales']['nb_communes'] = Commune::select(DB::raw('COUNT(communes.id) as nb_communes'))

            ->value('nb_communes');
        $report['donnees_generales']['nb_moughatas'] = Moughata::select(DB::raw('COUNT(moughatas.id) as nb_moughatas'))

            ->value('nb_moughatas');
        $report['donnees_generales']['nb_wilayas'] = Wilaya::select(DB::raw('COUNT(wilayas.id) as nb_wilayas'))
            ->value('nb_wilayas');
        // a verifier
        $report['donnees_generales']['nb_eleves'] = InfrastructureReport::select(DB::raw('SUM(infrastructure_operations.effectif) as nb_eleves'))

            ->value('nb_eleves');
        $report['donnees_generales']['nb_ecoles'] = Infrastructure::where('infra_publ', 'LIKE', '%ecole')->count();
        $report['donnees_generales']['nb_santes'] = Infrastructure::where('infra_publ', 'LIKE', '%santé')->count();


        $report['nb_loc_declench'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites'))
            ->groupBy('localite_id')
            ->where('operation_id', 1)

            ->get()->count();

        $result2 = LocaliteReport::where('operation_id', 2)
            ->whereNotNull('date_op')
            ->groupBy('localite_id')
            ->selectRaw('MIN(date_op) as min_date,localite_id,MIN(id) as min_id')
            ->get();

        $result1 = LocaliteReport::where('operation_id', 4)
            ->whereNotNull('date_op')
            ->groupBy('localite_id')
            ->selectRaw('MAX(date_op) as max_date,localite_id,max(id) as max_id')
            ->get();

        $result = LocaliteReport::join('localites', 'localite_operations.localite_id', '=', 'localites.id')

            ->where('operation_id', 2)
            ->where(function ($query) use ($result1) {
                foreach ($result1 as $res) {
                    $query->orWhere(function ($query) use ($res) {
                        $query->where('localite_id', $res->localite_id)
                            ->where('date_op', '<=', $res->max_date);
                    });
                }
            })
            ->whereNotNull('date_op')
            ->groupBy('localite_id')
            ->selectRaw('MAX(date_op) as max_date,MAX(localite_operations.id) as max_id,localites.intitule')
            ->get();

        $mins2 = $result2->pluck('min_id');



        $maxs2 = $result->pluck('max_id');

        $report['menages_EDL_max'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
        )
            ->whereIn('localite_operations.id', $maxs2)
            ->value('nb_menages_comptabilises');
        $report['charts']['nb_loc'] = LocaliteReport::join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(
                DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                DB::raw("YEAR(date_op) as year")
            )
            ->groupBy('year')
            ->whereIn('operations.code', [1, 3])
            ->whereNotNull('date_op')

            ->get();
        $report['charts']['access_hygiene'] = LocaliteReport::join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(
                DB::raw('
            SUM(localite_operations.nbr_menages_dlm) as nbr_menages_dlm,
            SUM(localite_operations.nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
            SUM(localite_operations.nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
            SUM(localite_operations.nbr_menages_sans_dlm) as nbr_menages_sans_dlm')
            )

            ->where('operations.code', 4)
            ->first();
        $report['charts']['base'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
            ->fromSub(function ($query) {
                $query->select(DB::raw('MAX(nb_latrine_amelior) as max_value'))
                    ->from('localite_operations')
                    ->where('confirmed', true)
                    ->where('operation_id', 4)
                    ->whereNotNull('nb_latrine_amelior')
                    ->groupBy('localite_id');
            }, 'subquery_alias')
            ->first()
            ->total_max_value;


        $report['charts']['non_ameliores'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
            ->fromSub(function ($query) {
                $query->select(DB::raw('MAX(nb_tot_latr_fam - nb_latrine_amelior) as max_value'))
                    ->from('localite_operations')
                    ->where('confirmed', true)
                    ->where('operation_id', 4)
                    ->whereNotNull('nb_latrine_amelior')
                    ->groupBy('localite_id');
            }, 'subquery_alias')
            ->first()
            ->total_max_value;
        $report['charts']['men_dal'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
            ->fromSub(function ($query) {
                $query->select(DB::raw('MAX(nbr_menages_dal) as max_value'))
                    ->from('localite_operations')
                    ->where('confirmed', true)
                    ->where('operation_id', 4)
                    ->groupBy('localite_id');
            }, 'subquery_alias')
            ->first()
            ->total_max_value;


        $report['charts']['securise'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
        )
            ->where('operation_id', 4)

            ->value('securise');
        $report['charts']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
            ->groupBy('localite_operations.approv_eau')
            ->where('operations.code', 2)
            ->whereNotNull('localite_operations.approv_eau')

            ->get();

        $report['access_assainisement_familial']['projets'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('projets.intitule_resume', 'operateurs.intitule', DB::raw('COUNT(DISTINCT localites.id) as localites'), DB::raw("YEAR(date_op) as year"))
            ->whereIn('operation_id', [2])
            ->whereNotNull('date_op')
            ->groupBy('projets.intitule_resume', 'operateurs.intitule', 'year')
            ->orderBy('year', 'ASC')
            ->get();



        $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(localites.id) as localites'), 'localites.id')
            ->groupBy('localites.id')
            ->where('operations.code', 1)
            ->whereNotNull('localites.code_ansade')

            ->get()->count();


        $report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(localites.id) as localites'))
            ->groupBy('localites.id')
            ->where('operations.code', 1)
            ->whereNull('localites.code_ansade')

            ->get()->count();

        $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(localites.id) as localites'))
            ->groupBy('localites.id')
            ->havingRaw("COUNT(localites.id) > 1")
            ->where('operations.code', 1)
            ->whereNotNull('localites.code_ansade')

            ->get()->count();


        $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(localites.id) as localites'))
            ->groupBy('localites.id')
            ->havingRaw("COUNT(localites.id) > 1")
            ->where('operations.code', 1)
            ->whereNull('localites.code_ansade')

            ->get()->count();

        $nb_loc_suivi_apres_certif = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->joinSub(
                DB::table('localite_operations as certif')
                    ->select('localite_id', DB::raw('MAX(date_op) as max_certif'))
                    ->where('operation_id', 3)
                    ->groupBy('localite_id'),
                'certif_max',
                'certif_max.localite_id',
                '=',
                'localites.id'
            )
            ->joinSub(
                DB::table('localite_operations as suivi')
                    ->select('localite_id', DB::raw('MAX(date_op) as max_suivi'))
                    ->where('operation_id', 2)
                    ->groupBy('localite_id'),
                'suivi_max',
                'suivi_max.localite_id',
                '=',
                'localites.id'
            )
            ->select(
                DB::raw('COUNT(localites.id) as localites'),
                'localites.id'
            )
            ->whereColumn('suivi_max.max_suivi', '>', 'certif_max.max_certif')
            ->groupBy('localites.id')
            ->get();
        $dernieresCertifications = DB::table('localite_operations')
            ->select('localite_id', DB::raw('MAX(date_op) as date_derniere_certification'))
            ->where('operation_id', 3) // Certification FDAL
            ->groupBy('localite_id');

        $results = DB::table('localite_operations as el')
            ->joinSub($dernieresCertifications, 'dc', function ($join) {
                $join->on('el.localite_id', '=', 'dc.localite_id');
            })
            ->selectRaw('COUNT(DISTINCT el.localite_id) as nb_localites')
            ->selectRaw('COALESCE(SUM(el.nb_tot_pop), 0) as nb_tot_pop')
            ->selectRaw('COALESCE(SUM(el.nb_menages_comptabilises), 0) as nb_menages_comptabilises')
            ->where('el.operation_id', 2) // État des lieux
            ->whereRaw('el.date_op > dc.date_derniere_certification')
            ->first();

        $report['access_assainisement_familial']['mise_atcp']['villages_appui_suivi'] = $results->nb_localites ?? 0;


        $maxIds = LocaliteReport::select('localite_id', DB::raw('MAX(id) as max_id'))

            ->where('operation_id', 2)
            ->groupBy('localite_id')
            ->havingRaw('COUNT(localite_id) >= 1')
            ->pluck('max_id');
        $report['access_assainisement_familial']['pm']['population'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_pop) as nb_tot_pop'))
            ->whereIn('id', $maxIds)
            ->value('nb_tot_pop');


        $report['access_assainisement_familial']['pm']['menages'] = LocaliteReport::selectRaw('SUM(nb_menages_comptabilises) as menages')
            ->whereIn('id', $maxIds)
            ->value('menages');

        $report['access_assainisement_familial']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
            ->groupBy('localite_operations.approv_eau')
            ->where('operations.code', 2)
            ->whereNotNull('localite_operations.approv_eau')

            ->get();
        $report['access_assainisement_familial']['fdal'] = LocaliteReport::join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(
                DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                DB::raw("YEAR(date_op) as year")
            )
            ->groupBy('year')
            ->whereIn('operations.code', [1, 3])

            ->get();




        $minIds = LocaliteReport::select('localite_id', DB::raw('min(id) as min_id'))
            ->where('operation_id', 2)
            ->groupBy('localite_id')
            ->havingRaw('COUNT(localite_id) >= 1')

            ->pluck('min_id');
        $maxIds = LocaliteReport::select('localite_id', DB::raw('max(id) as max_id'))
            ->where('operation_id', 4)
            ->groupBy('localite_id')
            ->havingRaw('COUNT(localite_id) >= 1')

            ->pluck('max_id');
        $report['access_assainisement_familial']['menages_EDL1'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
        )
            ->whereIn('localite_operations.id', $mins2)
            ->value('nb_menages_comptabilises');
        $report['access_assainisement_familial']['couvertures_EDL'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
            ->whereIn('localite_operations.id', $minIds)
            ->value('couvertures');
        $report['access_assainisement_familial']['nb_localites_couvertures_EDL'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_EDL'))
            ->whereIn('localite_operations.id', $minIds)
            ->whereNotNull('nb_tot_latr_fam')
            ->value('nb_localites_couvertures_EDL');

        $report['access_assainisement_familial']['couvertures_dernier'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
            ->whereIn('localite_operations.id', $maxIds)
            ->value('couvertures');

        $report['access_assainisement_familial']['nb_localites_couvertures_dernier'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_dernier'))
            ->whereIn('localite_operations.id', $maxIds)
            ->whereNotNull('nb_tot_latr_fam')
            ->value('nb_localites_couvertures_dernier');
        $report['access_assainisement_familial']['nouv_latr_const'] = $report['access_assainisement_familial']['couvertures_dernier'] - $report['access_assainisement_familial']['couvertures_EDL'];
        $report['access_assainisement_familial']['nb_localites_nouv_latr_const'] = max($report['access_assainisement_familial']['nb_localites_couvertures_dernier'], $report['access_assainisement_familial']['nb_localites_couvertures_EDL']);
        $report['access_assainisement_familial']['nbr_latrine_autoconst'] = LocaliteReport::select(
            DB::raw('SUM(nbr_latrine_autoconst) as nbr_latrine_autoconst')
        )
            ->where('operation_id', 4)

            ->value('nbr_latrine_autoconst');
        $report['access_assainisement_familial']['nbr_localites_latrine_autoconst'] = LocaliteReport::select(
            DB::raw('COUNT(localite_id) as nbr_localites_latrine_autoconst')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_latrine_autoconst')

            ->value('nbr_localites_latrine_autoconst');
        $report['access_assainisement_familial']['nbr_latrine_realise_macon'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nbr_latrine_realise_macon) as nbr_latrine_realise_macon')
        )
            ->where('operation_id', 4)

            ->value('nbr_latrine_realise_macon');

        $report['access_assainisement_familial']['nbr_localites_latrine_realise_macon'] = LocaliteReport::select(
            DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_realise_macon')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_latrine_realise_macon')

            ->value('nbr_localites_latrine_realise_macon');
        $report['access_assainisement_familial']['nbr_latrine_en_constr'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nbr_latrine_en_constr) as nbr_latrine_en_constr')
        )
            ->where('operation_id', 4)

            ->value('nbr_latrine_en_constr');
        $report['access_assainisement_familial']['nbr_localites_latrine_en_constr'] = LocaliteReport::select(
            DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_en_constr')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_latrine_en_constr')

            ->value('nbr_localites_latrine_en_constr');
        $report['access_assainisement_familial']['nbr_menages_ltr_end_phm'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nbr_menages_ltr_end_phm) as nbr_menages_ltr_end_phm')
        )
            ->where('operation_id', 4)

            ->value('nbr_menages_ltr_end_phm');
        $report['access_assainisement_familial']['nbr_localites_menages_ltr_end_phm'] = LocaliteReport::select(
            DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_menages_ltr_end_phm')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_menages_ltr_end_phm')

            ->value('nbr_localites_menages_ltr_end_phm');


        $report['access_assainisement_familial']['nbr_menages_dlm'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nbr_menages_dlm) as nbr_dlm'),
            DB::raw('SUM(localite_operations.nb_tot_latr_fam) as nb_tot_latr_fam'),
            DB::raw('COUNT(DISTINCT localite_operations.localite_id) as nbr_localites')
        )
            ->whereIn('id', $maxIds)
            ->first();



        // Calcul de la somme des latrines réalisées
        $totalLatrinesRealisees = LocaliteReport::where('operation_id', 4)->sum('nbr_latrine_realise');

        // Calcul de la somme des latrines auto-financées
        $totalLatrinesAutofin = LocaliteReport::where('operation_id', 4)->sum('nbr_latrine_autofin');

        // Calcul du taux
        $report['access_assainisement_familial']['auto_finance'] = ($totalLatrinesRealisees > 0)
            ? ($totalLatrinesAutofin * 100 / $totalLatrinesRealisees)
            : 0;

        // Comptage des localités avec latrines auto-financées
        $report['access_assainisement_familial']['localites_latrine_autofin'] = LocaliteReport::where('operation_id', 4)
            ->where('nbr_latrine_autofin', '>', 0)
            ->distinct('localite_id')
            ->count('localite_id');
        $report['access_assainisement_familial']['nb_menages_inv'] = LocaliteReport::select(

            DB::raw('SUM( localite_operations.nb_latr_invest_men_dispo) as nb_latr_invest_men_dispo')
        )
            ->where('operation_id', 4)

            ->value('nb_latr_invest_men_dispo');
        $report['access_assainisement_familial']['localites_latr_invest_men_dispo'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_latr_invest_men_dispo'))
            ->where('operation_id', 4)
            ->whereNotNull('nb_latr_invest_men_dispo')

            ->value('localites_latr_invest_men_dispo');
        $report['access_assainisement_familial']['investi'] = LocaliteReport::select(

            DB::raw('SUM( localite_operations.mont_invest_men) as mont_invest_men')
        )
            ->where('operation_id', 4)

            ->value('mont_invest_men');

        $report['access_assainisement_familial']['investi_menages'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.mont_invest_men) / NULLIF(SUM(localite_operations.nb_latr_invest_men_dispo), 0), 0) as invest_moy_men_latr'))
            ->where('operation_id', 4)
            ->where('mont_invest_men', '>', 0)

            ->value('invest_moy_men_latr');
        $report['access_assainisement_familial']['localites_mont_invest_men'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_mont_invest_men'))
            ->where('operation_id', 4)
            ->whereNotNull('mont_invest_men')

            ->value('localites_mont_invest_men');


        $report['access_assainisement_familial']['latrines_entrainde'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_entraid) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_entrainde'))
            ->where('operation_id', 4)

            ->value('latrines_entrainde');
        $report['access_assainisement_familial']['localites_entrainde'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_entrainde'))
            ->where('operation_id', 4)
            ->whereNotNull('nbr_latrine_entraid')

            ->value('localites_entrainde');
        $report['access_assainisement_familial']['latrines_subv'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_subv) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_subv'))
            ->where('operation_id', 4)

            ->value('latrines_subv');
        $report['access_assainisement_familial']['localites_subv'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_subv'))
            ->where('operation_id', 4)
            ->whereNotNull('nbr_latrine_subv')

            ->value('localites_subv');

        $report['access_assainisement_familial']['menages'] = LocaliteReport::where('operation_id', 2)
            ->sum('nb_menages_comptabilises');

        $ameliores = DB::table('localite_operations as lo')
            ->select(
                DB::raw('SUM(nb_latrine_amelior) as total_ameliores'),
                DB::raw('COUNT(DISTINCT localite_id) as nb_localites')
            )
            ->whereIn('lo.operation_id', [2, 4])
            ->whereNotNull('lo.nb_latrine_amelior')
            ->whereIn('lo.id', function ($query) {
                $query->select('id')
                    ->from(DB::raw('(SELECT id, ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                                   FROM localite_operations 
                                   WHERE operation_id IN (2, 4)
                                   AND nb_latrine_amelior IS NOT NULL) as ranked'))
                    ->where('rn', 1);
            })
            ->first();
        $report['access_assainisement_familial']['base'] = $ameliores->total_ameliores ?? 0;


        $report['access_assainisement_familial']['nb_localite_base'] = $ameliores->nb_localites ?? 0;

        $nonAmeliores = DB::table('localite_operations as lo')
            ->select(
                DB::raw('SUM(nb_tot_latr_fam - nb_latrine_amelior) as total_non_ameliores'),
                DB::raw('COUNT(DISTINCT localite_id) as nb_localites')
            )
            ->whereIn('lo.operation_id', [2, 4])
            ->whereNotNull('lo.nb_latrine_amelior')
            ->whereIn('lo.id', function ($query) {
                $query->select('id')
                    ->from(DB::raw('(SELECT id, ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                           FROM localite_operations 
                           WHERE operation_id IN (2, 4)
                           
                           AND nb_latrine_amelior IS NOT NULL) as ranked'))
                    ->where('rn', 1);
            })
            ->first();

        $report['access_assainisement_familial']['non_ameliores'] = $nonAmeliores->total_non_ameliores ?? 0;
        $report['access_assainisement_familial']['nb_localite_non_ameliores'] = $nonAmeliores->nb_localites ?? 0;

        $menagesDal = DB::query()
            ->from(DB::raw('(
        SELECT 
            localite_id,
            nbr_menages_dal,
            ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
        FROM localite_operations
        WHERE operation_id IN (2, 4)
          
          AND nbr_menages_dal IS NOT NULL
    ) as ranked'))
            ->selectRaw('SUM(nbr_menages_dal) as total_menages_dal')
            ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites_menages_dal')
            ->where('rn', 1)
            ->first();
        $report['access_assainisement_familial']['men_dal'] = $menagesDal->total_menages_dal ?? 0;

        $report['access_assainisement_familial']['nb_localite_men_dal'] = $menagesDal->nb_localites_menages_dal ?? 0;
        $report['access_assainisement_familial']['securise'] = LocaliteReport::select(
            DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
        )
            ->where('operation_id', 4)

            ->value('securise');
        $report['access_assainisement_familial']['nb_localite_securise'] = LocaliteReport::select(
            DB::raw('count(id) as nb_localite_securise')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_menages_latr_amel_adeq')

            ->value('nb_localite_securise');

        //$report['access_assainisement_familial']['limite'] = (100 - $report['access_assainisement_familial']['base'] - $report['access_assainisement_familial']['men_dal'] - $report['access_assainisement_familial']['non_ameliores']);
        //$report['access_assainisement_familial']['limite'] = $report['access_assainisement_familial']['limite'] > 0 ? $report['access_assainisement_familial']['limite'] : 0;
        $report['access_hygiene']['nbr_localites'] = LocaliteReport::select(
            DB::raw('count(id) as nbr_localites')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_menages_dlm')

            ->value('nbr_localites');
        $report['access_hygiene']['nbr_localites_complet'] = LocaliteReport::select(
            DB::raw('count(id) as nbr_localites_complet')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_menages_dlm_complet')

            ->value('nbr_localites_complet');
        $report['access_hygiene']['nbr_localites_incomplet'] = LocaliteReport::select(
            DB::raw('count(id) as nbr_localites_incomplet')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_menages_dlm_incomplet')

            ->value('nbr_localites_complet');
        $report['access_hygiene']['nbr_sans_dlm'] = LocaliteReport::select(
            DB::raw('count(id) as nbr_sans_dlm')
        )
            ->where('operation_id', 4)
            ->whereNotNull('nbr_menages_sans_dlm')

            ->value('nbr_sans_dlm');



        $report['access_hygiene'] = LocaliteReport::select(DB::raw('
            SUM(nbr_menages_dlm) as nbr_menages_dlm,
            SUM(nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
            SUM(nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
            SUM(nbr_menages_sans_dlm) as nbr_menages_sans_dlm'))
            ->where('operation_id', 4)


            ->first();

        $report['ecoles']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')

            ->where('infrastructures.infra_publ', 'Ecole')->get()->count();

        $report['ecoles']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
            ->groupBy('infrastructure_operations.approv_eau')
            ->where('operations.code', 5)

            ->whereNotNull('infrastructure_operations.approv_eau')
            ->where('infrastructures.infra_publ', 'Ecole')
            ->get();

        $report['ecoles']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as couvert_ecole_latr '),
                DB::raw('SUM(effectif) as effectif '),
                DB::raw('SUM(nbr_cabine_fonct) as nbr_cabine_fonct_sum '),

            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_fonct', '>', 0)
            ->whereNotNull('nbr_cabine_fonct')->first();
        $report['ecoles']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_pmr', '>', 0)
            ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
        $report['ecoles']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->where('dist_cab_hom_fem', '>', 0)
            ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
        $report['ecoles']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as gest_dechet ')

            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->where('gest_dechet', '>', 0)
            ->whereNotNull('gest_dechet')->value('gest_dechet');
        $report['ecoles']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->where('nb_dlm_instal', '>', 0)
            ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');
        $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_satisf '),
            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_a_const', 0)
            ->where('nbr_bloc_a_const', 0)
            ->whereNotNull('nbr_bloc_a_const')
            ->whereNotNull('nbr_cabine_a_const')
            ->value('nb_ecole_besoin_latr_satisf');
        $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_non_satisf '),
            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)
            ->where(function ($q) {
                $q->where('nbr_cabine_a_const', '>', 0)
                  ->orWhere('nbr_bloc_a_const', '>', 0);
            })
            ->where(function ($q) {
                $q->whereNotNull('nbr_cabine_a_const')
                  ->orWhereNotNull('nbr_bloc_a_const');
            })
            ->value('nb_ecole_besoin_latr_non_satisf');
        $report['ecoles']['besoin_assainissement']['nb_ecole_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_ecole_sans_latr '),
            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_exist', 0)
            ->whereNotNull('nbr_cabine_exist')
            ->value('nb_ecole_sans_latr');
        $report['ecoles']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
            )
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('operation_id', 5)

            ->whereNotNull('nbr_cabine_a_const')
            ->value('nbr_cabine_a_const');



        $report['ecoles']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->where('nbr_bloc_a_const', '>', 0)
            ->where('nbr_cabine_a_const', '>', 0)
            ->value('projet_besoins');
        $report['ecoles']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->whereNull('nbr_bloc_a_const')
            ->whereNull('nbr_cabine_a_const')

            ->value('projet_sans_besoins');

        $report['ecoles']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
            ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
            ->where('operation_id', 6)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->get();

        $report['ecoles']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
            ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->get();
        $report['ecoles']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('
                SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->first();
        $report['ecoles']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->whereNotNull('nb_dlm_complet')
            ->value('nbr_inf_complet');
        $report['ecoles']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->whereNotNull('nb_dlm_incomplet')
            ->value('nbr_inf_incomplet');
        $report['ecoles']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', 'Ecole')

            ->whereNotNull('nb_sans_dlm')
            ->value('nbr_inf_sans');

        $report['santes']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')

            ->where('infrastructures.infra_publ', '<>', 'Ecole')->get()->count();
        $report['santes']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
            ->groupBy('infrastructure_operations.approv_eau')
            ->where('operations.code', 5)

            ->whereNotNull('infrastructure_operations.approv_eau')
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->get();

        $report['santes']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as couvert_sante_latr '),
                DB::raw('SUM(effectif) as effectif '),

            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_fonct', '>', 0)
            ->whereNotNull('nbr_cabine_fonct')->first();
        $report['santes']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_pmr', '>', 0)
            ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
        $report['santes']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->where('dist_cab_hom_fem', '>', 0)
            ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
        $report['santes']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as gest_dechet ')

            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->where('gest_dechet', '>', 0)
            ->whereNotNull('gest_dechet')->value('gest_dechet');
        $report['santes']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->where('nb_dlm_instal', '>', 0)
            ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');

        $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_satisf '),
            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_a_const', 0)
            ->where('nbr_bloc_a_const', 0)
            ->whereNotNull('nbr_bloc_a_const')
            ->whereNotNull('nbr_cabine_a_const')
            ->value('nb_sante_besoin_latr_satisf');
        $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_non_satisf '),
            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)
            ->where(function ($q) {
                $q->where('nbr_cabine_a_const', '>', 0)
                  ->orWhere('nbr_bloc_a_const', '>', 0);
            })
            ->where(function ($q) {
                $q->whereNotNull('nbr_cabine_a_const')
                  ->orWhereNotNull('nbr_bloc_a_const');
            })
            ->value('nb_sante_besoin_latr_non_satisf');
        $report['santes']['besoin_assainissement']['nb_sante_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('COUNT(infrastructure_id) as nb_sante_sans_latr '),
            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->where('nbr_cabine_exist', 0)
            ->whereNotNull('nbr_cabine_exist')
            ->value('nb_sante_sans_latr');
        $report['santes']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
            )
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('operation_id', 5)

            ->whereNotNull('nbr_cabine_a_const')
            ->value('nbr_cabine_a_const');



        $report['santes']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')

            ->where('nbr_bloc_a_const', '>', 0)
            ->where('nbr_cabine_a_const', '>', 0)
            ->value('projet_besoins');
        $report['santes']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')

            ->whereNull('nbr_bloc_a_const')
            ->whereNull('nbr_cabine_a_const')

            ->value('projet_sans_besoins');

        $report['santes']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
            ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
            ->where('operation_id', 6)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')

            ->get();

        $report['santes']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
            ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')

            ->get();
        $report['santes']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('
                SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->first();

        $report['santes']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')

            ->whereNotNull('nb_dlm_complet')
            ->value('nbr_inf_complet');
        $report['santes']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')

            ->whereNotNull('nb_dlm_incomplet')
            ->value('nbr_inf_incomplet');
        $report['santes']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')

            ->whereNotNull('nb_sans_dlm')
            ->value('nbr_inf_sans');
        $localite_reports = LocaliteReport::pluck('wilaya_id');
        $wilayas = Wilaya::whereIn('id', $localite_reports)->orderBy('intitule_fr')->get();
        return view('admin.reportings.index', compact('wilayas', 'report'));
    }


    function fetch_reporting_wilaya(Request $request)
    {

        $report = array();
        if (isset($request->wilaya_id)) {
            $wilaya_id = $request->wilaya_id;
            $report['donnees_generales']['wilaya'] = Wilaya::where('id', $wilaya_id)
                ->value('intitule');

            $maxs = LocaliteReport::where('wilaya_id', $wilaya_id)->where('operation_id', 2)->selectRaw('max(id) as id')->groupBy('localite_id')->get()->pluck('id');

            $ids = LocaliteReport::where('wilaya_id', $wilaya_id)->where('operation_id', 2)->selectRaw('DISTINCT localite_id')->orderBy('localite_id')->get()->pluck('localite_id');
            $pop1 = Localite::whereNotIn('id', $ids)->where('wilaya_id', $wilaya_id)->sum('nb_pop_ansade');
            $pop2 = LocaliteReport::whereIn('id', $maxs)->sum('nb_tot_pop');
            $report['donnees_generales']['population'] = $pop1 + $pop2;

            $report['donnees_generales']['nb_localites'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites'))
                ->where('localites.wilaya_id', $wilaya_id)
                ->value('nb_localites');


            $report['donnees_generales']['nb_localites_codifiees'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites_codifiees'))
                ->whereNotNull('localites.code_ansade')
                ->where('localites.wilaya_id', $wilaya_id)
                ->value('nb_localites_codifiees');

            $report['donnees_generales']['nb_localites_non_codifiees'] = Localite::select(DB::raw('COUNT(DISTINCT localites.id) as nb_localites_non_codifiees'))
                ->whereNull('localites.code_ansade')
                ->where('localites.wilaya_id', $wilaya_id)
                ->value('nb_localites_non_codifiees');

            $report['donnees_generales']['nb_communes'] = Commune::select(DB::raw('COUNT(communes.id) as nb_communes'))
                ->where('communes.wilaya_id', $wilaya_id)
                ->value('nb_communes');
            $report['donnees_generales']['nb_moughatas'] = Moughata::select(DB::raw('COUNT(moughatas.id) as nb_moughatas'))
                ->where('moughatas.wilaya_id', $wilaya_id)
                ->value('nb_moughatas');
            $report['donnees_generales']['nb_wilayas'] = Wilaya::select(DB::raw('COUNT(wilayas.id) as nb_wilayas'))
                ->where('wilayas.id', $wilaya_id)->value('nb_wilayas');
            // a verifier
            $report['donnees_generales']['nb_eleves'] = InfrastructureReport::select(DB::raw('SUM(infrastructure_operations.effectif) as nb_eleves'))
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->value('nb_eleves');
            $report['donnees_generales']['nb_ecoles'] = Infrastructure::where('infra_publ', 'LIKE', '%ecole')->where('infrastructures.wilaya_id', $wilaya_id)->count();
            $report['donnees_generales']['nb_santes'] = Infrastructure::where('infra_publ', 'LIKE', '%santé')->where('infrastructures.wilaya_id', $wilaya_id)->count();


            $report['nb_loc_declench'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites'))
                ->groupBy('localite_id')
                ->where('operation_id', 1)
                ->where('wilaya_id', $wilaya_id)
                ->get()->count();

            $result2 = LocaliteReport::where('localite_operations.wilaya_id', $wilaya_id)
                ->where('operation_id', 2)
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MIN(date_op) as min_date,localite_id,MIN(id) as min_id')
                ->get();

            $result1 = LocaliteReport::where('localite_operations.wilaya_id', $wilaya_id)
                ->where('operation_id', 4)
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MAX(date_op) as max_date,localite_id,max(id) as max_id')
                ->get();

            $result = LocaliteReport::join('localites', 'localite_operations.localite_id', '=', 'localites.id')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->where('operation_id', 2)
                ->where(function ($query) use ($result1) {
                    foreach ($result1 as $res) {
                        $query->orWhere(function ($query) use ($res) {
                            $query->where('localite_id', $res->localite_id)
                                ->where('date_op', '<=', $res->max_date);
                        });
                    }
                })
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MAX(date_op) as max_date,MAX(localite_operations.id) as max_id,localites.intitule')
                ->get();

            $mins2 = $result2->pluck('min_id');



            $maxs2 = $result->pluck('max_id');

            $report['menages_EDL_max'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
            )
                ->whereIn('localite_operations.id', $maxs2)
                ->value('nb_menages_comptabilises');
            $report['charts']['nb_loc'] = LocaliteReport::join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                    DB::raw("YEAR(date_op) as year")
                )
                ->groupBy('year')
                ->whereIn('operations.code', [1, 3])
                ->whereNotNull('date_op')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get();
            $report['charts']['access_hygiene'] = LocaliteReport::join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('
                SUM(localite_operations.nbr_menages_dlm) as nbr_menages_dlm,
                SUM(localite_operations.nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
                SUM(localite_operations.nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
                SUM(localite_operations.nbr_menages_sans_dlm) as nbr_menages_sans_dlm')
                )
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->where('operations.code', 4)
                ->first();
            $report['charts']['base'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($wilaya_id) {
                    $query->select(DB::raw('MAX(nb_latrine_amelior) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('wilaya_id', $wilaya_id)
                        ->where('operation_id', 4)
                        ->whereNotNull('nb_latrine_amelior')
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;


            $report['charts']['non_ameliores'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($wilaya_id) {
                    $query->select(DB::raw('MAX(nb_tot_latr_fam - nb_latrine_amelior) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('wilaya_id', $wilaya_id)
                        ->where('operation_id', 4)
                        ->whereNotNull('nb_latrine_amelior')
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;
            $report['charts']['men_dal'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($wilaya_id) {
                    $query->select(DB::raw('MAX(nbr_menages_dal) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('wilaya_id', $wilaya_id)
                        ->where('operation_id', 4)
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;


            $report['charts']['securise'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
            )
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('securise');
            $report['charts']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
                ->groupBy('localite_operations.approv_eau')
                ->where('operations.code', 2)
                ->whereNotNull('localite_operations.approv_eau')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get();

            $report['access_assainisement_familial']['projets'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
                ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->select('projets.intitule_resume', 'operateurs.intitule', DB::raw('COUNT(DISTINCT localites.id) as localites'), DB::raw("YEAR(date_op) as year"))
                ->whereIn('operation_id', [2])
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('date_op')
                ->groupBy('projets.intitule_resume', 'operateurs.intitule', 'year')
                ->orderBy('year', 'ASC')
                ->get();



            $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'), 'localites.id')
                ->groupBy('localites.id')
                ->where('operations.code', 1)
                ->whereNotNull('localites.code_ansade')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get()->count();


            $report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->where('operations.code', 1)
                ->whereNull('localites.code_ansade')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get()->count();

            $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->where('operations.code', 1)
                ->whereNotNull('localites.code_ansade')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get()->count();


            $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->where('operations.code', 1)
                ->whereNull('localites.code_ansade')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get()->count();


            $nb_loc_suivi_apres_certif = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->joinSub(
                    DB::table('localite_operations as certif')
                        ->select('localite_id', DB::raw('MAX(date_op) as max_certif'))
                        ->where('operation_id', 3)
                        ->where('wilaya_id', $wilaya_id)
                        ->groupBy('localite_id'),
                    'certif_max',
                    'certif_max.localite_id',
                    '=',
                    'localites.id'
                )
                ->joinSub(
                    DB::table('localite_operations as suivi')
                        ->select('localite_id', DB::raw('MAX(date_op) as max_suivi'))
                        ->where('operation_id', 2)
                        ->groupBy('localite_id'),
                    'suivi_max',
                    'suivi_max.localite_id',
                    '=',
                    'localites.id'
                )
                ->select(
                    DB::raw('COUNT(localites.id) as localites'),
                    'localites.id'
                )
                ->whereColumn('suivi_max.max_suivi', '>', 'certif_max.max_certif')
                ->groupBy('localites.id')
                ->get();

            $dernieresCertifications = DB::table('localite_operations')
                ->select('localite_id', DB::raw('MAX(date_op) as date_derniere_certification'))
                ->where('operation_id', 3) // Certification FDAL
                ->where('wilaya_id', $wilaya_id)
                ->groupBy('localite_id');

            $results = DB::table('localite_operations as el')
                ->joinSub($dernieresCertifications, 'dc', function ($join) {
                    $join->on('el.localite_id', '=', 'dc.localite_id');
                })
                ->selectRaw('COUNT(DISTINCT el.localite_id) as nb_localites')
                ->selectRaw('COALESCE(SUM(el.nb_tot_pop), 0) as nb_tot_pop')
                ->selectRaw('COALESCE(SUM(el.nb_menages_comptabilises), 0) as nb_menages_comptabilises')
                ->where('el.operation_id', 2) // État des lieux
                ->where('el.wilaya_id', $wilaya_id)
                ->whereRaw('el.date_op > dc.date_derniere_certification')
                ->first();

            $report['access_assainisement_familial']['mise_atcp']['villages_appui_suivi'] = $results->nb_localites ?? 0;


            $maxIds = LocaliteReport::select('localite_id', DB::raw('MAX(id) as max_id'))
                ->where('wilaya_id', $wilaya_id)
                ->where('operation_id', 2)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->pluck('max_id');
            $report['access_assainisement_familial']['pm']['population'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_pop) as nb_tot_pop'))
                ->whereIn('id', $maxIds)
                ->value('nb_tot_pop');


            $report['access_assainisement_familial']['pm']['menages'] = LocaliteReport::selectRaw('SUM(nb_menages_comptabilises) as menages')
                ->whereIn('id', $maxIds)
                ->value('menages');

            $report['access_assainisement_familial']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
                ->groupBy('localite_operations.approv_eau')
                ->where('operations.code', 2)
                ->whereNotNull('localite_operations.approv_eau')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get();
            $report['access_assainisement_familial']['fdal'] = LocaliteReport::join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                    DB::raw("YEAR(date_op) as year")
                )
                ->groupBy('year')
                ->whereIn('operations.code', [1, 3])
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get()->toArray();



            $minIds = LocaliteReport::select('localite_id', DB::raw('min(id) as min_id'))
                ->where('operation_id', 2)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->where('wilaya_id', $wilaya_id)
                ->pluck('min_id');
            $maxIds = LocaliteReport::select('localite_id', DB::raw('max(id) as max_id'))
                ->where('operation_id', 4)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->where('wilaya_id', $wilaya_id)
                ->pluck('max_id');
            $report['access_assainisement_familial']['menages_EDL1'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
            )
                ->whereIn('localite_operations.id', $mins2)
                ->value('nb_menages_comptabilises');
            $report['access_assainisement_familial']['couvertures_EDL'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
                ->whereIn('localite_operations.id', $minIds)
                ->value('couvertures');
            $report['access_assainisement_familial']['nb_localites_couvertures_EDL'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_EDL'))
                ->whereIn('localite_operations.id', $minIds)
                ->whereNotNull('nb_tot_latr_fam')
                ->value('nb_localites_couvertures_EDL');

            $report['access_assainisement_familial']['couvertures_dernier'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
                ->whereIn('localite_operations.id', $maxIds)
                ->value('couvertures');

            $report['access_assainisement_familial']['nb_localites_couvertures_dernier'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_dernier'))
                ->whereIn('localite_operations.id', $maxIds)
                ->whereNotNull('nb_tot_latr_fam')
                ->value('nb_localites_couvertures_dernier');
            $report['access_assainisement_familial']['nouv_latr_const'] = $report['access_assainisement_familial']['couvertures_dernier'] - $report['access_assainisement_familial']['couvertures_EDL'];
            $report['access_assainisement_familial']['nb_localites_nouv_latr_const'] = max($report['access_assainisement_familial']['nb_localites_couvertures_dernier'], $report['access_assainisement_familial']['nb_localites_couvertures_EDL']);
            $report['access_assainisement_familial']['nbr_latrine_autoconst'] = LocaliteReport::select(
                DB::raw('SUM(nbr_latrine_autoconst) as nbr_latrine_autoconst')
            )
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_latrine_autoconst');
            $report['access_assainisement_familial']['nbr_localites_latrine_autoconst'] = LocaliteReport::select(
                DB::raw('COUNT(localite_id) as nbr_localites_latrine_autoconst')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_autoconst')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_localites_latrine_autoconst');
            $report['access_assainisement_familial']['nbr_latrine_realise_macon'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_latrine_realise_macon) as nbr_latrine_realise_macon')
            )
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_latrine_realise_macon');

            $report['access_assainisement_familial']['nbr_localites_latrine_realise_macon'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_realise_macon')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_realise_macon')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_localites_latrine_realise_macon');
            $report['access_assainisement_familial']['nbr_latrine_en_constr'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_latrine_en_constr) as nbr_latrine_en_constr')
            )
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_latrine_en_constr');
            $report['access_assainisement_familial']['nbr_localites_latrine_en_constr'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_en_constr')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_en_constr')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_localites_latrine_en_constr');
            $report['access_assainisement_familial']['nbr_menages_ltr_end_phm'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_ltr_end_phm) as nbr_menages_ltr_end_phm')
            )
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_menages_ltr_end_phm');
            $report['access_assainisement_familial']['nbr_localites_menages_ltr_end_phm'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_menages_ltr_end_phm')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_ltr_end_phm')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_localites_menages_ltr_end_phm');


            $report['access_assainisement_familial']['nbr_menages_dlm'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_dlm) as nbr_dlm'),
                DB::raw('SUM(localite_operations.nb_tot_latr_fam) as nb_tot_latr_fam'),
                DB::raw('COUNT(DISTINCT localite_operations.localite_id) as nbr_localites')
            )
                ->whereIn('id', $maxIds)
                ->first();



            // Calcul de la somme des latrines réalisées
            $totalLatrinesRealisees = LocaliteReport::where('wilaya_id', $wilaya_id)->where('operation_id', 4)
                ->sum('nbr_latrine_realise');

            // Calcul de la somme des latrines auto-financées
            $totalLatrinesAutofin = LocaliteReport::where('wilaya_id', $wilaya_id)->where('operation_id', 4)
                ->sum('nbr_latrine_autofin');

            // Calcul du taux
            $report['access_assainisement_familial']['auto_finance'] = ($totalLatrinesRealisees > 0)
                ? ($totalLatrinesAutofin * 100 / $totalLatrinesRealisees)
                : 0;

            // Comptage des localités avec latrines auto-financées
            $report['access_assainisement_familial']['localites_latrine_autofin'] = LocaliteReport::where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->where('nbr_latrine_autofin', '>', 0)
                ->distinct('localite_id')
                ->count('localite_id');
            $report['access_assainisement_familial']['nb_menages_inv'] = LocaliteReport::select(

                DB::raw('SUM( localite_operations.nb_latr_invest_men_dispo) as nb_latr_invest_men_dispo')
            )
                ->where('operation_id', 4)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->value('nb_latr_invest_men_dispo');
            $report['access_assainisement_familial']['localites_latr_invest_men_dispo'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_latr_invest_men_dispo'))
                ->where('operation_id', 4)
                ->whereNotNull('nb_latr_invest_men_dispo')
                ->where('wilaya_id', $wilaya_id)
                ->value('localites_latr_invest_men_dispo');
            $report['access_assainisement_familial']['investi'] = LocaliteReport::select(

                DB::raw('SUM( localite_operations.mont_invest_men) as mont_invest_men')
            )
                ->where('operation_id', 4)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->value('mont_invest_men');

            $report['access_assainisement_familial']['investi_menages'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.mont_invest_men) / NULLIF(SUM(localite_operations.nb_latr_invest_men_dispo), 0), 0) as invest_moy_men_latr'))
                ->where('operation_id', 4)
                ->where('mont_invest_men', '>', 0)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->value('invest_moy_men_latr');
            $report['access_assainisement_familial']['localites_mont_invest_men'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_mont_invest_men'))
                ->where('operation_id', 4)
                ->whereNotNull('mont_invest_men')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->value('localites_mont_invest_men');


            $report['access_assainisement_familial']['latrines_entrainde'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_entraid) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_entrainde'))
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('latrines_entrainde');
            $report['access_assainisement_familial']['localites_entrainde'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_entrainde'))
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_entraid')
                ->where('wilaya_id', $wilaya_id)
                ->value('localites_entrainde');
            $report['access_assainisement_familial']['latrines_subv'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_subv) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_subv'))
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('latrines_subv');
            $report['access_assainisement_familial']['localites_subv'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_subv'))
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_subv')
                ->where('wilaya_id', $wilaya_id)
                ->value('localites_subv');

            $report['access_assainisement_familial']['menages'] = LocaliteReport::where('wilaya_id', $wilaya_id)
                ->where('operation_id', 2)
                ->sum('nb_menages_comptabilises');

            $ameliores = DB::query()
                ->from(DB::raw("(
                    SELECT 
                        localite_id,
                        nb_latrine_amelior,
                        ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                    FROM localite_operations
                    WHERE operation_id IN (2, 4)
                      AND wilaya_id = $wilaya_id
                      AND nb_latrine_amelior IS NOT NULL
                ) as ranked"))
                ->selectRaw('SUM(nb_latrine_amelior) as total_ameliores')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites')
                ->where('rn', 1)
                ->first();
            $report['access_assainisement_familial']['base'] = $ameliores->total_ameliores ?? 0;


            $report['access_assainisement_familial']['nb_localite_base'] = $ameliores->nb_localites ?? 0;

            $nonAmeliores = DB::query()
                ->from(DB::raw("(
                        SELECT 
                            localite_id,
                            nb_tot_latr_fam,
                            nb_latrine_amelior,
                            ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                        FROM localite_operations
                        WHERE operation_id IN (2, 4)
                          AND wilaya_id = {$wilaya_id}
                          AND nb_latrine_amelior IS NOT NULL
                    ) as ranked"))
                ->selectRaw('SUM(nb_tot_latr_fam - nb_latrine_amelior) as total_non_ameliores')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites')
                ->where('rn', 1)
                ->first();

            $report['access_assainisement_familial']['non_ameliores'] = $nonAmeliores->total_non_ameliores ?? 0;
            $report['access_assainisement_familial']['nb_localite_non_ameliores'] = $nonAmeliores->nb_localites ?? 0;

            $menagesDal = DB::query()
                ->from(DB::raw("(
        SELECT 
            localite_id,
            nbr_menages_dal,
            ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
        FROM localite_operations
        WHERE operation_id IN (2, 4)
          AND wilaya_id = {$wilaya_id}
          AND nbr_menages_dal IS NOT NULL
    ) as ranked"))
                ->selectRaw('SUM(nbr_menages_dal) as total_menages_dal')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites_menages_dal')
                ->where('rn', 1)
                ->first();
            $report['access_assainisement_familial']['men_dal'] = $menagesDal->total_menages_dal ?? 0;

            $report['access_assainisement_familial']['nb_localite_men_dal'] = $menagesDal->nb_localites_menages_dal ?? 0;

            $report['access_assainisement_familial']['securise'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
            )
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)
                ->value('securise');
            $report['access_assainisement_familial']['nb_localite_securise'] = LocaliteReport::select(
                DB::raw('count(id) as nb_localite_securise')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_latr_amel_adeq')
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->value('nb_localite_securise');

            $report['access_hygiene']['nbr_localites'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_localites');
            $report['access_hygiene']['nbr_localites_complet'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites_complet')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm_complet')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_localites_complet');
            $report['access_hygiene']['nbr_localites_incomplet'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites_incomplet')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm_incomplet')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_localites_complet');
            $report['access_hygiene']['nbr_sans_dlm'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_sans_dlm')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_sans_dlm')
                ->where('wilaya_id', $wilaya_id)
                ->value('nbr_sans_dlm');



            $report['access_hygiene'] = LocaliteReport::select(DB::raw('
                SUM(nbr_menages_dlm) as nbr_menages_dlm,
                SUM(nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
                SUM(nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
                SUM(nbr_menages_sans_dlm) as nbr_menages_sans_dlm'))
                ->where('operation_id', 4)
                ->where('wilaya_id', $wilaya_id)

                ->first();

            $report['ecoles']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('infrastructures.infra_publ', 'Ecole')->get()->count();

            $report['ecoles']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
                ->groupBy('infrastructure_operations.approv_eau')
                ->where('operations.code', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('infrastructure_operations.approv_eau')
                ->where('infrastructures.infra_publ', 'Ecole')
                ->get();

            $report['ecoles']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as couvert_ecole_latr '),
                    DB::raw('SUM(effectif) as effectif '),
                    DB::raw('SUM(nbr_cabine_fonct) as nbr_cabine_fonct_sum '),

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_fonct', '>', 0)
                ->whereNotNull('nbr_cabine_fonct')->first();
            $report['ecoles']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_pmr', '>', 0)
                ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
            $report['ecoles']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('dist_cab_hom_fem', '>', 0)
                ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
            $report['ecoles']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as gest_dechet ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('gest_dechet', '>', 0)
                ->whereNotNull('gest_dechet')->value('gest_dechet');
            $report['ecoles']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nb_dlm_instal', '>', 0)
                ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');
            $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_satisf '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_a_const', 0)
                ->where('nbr_bloc_a_const', 0)
                ->whereNotNull('nbr_bloc_a_const')
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_ecole_besoin_latr_satisf');
            $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_non_satisf '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where(function ($q) {
                    $q->where('nbr_cabine_a_const', '>', 0)
                      ->orWhere('nbr_bloc_a_const', '>', 0);
                })
                ->where(function ($q) {
                    $q->whereNotNull('nbr_cabine_a_const')
                      ->orWhereNotNull('nbr_bloc_a_const');
                })
                ->value('nb_ecole_besoin_latr_non_satisf');
            $report['ecoles']['besoin_assainissement']['nb_ecole_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_sans_latr '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_exist', 0)
                ->whereNotNull('nbr_cabine_exist')
                ->value('nb_ecole_sans_latr');
            $report['ecoles']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nbr_cabine_a_const');



            $report['ecoles']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_bloc_a_const', '>', 0)
                ->where('nbr_cabine_a_const', '>', 0)
                ->value('projet_besoins');
            $report['ecoles']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNull('nbr_bloc_a_const')
                ->whereNull('nbr_cabine_a_const')

                ->value('projet_sans_besoins');

            $report['ecoles']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->get();

            $report['ecoles']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->get();
            $report['ecoles']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('
                    SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                    SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                    SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->first();
            $report['ecoles']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nb_dlm_complet')
                ->value('nbr_inf_complet');
            $report['ecoles']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nb_dlm_incomplet')
                ->value('nbr_inf_incomplet');
            $report['ecoles']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nb_sans_dlm')
                ->value('nbr_inf_sans');

            $report['santes']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')->get()->count();
            $report['santes']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
                ->groupBy('infrastructure_operations.approv_eau')
                ->where('operations.code', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('infrastructure_operations.approv_eau')
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->get();

            $report['santes']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as couvert_sante_latr '),
                    DB::raw('SUM(effectif) as effectif '),

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_fonct', '>', 0)
                ->whereNotNull('nbr_cabine_fonct')->first();
            $report['santes']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_pmr', '>', 0)
                ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
            $report['santes']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('dist_cab_hom_fem', '>', 0)
                ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
            $report['santes']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as gest_dechet ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('gest_dechet', '>', 0)
                ->whereNotNull('gest_dechet')->value('gest_dechet');
            $report['santes']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nb_dlm_instal', '>', 0)
                ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');

            $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_satisf '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_a_const', 0)
                ->where('nbr_bloc_a_const', 0)
                ->whereNotNull('nbr_bloc_a_const')
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_sante_besoin_latr_satisf');
            $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_non_satisf '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where(function ($q) {
                    $q->where('nbr_cabine_a_const', '>', 0)
                      ->orWhere('nbr_bloc_a_const', '>', 0);
                })
                ->where(function ($q) {
                    $q->whereNotNull('nbr_cabine_a_const')
                      ->orWhereNotNull('nbr_bloc_a_const');
                })
                ->value('nb_sante_besoin_latr_non_satisf');
            $report['santes']['besoin_assainissement']['nb_sante_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_sans_latr '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_cabine_exist', 0)
                ->whereNotNull('nbr_cabine_exist')
                ->value('nb_sante_sans_latr');
            $report['santes']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nbr_cabine_a_const');



            $report['santes']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('nbr_bloc_a_const', '>', 0)
                ->where('nbr_cabine_a_const', '>', 0)
                ->value('projet_besoins');
            $report['santes']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNull('nbr_bloc_a_const')
                ->whereNull('nbr_cabine_a_const')

                ->value('projet_sans_besoins');

            $report['santes']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->get();

            $report['santes']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->get();

            $report['santes']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('
                    SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                    SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                    SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
                ->where('operation_id', 5)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->first();
            $report['santes']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nb_dlm_complet')
                ->value('nbr_inf_complet');
            $report['santes']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nb_dlm_incomplet')
                ->value('nbr_inf_incomplet');
            $report['santes']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->whereNotNull('nb_sans_dlm')
                ->value('nbr_inf_sans');
        }
        return response()->json($report);
    }
    function fetch_reporting_moughata(Request $request)
    {
        $report = array();
        if (isset($request->moughata_id)) {
            $moughata_id = $request->moughata_id;
            $report['donnees_generales']['wilaya'] = Moughata::where('id', $moughata_id)
                ->value('intitule');

            $maxs = LocaliteReport::where('moughata_id', $moughata_id)->where('operation_id', 2)->selectRaw('max(id) as id')->groupBy('localite_id')->get()->pluck('id');

            $ids = LocaliteReport::where('moughata_id', $moughata_id)->where('operation_id', 2)->selectRaw('DISTINCT localite_id')->orderBy('localite_id')->get()->pluck('localite_id');
            $pop1 = Localite::whereNotIn('id', $ids)->where('moughata_id', $moughata_id)->sum('nb_pop_ansade');
            $pop2 = LocaliteReport::whereIn('id', $maxs)->sum('nb_tot_pop');
            $report['donnees_generales']['population'] = $pop1 + $pop2;

            $report['donnees_generales']['nb_localites'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites'))
                ->where('localites.moughata_id', $moughata_id)
                ->value('nb_localites');


            $report['donnees_generales']['nb_localites_codifiees'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites_codifiees'))
                ->whereNotNull('localites.code_ansade')
                ->where('localites.moughata_id', $moughata_id)
                ->value('nb_localites_codifiees');

            $report['donnees_generales']['nb_localites_non_codifiees'] = Localite::select(DB::raw('COUNT(DISTINCT localites.id) as nb_localites_non_codifiees'))
                ->whereNull('localites.code_ansade')
                ->where('localites.moughata_id', $moughata_id)
                ->value('nb_localites_non_codifiees');

            $report['donnees_generales']['nb_communes'] = Commune::select(DB::raw('COUNT(communes.id) as nb_communes'))
                ->where('communes.moughata_id', $moughata_id)
                ->value('nb_communes');
            $report['donnees_generales']['nb_moughatas'] = Moughata::select(DB::raw('COUNT(moughatas.id) as nb_moughatas'))
                ->where('moughatas.id', $moughata_id)
                ->value('nb_moughatas');
            $report['donnees_generales']['nb_wilayas'] = 1;
            // a verifier
            $report['donnees_generales']['nb_eleves'] = InfrastructureReport::select(DB::raw('SUM(infrastructure_operations.effectif) as nb_eleves'))
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->value('nb_eleves');
            $report['donnees_generales']['nb_ecoles'] = Infrastructure::where('infra_publ', 'LIKE', '%ecole')->where('infrastructures.moughata_id', $moughata_id)->count();
            $report['donnees_generales']['nb_santes'] = Infrastructure::where('infra_publ', 'LIKE', '%santé')->where('infrastructures.moughata_id', $moughata_id)->count();


            $report['nb_loc_declench'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites'))
                ->groupBy('localite_id')
                ->where('operation_id', 1)
                ->where('moughata_id', $moughata_id)
                ->get()->count();

            $result2 = LocaliteReport::where('localite_operations.moughata_id', $moughata_id)
                ->where('operation_id', 2)
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MIN(date_op) as min_date,localite_id,MIN(id) as min_id')
                ->get();

            $result1 = LocaliteReport::where('localite_operations.moughata_id', $moughata_id)
                ->where('operation_id', 4)
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MAX(date_op) as max_date,localite_id,max(id) as max_id')
                ->get();

            $result = LocaliteReport::join('localites', 'localite_operations.localite_id', '=', 'localites.id')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->where('operation_id', 2)
                ->where(function ($query) use ($result1) {
                    foreach ($result1 as $res) {
                        $query->orWhere(function ($query) use ($res) {
                            $query->where('localite_id', $res->localite_id)
                                ->where('date_op', '<=', $res->max_date);
                        });
                    }
                })
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MAX(date_op) as max_date,MAX(localite_operations.id) as max_id,localites.intitule')
                ->get();

            $mins2 = $result2->pluck('min_id');



            $maxs2 = $result->pluck('max_id');

            $report['menages_EDL_max'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
            )
                ->whereIn('localite_operations.id', $maxs2)
                ->value('nb_menages_comptabilises');
            $report['charts']['nb_loc'] = LocaliteReport::join('wilayas', 'localite_operations.moughata_id', 'wilayas.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                    DB::raw("YEAR(date_op) as year")
                )
                ->groupBy('year')
                ->whereIn('operations.code', [1, 3])
                ->whereNotNull('date_op')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get();
            $report['charts']['access_hygiene'] = LocaliteReport::join('wilayas', 'localite_operations.moughata_id', 'wilayas.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('
                SUM(localite_operations.nbr_menages_dlm) as nbr_menages_dlm,
                SUM(localite_operations.nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
                SUM(localite_operations.nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
                SUM(localite_operations.nbr_menages_sans_dlm) as nbr_menages_sans_dlm')
                )
                ->where('localite_operations.moughata_id', $moughata_id)
                ->where('operations.code', 4)
                ->first();
            $report['charts']['base'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($moughata_id) {
                    $query->select(DB::raw('MAX(nb_latrine_amelior) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('moughata_id', $moughata_id)
                        ->where('operation_id', 4)
                        ->whereNotNull('nb_latrine_amelior')
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;


            $report['charts']['non_ameliores'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($moughata_id) {
                    $query->select(DB::raw('MAX(nb_tot_latr_fam - nb_latrine_amelior) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('moughata_id', $moughata_id)
                        ->where('operation_id', 4)
                        ->whereNotNull('nb_latrine_amelior')
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;
            $report['charts']['men_dal'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($moughata_id) {
                    $query->select(DB::raw('MAX(nbr_menages_dal) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('moughata_id', $moughata_id)
                        ->where('operation_id', 4)
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;


            $report['charts']['securise'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
            )
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('securise');
            $report['charts']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
                ->groupBy('localite_operations.approv_eau')
                ->where('operations.code', 2)
                ->whereNotNull('localite_operations.approv_eau')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get();

            $report['access_assainisement_familial']['projets'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
                ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->select('projets.intitule_resume', 'operateurs.intitule', DB::raw('COUNT(DISTINCT localites.id) as localites'), DB::raw("YEAR(date_op) as year"))
                ->whereIn('operation_id', [2])
                ->where('localite_operations.moughata_id', $moughata_id)
                ->whereNotNull('date_op')
                ->groupBy('projets.intitule_resume', 'operateurs.intitule', 'year')
                ->orderBy('year', 'ASC')
                ->get();



            $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'), 'localites.id')
                ->groupBy('localites.id')
                ->where('operations.code', 1)
                ->whereNotNull('localites.code_ansade')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get()->count();


            $report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->where('operations.code', 1)
                ->whereNull('localites.code_ansade')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get()->count();

            $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->where('operations.code', 1)
                ->whereNotNull('localites.code_ansade')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get()->count();


            $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->where('operations.code', 1)
                ->whereNull('localites.code_ansade')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get()->count();


            $nb_loc_suivi_apres_certif = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->joinSub(
                    DB::table('localite_operations as certif')
                        ->select('localite_id', DB::raw('MAX(date_op) as max_certif'))
                        ->where('operation_id', 3)
                        ->where('moughata_id', $moughata_id)
                        ->groupBy('localite_id'),
                    'certif_max',
                    'certif_max.localite_id',
                    '=',
                    'localites.id'
                )
                ->joinSub(
                    DB::table('localite_operations as suivi')
                        ->select('localite_id', DB::raw('MAX(date_op) as max_suivi'))
                        ->where('operation_id', 2)
                        ->groupBy('localite_id'),
                    'suivi_max',
                    'suivi_max.localite_id',
                    '=',
                    'localites.id'
                )
                ->select(
                    DB::raw('COUNT(localites.id) as localites'),
                    'localites.id'
                )
                ->whereColumn('suivi_max.max_suivi', '>', 'certif_max.max_certif')
                ->groupBy('localites.id')
                ->get();

            $dernieresCertifications = DB::table('localite_operations')
                ->select('localite_id', DB::raw('MAX(date_op) as date_derniere_certification'))
                ->where('operation_id', 3) // Certification FDAL
                ->where('moughata_id', $moughata_id)
                ->groupBy('localite_id');

            $results = DB::table('localite_operations as el')
                ->joinSub($dernieresCertifications, 'dc', function ($join) {
                    $join->on('el.localite_id', '=', 'dc.localite_id');
                })
                ->selectRaw('COUNT(DISTINCT el.localite_id) as nb_localites')
                ->selectRaw('COALESCE(SUM(el.nb_tot_pop), 0) as nb_tot_pop')
                ->selectRaw('COALESCE(SUM(el.nb_menages_comptabilises), 0) as nb_menages_comptabilises')
                ->where('el.operation_id', 2) // État des lieux
                ->where('moughata_id', $moughata_id)
                ->whereRaw('el.date_op > dc.date_derniere_certification')
                ->first();
            $report['access_assainisement_familial']['mise_atcp']['villages_appui_suivi'] = $results->nb_localites ?? 0;



            $maxIds = LocaliteReport::select('localite_id', DB::raw('MAX(id) as max_id'))
                ->where('moughata_id', $moughata_id)
                ->where('operation_id', 2)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->pluck('max_id');
            $report['access_assainisement_familial']['pm']['population'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_pop) as nb_tot_pop'))
                ->whereIn('id', $maxIds)
                ->value('nb_tot_pop');


            $report['access_assainisement_familial']['pm']['menages'] = LocaliteReport::selectRaw('SUM(nb_menages_comptabilises) as menages')
                ->whereIn('id', $maxIds)
                ->value('menages');

            $report['access_assainisement_familial']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
                ->groupBy('localite_operations.approv_eau')
                ->where('operations.code', 2)
                ->whereNotNull('localite_operations.approv_eau')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get();
            $report['access_assainisement_familial']['fdal'] = LocaliteReport::join('wilayas', 'localite_operations.moughata_id', 'wilayas.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                    DB::raw("YEAR(date_op) as year")
                )
                ->groupBy('year')
                ->whereIn('operations.code', [1, 3])
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get()->toArray();



            $minIds = LocaliteReport::select('localite_id', DB::raw('min(id) as min_id'))
                ->where('operation_id', 2)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->where('moughata_id', $moughata_id)
                ->pluck('min_id');
            $maxIds = LocaliteReport::select('localite_id', DB::raw('max(id) as max_id'))
                ->where('operation_id', 4)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->where('moughata_id', $moughata_id)
                ->pluck('max_id');
            $report['access_assainisement_familial']['menages_EDL1'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
            )
                ->whereIn('localite_operations.id', $mins2)
                ->value('nb_menages_comptabilises');
            $report['access_assainisement_familial']['couvertures_EDL'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
                ->whereIn('localite_operations.id', $minIds)
                ->value('couvertures');
            $report['access_assainisement_familial']['nb_localites_couvertures_EDL'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_EDL'))
                ->whereIn('localite_operations.id', $minIds)
                ->whereNotNull('nb_tot_latr_fam')
                ->value('nb_localites_couvertures_EDL');

            $report['access_assainisement_familial']['couvertures_dernier'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
                ->whereIn('localite_operations.id', $maxIds)
                ->value('couvertures');

            $report['access_assainisement_familial']['nb_localites_couvertures_dernier'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_dernier'))
                ->whereIn('localite_operations.id', $maxIds)
                ->whereNotNull('nb_tot_latr_fam')
                ->value('nb_localites_couvertures_dernier');
            $report['access_assainisement_familial']['nouv_latr_const'] = $report['access_assainisement_familial']['couvertures_dernier'] - $report['access_assainisement_familial']['couvertures_EDL'];
            $report['access_assainisement_familial']['nb_localites_nouv_latr_const'] = max($report['access_assainisement_familial']['nb_localites_couvertures_dernier'], $report['access_assainisement_familial']['nb_localites_couvertures_EDL']);
            $report['access_assainisement_familial']['nbr_latrine_autoconst'] = LocaliteReport::select(
                DB::raw('SUM(nbr_latrine_autoconst) as nbr_latrine_autoconst')
            )
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('nbr_latrine_autoconst');
            $report['access_assainisement_familial']['nbr_localites_latrine_autoconst'] = LocaliteReport::select(
                DB::raw('COUNT(localite_id) as nbr_localites_latrine_autoconst')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_autoconst')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_localites_latrine_autoconst');
            $report['access_assainisement_familial']['nbr_latrine_realise_macon'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_latrine_realise_macon) as nbr_latrine_realise_macon')
            )
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('nbr_latrine_realise_macon');

            $report['access_assainisement_familial']['nbr_localites_latrine_realise_macon'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_realise_macon')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_realise_macon')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_localites_latrine_realise_macon');
            $report['access_assainisement_familial']['nbr_latrine_en_constr'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_latrine_en_constr) as nbr_latrine_en_constr')
            )
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('nbr_latrine_en_constr');
            $report['access_assainisement_familial']['nbr_localites_latrine_en_constr'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_en_constr')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_en_constr')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_localites_latrine_en_constr');
            $report['access_assainisement_familial']['nbr_menages_ltr_end_phm'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_ltr_end_phm) as nbr_menages_ltr_end_phm')
            )
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('nbr_menages_ltr_end_phm');
            $report['access_assainisement_familial']['nbr_localites_menages_ltr_end_phm'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_menages_ltr_end_phm')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_ltr_end_phm')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_localites_menages_ltr_end_phm');


            $report['access_assainisement_familial']['nbr_menages_dlm'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_dlm) as nbr_dlm'),
                DB::raw('SUM(localite_operations.nb_tot_latr_fam) as nb_tot_latr_fam'),
                DB::raw('COUNT(DISTINCT localite_operations.localite_id) as nbr_localites')
            )
                ->whereIn('id', $maxIds)
                ->first();



            // Calcul de la somme des latrines réalisées
            $totalLatrinesRealisees = LocaliteReport::where('moughata_id', $moughata_id)->where('operation_id', 4)
                ->sum('nbr_latrine_realise');

            // Calcul de la somme des latrines auto-financées
            $totalLatrinesAutofin = LocaliteReport::where('moughata_id', $moughata_id)->where('operation_id', 4)
                ->sum('nbr_latrine_autofin');

            // Calcul du taux
            $report['access_assainisement_familial']['auto_finance'] = ($totalLatrinesRealisees > 0)
                ? ($totalLatrinesAutofin * 100 / $totalLatrinesRealisees)
                : 0;

            // Comptage des localités avec latrines auto-financées
            $report['access_assainisement_familial']['localites_latrine_autofin'] = LocaliteReport::where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->where('nbr_latrine_autofin', '>', 0)
                ->distinct('localite_id')
                ->count('localite_id');
            $report['access_assainisement_familial']['nb_menages_inv'] = LocaliteReport::select(

                DB::raw('SUM( localite_operations.nb_latr_invest_men_dispo) as nb_latr_invest_men_dispo')
            )
                ->where('operation_id', 4)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->value('nb_latr_invest_men_dispo');
            $report['access_assainisement_familial']['localites_latr_invest_men_dispo'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_latr_invest_men_dispo'))
                ->where('operation_id', 4)
                ->whereNotNull('nb_latr_invest_men_dispo')
                ->where('moughata_id', $moughata_id)
                ->value('localites_latr_invest_men_dispo');
            $report['access_assainisement_familial']['investi'] = LocaliteReport::select(

                DB::raw('SUM( localite_operations.mont_invest_men) as mont_invest_men')
            )
                ->where('operation_id', 4)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->value('mont_invest_men');

            $report['access_assainisement_familial']['investi_menages'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.mont_invest_men) / NULLIF(SUM(localite_operations.nb_latr_invest_men_dispo), 0), 0) as invest_moy_men_latr'))
                ->where('operation_id', 4)
                ->where('mont_invest_men', '>', 0)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->value('invest_moy_men_latr');
            $report['access_assainisement_familial']['localites_mont_invest_men'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_mont_invest_men'))
                ->where('operation_id', 4)
                ->whereNotNull('mont_invest_men')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->value('localites_mont_invest_men');


            $report['access_assainisement_familial']['latrines_entrainde'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_entraid) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_entrainde'))
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('latrines_entrainde');
            $report['access_assainisement_familial']['localites_entrainde'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_entrainde'))
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_entraid')
                ->where('moughata_id', $moughata_id)
                ->value('localites_entrainde');
            $report['access_assainisement_familial']['latrines_subv'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_subv) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_subv'))
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('latrines_subv');
            $report['access_assainisement_familial']['localites_subv'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_subv'))
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_subv')
                ->where('moughata_id', $moughata_id)
                ->value('localites_subv');

            $report['access_assainisement_familial']['menages'] = LocaliteReport::where('moughata_id', $moughata_id)
                ->where('operation_id', 2)
                ->sum('nb_menages_comptabilises');

            $ameliores = DB::query()
                ->from(DB::raw("(
                    SELECT 
                        localite_id,
                        nb_latrine_amelior,
                        ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                    FROM localite_operations
                    WHERE operation_id IN (2, 4)
                      AND moughata_id = $moughata_id
                      AND nb_latrine_amelior IS NOT NULL
                ) as ranked"))
                ->selectRaw('SUM(nb_latrine_amelior) as total_ameliores')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites')
                ->where('rn', 1)
                ->first();
            $report['access_assainisement_familial']['base'] = $ameliores->total_ameliores ?? 0;


            $report['access_assainisement_familial']['nb_localite_base'] = $ameliores->nb_localites ?? 0;

            $nonAmeliores = DB::query()
                ->from(DB::raw("(
                        SELECT 
                            localite_id,
                            nb_tot_latr_fam,
                            nb_latrine_amelior,
                            ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                        FROM localite_operations
                        WHERE operation_id IN (2, 4)
                          AND moughata_id = {$moughata_id}
                          AND nb_latrine_amelior IS NOT NULL
                    ) as ranked"))
                ->selectRaw('SUM(nb_tot_latr_fam - nb_latrine_amelior) as total_non_ameliores')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites')
                ->where('rn', 1)
                ->first();

            $report['access_assainisement_familial']['non_ameliores'] = $nonAmeliores->total_non_ameliores ?? 0;
            $report['access_assainisement_familial']['nb_localite_non_ameliores'] = $nonAmeliores->nb_localites ?? 0;

            $menagesDal = DB::query()
                ->from(DB::raw("(
        SELECT 
            localite_id,
            nbr_menages_dal,
            ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
        FROM localite_operations
        WHERE operation_id IN (2, 4)
          AND moughata_id = {$moughata_id}
          AND nbr_menages_dal IS NOT NULL
    ) as ranked"))
                ->selectRaw('SUM(nbr_menages_dal) as total_menages_dal')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites_menages_dal')
                ->where('rn', 1)
                ->first();
            $report['access_assainisement_familial']['men_dal'] = $menagesDal->total_menages_dal ?? 0;

            $report['access_assainisement_familial']['nb_localite_men_dal'] = $menagesDal->nb_localites_menages_dal ?? 0;

            $report['access_assainisement_familial']['securise'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
            )
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)
                ->value('securise');
            $report['access_assainisement_familial']['nb_localite_securise'] = LocaliteReport::select(
                DB::raw('count(id) as nb_localite_securise')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_latr_amel_adeq')
                ->where('localite_operations.moughata_id', $moughata_id)
                ->value('nb_localite_securise');

            $report['access_hygiene']['nbr_localites'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_localites');
            $report['access_hygiene']['nbr_localites_complet'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites_complet')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm_complet')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_localites_complet');
            $report['access_hygiene']['nbr_localites_incomplet'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites_incomplet')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm_incomplet')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_localites_complet');
            $report['access_hygiene']['nbr_sans_dlm'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_sans_dlm')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_sans_dlm')
                ->where('moughata_id', $moughata_id)
                ->value('nbr_sans_dlm');



            $report['access_hygiene'] = LocaliteReport::select(DB::raw('
                SUM(nbr_menages_dlm) as nbr_menages_dlm,
                SUM(nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
                SUM(nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
                SUM(nbr_menages_sans_dlm) as nbr_menages_sans_dlm'))
                ->where('operation_id', 4)
                ->where('moughata_id', $moughata_id)

                ->first();

            $report['ecoles']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('infrastructures.infra_publ', 'Ecole')->get()->count();

            $report['ecoles']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
                ->groupBy('infrastructure_operations.approv_eau')
                ->where('operations.code', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('infrastructure_operations.approv_eau')
                ->where('infrastructures.infra_publ', 'Ecole')
                ->get();

            $report['ecoles']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as couvert_ecole_latr '),
                    DB::raw('SUM(effectif) as effectif '),
                    DB::raw('SUM(nbr_cabine_fonct) as nbr_cabine_fonct_sum '),

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_fonct', '>', 0)
                ->whereNotNull('nbr_cabine_fonct')->first();
            $report['ecoles']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_pmr', '>', 0)
                ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
            $report['ecoles']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('dist_cab_hom_fem', '>', 0)
                ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
            $report['ecoles']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as gest_dechet ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('gest_dechet', '>', 0)
                ->whereNotNull('gest_dechet')->value('gest_dechet');
            $report['ecoles']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nb_dlm_instal', '>', 0)
                ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');
            $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_satisf '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_a_const', 0)
                ->where('nbr_bloc_a_const', 0)
                ->whereNotNull('nbr_bloc_a_const')
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_ecole_besoin_latr_satisf');
            $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_non_satisf '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where(function ($q) {
                    $q->where('nbr_cabine_a_const', '>', 0)
                      ->orWhere('nbr_bloc_a_const', '>', 0);
                })
                ->where(function ($q) {
                    $q->whereNotNull('nbr_cabine_a_const')
                      ->orWhereNotNull('nbr_bloc_a_const');
                })
                ->value('nb_ecole_besoin_latr_non_satisf');
            $report['ecoles']['besoin_assainissement']['nb_ecole_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_sans_latr '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_exist', 0)
                ->whereNotNull('nbr_cabine_exist')
                ->value('nb_ecole_sans_latr');
            $report['ecoles']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nbr_cabine_a_const');



            $report['ecoles']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_bloc_a_const', '>', 0)
                ->where('nbr_cabine_a_const', '>', 0)
                ->value('projet_besoins');
            $report['ecoles']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNull('nbr_bloc_a_const')
                ->whereNull('nbr_cabine_a_const')

                ->value('projet_sans_besoins');

            $report['ecoles']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->get();

            $report['ecoles']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->get();
            $report['ecoles']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('
                    SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                    SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                    SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->first();
            $report['ecoles']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nb_dlm_complet')
                ->value('nbr_inf_complet');
            $report['ecoles']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nb_dlm_incomplet')
                ->value('nbr_inf_incomplet');
            $report['ecoles']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nb_sans_dlm')
                ->value('nbr_inf_sans');

            $report['santes']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')->get()->count();
            $report['santes']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
                ->groupBy('infrastructure_operations.approv_eau')
                ->where('operations.code', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('infrastructure_operations.approv_eau')
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->get();

            $report['santes']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as couvert_sante_latr '),
                    DB::raw('SUM(effectif) as effectif '),

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_fonct', '>', 0)
                ->whereNotNull('nbr_cabine_fonct')->first();
            $report['santes']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_pmr', '>', 0)
                ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
            $report['santes']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('dist_cab_hom_fem', '>', 0)
                ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
            $report['santes']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as gest_dechet ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('gest_dechet', '>', 0)
                ->whereNotNull('gest_dechet')->value('gest_dechet');
            $report['santes']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nb_dlm_instal', '>', 0)
                ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');

            $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_satisf '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_a_const', 0)
                ->where('nbr_bloc_a_const', 0)
                ->whereNotNull('nbr_bloc_a_const')
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_sante_besoin_latr_satisf');
            $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_non_satisf '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where(function ($q) {
                    $q->where('nbr_cabine_a_const', '>', 0)
                      ->orWhere('nbr_bloc_a_const', '>', 0);
                })
                ->where(function ($q) {
                    $q->whereNotNull('nbr_cabine_a_const')
                      ->orWhereNotNull('nbr_bloc_a_const');
                })
                ->value('nb_sante_besoin_latr_non_satisf');
            $report['santes']['besoin_assainissement']['nb_sante_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_sans_latr '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_cabine_exist', 0)
                ->whereNotNull('nbr_cabine_exist')
                ->value('nb_sante_sans_latr');
            $report['santes']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nbr_cabine_a_const');



            $report['santes']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('nbr_bloc_a_const', '>', 0)
                ->where('nbr_cabine_a_const', '>', 0)
                ->value('projet_besoins');
            $report['santes']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNull('nbr_bloc_a_const')
                ->whereNull('nbr_cabine_a_const')

                ->value('projet_sans_besoins');

            $report['santes']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->get();

            $report['santes']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->get();
            $report['santes']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('
                    SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                    SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                    SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
                ->where('operation_id', 5)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->first();
            $report['santes']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nb_dlm_complet')
                ->value('nbr_inf_complet');
            $report['santes']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nb_dlm_incomplet')
                ->value('nbr_inf_incomplet');
            $report['santes']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->whereNotNull('nb_sans_dlm')
                ->value('nbr_inf_sans');
        }
        return response()->json($report);
    }
    function fetch_reporting_commune(Request $request)
    {

        $report = array();
        if (isset($request->commune_id)) {
            $commune_id = $request->commune_id;
            $report['donnees_generales']['wilaya'] = Commune::where('id', $commune_id)
                ->value('intitule');

            $maxs = LocaliteReport::where('commune_id', $commune_id)->where('operation_id', 2)->selectRaw('max(id) as id')->groupBy('localite_id')->get()->pluck('id');

            $ids = LocaliteReport::where('commune_id', $commune_id)->where('operation_id', 2)->selectRaw('DISTINCT localite_id')->orderBy('localite_id')->get()->pluck('localite_id');
            $pop1 = Localite::whereNotIn('id', $ids)->where('commune_id', $commune_id)->sum('nb_pop_ansade');
            $pop2 = LocaliteReport::whereIn('id', $maxs)->sum('nb_tot_pop');
            $report['donnees_generales']['population'] = $pop1 + $pop2;

            $report['donnees_generales']['nb_localites'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites'))
                ->where('localites.commune_id', $commune_id)
                ->value('nb_localites');


            $report['donnees_generales']['nb_localites_codifiees'] = Localite::select(DB::raw('COUNT(localites.id) as nb_localites_codifiees'))
                ->whereNotNull('localites.code_ansade')
                ->where('localites.commune_id', $commune_id)
                ->value('nb_localites_codifiees');

            $report['donnees_generales']['nb_localites_non_codifiees'] = Localite::select(DB::raw('COUNT(DISTINCT localites.id) as nb_localites_non_codifiees'))
                ->whereNull('localites.code_ansade')
                ->where('localites.commune_id', $commune_id)
                ->value('nb_localites_non_codifiees');

            $report['donnees_generales']['nb_communes'] = Commune::select(DB::raw('COUNT(communes.id) as nb_communes'))
                ->where('communes.id', $commune_id)
                ->value('nb_communes');
            $report['donnees_generales']['nb_moughatas'] = 1;
            $report['donnees_generales']['nb_wilayas'] = 1;
            // a verifier
            $report['donnees_generales']['nb_eleves'] = InfrastructureReport::select(DB::raw('SUM(infrastructure_operations.effectif) as nb_eleves'))
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->value('nb_eleves');
            $report['donnees_generales']['nb_ecoles'] = Infrastructure::where('infra_publ', 'LIKE', '%ecole')->where('infrastructures.commune_id', $commune_id)->count();
            $report['donnees_generales']['nb_santes'] = Infrastructure::where('infra_publ', 'LIKE', '%santé')->where('infrastructures.commune_id', $commune_id)->count();


            $report['nb_loc_declench'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites'))
                ->groupBy('localite_id')
                ->where('operation_id', 1)
                ->where('commune_id', $commune_id)
                ->get()->count();

            $result2 = LocaliteReport::where('localite_operations.commune_id', $commune_id)
                ->where('operation_id', 2)
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MIN(date_op) as min_date,localite_id,MIN(id) as min_id')
                ->get();

            $result1 = LocaliteReport::where('localite_operations.commune_id', $commune_id)
                ->where('operation_id', 4)
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MAX(date_op) as max_date,localite_id,max(id) as max_id')
                ->get();

            $result = LocaliteReport::join('localites', 'localite_operations.localite_id', '=', 'localites.id')
                ->where('localite_operations.commune_id', $commune_id)
                ->where('operation_id', 2)
                ->where(function ($query) use ($result1) {
                    foreach ($result1 as $res) {
                        $query->orWhere(function ($query) use ($res) {
                            $query->where('localite_id', $res->localite_id)
                                ->where('date_op', '<=', $res->max_date);
                        });
                    }
                })
                ->whereNotNull('date_op')
                ->groupBy('localite_id')
                ->selectRaw('MAX(date_op) as max_date,MAX(localite_operations.id) as max_id,localites.intitule')
                ->get();

            $mins2 = $result2->pluck('min_id');



            $maxs2 = $result->pluck('max_id');

            $report['menages_EDL_max'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
            )
                ->whereIn('localite_operations.id', $maxs2)
                ->value('nb_menages_comptabilises');
            $report['charts']['nb_loc'] = LocaliteReport::join('wilayas', 'localite_operations.commune_id', 'wilayas.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                    DB::raw("YEAR(date_op) as year")
                )
                ->groupBy('year')
                ->whereIn('operations.code', [1, 3])
                ->whereNotNull('date_op')
                ->where('localite_operations.commune_id', $commune_id)
                ->get();
            $report['charts']['access_hygiene'] = LocaliteReport::join('wilayas', 'localite_operations.commune_id', 'wilayas.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('
                SUM(localite_operations.nbr_menages_dlm) as nbr_menages_dlm,
                SUM(localite_operations.nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
                SUM(localite_operations.nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
                SUM(localite_operations.nbr_menages_sans_dlm) as nbr_menages_sans_dlm')
                )
                ->where('localite_operations.commune_id', $commune_id)
                ->where('operations.code', 4)
                ->first();
            $report['charts']['base'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($commune_id) {
                    $query->select(DB::raw('MAX(nb_latrine_amelior) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('commune_id', $commune_id)
                        ->where('operation_id', 4)
                        ->whereNotNull('nb_latrine_amelior')
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;


            $report['charts']['non_ameliores'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($commune_id) {
                    $query->select(DB::raw('MAX(nb_tot_latr_fam - nb_latrine_amelior) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('commune_id', $commune_id)
                        ->where('operation_id', 4)
                        ->whereNotNull('nb_latrine_amelior')
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;
            $report['charts']['men_dal'] = DB::query()->selectRaw('SUM(max_value) as total_max_value')
                ->fromSub(function ($query) use ($commune_id) {
                    $query->select(DB::raw('MAX(nbr_menages_dal) as max_value'))
                        ->from('localite_operations')
                        ->where('confirmed', true)
                        ->where('commune_id', $commune_id)
                        ->where('operation_id', 4)
                        ->groupBy('localite_id');
                }, 'subquery_alias')
                ->first()
                ->total_max_value;


            $report['charts']['securise'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
            )
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('securise');
            $report['charts']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
                ->groupBy('localite_operations.approv_eau')
                ->where('operations.code', 2)
                ->whereNotNull('localite_operations.approv_eau')
                ->where('localite_operations.commune_id', $commune_id)
                ->get();

            $report['access_assainisement_familial']['projets'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
                ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->select('projets.intitule_resume', 'operateurs.intitule', DB::raw('COUNT(DISTINCT localites.id) as localites'), DB::raw("YEAR(date_op) as year"))
                ->whereIn('operation_id', [2])
                ->where('localite_operations.commune_id', $commune_id)
                ->whereNotNull('date_op')
                ->groupBy('projets.intitule_resume', 'operateurs.intitule', 'year')
                ->orderBy('year', 'ASC')
                ->get();



            $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'), 'localites.id')
                ->groupBy('localites.id')
                ->where('operations.code', 1)
                ->whereNotNull('localites.code_ansade')
                ->where('localite_operations.commune_id', $commune_id)
                ->get()->count();


            $report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->where('operations.code', 1)
                ->whereNull('localites.code_ansade')
                ->where('localite_operations.commune_id', $commune_id)
                ->get()->count();

            $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_cod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->where('operations.code', 1)
                ->whereNotNull('localites.code_ansade')
                ->where('localite_operations.commune_id', $commune_id)
                ->get()->count();


            $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_noncod'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(localites.id) as localites'))
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->where('operations.code', 1)
                ->whereNull('localites.code_ansade')
                ->where('localite_operations.commune_id', $commune_id)
                ->get()->count();


            $nb_loc_suivi_apres_certif = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->joinSub(
                    DB::table('localite_operations as certif')
                        ->select('localite_id', DB::raw('MAX(date_op) as max_certif'))
                        ->where('operation_id', 3)
                        ->where('commune_id', $commune_id)
                        ->groupBy('localite_id'),
                    'certif_max',
                    'certif_max.localite_id',
                    '=',
                    'localites.id'
                )
                ->joinSub(
                    DB::table('localite_operations as suivi')
                        ->select('localite_id', DB::raw('MAX(date_op) as max_suivi'))
                        ->where('operation_id', 2)
                        ->groupBy('localite_id'),
                    'suivi_max',
                    'suivi_max.localite_id',
                    '=',
                    'localites.id'
                )
                ->select(
                    DB::raw('COUNT(localites.id) as localites'),
                    'localites.id'
                )
                ->whereColumn('suivi_max.max_suivi', '>', 'certif_max.max_certif')
                ->groupBy('localites.id')
                ->get();

            $dernieresCertifications = DB::table('localite_operations')
                ->select('localite_id', DB::raw('MAX(date_op) as date_derniere_certification'))
                ->where('operation_id', 3) // Certification FDAL
                ->where('commune_id', $commune_id)
                ->groupBy('localite_id');

            $results = DB::table('localite_operations as el')
                ->joinSub($dernieresCertifications, 'dc', function ($join) {
                    $join->on('el.localite_id', '=', 'dc.localite_id');
                })
                ->selectRaw('COUNT(DISTINCT el.localite_id) as nb_localites')
                ->selectRaw('COALESCE(SUM(el.nb_tot_pop), 0) as nb_tot_pop')
                ->selectRaw('COALESCE(SUM(el.nb_menages_comptabilises), 0) as nb_menages_comptabilises')
                ->where('el.operation_id', 2) // État des lieux
                ->where('commune_id', $commune_id)
                ->whereRaw('el.date_op > dc.date_derniere_certification')
                ->first();

            $report['access_assainisement_familial']['mise_atcp']['villages_appui_suivi'] = $results->nb_localites ?? 0;

            $maxIds = LocaliteReport::select('localite_id', DB::raw('MAX(id) as max_id'))
                ->where('commune_id', $commune_id)
                ->where('operation_id', 2)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->pluck('max_id');
            $report['access_assainisement_familial']['pm']['population'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_pop) as nb_tot_pop'))
                ->whereIn('id', $maxIds)
                ->value('nb_tot_pop');


            $report['access_assainisement_familial']['pm']['menages'] = LocaliteReport::selectRaw('SUM(nb_menages_comptabilises) as menages')
                ->whereIn('id', $maxIds)
                ->value('menages');

            $report['access_assainisement_familial']['approv_eau'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(DISTINCT localites.id) as localites'), 'localite_operations.approv_eau')
                ->groupBy('localite_operations.approv_eau')
                ->where('operations.code', 2)
                ->whereNotNull('localite_operations.approv_eau')
                ->where('localite_operations.commune_id', $commune_id)
                ->get();
            $report['access_assainisement_familial']['fdal'] = LocaliteReport::join('wilayas', 'localite_operations.commune_id', 'wilayas.id')
                ->join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 3 THEN localite_operations.localite_id ELSE NULL END) as localites_3'),
                    DB::raw('COUNT(DISTINCT CASE WHEN operations.code = 1 THEN localite_operations.localite_id ELSE NULL END) as localites_1'),
                    DB::raw("YEAR(date_op) as year")
                )
                ->groupBy('year')
                ->whereIn('operations.code', [1, 3])
                ->where('localite_operations.commune_id', $commune_id)
                ->get()->toArray();



            $minIds = LocaliteReport::select('localite_id', DB::raw('min(id) as min_id'))
                ->where('operation_id', 2)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->where('commune_id', $commune_id)
                ->pluck('min_id');
            $maxIds = LocaliteReport::select('localite_id', DB::raw('max(id) as max_id'))
                ->where('operation_id', 4)
                ->groupBy('localite_id')
                ->havingRaw('COUNT(localite_id) >= 1')
                ->where('commune_id', $commune_id)
                ->pluck('max_id');
            $report['access_assainisement_familial']['menages_EDL1'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nb_menages_comptabilises) as nb_menages_comptabilises')
            )
                ->whereIn('localite_operations.id', $mins2)
                ->value('nb_menages_comptabilises');
            $report['access_assainisement_familial']['couvertures_EDL'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
                ->whereIn('localite_operations.id', $minIds)
                ->value('couvertures');
            $report['access_assainisement_familial']['nb_localites_couvertures_EDL'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_EDL'))
                ->whereIn('localite_operations.id', $minIds)
                ->whereNotNull('nb_tot_latr_fam')
                ->value('nb_localites_couvertures_EDL');

            $report['access_assainisement_familial']['couvertures_dernier'] = LocaliteReport::select(DB::raw('SUM(localite_operations.nb_tot_latr_fam) as couvertures'))
                ->whereIn('localite_operations.id', $maxIds)
                ->value('couvertures');

            $report['access_assainisement_familial']['nb_localites_couvertures_dernier'] = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id)  as nb_localites_couvertures_dernier'))
                ->whereIn('localite_operations.id', $maxIds)
                ->whereNotNull('nb_tot_latr_fam')
                ->value('nb_localites_couvertures_dernier');
            $report['access_assainisement_familial']['nouv_latr_const'] = $report['access_assainisement_familial']['couvertures_dernier'] - $report['access_assainisement_familial']['couvertures_EDL'];
            $report['access_assainisement_familial']['nb_localites_nouv_latr_const'] = max($report['access_assainisement_familial']['nb_localites_couvertures_dernier'], $report['access_assainisement_familial']['nb_localites_couvertures_EDL']);
            $report['access_assainisement_familial']['nbr_latrine_autoconst'] = LocaliteReport::select(
                DB::raw('SUM(nbr_latrine_autoconst) as nbr_latrine_autoconst')
            )
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('nbr_latrine_autoconst');
            $report['access_assainisement_familial']['nbr_localites_latrine_autoconst'] = LocaliteReport::select(
                DB::raw('COUNT(localite_id) as nbr_localites_latrine_autoconst')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_autoconst')
                ->where('commune_id', $commune_id)
                ->value('nbr_localites_latrine_autoconst');
            $report['access_assainisement_familial']['nbr_latrine_realise_macon'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_latrine_realise_macon) as nbr_latrine_realise_macon')
            )
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('nbr_latrine_realise_macon');

            $report['access_assainisement_familial']['nbr_localites_latrine_realise_macon'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_realise_macon')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_realise_macon')
                ->where('commune_id', $commune_id)
                ->value('nbr_localites_latrine_realise_macon');
            $report['access_assainisement_familial']['nbr_latrine_en_constr'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_latrine_en_constr) as nbr_latrine_en_constr')
            )
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('nbr_latrine_en_constr');
            $report['access_assainisement_familial']['nbr_localites_latrine_en_constr'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_latrine_en_constr')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_en_constr')
                ->where('commune_id', $commune_id)
                ->value('nbr_localites_latrine_en_constr');
            $report['access_assainisement_familial']['nbr_menages_ltr_end_phm'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_ltr_end_phm) as nbr_menages_ltr_end_phm')
            )
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('nbr_menages_ltr_end_phm');
            $report['access_assainisement_familial']['nbr_localites_menages_ltr_end_phm'] = LocaliteReport::select(
                DB::raw('COUNT(DISTINCT localite_id) as nbr_localites_menages_ltr_end_phm')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_ltr_end_phm')
                ->where('commune_id', $commune_id)
                ->value('nbr_localites_menages_ltr_end_phm');


            $report['access_assainisement_familial']['nbr_menages_dlm'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_dlm) as nbr_dlm'),
                DB::raw('SUM(localite_operations.nb_tot_latr_fam) as nb_tot_latr_fam'),
                DB::raw('COUNT(DISTINCT localite_operations.localite_id) as nbr_localites')
            )
                ->whereIn('id', $maxIds)
                ->first();



            // Calcul de la somme des latrines réalisées
            $totalLatrinesRealisees = LocaliteReport::where('commune_id', $commune_id)->where('operation_id', 4)
                ->sum('nbr_latrine_realise');

            // Calcul de la somme des latrines auto-financées
            $totalLatrinesAutofin = LocaliteReport::where('commune_id', $commune_id)->where('operation_id', 4)
                ->sum('nbr_latrine_autofin');

            // Calcul du taux
            $report['access_assainisement_familial']['auto_finance'] = ($totalLatrinesRealisees > 0)
                ? ($totalLatrinesAutofin * 100 / $totalLatrinesRealisees)
                : 0;

            // Comptage des localités avec latrines auto-financées
            $report['access_assainisement_familial']['localites_latrine_autofin'] = LocaliteReport::where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->where('nbr_latrine_autofin', '>', 0)
                ->distinct('localite_id')
                ->count('localite_id');
            $report['access_assainisement_familial']['nb_menages_inv'] = LocaliteReport::select(

                DB::raw('SUM( localite_operations.nb_latr_invest_men_dispo) as nb_latr_invest_men_dispo')
            )
                ->where('operation_id', 4)
                ->where('localite_operations.commune_id', $commune_id)
                ->value('nb_latr_invest_men_dispo');
            $report['access_assainisement_familial']['localites_latr_invest_men_dispo'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_latr_invest_men_dispo'))
                ->where('operation_id', 4)
                ->whereNotNull('nb_latr_invest_men_dispo')
                ->where('commune_id', $commune_id)
                ->value('localites_latr_invest_men_dispo');
            $report['access_assainisement_familial']['investi'] = LocaliteReport::select(

                DB::raw('SUM( localite_operations.mont_invest_men) as mont_invest_men')
            )
                ->where('operation_id', 4)
                ->where('localite_operations.commune_id', $commune_id)
                ->value('mont_invest_men');

            $report['access_assainisement_familial']['investi_menages'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.mont_invest_men) / NULLIF(SUM(localite_operations.nb_latr_invest_men_dispo), 0), 0) as invest_moy_men_latr'))
                ->where('operation_id', 4)
                ->where('mont_invest_men', '>', 0)
                ->where('localite_operations.commune_id', $commune_id)
                ->value('invest_moy_men_latr');
            $report['access_assainisement_familial']['localites_mont_invest_men'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_mont_invest_men'))
                ->where('operation_id', 4)
                ->whereNotNull('mont_invest_men')
                ->where('localite_operations.commune_id', $commune_id)
                ->value('localites_mont_invest_men');


            $report['access_assainisement_familial']['latrines_entrainde'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_entraid) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_entrainde'))
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('latrines_entrainde');
            $report['access_assainisement_familial']['localites_entrainde'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_entrainde'))
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_entraid')
                ->where('commune_id', $commune_id)
                ->value('localites_entrainde');
            $report['access_assainisement_familial']['latrines_subv'] = LocaliteReport::select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_subv) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_subv'))
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('latrines_subv');
            $report['access_assainisement_familial']['localites_subv'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites_subv'))
                ->where('operation_id', 4)
                ->whereNotNull('nbr_latrine_subv')
                ->where('commune_id', $commune_id)
                ->value('localites_subv');

            $report['access_assainisement_familial']['menages'] = LocaliteReport::where('commune_id', $commune_id)
                ->where('operation_id', 2)
                ->sum('nb_menages_comptabilises');
            $ameliores = DB::query()
                ->from(DB::raw("(
                    SELECT 
                        localite_id,
                        nb_latrine_amelior,
                        ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                    FROM localite_operations
                    WHERE operation_id IN (2, 4)
                      AND commune_id = $commune_id
                      AND nb_latrine_amelior IS NOT NULL
                ) as ranked"))
                ->selectRaw('SUM(nb_latrine_amelior) as total_ameliores')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites')
                ->where('rn', 1)
                ->first();
            $report['access_assainisement_familial']['base'] = $ameliores->total_ameliores ?? 0;


            $report['access_assainisement_familial']['nb_localite_base'] = $ameliores->nb_localites ?? 0;

            $nonAmeliores = DB::query()
                ->from(DB::raw("(
                        SELECT 
                            localite_id,
                            nb_tot_latr_fam,
                            nb_latrine_amelior,
                            ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
                        FROM localite_operations
                        WHERE operation_id IN (2, 4)
                          AND commune_id = {$commune_id}
                          AND nb_latrine_amelior IS NOT NULL
                    ) as ranked"))
                ->selectRaw('SUM(nb_tot_latr_fam - nb_latrine_amelior) as total_non_ameliores')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites')
                ->where('rn', 1)
                ->first();

            $report['access_assainisement_familial']['non_ameliores'] = $nonAmeliores->total_non_ameliores ?? 0;
            $report['access_assainisement_familial']['nb_localite_non_ameliores'] = $nonAmeliores->nb_localites ?? 0;

            $menagesDal = DB::query()
                ->from(DB::raw("(
        SELECT 
            localite_id,
            nbr_menages_dal,
            ROW_NUMBER() OVER (PARTITION BY localite_id ORDER BY date_op DESC) as rn
        FROM localite_operations
        WHERE operation_id IN (2, 4)
          AND commune_id = {$commune_id}
          AND nbr_menages_dal IS NOT NULL
    ) as ranked"))
                ->selectRaw('SUM(nbr_menages_dal) as total_menages_dal')
                ->selectRaw('COUNT(DISTINCT localite_id) as nb_localites_menages_dal')
                ->where('rn', 1)
                ->first();
            $report['access_assainisement_familial']['men_dal'] = $menagesDal->total_menages_dal ?? 0;

            $report['access_assainisement_familial']['nb_localite_men_dal'] = $menagesDal->nb_localites_menages_dal ?? 0;

            $report['access_assainisement_familial']['securise'] = LocaliteReport::select(
                DB::raw('SUM(localite_operations.nbr_menages_latr_amel_adeq) - SUM(localite_operations.nbr_menages_latr_amel_vid_adeq_part) - SUM(localite_operations.nbr_menages_latr_amel_inadeq_part) as securise')
            )
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)
                ->value('securise');
            $report['access_assainisement_familial']['nb_localite_securise'] = LocaliteReport::select(
                DB::raw('count(id) as nb_localite_securise')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_latr_amel_adeq')
                ->where('localite_operations.commune_id', $commune_id)
                ->value('nb_localite_securise');

            $report['access_hygiene']['nbr_localites'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm')
                ->where('commune_id', $commune_id)
                ->value('nbr_localites');
            $report['access_hygiene']['nbr_localites_complet'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites_complet')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm_complet')
                ->where('commune_id', $commune_id)
                ->value('nbr_localites_complet');
            $report['access_hygiene']['nbr_localites_incomplet'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_localites_incomplet')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_dlm_incomplet')
                ->where('commune_id', $commune_id)
                ->value('nbr_localites_complet');
            $report['access_hygiene']['nbr_sans_dlm'] = LocaliteReport::select(
                DB::raw('count(id) as nbr_sans_dlm')
            )
                ->where('operation_id', 4)
                ->whereNotNull('nbr_menages_sans_dlm')
                ->where('commune_id', $commune_id)
                ->value('nbr_sans_dlm');



            $report['access_hygiene'] = LocaliteReport::select(DB::raw('
                SUM(nbr_menages_dlm) as nbr_menages_dlm,
                SUM(nbr_menages_dlm_complet) as nbr_menages_dlm_complet,
                SUM(nbr_menages_dlm_incomplet) as nbr_menages_dlm_incomplet,
                SUM(nbr_menages_sans_dlm) as nbr_menages_sans_dlm'))
                ->where('operation_id', 4)
                ->where('commune_id', $commune_id)

                ->first();

            $report['ecoles']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('infrastructures.infra_publ', 'Ecole')->get()->count();

            $report['ecoles']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
                ->groupBy('infrastructure_operations.approv_eau')
                ->where('operations.code', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('infrastructure_operations.approv_eau')
                ->where('infrastructures.infra_publ', 'Ecole')
                ->get();

            $report['ecoles']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as couvert_ecole_latr '),
                    DB::raw('SUM(effectif) as effectif '),
                    DB::raw('SUM(nbr_cabine_fonct) as nbr_cabine_fonct_sum '),

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_fonct', '>', 0)
                ->whereNotNull('nbr_cabine_fonct')->first();
            $report['ecoles']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_pmr', '>', 0)
                ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
            $report['ecoles']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('dist_cab_hom_fem', '>', 0)
                ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
            $report['ecoles']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as gest_dechet ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('gest_dechet', '>', 0)
                ->whereNotNull('gest_dechet')->value('gest_dechet');
            $report['ecoles']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nb_dlm_instal', '>', 0)
                ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');
            $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_satisf '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_a_const', 0)
                ->where('nbr_bloc_a_const', 0)
                ->whereNotNull('nbr_bloc_a_const')
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_ecole_besoin_latr_satisf');
            $report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_besoin_latr_non_satisf '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where(function ($q) {
                    $q->where('nbr_cabine_a_const', '>', 0)
                      ->orWhere('nbr_bloc_a_const', '>', 0);
                })
                ->where(function ($q) {
                    $q->whereNotNull('nbr_cabine_a_const')
                      ->orWhereNotNull('nbr_bloc_a_const');
                })
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_ecole_besoin_latr_non_satisf');
            $report['ecoles']['besoin_assainissement']['nb_ecole_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_ecole_sans_latr '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_exist', 0)
                ->whereNotNull('nbr_cabine_exist')
                ->value('nb_ecole_sans_latr');
            $report['ecoles']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
                )
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nbr_cabine_a_const');



            $report['ecoles']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_bloc_a_const', '>', 0)
                ->where('nbr_cabine_a_const', '>', 0)
                ->value('projet_besoins');
            $report['ecoles']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNull('nbr_bloc_a_const')
                ->whereNull('nbr_cabine_a_const')

                ->value('projet_sans_besoins');

            $report['ecoles']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->get();

            $report['ecoles']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->get();
            $report['ecoles']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('
                    SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                    SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                    SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->first();
            $report['ecoles']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nb_dlm_complet')
                ->value('nbr_inf_complet');
            $report['ecoles']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nb_dlm_incomplet')
                ->value('nbr_inf_incomplet');
            $report['ecoles']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nb_sans_dlm')
                ->value('nbr_inf_sans');

            $report['santes']['count'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')->get()->count();
            $report['santes']['approv_eau'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), 'infrastructure_operations.approv_eau')
                ->groupBy('infrastructure_operations.approv_eau')
                ->where('operations.code', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('infrastructure_operations.approv_eau')
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->get();

            $report['santes']['acces_assainissement'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as couvert_sante_latr '),
                    DB::raw('SUM(effectif) as effectif '),

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_fonct', '>', 0)
                ->whereNotNull('nbr_cabine_fonct')->first();
            $report['santes']['acces_assainissement']['nbr_cabine_pmr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_pmr ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_pmr', '>', 0)
                ->whereNotNull('nbr_cabine_pmr')->value('nbr_cabine_pmr');
            $report['santes']['acces_assainissement']['nbr_cabine_fem_hyg'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nbr_cabine_fem_hyg ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('dist_cab_hom_fem', '>', 0)
                ->whereNotNull('dist_cab_hom_fem')->value('nbr_cabine_fem_hyg');
            $report['santes']['acces_assainissement']['gest_dechet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as gest_dechet ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('gest_dechet', '>', 0)
                ->whereNotNull('gest_dechet')->value('gest_dechet');
            $report['santes']['acces_assainissement']['nb_dlm_instal'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_dlm_instal ')

                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nb_dlm_instal', '>', 0)
                ->whereNotNull('nb_dlm_instal')->value('nb_dlm_instal');

            $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_satisf '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_a_const', 0)
                ->where('nbr_bloc_a_const', 0)
                ->whereNotNull('nbr_bloc_a_const')
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_sante_besoin_latr_satisf');
            $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_non_satisf'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_besoin_latr_non_satisf '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where(function ($q) {
                    $q->where('nbr_cabine_a_const', '>', 0)
                      ->orWhere('nbr_bloc_a_const', '>', 0);
                })
                ->where(function ($q) {
                    $q->whereNotNull('nbr_cabine_a_const')
                      ->orWhereNotNull('nbr_bloc_a_const');
                })
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nb_sante_besoin_latr_non_satisf');
            $report['santes']['besoin_assainissement']['nb_sante_sans_latr'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('COUNT(infrastructure_id) as nb_sante_sans_latr '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_cabine_exist', 0)
                ->whereNotNull('nbr_cabine_exist')
                ->value('nb_sante_sans_latr');
            $report['santes']['besoin_assainissement']['nbr_cabine_a_const'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(
                    DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const '),
                )
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nbr_cabine_a_const')
                ->value('nbr_cabine_a_const');



            $report['santes']['projet_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('nbr_bloc_a_const', '>', 0)
                ->where('nbr_cabine_a_const', '>', 0)
                ->value('projet_besoins');
            $report['santes']['projet_sans_besoins'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as projet_sans_besoins'),)
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNull('nbr_bloc_a_const')
                ->whereNull('nbr_cabine_a_const')

                ->value('projet_sans_besoins');

            $report['santes']['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'), DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_recept')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->get();

            $report['santes']['projets_perspectives'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(DB::raw('COUNT(infrastructures.id) as infrastructures'), DB::raw('SUM(nbr_cabine_a_rehab) as nbr_cabine_a_rehab'), DB::raw('SUM(nbr_cabine_a_const) as nbr_cabine_a_const'), 'projets.intitule_resume', 'infrastructures.infra_publ', 'operateurs.intitule', 'an_program')
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_program', 'operateurs.intitule')
                ->where('operation_id', 7)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->get();
            $report['santes']['access_hygiene'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('
                    SUM(infrastructure_operations.nb_dlm_complet) as nb_dlm_complet,
                    SUM(infrastructure_operations.nb_dlm_incomplet) as nb_dlm_incomplet,
                    SUM(infrastructure_operations.nb_sans_dlm) as nb_sans_dlm'))
                ->where('operation_id', 5)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->first();
            $report['santes']['access_hygiene']['nbr_inf_complet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_complet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nb_dlm_complet')
                ->value('nbr_inf_complet');
            $report['santes']['access_hygiene']['nbr_inf_incomplet'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_incomplet'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nb_dlm_incomplet')
                ->value('nbr_inf_incomplet');
            $report['santes']['access_hygiene']['nbr_inf_sans'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->select(DB::raw('COUNT(infrastructure_id) as nbr_inf_sans'),)
                ->where('operation_id', 5)
                ->where('infrastructures.infra_publ', '<>', 'Ecole')
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->whereNotNull('nb_sans_dlm')
                ->value('nbr_inf_sans');
        }
        return response()->json($report);
    }
    public function fetch_reporting_localite()
    {

        $localite_reports = LocaliteReport::pluck('wilaya_id');
        $wilayas = Wilaya::whereIn('id', $localite_reports)->orderBy('intitule_fr')->get();
        return view('admin.reportings.reporting_localite', ['wilayas' => $wilayas]);
    }

    public function fetch_reporting_localite_api(Request $request)
    {

        $localite_id = $request->localite_id;
        $report = array();

        $report['info'] = Localite::join('wilayas', 'localites.wilaya_id', 'wilayas.id')
            ->join('moughatas', 'localites.moughata_id', 'moughatas.id')
            ->join('communes', 'localites.commune_id', 'communes.id')
            ->select('wilayas.intitule as wilaya', 'moughatas.intitule as moughata', 'communes.intitule as commune', 'localites.*')
            ->where('localites.id', $localite_id)
            ->first();

        $report['info_infrastructure_ecole'] = Infrastructure::select('infrastructures.*')
            ->where('infrastructures.infra_publ', 'Ecole')
            ->where('infrastructures.localite_id', $localite_id)
            ->get();
        $report['info_infrastructure_sante'] = Infrastructure::select('infrastructures.*')
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->where('infrastructures.localite_id', $localite_id)
            ->get();

        $report['approv'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('approv_eau')
            ->where('localites.id', $localite_id)
            ->whereNotNull('approv_eau')
            ->pluck('approv_eau');

        $report['projets_atcp'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('projets.intitule_resume', DB::raw('YEAR(date_op) as year'), 'operateurs.intitule')
            ->groupBy('projets.intitule_resume', 'operateurs.intitule', 'year')
            ->where('localites.id', $localite_id)
            ->whereIn('operations.code', [1, 2, 4])
            ->get();

        $report['operations'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')

            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('operations.intitule', 'date_op', 'nb_tot_pop', 'nb_menages_comptabilises', 'nb_tot_latr_fam', 'nbr_menages_dal')
            ->where('localites.id', $localite_id)
            ->orderBy('date_op')
            ->orderBy('operations.code')
            ->get();



        $report['auto_finance'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_autofin) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as auto_finance'))
            ->where('operations.code', 4)
            ->where('localite_operations.localite_id', $localite_id)
            ->value('auto_finance');

        $report['nb_menages_inv'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->where('operations.code', 4)
            ->where('nb_latr_invest_men_dispo', '>', 0)
            ->where('localite_operations.localite_id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->max('nb_latr_invest_men_dispo');

        $report['investi'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->where('operations.code', 4)
            ->where('localite_operations.localite_id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->max('mont_invest_men');

        $report['investi_menages'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COALESCE(Max(localite_operations.mont_invest_men) / NULLIF(Max(localite_operations.nb_latr_invest_men_dispo), 0), 0) as montant, operations.id'))
            ->groupBy('operations.id')
            ->where('operations.code', 4)
            ->where('localite_operations.localite_id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->value('montant');


        $report['latrines_entrainde'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COALESCE(SUM(localite_operations.nbr_latrine_entraid) * 100.0 / NULLIF(SUM(localite_operations.nbr_latrine_realise), 0), 0) as latrines_entrainde, operations.id'))
            ->groupBy('operations.id')
            ->where('operations.code', 4)
            ->where('nbr_latrine_realise', '>', 0)
            ->where('localite_operations.localite_id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->value('latrines_entrainde');

        $report['latrines_subv'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select(DB::raw('COALESCE(sum(localite_operations.nbr_latrine_subv) * 100.0 / NULLIF(sum(localite_operations.nbr_latrine_realise), 0), 0) as latrines_subv, operations.id'))
            ->groupBy('operations.id')
            ->where('operations.code', 4)
            ->where('nbr_latrine_realise', '>', 0)
            ->where('localite_operations.localite_id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->value('latrines_subv');
        $report['nb_menages_comptabilises'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('nb_menages_comptabilises')
            ->where('operations.code', 2)
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->value('nb_menages_comptabilises');
        $report['securise'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select(DB::raw('nbr_menages_latr_amel_adeq - (nbr_menages_latr_amel_vid_adeq_part + nbr_menages_latr_amel_inadeq_part) as securise'))
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nbr_menages_latr_amel_adeq', 'desc')
            ->orderBy('nbr_menages_latr_amel_vid_adeq_part', 'desc')
            ->orderBy('nbr_menages_latr_amel_inadeq_part', 'desc')
            ->value('securise');



        $report['limite'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select(DB::raw('(nbr_menages_ltr_vsn + (nbr_menages_latr_amel_vid_adeq_part + nbr_menages_latr_amel_inadeq_part)) as limite'))
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nbr_menages_ltr_vsn', 'desc')
            ->orderBy('nbr_menages_latr_amel_vid_adeq_part', 'desc')
            ->orderBy('nbr_menages_latr_amel_inadeq_part', 'desc')

            ->value('limite');

        $report['non_amelior'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select(DB::raw('nb_latrine_non_amelior as non_amelior'))

            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nb_latrine_non_amelior', 'desc')
            ->value('non_amelior');


        $report['nbr_menages_dal'] = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('localite_operations.nbr_menages_dal')
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nbr_menages_dal', 'desc')
            ->value('nbr_menages_dal');

        $report['basique'] = 0;
        $report['nbr_menages_dlm'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('localite_operations.nbr_menages_dlm')
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nbr_menages_dlm', 'desc')
            ->value('nbr_menages_dlm');
        $report['nbr_menages_dlm_complet'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('localite_operations.nbr_menages_dlm_complet')
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nbr_menages_dlm_complet', 'desc')
            ->value('nbr_menages_dlm_complet');
        $report['nbr_menages_dlm_incomplet'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('localite_operations.nbr_menages_dlm_incomplet')
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nbr_menages_dlm_incomplet', 'desc')
            ->value('nbr_menages_dlm_incomplet');
        $report['nbr_menages_sans_dlm'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->select('localite_operations.nbr_menages_sans_dlm')
            ->where('localites.id', $localite_id)
            ->orderBy('date_op', 'desc')
            ->orderBy('nbr_menages_sans_dlm', 'desc')
            ->value('nbr_menages_sans_dlm');

        $report['info_ecoles'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                'infrastructures.infra_publ',
                'infrastructures.intitule_infra_publ',
                'infrastructures.code_men',
                'infrastructure_operations.date_op',
                'infrastructure_operations.approv_eau',
                'infrastructure_operations.dist_approv_eau',
                'infrastructure_operations.effectif_garcon',
                'infrastructure_operations.effectif_fille',
                'infrastructure_operations.effectif',
                'infrastructure_operations.nbr_bloc',
                'infrastructure_operations.nbr_cabine_exist',
                'infrastructure_operations.nbr_cabine_fonct',
                DB::raw('(CASE WHEN infrastructure_operations.nbr_bloc_a_const = 0 AND infrastructure_operations.nbr_cabine_a_const = 0 THEN "OUI" ELSE "NR" END) as besoin_latr_satisf'),
                DB::raw('(CASE WHEN infrastructure_operations.dist_cab_hom_fem = 0 THEN "NR" ELSE "OUI" END) as dist_cab_hom_fem'),
                DB::raw('(CASE WHEN infrastructure_operations.nbr_cabine_fem_hyg = 0 THEN "NR" ELSE "OUI" END) as nbr_cabine_fem_hyg'),
                DB::raw('(CASE WHEN infrastructure_operations.nbr_cabine_pmr = 0 THEN "NR" ELSE "OUI" END) as nbr_cabine_pmr'),
                'infrastructure_operations.nb_dlm',
                DB::raw('(CASE WHEN infrastructure_operations.nb_dlm_complet > 0 THEN "OUI" ELSE "NR" END) as nb_dlm_complet'),
                DB::raw('(CASE WHEN infrastructure_operations.nb_dlm_incomplet > 0 THEN "OUI" ELSE "NR" END) as nb_dlm_incomplet'),
                DB::raw('(CASE WHEN infrastructure_operations.nb_sans_dlm > 0 THEN "OUI" ELSE "NR" END) as nb_sans_dlm'),
                DB::raw('(CASE WHEN infrastructure_operations.operation_id = 7 THEN 0 ELSE nbr_cabine_a_rehab END) as nbr_cabine_a_rehab'),
                DB::raw('(CASE WHEN infrastructure_operations.operation_id = 7 THEN 0 ELSE nbr_cabine_a_const END) as nbr_cabine_a_const'),
                DB::raw('(CASE WHEN infrastructure_operations.operation_id = 7 THEN 0 ELSE an_program END) as an_program')
            )

            ->where('infrastructure_operations.localite_id', $localite_id)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', 'Ecole')
            ->orderBy('infrastructure_operations.date_op', 'DESC')
            ->distinct()
            ->get();


        $report['projets_ecoles'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->select(
                DB::raw('YEAR(date_op) as year'),
                'projets.intitule_resume',
                'infrastructures.intitule_infra_publ',
                'operateurs.intitule',
                'infrastructure_operations.nbr_cabine_a_rehab',
                'infrastructure_operations.nbr_cabine_a_const'
            )
            ->where('infrastructure_operations.localite_id', $localite_id)
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', 'Ecole')
            ->orderBy('infrastructure_operations.date_op', 'DESC')
            ->distinct()
            ->get();


        foreach ($report['info_ecoles'] as $key => $value) {
            $i = 0;
            foreach ($report['projets_ecoles'] as $k => $val) {
                if ($value->intitule_infra_publ == $val->intitule_infra_publ) {
                    $report['info_ecoles'][$key][$i++] = $report['projets_ecoles'][$k];
                }
            }
        }

        $report['info_santes'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->select(
                'infrastructures.infra_publ',
                'infrastructures.intitule_infra_publ',
                'infrastructure_operations.date_op',
                'infrastructure_operations.approv_eau',
                'infrastructure_operations.dist_approv_eau',
                'infrastructure_operations.effectif_garcon',
                'infrastructure_operations.effectif_fille',
                'infrastructure_operations.effectif',
                'infrastructure_operations.nbr_bloc',
                'infrastructure_operations.nbr_cabine_exist',
                'infrastructure_operations.nbr_cabine_fonct',
                DB::raw('(CASE WHEN infrastructure_operations.nbr_bloc_a_const = 0 AND infrastructure_operations.nbr_cabine_a_const = 0 THEN "OUI" ELSE "NR" END) as besoin_latr_satisf'),
                DB::raw('(CASE WHEN infrastructure_operations.dist_cab_hom_fem = 0 THEN "NR" ELSE "OUI" END) as dist_cab_hom_fem'),
                DB::raw('(CASE WHEN infrastructure_operations.nbr_cabine_fem_hyg = 0 THEN "NR" ELSE "OUI" END) as nbr_cabine_fem_hyg'),
                DB::raw('(CASE WHEN infrastructure_operations.nbr_cabine_pmr = 0 THEN "NR" ELSE "OUI" END) as nbr_cabine_pmr'),
                'infrastructure_operations.nb_dlm',
                DB::raw('(CASE WHEN infrastructure_operations.nb_dlm_complet > 0 THEN "OUI" ELSE "NR" END) as nb_dlm_complet'),
                DB::raw('(CASE WHEN infrastructure_operations.nb_dlm_incomplet > 0 THEN "OUI" ELSE "NR" END) as nb_dlm_incomplet'),
                DB::raw('(CASE WHEN infrastructure_operations.nb_sans_dlm > 0 THEN "OUI" ELSE "NR" END) as nb_sans_dlm'),
                DB::raw('(CASE WHEN infrastructure_operations.operation_id = 7 THEN 0 ELSE nbr_cabine_a_rehab END) as nbr_cabine_a_rehab'),
                DB::raw('(CASE WHEN infrastructure_operations.operation_id = 7 THEN 0 ELSE nbr_cabine_a_const END) as nbr_cabine_a_const'),
                DB::raw('(CASE WHEN infrastructure_operations.operation_id = 7 THEN 0 ELSE an_program END) as an_program')
            )

            ->where('infrastructure_operations.localite_id', $localite_id)
            ->where('operation_id', 5)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->orderBy('infrastructure_operations.date_op', 'DESC')
            ->distinct()
            ->get();
        $report['projets_santes'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->select(
                DB::raw('YEAR(date_op) as year'),
                'projets.intitule_resume',
                'infrastructures.intitule_infra_publ',
                'operateurs.intitule',
                'infrastructure_operations.nbr_cabine_a_rehab',
                'infrastructure_operations.nbr_cabine_a_const'
            )
            ->where('infrastructure_operations.localite_id', $localite_id)
            ->where('operation_id', 7)
            ->where('infrastructures.infra_publ', '<>', 'Ecole')
            ->orderBy('infrastructure_operations.date_op', 'DESC')
            ->distinct()
            ->get();


        foreach ($report['info_santes'] as $key => $value) {
            $i = 0;
            foreach ($report['projets_santes'] as $k => $val) {
                if ($value->intitule_infra_publ == $val->intitule_infra_publ) {
                    $report['info_santes'][$key][$i++] = $report['projets_santes'][$k];
                }
            }
        }

        return response()->json($report);
    }


    public function fetch_reporting_annuel()
    {
        $localite_reports = LocaliteReport::pluck('wilaya_id');
        $wilayas = Wilaya::whereIn('id', $localite_reports)->orderBy('intitule_fr')->get();
        return view('admin.reportings.reporting_annuel', ['wilayas' => $wilayas]);
    }

    public function fetch_reporting_annuel_api(Request $request)
    {

        $year = $request->annee;
        if (isset($request->commune_id)) {
            $commune_id = $request->commune_id;
            # code...
            $report = array();
            $report['territoire'] = Commune::where('id', $commune_id)->value('intitule');

            $report['fdal'] = LocaliteReport::select(
                DB::raw('YEAR(date_op) as year'),
                DB::raw('COUNT(DISTINCT localite_id) as localites')
            )
                ->where('operation_id', 3)
                ->whereYear('date_op', $year)
                ->where('localite_operations.commune_id', $commune_id)
                ->groupBy('year')
                ->value('localites');
            $dech = LocaliteReport::select('localite_id')
                ->where('commune_id', $commune_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', '<', $year);
            $report['declenches'] = LocaliteReport::select('localite_id')
                ->where('commune_id', $commune_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereNotIn('localite_id', $dech)
                ->count();
            $report['redeclenches'] = LocaliteReport::select('localite_id')
                ->where('commune_id', $commune_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $dech)
                ->count();
            $report['menages'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->where('localite_operations.commune_id', $commune_id)
                ->sum('localite_operations.nb_menages_comptabilises');
            $report['population'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->where('commune_id', $commune_id)
                ->sum('nb_tot_pop');

            $fdal = LocaliteReport::select('localite_id')
                ->where('commune_id', $commune_id)
                ->where('operation_id', 3)
                ->whereYear('date_op', $year)
                ->get()->pluck('localite_id');

            $sum1 = LocaliteReport::where('commune_id', $commune_id)
                ->where('operation_id', 4)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');
            $sum2 = LocaliteReport::where('commune_id', $commune_id)
                ->where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');

            $report['realise'] = $sum1 - $sum2;
            $dech_1 = LocaliteReport::select('localite_id')
                ->where('commune_id', $commune_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', '=', $year)
                ->distinct()
                ->get()->pluck('localite_id');
            $certif_parmi_dech_commune = DB::table('localite_operations')
                ->where('commune_id', $commune_id)
                ->where('operation_id', 3)
                ->whereIn('localite_id', $dech_1)
                ->distinct('localite_id')
                ->count('localite_id');
            $report['non_certif'] = $dech_1->count() - $certif_parmi_dech_commune;
            $report['appui'] = LocaliteReport::select(
                DB::raw('YEAR(date_op) as year'),
                DB::raw('COUNT(DISTINCT localite_id) as localites'),
                DB::raw('SUM(nb_tot_pop) as nb_tot_pop'),
                DB::raw('SUM(nb_menages_comptabilises) as nb_menages_comptabilises'),
            )
                ->whereIn('localite_id', function ($query) {
                    $query->select('localite_id')
                        ->from('localite_operations')
                        ->distinct()
                        ->where('operation_id', 3);
                })
                ->whereYear('date_op', $year)
                ->where('localite_operations.commune_id', $commune_id)
                ->groupBy('year')
                ->first();


            $report['projets'] = LocaliteReport::join('projets', 'projets.id', 'localite_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'localite_operations.operateur_id')
                ->join('wilayas', 'wilayas.id', 'localite_operations.commune_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'wilayas.intitule as wilaya')

                ->whereIn('localite_operations.operation_id', [1, 2, 4])
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('localite_operations.commune_id', $commune_id)
                ->get();
            $report['projets_post_fdal'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
                ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
                ->join('communes', 'localite_operations.commune_id', 'communes.id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'communes.intitule as wilaya')

                ->where('localite_operations.operation_id', 3)
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('localite_operations.commune_id', $commune_id)
                ->get();
            $report['projets_p'] = InfrastructureReport::join('projets', 'projets.id', 'infrastructure_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'infrastructure_operations.operateur_id')
                ->join('wilayas', 'wilayas.id', 'infrastructure_operations.commune_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'wilayas.intitule as wilaya')

                ->where('infrastructure_operations.operation_id', 6)
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->get();
            $report['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(
                    DB::raw('COUNT(infrastructures.id) as infrastructures'),
                    DB::raw('SUM(effectif) as effectif'),
                    DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'),
                    DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'),
                    DB::raw('SUM(nbr_bloc_const) as nbr_bloc_const'),
                    'projets.intitule_resume',
                    'infrastructures.infra_publ',
                    'operateurs.intitule',

                )
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->whereYear('date_op', $year)
                ->where('infrastructure_operations.commune_id', $commune_id)
                ->get();

            return response()->json($report);
        }

        if (isset($request->moughata_id)) {
            $moughata_id = $request->moughata_id;

            # code...
            $report = array();

            $report['territoire'] = Moughata::where('id', $moughata_id)->value('intitule');
            $report['fdal'] = LocaliteReport::select(
                DB::raw('YEAR(date_op) as year'),
                DB::raw('COUNT(DISTINCT localite_id) as localites')
            )
                ->where('operation_id', 3)
                ->whereYear('date_op', $year)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->groupBy('year')
                ->value('localites');
            $dech = LocaliteReport::select('localite_id')
                ->where('moughata_id', $moughata_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', '<', $year);
            $report['declenches'] = LocaliteReport::select('localite_id')
                ->where('moughata_id', $moughata_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereNotIn('localite_id', $dech)
                ->count();
            $report['redeclenches'] = LocaliteReport::select('localite_id')
                ->where('moughata_id', $moughata_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $dech)
                ->count();
            $report['menages'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->sum('localite_operations.nb_menages_comptabilises');
            $report['population'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->where('moughata_id', $moughata_id)
                ->sum('nb_tot_pop');

            $fdal = LocaliteReport::select('localite_id')
                ->where('moughata_id', $moughata_id)
                ->where('operation_id', 3)
                ->whereYear('date_op', $year)
                ->get()->pluck('localite_id');

            $sum1 = LocaliteReport::where('moughata_id', $moughata_id)
                ->where('operation_id', 4)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');
            $sum2 = LocaliteReport::where('moughata_id', $moughata_id)
                ->where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');

            $report['realise'] = $sum1 - $sum2;
            $dech_1 = LocaliteReport::select('localite_id')
                ->where('moughata_id', $moughata_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', '=', $year)
                ->distinct()
                ->get()->pluck('localite_id');
            $certif_parmi_dech_moughata = DB::table('localite_operations')
                ->where('moughata_id', $moughata_id)
                ->where('operation_id', 3)
                ->whereIn('localite_id', $dech_1)
                ->distinct('localite_id')
                ->count('localite_id');
            $report['non_certif'] = $dech_1->count() - $certif_parmi_dech_moughata;
            $report['appui'] = LocaliteReport::select(
                DB::raw('YEAR(date_op) as year'),
                DB::raw('COUNT(DISTINCT localite_id) as localites'),
                DB::raw('SUM(nb_tot_pop) as nb_tot_pop'),
                DB::raw('SUM(nb_menages_comptabilises) as nb_menages_comptabilises'),
            )
                ->whereIn('localite_id', function ($query) {
                    $query->select('localite_id')
                        ->from('localite_operations')
                        ->distinct()
                        ->where('operation_id', 3);
                })
                ->whereYear('date_op', $year)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->groupBy('year')
                ->first();


            $report['projets'] = LocaliteReport::join('projets', 'projets.id', 'localite_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'localite_operations.operateur_id')
                ->join('communes', 'communes.id', 'localite_operations.commune_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'communes.intitule as wilaya')

                ->whereIn('localite_operations.operation_id', [1, 2, 4])
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get();
            $report['projets_post_fdal'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
                ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
                ->join('communes', 'communes.id', 'localite_operations.commune_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'communes.intitule as wilaya')

                ->where('localite_operations.operation_id', 3)
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('localite_operations.moughata_id', $moughata_id)
                ->get();
            $report['projets_p'] = InfrastructureReport::join('projets', 'projets.id', 'infrastructure_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'infrastructure_operations.operateur_id')
                ->join('wilayas', 'wilayas.id', 'infrastructure_operations.moughata_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'wilayas.intitule as wilaya')

                ->where('infrastructure_operations.operation_id', 6)
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->get();
            $report['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(
                    DB::raw('COUNT(infrastructures.id) as infrastructures'),
                    DB::raw('SUM(effectif) as effectif'),
                    DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'),
                    DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'),
                    DB::raw('SUM(nbr_bloc_const) as nbr_bloc_const'),
                    'projets.intitule_resume',
                    'infrastructures.infra_publ',
                    'operateurs.intitule',

                )
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->whereYear('date_op', $year)
                ->where('infrastructure_operations.moughata_id', $moughata_id)
                ->get();

            return response()->json($report);
        }
        if (isset($request->wilaya_id)) {
            $wilaya_id = $request->wilaya_id;

            # code...
            $report = array();
            $report['territoire'] = Wilaya::where('id', $wilaya_id)->value('intitule');
            $report['fdal'] = LocaliteReport::select(
                DB::raw('COUNT(localite_id) as localites')
            )
                ->where('operation_id', 3)
                ->whereYear('date_op', $year)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->value('localites');

            $dech = LocaliteReport::select('localite_id')
                ->where('wilaya_id', $wilaya_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', '<', $year);
            $report['declenches'] = LocaliteReport::select('localite_id')
                ->where('wilaya_id', $wilaya_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereNotIn('localite_id', $dech)
                ->count();
            $report['redeclenches'] = LocaliteReport::select('localite_id')
                ->where('wilaya_id', $wilaya_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $dech)
                ->count();
            $report['menages'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->sum('localite_operations.nb_menages_comptabilises');
            $report['population'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->where('wilaya_id', $wilaya_id)
                ->sum('nb_tot_pop');

            $fdal = LocaliteReport::select('localite_id')
                ->where('wilaya_id', $wilaya_id)
                ->where('operation_id', 3)
                ->whereYear('date_op', $year)
                ->get()->pluck('localite_id');

            $sum1 = LocaliteReport::where('wilaya_id', $wilaya_id)
                ->where('operation_id', 4)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');
            $sum2 = LocaliteReport::where('wilaya_id', $wilaya_id)
                ->where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');

            $report['realise'] = $sum1 - $sum2;
            $dech_1 = LocaliteReport::select('localite_id')
                ->where('wilaya_id', $wilaya_id)
                ->where('operation_id', 1)
                ->whereYear('date_op', '=', $year)
                ->distinct()
                ->get()->pluck('localite_id');
            // Localités certifiées parmi celles déclenchées en $year (toute certification, pas seulement en $year)
            $certif_parmi_dech = DB::table('localite_operations')
                ->where('wilaya_id', $wilaya_id)
                ->where('operation_id', 3)
                ->whereIn('localite_id', $dech_1)
                ->distinct('localite_id')
                ->count('localite_id');
            $report['non_certif'] = $dech_1->count() - $certif_parmi_dech;
            $nb_loc_benefic_appui = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(localites.id) as localites'),
                    'localites.id'
                )
                ->where('operations.code', 4)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->whereYear('date_op', $year)
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->get();
            $report['appui'] = $nb_loc_benefic_appui->count();
            $localite_id = $nb_loc_benefic_appui->pluck('id');

            $report['appui_realise'] = LocaliteReport::select(
                DB::raw('SUM(nbr_latrine_realise) as nbr_latrine_realise')
            )
                ->where('operation_id', 4)
                ->whereYear('date_op', $year)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->whereIn('localite_id', $localite_id)
                ->value('nbr_latrine_realise');


            $report['appui_nb_menages_comptabilises'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->select(
                    DB::raw('SUM(nb_menages_comptabilises) as nb_menages_comptabilises')
                )
                ->whereIn('localites.id', $localite_id)
                ->value('nb_menages_comptabilises');

            $report['appui_nb_tot_pop'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->select(
                    DB::raw('SUM(nb_tot_pop) as nb_tot_pop')
                )
                ->whereIn('localites.id', $localite_id)
                ->value('nb_tot_pop');
            $report['projets'] = LocaliteReport::join('projets', 'projets.id', 'localite_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'localite_operations.operateur_id')
                ->join('moughatas', 'moughatas.id', 'localite_operations.moughata_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'moughatas.intitule as wilaya')

                ->whereIn('localite_operations.operation_id', [1, 2, 4])
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get();
            $report['projets_post_fdal'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
                ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
                ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'moughatas.intitule as wilaya')

                ->whereIn('localite_operations.operation_id', [1, 2, 4])
                ->whereIn('localite_id', $localite_id)
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('localite_operations.wilaya_id', $wilaya_id)
                ->get();
            $report['projets_p'] = InfrastructureReport::join('projets', 'projets.id', 'infrastructure_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'infrastructure_operations.operateur_id')
                ->join('wilayas', 'wilayas.id', 'infrastructure_operations.wilaya_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'wilayas.intitule as wilaya')

                ->where('infrastructure_operations.operation_id', 6)
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->get();
            $report['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(
                    DB::raw('COUNT(infrastructures.id) as infrastructures'),
                    DB::raw('SUM(effectif) as effectif'),
                    DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'),
                    DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'),
                    DB::raw('SUM(nbr_bloc_const) as nbr_bloc_const'),
                    'projets.intitule_resume',
                    'infrastructures.infra_publ',
                    'operateurs.intitule',

                )
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->distinct()
                ->whereYear('date_op', $year)
                ->where('infrastructure_operations.wilaya_id', $wilaya_id)
                ->get();

            return response()->json($report);
        }

        if (isset($request->annee)) {
            $year = $request->annee;
            # code...
            $report = array();
            $report['territoire'] = null;
            $report['fdal'] = LocaliteReport::select(DB::raw('COUNT(localite_id) as localites'))
                ->where('operation_id', 3)
                ->whereYear('date_op', $year)
                ->value('localites');

            $dech = LocaliteReport::select('localite_id')
                ->where('operation_id', 1)
                ->whereYear('date_op', '<', $year);
            $report['declenches'] = LocaliteReport::select('localite_id')
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereNotIn('localite_id', $dech)
                ->count();
            $report['redeclenches'] = LocaliteReport::select('localite_id')
                ->where('operation_id', 1)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $dech)
                ->count();
            $report['menages'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->sum('localite_operations.nb_menages_comptabilises');
            $report['population'] = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->sum('nb_tot_pop');

            $fdal = LocaliteReport::select('localite_id')
                ->where('operation_id', 3)
                ->get()->pluck('localite_id');

            $sum1 = LocaliteReport::where('operation_id', 4)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');
            $sum2 = LocaliteReport::where('operation_id', 2)
                ->whereYear('date_op', $year)
                ->whereIn('localite_id', $fdal)
                ->sum('nb_tot_latr_fam');
            $report['realise'] = $sum1 - $sum2;
            $dech_1 = LocaliteReport::select('localite_id')
                ->where('operation_id', 1)
                ->whereYear('date_op', '=', $year)
                ->distinct()
                ->get()->pluck('localite_id');
            $certif_parmi_dech_global = DB::table('localite_operations')
                ->where('operation_id', 3)
                ->whereIn('localite_id', $dech_1)
                ->distinct('localite_id')
                ->count('localite_id');
            $report['non_certif'] = $dech_1->count() - $certif_parmi_dech_global;
            $nb_loc_benefic_appui = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->join('operations', 'localite_operations.operation_id', 'operations.id')
                ->select(
                    DB::raw('COUNT(localites.id) as localites'),
                    'localites.id'
                )
                ->where('operations.code', 4)

                ->whereYear('date_op', $year)
                ->groupBy('localites.id')
                ->havingRaw("COUNT(localites.id) > 1")
                ->get();
            $report['appui'] = $nb_loc_benefic_appui->count();
            $localite_id = $nb_loc_benefic_appui->pluck('id');

            $report['appui_realise'] = LocaliteReport::select(
                DB::raw('SUM(nbr_latrine_realise) as nbr_latrine_realise')
            )
                ->where('operation_id', 4)
                ->whereYear('date_op', $year)

                ->whereIn('localite_id', $localite_id)
                ->value('nbr_latrine_realise');


            $report['appui_nb_menages_comptabilises'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->select(
                    DB::raw('SUM(nb_menages_comptabilises) as nb_menages_comptabilises')
                )
                ->whereIn('localites.id', $localite_id)
                ->value('nb_menages_comptabilises');

            $report['appui_nb_tot_pop'] = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
                ->select(
                    DB::raw('SUM(nb_tot_pop) as nb_tot_pop')
                )
                ->whereIn('localites.id', $localite_id)
                ->value('nb_tot_pop');
            $report['projets'] = LocaliteReport::join('projets', 'projets.id', 'localite_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'localite_operations.operateur_id')
                ->join('wilayas', 'wilayas.id', 'localite_operations.wilaya_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'wilayas.intitule as wilaya')

                ->whereIn('localite_operations.operation_id', [1, 2, 4])
                ->distinct()
                ->whereYear('date_op', $year)

                ->get();
            $report['projets_post_fdal'] = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
                ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
                ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'wilayas.intitule as wilaya')

                ->whereIn('localite_operations.operation_id', [1, 2, 4])
                ->whereIn('localite_id', $localite_id)
                ->distinct()
                ->whereYear('date_op', $year)

                ->get();
            $report['projets_p'] = InfrastructureReport::join('projets', 'projets.id', 'infrastructure_operations.projet_id')
                ->join('operateurs', 'operateurs.id', 'infrastructure_operations.operateur_id')
                ->join('wilayas', 'wilayas.id', 'infrastructure_operations.wilaya_id')
                ->select('projets.intitule_resume', 'operateurs.intitule', 'wilayas.intitule as wilaya')

                ->where('infrastructure_operations.operation_id', 6)
                ->distinct()
                ->whereYear('date_op', $year)

                ->get();
            $report['projets_passees'] = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
                ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
                ->select(
                    DB::raw('COUNT(infrastructures.id) as infrastructures'),
                    DB::raw('SUM(effectif) as effectif'),
                    DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'),
                    DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'),
                    DB::raw('SUM(nbr_bloc_const) as nbr_bloc_const'),
                    'projets.intitule_resume',
                    'infrastructures.infra_publ',
                    'operateurs.intitule',

                )
                ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume', 'an_recept', 'operateurs.intitule')
                ->where('operation_id', 6)
                ->distinct()
                ->whereYear('date_op', $year)

                ->get();

            return response()->json($report);
        }
    }
    function fetch_reporting_projet()
    {




        $projets = Projet::select('*')->orderBy('intitule_resume')->get();


        return view('admin.reportings.reporting_projet', ['projets' => $projets]);
    }
    public function fetch_reporting_projet_api(Request $request)
    {
        $report = array();

        if (isset($request->projet_id)) {
            $projet_id = $request->projet_id;
            $operateur_id = $request->operateur_id ?? null;

            // 1. DONNÉES GÉNÉRALES - Optimisées
            $report['g'] = $this->getGeneralProjectData($projet_id, $operateur_id);
            $report['g2'] = $this->getProjectWilayas($projet_id, $operateur_id);
            $report['g3'] = $this->getProjectOperateurs($projet_id, $operateur_id);
            $report['g2_infrastructures'] = $this->getInfrastructureWilayas($projet_id, $operateur_id);
            $report['g3_infrastructures'] = $this->getInfrastructureOperateurs($projet_id, $operateur_id);

            // 2. INDICATEURS DE BASE - Corrigés
            $report['villages_declenches'] = $this->getVillagesDeclenches($projet_id, $operateur_id);

            $report['villages_redeclenches'] = $this->getVillagesRedeclenches($projet_id, $operateur_id);

            $localiteIds = $this->getVillagesDeclenchesIds($projet_id, $operateur_id);
            $report['population'] = $this->getProjectPopulation($projet_id, $operateur_id, $localiteIds);
            $report['menages'] = $this->getProjectMenages($projet_id, $operateur_id, $localiteIds);



            // 3. INDICATEURS FDAL - OPERATEUR
            $report['fdal'] = $this->getFDALStatistics($projet_id, $operateur_id);

            // 4. INDICATEURS DE CONSTRUCTION
            $max4Ids = $this->getMaxOperationIds($projet_id, 4, $operateur_id);

            // Remplacer tout votre code répétitif par :
            $constructionIndicators = $this->getConstructionIndicators($max4Ids, $projet_id, $operateur_id);
            $financialIndicators = $this->getFinancialIndicators($max4Ids, $projet_id, $operateur_id);

            // 8. INDICATEURS DE COMPARAISON AVANT/APRÈS
            $report = array_merge($report, $this->getComparisonIndicators($projet_id, $operateur_id));




            // Fusionner les résultats
            $report['r'] = array_merge($constructionIndicators, $financialIndicators);
            $report = array_merge($report, $this->getEtatLieuxApresCertificationStats($projet_id, $operateur_id));

            $maxOperationIds = $this->getMaxOperationIdsAfterCertification($projet_id, 2, $operateur_id);

            // 6. INDICATEURS POST-FDAL - Optimisés
            $report['r3'] = array_merge($report, $this->getPostFDALIndicators($maxOperationIds, $projet_id, $operateur_id));

            // 5. ÉVOLUTION ASSAINISSEMENT (Comparaison avant/après appui)
            $report = array_merge($report, $this->getEvolutionAfterCertification($projet_id, $operateur_id));



            // 7. INFRASTRUCTURES
            $report['projets'] = $this->getInfrastructureProjects($projet_id, $operateur_id);
        }

        return response()->json($report);
    }

    // MÉTHODES HELPER OPTIMISÉES

    private function getGeneralProjectData($projet_id, $operateur_id = null)
    {
        $query = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
            ->select(
                DB::raw("MAX(YEAR(date_op)) as max_year"),
                DB::raw("MIN(YEAR(date_op)) as min_year"),
                'projets.intitule_resume as projet'
            )
            ->groupBy('projets.id')
            ->where('projet_id', $projet_id)
            ->whereNotNull('date_op');

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
        }

        $result = $query->first();

        if (!$result) {
            $result = InfrastructureReport::join('projets', 'infrastructure_operations.projet_id', 'projets.id')
                ->select(
                    DB::raw("MAX(YEAR(date_op)) as max_year"),
                    DB::raw("MIN(YEAR(date_op)) as min_year"),
                    'projets.intitule_resume as projet'
                )
                ->groupBy('projets.id')
                ->where('projet_id', $projet_id);

            if ($operateur_id) {
                $result->where('operateur_id', $operateur_id);
            }

            $result = $result->first();
        }

        return $result;
    }

    private function getProjectWilayas($projet_id, $operateur_id = null)
    {
        $query = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->select('wilayas.intitule as wilaya')
            ->where('projet_id', $projet_id)
            ->distinct();

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
        }

        return $query->get();
    }

    private function getProjectOperateurs($projet_id, $operateur_id = null)
    {
        $query = LocaliteReport::join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->select('operateurs.intitule as operateur')
            ->where('projet_id', $projet_id)
            ->where('operation_id', '<>', 3)
            ->distinct();

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
            return $query->first();
        }

        return $query->get();
    }

    private function getInfrastructureWilayas($projet_id, $operateur_id = null)
    {
        $query = InfrastructureReport::join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->select('wilayas.intitule as wilaya')
            ->where('projet_id', $projet_id)
            ->distinct();

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
        }

        return $query->get();
    }

    private function getInfrastructureOperateurs($projet_id, $operateur_id = null)
    {
        $query = InfrastructureReport::join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->select('operateurs.intitule as operateur')
            ->where('projet_id', $projet_id)
            ->distinct();

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
        }

        return $query->get();
    }

    private function getVillagesDeclenches($projet_id, $operateur_id = null)
    {
        $min1Ids = DB::table('localite_operations')
            ->select(DB::raw('MIN(id) as min_id'))
            ->where('operation_id', 1)
            ->where('projet_id', $projet_id);

        if ($operateur_id) {
            $min1Ids->where('operateur_id', $operateur_id);
        }

        $min1Ids = $min1Ids->groupBy('localite_id')->get()->pluck('min_id');

        return LocaliteReport::whereIn('id', $min1Ids)
            ->where('projet_id', $projet_id)
            ->count();
    }
    private function getVillagesDeclenchesIds($projet_id, $operateur_id = null)
    {
        if ($operateur_id) {
            return LocaliteReport::where('operation_id', 1)
                ->where('projet_id', $projet_id)
                ->where('operateur_id', $operateur_id)
                ->distinct('localite_id')
                ->pluck('localite_id');
        }
        return LocaliteReport::where('operation_id', 1)
            ->where('projet_id', $projet_id)
            ->distinct('localite_id')
            ->pluck('localite_id');
    }
    private function getVillagesFdal($projet_id, $operateur_id = null)
    {
        // Récupérer d'abord toutes les localités déclenchées
        $localitesDeclenchees = $this->getVillagesDeclenchesIds($projet_id, $operateur_id);

        if (empty($localitesDeclenchees)) {
            return collect();
        }
        // Filtrer celles qui ont une opération FDAL (operation_id = 3)
        return LocaliteReport::whereIn('localite_id', $localitesDeclenchees)
            ->where('operation_id', 3) // FDAL
            ->where('projet_id', $projet_id)
            ->when($operateur_id, function ($query) use ($operateur_id) {
                $query->where('operateur_id', $operateur_id);
            })
            ->distinct('localite_id')
            ->pluck('localite_id');
    }

    private function getVillagesRedeclenches($projet_id, $operateur_id = null)
    {
        $max1HavingIds = DB::table('localite_operations')
            ->select(DB::raw('MAX(id) as max_id'))
            ->where('operation_id', 1)
            ->where('projet_id', $projet_id)
            ->when($operateur_id, function ($query) use ($operateur_id) {
                $query->where('operateur_id', $operateur_id);
            })
            ->groupBy('localite_id')
            ->havingRaw('COUNT(id) = 2')
            ->get()->pluck('max_id');

        return LocaliteReport::whereIn('id', $max1HavingIds)
            ->where('projet_id', $projet_id)
            ->count();
    }

    private function getProjectPopulation($projet_id, $operateur_id = null, $localiteIds = [])
    {
        if ($operateur_id) {
            return LocaliteReport::where('projet_id', $projet_id)
                ->where('operateur_id', $operateur_id)
                ->when(!empty($localiteIds), function ($query) use ($localiteIds) {
                    $query->whereIn('localite_operations.localite_id', $localiteIds);
                })
                ->sum('localite_operations.nb_tot_pop');
        }

        return LocaliteReport::where('projet_id', $projet_id)
            ->when(!empty($localiteIds), function ($query) use ($localiteIds) {
                $query->whereIn('localite_operations.localite_id', $localiteIds);
            })
            ->sum('localite_operations.nb_tot_pop');
    }

    private function getProjectMenages($projet_id, $operateur_id = null, $localiteIds = [])
    {
        if ($operateur_id) {
            return LocaliteReport::where('projet_id', $projet_id)
                ->where('operateur_id', $operateur_id)
                ->when(!empty($localiteIds), function ($query) use ($localiteIds) {
                    $query->whereIn('localite_operations.localite_id', $localiteIds);
                })
                ->sum('localite_operations.nb_menages_comptabilises');
        }

        return LocaliteReport::where('projet_id', $projet_id)
            ->when(!empty($localiteIds), function ($query) use ($localiteIds) {
                $query->whereIn('localite_operations.localite_id', $localiteIds);
            })
            ->sum('localite_operations.nb_menages_comptabilises');
    }

    private function getFDALStatistics($projet_id, $operateur_id = null)
    {
        $localitesFdalIds = $this->getVillagesFdal($projet_id, $operateur_id);
        $localitesDeclenchees = $this->getVillagesDeclenchesIds($projet_id, $operateur_id);
        $query = LocaliteReport::select(
            DB::raw("SUM(CASE WHEN operation_id = 1 THEN 1 ELSE 0 END) as nbr_localites_declenchees"),
            DB::raw("SUM(CASE WHEN operation_id = 3 THEN 1 ELSE 0 END) as nbr_localites_certifiees"),
            DB::raw("YEAR(date_op) as year")
        )
            ->groupBy('year')
            ->whereIn('operation_id', [1, 3])
            ->where('projet_id', $projet_id);

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
        }

        return $query->get();
    }

    private function getMaxOperationIdsAfterCertification($projet_id, $operation_id, $operateur_id = null)
    {
        $query = DB::table('localite_operations as op')
            ->select(DB::raw('MAX(op.id) as max_id'))
            ->where('op.projet_id', $projet_id)
            ->where('op.operation_id', $operation_id)
            ->whereExists(function ($subQuery) use ($projet_id) {
                $subQuery->select(DB::raw(1))
                    ->from('localite_operations as cert')
                    ->whereColumn('cert.localite_id', 'op.localite_id')
                    ->where('cert.operation_id', 3) // Certification
                    ->whereRaw('cert.date_op < op.date_op')
                    ->whereRaw('cert.date_op = (
                    SELECT MAX(date_op) 
                    FROM localite_operations 
                    WHERE operation_id = 3 
                    AND localite_id = cert.localite_id 
                )');
            })
            ->groupBy('op.localite_id')
            ->havingRaw('COUNT(op.localite_id) >= 1');

        if ($operateur_id) {
            $query->where('op.operateur_id', $operateur_id);
        }

        return $query->get()->pluck('max_id');
    }
    private function getMaxOperationIds($projet_id, $operation_id, $operateur_id = null)
    {
        $query = DB::table('localite_operations')
            ->select(DB::raw('MAX(id) as max_id'))
            ->where('projet_id', $projet_id)
            ->where('operation_id', $operation_id)
            ->groupBy('localite_id')
            ->havingRaw('COUNT(localite_id) >= 1');

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
        }

        return $query->get()->pluck('max_id');
    }

    private function getConstructionIndicators($maxIds, $projet_id, $operateur_id = null)
    {
        $indicators = [
            'nbr_latrine_realise',
            'nbr_latrine_autoconst',
            'nbr_latrine_realise_macon',
            'nbr_latrine_en_constr',
            'nbr_menages_ltr_end_phm'
        ];

        $results = [];

        foreach ($indicators as $indicator) {
            // Somme des indicateurs
            $query = LocaliteReport::select(DB::raw("SUM($indicator) as total_$indicator"))
                ->whereIn('id', $maxIds)
                ->whereNotNull($indicator);

            $results[$indicator] = $query->value("total_$indicator") ?? 0;

            // Count localités DISTINCT avec valeur > 0
            $countQuery = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id) as count'))
                ->whereIn('id', $maxIds)
                ->whereNotNull($indicator)
                ->where($indicator, '>=', 0);

            $results['nbr_localite_' . $indicator] = $countQuery->value('count') ?? 0;
        }

        return $results;
    }

    private function getFinancialIndicators($maxIds, $projet_id, $operateur_id = null)
    {
        $results = [];

        // Indicateurs de base
        $financialFields = [
            'nbr_latrine_realise',
            'nbr_latrine_autofin',
            'nb_latr_invest_men_dispo',
            'mont_invest_men',
            'nbr_latrine_entraid',
            'nbr_latrine_subv'
        ];

        foreach ($financialFields as $field) {
            // Somme des indicateurs
            $query = LocaliteReport::select(DB::raw("SUM($field) as total_$field"))
                ->whereIn('id', $maxIds)
                ->whereNotNull($field);

            $results[$field] = $query->value("total_$field") ?? 0;

            // Count localités DISTINCT avec valeur > 0
            $countQuery = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id) as count'))
                ->whereIn('id', $maxIds)
                ->whereNotNull($field)
                ->where($field, '>', 0);

            $results['nbr_localite_' . $field] = $countQuery->value('count') ?? 0;
        }

        // Calcul des taux avec gestion division par zéro
        $totalRealise = $results['nbr_latrine_realise'] ?? 0;


        $results['taux_latrine_autofin'] = $totalRealise > 0 ? ($results['nbr_latrine_autofin'] * 100.0 / $totalRealise) : 0;
        $results['taux_latrine_entraid'] = $totalRealise > 0 ? ($results['nbr_latrine_entraid'] * 100.0 / $totalRealise) : 0;
        $results['taux_latrine_subv'] = $totalRealise > 0 ? ($results['nbr_latrine_subv'] * 100.0 / $totalRealise) : 0;

        // Montant moyen investi
        $totalInvestDispo = $results['nb_latr_invest_men_dispo'] ?? 0;
        $results['mont_moyen_invest_men'] = $totalInvestDispo > 0 ? ($results['mont_invest_men'] / $totalInvestDispo) : 0;

        return $results;
    }
    private function getInfrastructureProjects($projet_id, $operateur_id = null)
    {
        $query = InfrastructureReport::join('infrastructures', 'infrastructure_operations.infrastructure_id', 'infrastructures.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->select(
                DB::raw('COUNT(infrastructures.id) as infrastructures'),
                DB::raw('SUM(effectif) as effectif'),
                DB::raw('SUM(nbr_cabine_rehab) as nbr_cabine_rehab'),
                DB::raw('SUM(nbr_cabine_const) as nbr_cabine_const'),
                DB::raw('SUM(nbr_bloc_const) as nbr_bloc_const'),
                'projets.intitule_resume',
                'infrastructures.infra_publ',
            )
            ->groupBy('infrastructures.infra_publ', 'projets.intitule_resume')
            ->where('projets.id', $projet_id);

        if ($operateur_id) {
            $query->where('operateur_id', $operateur_id);
        }

        return $query->get();
    }
    private function getEtatLieuxApresCertificationStats($projet_id, $operateur_id = null)
    {
        $dernieresCertifications = DB::table('localite_operations')
            ->select('localite_id', DB::raw('MAX(date_op) as date_derniere_certification'))
            ->where('operation_id', 3) // Certification FDAL
            ->groupBy('localite_id');

        $results = DB::table('localite_operations as el')
            ->joinSub($dernieresCertifications, 'dc', function ($join) {
                $join->on('el.localite_id', '=', 'dc.localite_id');
            })
            ->selectRaw('COUNT(DISTINCT el.localite_id) as nb_localites')
            ->selectRaw('COALESCE(SUM(el.nb_tot_pop), 0) as nb_tot_pop')
            ->selectRaw('COALESCE(SUM(el.nb_menages_comptabilises), 0) as nb_menages_comptabilises')
            ->where('el.operation_id', 2) // État des lieux
            ->where('el.projet_id', $projet_id)
            ->when(!empty($operateur_id), function ($query) use ($operateur_id) {
                $query->where('operateur_id', $operateur_id);
            })
            ->whereRaw('el.date_op > dc.date_derniere_certification')
            ->first();

        return [
            'nb_loc_benefic_appui' => $results->nb_localites ?? 0,
            'nb_tot_pop' => $results->nb_tot_pop ?? 0,
            'nb_menages_comptabilises' => $results->nb_menages_comptabilises ?? 0
        ];
    }
    private function getPostFDALIndicators($maxIds, $projet_id, $operateur_id = null)
    {
        $indicators = [
            'nbr_latrine_realise',
            'nbr_latrine_autoconst',
            'nbr_latrine_realise_macon',
            'nbr_latrine_en_constr',
            'nbr_menages_ltr_end_phm'
        ];

        $results = [];

        foreach ($indicators as $indicator) {
            // Somme des indicateurs
            $query = LocaliteReport::select(DB::raw("COALESCE(SUM($indicator), 0) as total_$indicator"))
                ->whereIn('id', $maxIds)
                ->whereNotNull($indicator);

            $results[$indicator] = $query->value("total_$indicator") ?? 0;

            // Count localités DISTINCT avec valeur > 0
            $countQuery = LocaliteReport::select(DB::raw('COUNT(DISTINCT localite_id) as count'))
                ->whereIn('id', $maxIds)
                ->whereNotNull($indicator)
                ->where($indicator, '>', 0);

            $results['nbr_localite_' . $indicator] = $countQuery->value('count') ?? 0;
        }

        return $results;
    }
    private function getEvolutionAfterCertification($projet_id, $operateur_id = null)
    {
        $evolution = [];

        // 1. Récupérer les localités certifiées FDAL
        $localitesCertifiees = LocaliteReport::where('operation_id', 3)
            ->where('projet_id', $projet_id);
        if ($operateur_id) {
            $localitesCertifiees->where('operateur_id', $operateur_id);
        }
        $localitesCertifiees = $localitesCertifiees->distinct()->pluck('localite_id');

        if ($localitesCertifiees->isEmpty()) {
            return $this->getEmptyEvolutionData();
        }

        // 2. Récupérer les dates du premier état des lieux après certification
        $premierEtatLieuxDates = DB::table('localite_operations as etat')
            ->select('etat.localite_id', DB::raw('MIN(etat.date_op) as date_premier_etat_lieux'))
            ->where('etat.projet_id', $projet_id)
            ->where('etat.operation_id', 2)
            ->whereIn('etat.localite_id', $localitesCertifiees)
            ->whereRaw('etat.date_op > (
            SELECT MAX(date_op) 
            FROM localite_operations 
            WHERE operation_id = 3 
            AND localite_id = etat.localite_id
        )')
            ->groupBy('etat.localite_id');

        // 3. Récupérer les IDs du premier état des lieux APRÈS certification
        $premierEtatLieuxIds = DB::table('localite_operations as op')
            ->select(DB::raw('MIN(op.id) as id'))
            ->where('op.projet_id', $projet_id)
            ->where('op.operation_id', 2)
            ->whereIn('op.localite_id', $localitesCertifiees)
            ->whereRaw('op.date_op > (
            SELECT MAX(date_op) 
            FROM localite_operations 
            WHERE operation_id = 3 
            AND localite_id = op.localite_id
        )')
            ->groupBy('op.localite_id');

        if ($operateur_id) {
            $premierEtatLieuxIds->where('op.operateur_id', $operateur_id);
        }

        $premierEtatLieuxIds = $premierEtatLieuxIds->get()->pluck('id');

        // 4. Récupérer les IDs du dernier appui-suivi APRÈS le premier état des lieux
        $dernierAppuiSuiviIds = DB::table('localite_operations as op')
            ->joinSub($premierEtatLieuxDates, 'premier_etat', function ($join) {
                $join->on('op.localite_id', '=', 'premier_etat.localite_id');
            })
            ->select(DB::raw('MAX(op.id) as id'))
            ->where('op.projet_id', $projet_id)
            ->where('op.operation_id', 2)
            ->whereRaw('op.date_op > premier_etat.date_premier_etat_lieux')
            ->groupBy('op.localite_id');

        if ($operateur_id) {
            $dernierAppuiSuiviIds->where('op.operateur_id', $operateur_id);
        }

        $dernierAppuiSuiviIds = $dernierAppuiSuiviIds->get()->pluck('id');

        // 5. Données de l'état des lieux initial APRÈS certification
        $etatLieuxInitial = $this->getAssainissementData($projet_id, $premierEtatLieuxIds);

        // 6. Données de l'appui-suivi final APRÈS le premier état des lieux
        $appuiSuiviFinal = $this->getAssainissementData($projet_id, $dernierAppuiSuiviIds);

        $evolution['r4'] = [
            'min' => $etatLieuxInitial,
            'max' => $appuiSuiviFinal,
            'localites_certifiees' => $localitesCertifiees->count(),
            'localites_avec_etat_lieux' => $etatLieuxInitial->nb_localites,
            'localites_avec_appui_suivi' => $appuiSuiviFinal->nb_localites
        ];

        return $evolution;
    }

    private function getAssainissementData($projet_id, $operationIds)
    {
        $result =  DB::table('localite_operations')
            ->select(
                DB::raw('COALESCE(SUM(nb_tot_latr_fam), 0) as nb_tot_latr_fam'),
                DB::raw('COALESCE(SUM(nb_menages_comptabilises), 0) as nb_menages_comptabilises'),
                DB::raw('COALESCE(SUM(nbr_menages_latr_amel_adeq) - SUM(nbr_menages_latr_amel_vid_adeq_part) - SUM(nbr_menages_latr_amel_inadeq_part), 0) as securise'),
                DB::raw('COALESCE(SUM(nbr_menages_latr_amel_vid_adeq_part) + SUM(nbr_menages_latr_amel_inadeq_part) + SUM(nbr_menages_ltr_vsn), 0) as limite'),
                DB::raw('COALESCE(SUM(nb_latrine_amelior), 0) as basique'),
                DB::raw('COALESCE(SUM(nb_tot_latr_fam - nb_latrine_amelior), 0) as non_ameliores'),
                DB::raw('COALESCE(SUM(nbr_menages_dal), 0) as men_dal'),
                DB::raw('COALESCE(SUM(nb_latrine_amelior), 0) as nb_ameliores'),
                DB::raw('COALESCE(SUM(nbr_menages_dlm), 0) as nbr_menages_dlm'),
                DB::raw('COALESCE(SUM(nbr_menages_dlm_complet), 0) as nbr_menages_dlm_complet'),
                DB::raw('COALESCE(SUM(nbr_menages_dlm_incomplet), 0) as nbr_menages_dlm_incomplet'),
                DB::raw('COALESCE(SUM(nbr_menages_sans_dlm), 0) as nbr_menages_sans_dlm'),
                DB::raw('COUNT(DISTINCT localite_id) as nb_localites'),
                DB::raw('COALESCE(SUM(nb_tot_pop), 0) as nb_tot_pop')
            )
            ->where('projet_id', $projet_id)
            ->whereIn('id', $operationIds)
            ->first();
        // Vérifier si des données existent
        if (!$result || $result->nb_localites == 0) {
            return $this->getNRData();
        }

        return $result;
    }

    private function getNRData()
    {
        return (object) [
            'nb_tot_latr_fam' => 'NR',
            'nb_menages_comptabilises' => 'NR',
            'securise' => 'NR',
            'limite' => 'NR',
            'basique' => 'NR',
            'non_ameliores' => 'NR',
            'men_dal' => 'NR',
            'nb_ameliores' => 'NR',
            'nbr_menages_dlm' => 'NR',
            'nbr_menages_dlm_complet' => 'NR',
            'nbr_menages_dlm_incomplet' => 'NR',
            'nbr_menages_sans_dlm' => 'NR',
            'nb_localites' => 'NR',
            'nb_tot_pop' => 'NR'
        ];
    }
    private function getEmptyEvolutionData()
    {
        $emptyData = $this->getEmptyAssainissementData();

        return [
            'r4' => [
                'min' => $emptyData,
                'max' => $emptyData,
                'localites_certifiees' => 0,
                'localites_avec_etat_lieux' => 0,
                'localites_avec_appui_suivi' => 0
            ]
        ];
    }

    private function getEmptyAssainissementData()
    {
        return (object) [
            'nb_tot_latr_fam' => 0,
            'nb_menages_comptabilises' => 0,
            'securise' => 0,
            'limite' => 0,
            'basique' => 0,
            'non_ameliores' => 0,
            'men_dal' => 0,
            'nb_ameliores' => 0,
            'nbr_menages_dlm' => 0,
            'nbr_menages_dlm_complet' => 0,
            'nbr_menages_dlm_incomplet' => 0,
            'nbr_menages_sans_dlm' => 0,
            'nb_localites' => 0,
            'nb_tot_pop' => 0
        ];
    }

    private function getComparisonIndicators($projet_id, $operateur_id = null)
    {
        $comparison = [];

        // 1. Récupérer TOUTES les localités du projet (pas seulement celles avec operation_id = 2)
        $localitesProjetQuery = LocaliteReport::where('projet_id', $projet_id);
        if ($operateur_id) {
            $localitesProjetQuery->where('operateur_id', $operateur_id);
        }

        $localites_projet = $localitesProjetQuery->distinct()->pluck('localite_id');
        if ($localites_projet->isEmpty()) {
            return $this->getEmptyComparisonData();
        }

        // 2. INDICATEURS INITIAUX - Premier enregistrement TOUTES opérations (début projet)
        $debut_ids = LocaliteReport::where('operation_id', 2)
            ->whereIn('localite_id', $localites_projet)
            ->selectRaw('MIN(id) as id') // Premier enregistrement de chaque localité
            ->groupBy('localite_id');



        $debut_ids = $debut_ids->get()->pluck('id');

        // 3. INDICATEURS FINAUX - Dernier enregistrement TOUTES opérations (fin projet)
        $fin_ids = LocaliteReport::where('operation_id', 4)
            ->whereIn('localite_id', $localites_projet)
            ->selectRaw('MAX(id) as id') // Dernier enregistrement de chaque localité
            ->groupBy('localite_id');
        $fin_ids = $fin_ids->get()->pluck('id');

        // 4. Données du DÉBUT du projet
        $debut_projet = DB::table('localite_operations')
            ->select(
                DB::raw('COALESCE(SUM(nb_tot_latr_fam), 0) as nb_tot_latr_fam'),
                DB::raw('COALESCE(SUM(nb_menages_comptabilises), 0) as nb_menages_comptabilises'),
                DB::raw('COALESCE(SUM(nbr_menages_latr_amel_adeq) - SUM(nbr_menages_latr_amel_vid_adeq_part) - SUM(nbr_menages_latr_amel_inadeq_part), 0) as securise'),
                DB::raw('COALESCE(SUM(nbr_menages_latr_amel_vid_adeq_part) + SUM(nbr_menages_latr_amel_inadeq_part) + SUM(nbr_menages_ltr_vsn), 0) as limite'),
                DB::raw('COALESCE(SUM(nb_latrine_amelior), 0) as basique'),
                DB::raw('COALESCE(SUM(nb_tot_latr_fam - nb_latrine_amelior), 0) as non_ameliores'),
                DB::raw('COALESCE(SUM(nbr_menages_dal), 0) as men_dal'),
                DB::raw('COALESCE(SUM(nb_latrine_amelior), 0) as nb_ameliores'),
                DB::raw('COALESCE(SUM(nbr_latrine_entraid), 0) as nbr_latrine_entraid'),
                DB::raw('COALESCE(SUM(nbr_latrine_subv), 0) as nbr_latrine_subv'),

                DB::raw('COALESCE(SUM(nbr_menages_dlm), 0) as nbr_menages_dlm'),
                DB::raw('COALESCE(SUM(nbr_menages_dlm_complet), 0) as nbr_menages_dlm_complet'),
                DB::raw('COALESCE(SUM(nbr_menages_dlm_incomplet), 0) as nbr_menages_dlm_incomplet'),
                DB::raw('COALESCE(SUM(nbr_menages_sans_dlm), 0) as nbr_menages_sans_dlm'),

                DB::raw('COUNT(DISTINCT localite_id) as nb_localites'),
                DB::raw('COALESCE(SUM(nb_tot_pop), 0) as nb_tot_pop')
            )
            ->where('projet_id', $projet_id)
            ->whereIn('id', $debut_ids)
            ->first();

        // 5. Données de la FIN du projet
        $fin_projet = DB::table('localite_operations')
            ->select(
                DB::raw('COALESCE(SUM(nb_tot_latr_fam), 0) as nb_tot_latr_fam'),
                DB::raw('COALESCE(SUM(nb_menages_comptabilises), 0) as nb_menages_comptabilises'),
                DB::raw('COALESCE(SUM(nbr_menages_latr_amel_adeq) - SUM(nbr_menages_latr_amel_vid_adeq_part) - SUM(nbr_menages_latr_amel_inadeq_part), 0) as securise'),
                DB::raw('COALESCE(SUM(nbr_menages_latr_amel_vid_adeq_part) + SUM(nbr_menages_latr_amel_inadeq_part) + SUM(nbr_menages_ltr_vsn), 0) as limite'),
                DB::raw('COALESCE(SUM(nb_latrine_amelior), 0) as basique'),
                DB::raw('COALESCE(SUM(nb_tot_latr_fam - nb_latrine_amelior), 0) as non_ameliores'),
                DB::raw('COALESCE(SUM(nbr_menages_dal), 0) as men_dal'),
                DB::raw('COALESCE(SUM(nb_latrine_amelior), 0) as nb_ameliores'),
                DB::raw('COALESCE(SUM(nbr_latrine_entraid), 0) as nbr_latrine_entraid'),
                DB::raw('COALESCE(SUM(nbr_latrine_subv), 0) as nbr_latrine_subv'),

                DB::raw('COALESCE(SUM(nbr_menages_dlm), 0) as nbr_menages_dlm'),
                DB::raw('COALESCE(SUM(nbr_menages_dlm_complet), 0) as nbr_menages_dlm_complet'),
                DB::raw('COALESCE(SUM(nbr_menages_dlm_incomplet), 0) as nbr_menages_dlm_incomplet'),
                DB::raw('COALESCE(SUM(nbr_menages_sans_dlm), 0) as nbr_menages_sans_dlm'),

                DB::raw('COUNT(DISTINCT localite_id) as nb_localites'),
                DB::raw('COALESCE(SUM(nb_tot_pop), 0) as nb_tot_pop')
            )
            ->where('projet_id', $projet_id)
            ->whereIn('id', $fin_ids)
            ->first();

        $comparison['r2'] = [
            'min' => $debut_projet,  // Situation en début de projet
            'max' => $fin_projet,    // Situation à la fin de projet
            'statistiques' => [
                'total_localites' => $localites_projet->count(),
                'localites_debut' => $debut_projet->nb_localites,
                'localites_fin' => $fin_projet->nb_localites,
                'population_totale' => $fin_projet->nb_tot_pop
            ]
        ];


        return $comparison;
    }

    private function getEmptyComparisonData()
    {
        $emptyData = $this->getEmptyAssainissementData();

        return [
            'r2' => [
                'min' => $emptyData,
                'max' => $emptyData
            ]
        ];
    }
}
