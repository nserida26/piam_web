<?php

namespace App\Http\Controllers\Api;

use App\Models\ControleTravaux;
use App\Models\InfrastructureReport;
use App\Models\LocaliteReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends ApplicationController
{

    public function index(Request $request)
    {
        $user = Auth::user();
        return [
            'reports' => $this->all_reports($request,$user),
            'status' => 'ok'
        ];
    }

    public function store_localite_reports(Request $request)
    {
        if ($denied = $this->denyIfLocaliteNotAssigned($request)) {
            return $denied;
        }
        if (!$this->projetId($request)) {
            return $this->projetManquant();
        }

        $user = Auth::id();
        $created_at = date('Y-m-d');
        
        try {
            $report = $this->saveByMobileRef(LocaliteReport::class, $request, $user, [
                'localite_id' => $request->localite_id,
                'commune_id' => $request->commune_id,
                'moughata_id' => $request->moughata_id,
                'wilaya_id' => $request->wilaya_id,
                'operation_id' => $request->operation_id,
                'projet_id' => $this->projetId($request),
                'operateur_id' => $this->ongId($request),
                'nbr_h_c' => $request->nbr_h_c,
                'nbr_f_c' => $request->nbr_f_c,
                'nb_tot_pop' => (isset($request->nbr_h_c) && isset($request->nbr_f_c)) 
                    ? $request->nbr_f_c + $request->nbr_h_c 
                    : null,
                'nbr_enfant_inf_5_ans' => $request->nbr_enfant_inf_5_ans,
                'nb_menages_comptabilises' => $request->nb_menages_comptabilises,
                'approv_eau' => $request->approv_eau,
                'nb_tot_latr_fam' => $request->nbr_tot_fam,
                'nb_latrine_amelior' => $request->nb_latrine_amelior,
                'nb_latrine_non_amelior' => $request->nb_latrine_non_amelior,
                'nbr_menages_latr_amel_inadeq' => $request->nbr_menages_latr_amel_inadeq,
                'nbr_menages_latr_amel_adeq' => $request->nbr_menages_latr_amel_adeq,
                'nbr_menages_latr_amel_vid_adeq_part' => $request->nbr_menages_latr_amel_vid_adeq_part,
                'nbr_menages_latr_amel_inadeq_part' => $request->nbr_menages_latr_amel_inadeq_part,
                'nbr_menages_ltr_end_phm' => $request->nbr_menages_ltr_end_phm,
                'nbr_menages_ltr_vsn' => $request->nbr_menages_ltr_vsn,
                'nbr_menages_dal' => $request->nbr_menages_dal,
                'nbr_menages_dlm' => $request->nbr_menages_dlm,
                'nbr_menages_dlm_complet' => $request->nbr_menages_dlm_complet,
                'nbr_menages_dlm_incomplet' => $request->nbr_menages_dlm_incomplet,
                'nbr_menages_sans_dlm' => $request->nbr_menages_sans_dlm,
                'nbr_latrine_realise' => $request->nbr_latrine_realise,
                'nbr_latrine_autoconst' => $request->nbr_latrine_autoconst,
                'nbr_latrine_realise_macon' => $request->nbr_latrine_realise_macon,
                'nbr_latrine_entraid' => $request->nbr_latrine_entraid,
                'nbr_latrine_subv' => $request->nbr_latrine_subv,
                'nbr_latrine_autofin' => $request->nbr_latrine_autofin,
                'nbr_latrine_en_constr' => $request->nbr_latrine_en_constr,
                'mont_invest_men' => $request->mont_invest_men,
                'nb_latr_invest_men_dispo' => $request->nb_latr_invest_men_dispo,
                'date_op' => $request->date_op ? date('Y-m-d', strtotime($request->date_op)) : null,
                'remarque' => $request->remarque ?? '',
                // Donnée (re)transmise : repasse en attente de validation
                // (coordinateur puis admin).
                'confirmed' => 0,
                'statut_coordinateur' => null,
                'coordinateur_id' => null,
                'coordinateur_valide_le' => null,
                'coordinateur_remarque' => null,
                'user_id' => $user,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ] + $this->gps('localite_operations', $request));

            if ($report) {
                \App\Services\ReportPhotos::syncFromRequest('localite_operations', $report->id, $request, $user);
                return [
                    'status' => 'ok',
                    'result' => $report
                ];
            }

            return [
                'status' => 'error',
                'result' => 'Failed to create report'
            ];

        } catch (\Throwable $th) {
            return $this->erreurEnregistrement($th);
        }
    }

    public function store_infrastructure_reports(Request $request)
    {
        if ($denied = $this->denyIfLocaliteNotAssigned($request)) {
            return $denied;
        }
        if (!$this->projetId($request)) {
            return $this->projetManquant();
        }

        try {
            $user = Auth::user();
            $created_at = date('Y-m-d');
            
            // Parse coordinates if provided
            $lat = null;
            $lng = null;
            if ($request->latlng) {
                $parts = explode(',', $request->latlng);
                $lat = isset($parts[0]) ? trim($parts[0]) : null;
                $lng = isset($parts[1]) ? trim($parts[1]) : null;
            }

            // Check if infrastructure already exists
            $infrastructure = DB::table('infrastructures')
                ->where('intitule_infra_publ', $request->nom_infrastructure)
                ->where('localite_id', $request->localite_id)
                ->first();

            if (!$infrastructure) {
                // Get count for code generation
                $count_infrastructures = DB::table('infrastructures')->count();
                
                // Insert new infrastructure
                $insertedId = DB::table('infrastructures')->insertGetId([
                    'intitule_infra_publ' => $request->nom_infrastructure,
                    'infra_publ' => $request->type_infrastructure,
                    'code_infra_publ' => 'INFRA-' . str_pad($count_infrastructures + 1, 6, '0', STR_PAD_LEFT),
                    'localite_id' => $request->localite_id,
                    'commune_id' => $request->commune_id,
                    'moughata_id' => $request->moughata_id,
                    'wilaya_id' => $request->wilaya_id,
                    'long_infra_publ' => $lng,
                    'lat_infra_publ' => $lat,
                    'created_at' => $created_at,
                    'updated_at' => $created_at
                ]);
            } else {
                $insertedId = $infrastructure->id;
            }

            // Determine DLM status
            $dlm = $request->dlm;
            $nb_dlm_complet = ($dlm == 'DLM muni d\'eau et savon' || $dlm == 'DLM Complet') ? 1 : 0;
            $nb_dlm_incomplet = ($dlm == 'DLM dépourvu d\'eau et/ou savon' || $dlm == 'DLM Incomplet') ? 1 : 0;
            $nb_sans_dlm = ($dlm == 'Aucune DLM' || $dlm == 'Sans DLM') ? 1 : 0;
            $nb_dlm = isset($request->dlm) ? 1 : 0;

            // Parse boolean fields
            $dist_cab_hom_fem = ($request->distinction_nette == 'Oui' || $request->dist_cab_hom_fem === true || $request->dist_cab_hom_fem == 1) ? 1 : 0;
            $gest_dechet_biomed = ($request->nbr_struct_sante_gest_dechet_biomed == 'Oui' || $request->gest_dechet_biomed === true || $request->gest_dechet_biomed == 1) ? 1 : 0;
            $gest_dechet = ($request->nbr_ecole_gest_dechet == 'Oui' || $request->gest_dechet === true || $request->gest_dechet == 1) ? 1 : 0;

            $report = $this->saveByMobileRef(InfrastructureReport::class, $request, $user->id, [
                'localite_id' => $request->localite_id,
                'commune_id' => $request->commune_id,
                'moughata_id' => $request->moughata_id,
                'wilaya_id' => $request->wilaya_id,
                'operation_id' => $request->operation_id,
                'projet_id' => $this->projetId($request),
                'operateur_id' => $this->ongId($request),
                'infrastructure_id' => $insertedId,
                'dist_approv_eau' => $request->dist_approv_eau,
                'approv_eau' => $request->approv_eau,
                'nbr_bloc' => $request->nbr_bloc,
                'nb_dlm' => $nb_dlm,
                'nb_dlm_complet' => $nb_dlm_complet,
                'nb_dlm_incomplet' => $nb_dlm_incomplet,
                'nb_sans_dlm' => $nb_sans_dlm,
                'nbr_cabine_exist' => $request->nbr_cabine_exist,
                'nbr_cabine_fonct' => $request->nbr_cabine_fonct,
                'nbr_cabine_fem' => $request->nbr_cabine_fem,
                'nbr_cabine_fem_hyg' => $request->nbr_cabine_fem_hyg,
                'dist_cab_hom_fem' => $dist_cab_hom_fem,
                'gest_dechet' => $gest_dechet,
                'gest_dechet_biomed' => $gest_dechet_biomed,
                'nbr_cabine_pmr' => $request->nbr_cabine_pmr,
                'effectif_garcon' => $request->effectif_garcon,
                'effectif_fille' => $request->effectif_fille,
                'effectif' => $request->effectif ?? (($request->effectif_garcon ?? 0) + ($request->effectif_fille ?? 0)),
                'nbr_cabine_a_rehab' => $request->nbr_cabine_a_rehab,
                'nbr_bloc_a_const' => $request->nbr_bloc_a_const,
                'nbr_cabine_a_const' => $request->nbr_cabine_a_const,
                'nbr_cabine_rehab' => $request->nbr_cabine_rehab,
                'nbr_bloc_const' => $request->nbr_bloc_const,
                'nbr_cabine_const' => $request->nbr_cabine_const,
                'nb_dlm_instal' => $request->nb_dlm_instal,
                'date_op' => $request->date_op ? date('Y-m-d', strtotime($request->date_op)) : null,
                'an_program' => $request->an_program ? date('Y', strtotime($request->an_program)) : null,
                'an_recept' => $request->an_recept ? date('Y', strtotime($request->an_recept)) : null,
                'remarque' => $request->remarque ?? '',
                // Donnée (re)transmise : repasse en attente de validation
                // (coordinateur puis admin).
                'confirmed' => 0,
                'statut_coordinateur' => null,
                'coordinateur_id' => null,
                'coordinateur_valide_le' => null,
                'coordinateur_remarque' => null,
                'user_id' => $user->id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ] + $this->gps('infrastructure_operations', $request));

            if ($report) {
                \App\Services\ReportPhotos::syncFromRequest('infrastructure_operations', $report->id, $request, $user->id);
                return [
                    'status' => 'ok',
                    'result' => $report
                ];
            }

            return [
                'status' => 'error',
                'result' => 'Failed to create infrastructure report'
            ];

        } catch (\Throwable $th) {
            return $this->erreurEnregistrement($th);
        }
    }

    public function store_menage_reports(Request $request)
    {
        if ($denied = $this->denyIfLocaliteNotAssigned($request)) {
            return $denied;
        }

        $user = Auth::id();
        $created_at = date('Y-m-d');

        try {
            // Parse coordinates
            $lat = null;
            $lng = null;
            if ($request->latlng) {
                $parts = explode(',', $request->latlng);
                $lat = isset($parts[0]) ? trim($parts[0]) : null;
                $lng = isset($parts[1]) ? trim($parts[1]) : null;
            }

            // Determine latrine and DLM status
            $latr_fam = ($request->amelior_latrine == 'Améliorée' || $request->latr_amel == 1) ? 1 : 0;
            $latr_amel = $latr_fam;
            $latr_non_amelior = ($request->amelior_latrine == 'Non Améliorée' || $request->latr_non_amelior == 1
                || ($request->latr_fam == 1 && !$latr_amel)) ? 1 : 0;
            
            // DLM status (l'app mobile envoie `dlm` ; `dlm_menage` = ancien nom)
            $dlmValue = $request->dlm_menage ?? $request->dlm;
            $dlm_complet = ($dlmValue == 'DLM muni d\'eau et savon' || $dlmValue == 'DLM Complet') ? 1 : 0;
            $dlm_incomplet = ($dlmValue == 'DLM dépourvu d\'eau et/ou savon' || $dlmValue == 'DLM Incomplet') ? 1 : 0;
            
            // Determine DLM enum value
            $dlm_enum = 'Sans DLM';
            if ($dlm_complet) {
                $dlm_enum = 'DLM Complet';
            } elseif ($dlm_incomplet) {
                $dlm_enum = 'DLM Incomplet';
            }

            // Calculate total population
            $nbr_h_c = $request->nbr_h_c ?? 0;
            $nbr_f_c = $request->nbr_f_c ?? 0;

            $menage = [
                'nbr_h_c' => $nbr_h_c,
                'nbr_f_c' => $nbr_f_c,
                'nbr_enfant_inf_5_ans' => $request->nbr_enfant_inf_5_ans ?? 0,
                'latr_fam' => $request->latr_fam ?? 0,
                'latr_amel' => $latr_amel,
                'latr_non_amelior' => $latr_non_amelior,
                'latr_amel_vid_adeq' => ($request->vidange == 'Adéquate' || $request->latr_amel_vid_adeq == 1) ? 1 : 0,
                'latr_amel_vid_inadeq' => ($request->vidange == 'Inadéquate' || $request->latr_amel_vid_inadeq == 1) ? 1 : 0,
                'latr_amel_vid_inadeq_part' => $request->latr_amel_vid_inadeq_part ?? 0,
                'latr_amel_vid_adeq_part' => $request->latr_amel_vid_adeq_part ?? 0,
                'ltr_endommag' => $request->ltr_endommag ?? 0,
                'latr_amelior_voisin' => $request->latr_amelior_voisin ?? 0,
                'men_dal' => $request->men_dal ?? 0,
                'dlm' => $dlm_enum,
                'mont_invest_men' => $request->mont_invest_men ?? 0,
                'invest_men_dispo' => $request->invest_men_dispo ?? 0,
                'latr_realise' => $request->latr_realise ?? 0,
                'latr_auto_const' => $request->latr_auto_const ?? 0,
                'latr_realise_macon' => $request->latr_realise_macon ?? 0,
                'latr_autofin' => $request->latr_autofin ?? 0,
                'latr_entraid' => $request->latr_entraid ?? 0,
                'latr_subv' => $request->latr_subv ?? 0,
                'latr_en_constr' => $request->latr_en_constr ?? 0,
                'latlng' => $request->latlng ?? '',
                'wilaya_id' => $request->wilaya_id ?? $request->region_id,
                'moughata_id' => $request->moughata_id ?? $request->state_id,
                'commune_id' => $request->commune_id ?? $request->city_id,
                'localite_id' => $request->localite_id,
                'operation_id' => $request->operation_id,
                'user_id' => $user,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ];

            // Nom du père de famille, projet / opérateur (fusion en fiche localité).
            // Colonnes ajoutées par la migration 2026_11_06_000001 : ignorées tant
            // qu'elle n'a pas été lancée.
            $extra = [
                'nom_chef_menage' => trim((string) $request->nom_chef_menage) ?: null,
                'projet_id' => $this->projetId($request),
                'operateur_id' => $this->ongId($request),
                'date_op' => $request->date_op ? date('Y-m-d', strtotime($request->date_op)) : $created_at,
            ];
            foreach ($extra as $col => $val) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('menages', $col)) {
                    $menage[$col] = $val;
                }
            }

            // Fiche déjà reçue (modifiée puis renvoyée) : mise à jour.
            $existing = $request->mobile_ref
                ? DB::table('menages')
                    ->where('mobile_ref', $request->mobile_ref)
                    ->where('user_id', $user)
                    ->first()
                : null;

            if ($existing) {
                unset($menage['created_at']);
                // Fiche modifiée : elle repasse « à valider » (onglet Ménages).
                if (\Illuminate\Support\Facades\Schema::hasColumn('menages', 'confirmed')) {
                    $menage['confirmed'] = 0;
                }
                DB::table('menages')->where('id', $existing->id)->update($menage);
                $insertedId = $existing->id;
            } else {
                $insertedId = DB::table('menages')->insertGetId(
                    $menage + ['mobile_ref' => $request->mobile_ref]
                );
            }

            if ($insertedId) {
                // Photos de la latrine choisies par le facilitateur (plusieurs).
                $photos = \App\Services\ReportPhotos::syncFromRequest('menages', $insertedId, $request, $user);
                if ($photos && \Illuminate\Support\Facades\Schema::hasColumn('menages', 'photo')) {
                    DB::table('menages')->where('id', $insertedId)->update(['photo' => $photos[0]]);
                }

                $report = DB::table('menages')->where('id', $insertedId)->first();

                // Fusion de tous les ménages de la localité en une fiche localité.
                // Une erreur ici ne doit pas faire échouer l'envoi du ménage.
                try {
                    \App\Services\MenageAggregator::syncForMenages([$existing, $report], $user);
                } catch (\Throwable $e) {
                    report($e);
                }

                return [
                    'status' => 'ok',
                    'result' => $report,
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to insert ménage report',
                'request' => $request->all(),
            ];

        } catch (\Throwable $th) {
            return $this->erreurEnregistrement($th);
        }
    }

    /**
     * Reçoit le payload de l'écran mobile "Contrôle des travaux" (latrines
     * publiques) — voir _buildPayload() dans
     * controle_travaux_latrine_screen.dart pour la forme exacte envoyée.
     * Les sections imbriquées (personnel, équipements, matériaux, et chaque
     * sous-section du Niveau 3) sont stockées telles quelles en JSON plutôt
     * que d'être éclatées en colonnes.
     */
    public function store_controle_travaux(Request $request)
    {
        if ($denied = $this->denyIfLocaliteNotAssigned($request)) {
            return $denied;
        }
        $user = Auth::id();
        $created_at = date('Y-m-d');

        try {
            $report = $this->saveByMobileRef(ControleTravaux::class, $request, $user, [
                'wilaya_id' => $request->wilaya_id,
                'moughata_id' => $request->moughataa_id,
                'commune_id' => $request->commune_id,
                'localite_id' => $request->localite_id,
                'projet_id' => $this->projetId($request),
                'operateur_id' => $request->operateur_id,
                'user_id' => $user,

                'intitule_site' => $request->intitule_site,
                'type_etablissement' => $request->type_etablissement,
                'code_ansade' => $request->code_ansade,
                'code_mesre' => $request->code_mesre,
                'code_ms' => $request->code_ms,
                'effectif_ecole' => $request->effectif_ecole,
                'nb_usagers' => $request->nb_usagers,

                'avancement' => $request->avancement,
                'form_status' => $request->form_status ?? 'submitted',
                'confirmed' => $request->confirmed ?? 0,
                'date_op' => $request->timestamp ? date('Y-m-d', strtotime($request->timestamp)) : $created_at,

                'marche_travaux' => $request->marche_travaux,
                'marche_controle' => $request->marche_controle,
                'travaux_a_realiser' => $request->travaux_a_realiser,
                'personnel' => $request->personnel,
                'equipements' => $request->equipements,
                'materiaux' => $request->materiaux,
                'statuts' => $request->statuts,
                'implantation' => $request->implantation,
                'fondation' => $request->fondation,
                'elevation' => $request->elevation,
                'toit' => $request->toit,
                'enduits' => $request->enduits,
                'menuiserie' => $request->menuiserie,
                'plomberie' => $request->plomberie,
                'peinture' => $request->peinture,
                'revetement' => $request->revetement,
                'dlm' => $request->dlm,
                'garde_fou' => $request->garde_fou,
                'pges' => $request->pges,
                'mgp' => $request->mgp,
                'appreciation' => $request->appreciation,
                'reception_technique' => $request->reception_technique,
                'reception_provisoire' => $request->reception_provisoire,

                'created_at' => $created_at,
                'updated_at' => $created_at,
            ]);

            if ($report) {
                return [
                    'status' => 'ok',
                    'result' => $report
                ];
            }

            return [
                'status' => 'error',
                'result' => 'Failed to create contrôle travaux report'
            ];

        } catch (\Throwable $th) {
            return $this->erreurEnregistrement($th);
        }
    }

    /**
     * Crée le rapport, ou le met à jour si l'application mobile l'a déjà
     * envoyé (même `mobile_ref` pour le même utilisateur). Évite les doublons
     * quand une fiche est modifiée sur le téléphone puis renvoyée.
     */
    /**
     * Position GPS du facilitateur (« lat,lng ») : colonne latlng ajoutée par
     * la migration 2026_11_07_000002 (ignorée tant qu'elle n'est pas lancée).
     */
    private function gps(string $table, Request $request): array
    {
        $latlng = trim((string) ($request->latlng ?? $request->location_coordinates));
        if (!preg_match('/^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/', $latlng)
            || !\Illuminate\Support\Facades\Schema::hasColumn($table, 'latlng')) {
            return [];
        }
        return ['latlng' => preg_replace('/\s+/', '', $latlng)];
    }

    private function saveByMobileRef(string $model, Request $request, $userId, array $attributes)
    {
        $ref = $request->mobile_ref;

        $existing = $ref
            ? $model::where('mobile_ref', $ref)->where('user_id', $userId)->first()
            : null;

        if ($existing) {
            unset($attributes['created_at']);
            $existing->update($attributes);
            return $existing;
        }

        return $model::create($attributes + ['mobile_ref' => $ref]);
    }

    /**
     * Un facilitateur ne peut envoyer des rapports que pour ses localités
     * assignées (voir User::allowedLocaliteIds).
     */
    private function denyIfLocaliteNotAssigned(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->canWorkOnLocalite($request->localite_id)) {
            return response([
                'status' => 'error',
                'result' => 'Localité non assignée à cet utilisateur.',
            ], 403);
        }
        return null;
    }

    /**
     * Opérateur du rapport : celui choisi dans l'application (la liste
     * complète est proposée), à défaut l'ONG du compte. Le coordinateur voit
     * aussi les rapports de ses facilitateurs via leur user_id.
     */
    private function ongId(Request $request)
    {
        return $request->operateur_id ?: optional(Auth::user())->operateur_id;
    }

    /**
     * Projet du rapport : celui choisi dans l'app, sinon le projet du compte
     * (user_projets). null si aucun : la colonne projet_id est obligatoire.
     */
    private function projetId(Request $request)
    {
        if ($request->filled('projet_id')) {
            return $request->projet_id;
        }

        $user = Auth::user();
        return $user ? DB::table('user_projets')->where('user_id', $user->id)->orderBy('id')->value('projet_id') : null;
    }

    private function projetManquant()
    {
        $message = 'Projet non renseigné. Choisissez le projet dans Paramètres, puis synchronisez de nouveau. '
            . '- لم يتم تحديد المشروع. اختر المشروع في الإعدادات ثم أعد المزامنة.';

        return ['status' => 'error', 'result' => $message, 'message' => $message];
    }

    /** Erreur serveur : message lisible pour l'app, détail technique dans les logs. */
    private function erreurEnregistrement(\Throwable $th)
    {
        report($th);

        $raw = $th->getMessage();
        if (preg_match("/Column '([a-z_]+)' cannot be null/i", $raw, $m)) {
            $message = 'Champ obligatoire manquant : ' . $m[1] . '. Vérifiez les Paramètres (wilaya, localité, projet, opérateur) puis renvoyez.';
        } elseif (stripos($raw, 'foreign key') !== false) {
            $message = 'Donnée de référence introuvable sur le serveur (localité, projet ou opérateur). Mettez à jour les données puis renvoyez.';
        } else {
            $message = "Le serveur n'a pas pu enregistrer le formulaire. Réessayez plus tard ou contactez l'administrateur.";
        }

        return ['status' => 'error', 'result' => $message, 'message' => $message];
    }
}
