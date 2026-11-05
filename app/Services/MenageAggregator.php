<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Fusionne les fiches ménage d'une localité (table menages, saisies depuis
 * l'app mobile ou le web, par un ou plusieurs utilisateurs) en UNE fiche
 * localité (table localite_operations), recalculée à chaque ajout /
 * modification / suppression de ménage.
 *
 * Clé de la fiche fusionnée : localité + opération + projet, stockée dans
 * localite_operations.mobile_ref = "menages:{operation}:{localite}:{projet}".
 * Requêtes en DB::table pour ne pas être filtré par les scopes globaux
 * (WilayaScope DRHA, confirmed_for_reporting).
 */
class MenageAggregator
{
    public static function ref(int $operationId, int $localiteId, int $projetId): string
    {
        return "menages:{$operationId}:{$localiteId}:{$projetId}";
    }

    /**
     * Recalcule les fiches localité concernées par ces lignes menages (ex. :
     * l'ancienne et la nouvelle version d'un ménage modifié, qui a pu changer
     * de localité ou de projet). Lignes null ignorées.
     */
    public static function syncForMenages(array $rows, ?int $userId): void
    {
        $done = [];
        foreach ($rows as $row) {
            if (!$row || empty($row->projet_id) || empty($row->localite_id) || empty($row->operation_id)) {
                continue;
            }
            $key = self::ref((int) $row->operation_id, (int) $row->localite_id, (int) $row->projet_id);
            if (isset($done[$key])) {
                continue;
            }
            $done[$key] = true;
            self::sync(
                (int) $row->localite_id,
                (int) $row->operation_id,
                (int) $row->projet_id,
                isset($row->operateur_id) ? (int) $row->operateur_id ?: null : null,
                $userId ?: (isset($row->user_id) ? (int) $row->user_id : null)
            );
        }
    }

    /**
     * Recalcule la fiche localité. Retourne son id, ou null si plus aucun
     * ménage (la fiche fusionnée est alors supprimée).
     */
    public static function sync(int $localiteId, int $operationId, int $projetId, ?int $operateurId, ?int $userId): ?int
    {
        $ref = self::ref($operationId, $localiteId, $projetId);

        $menages = DB::table('menages')
            ->where('localite_id', $localiteId)
            ->where('operation_id', $operationId)
            ->where('projet_id', $projetId);

        $s = (clone $menages)->selectRaw("
            COUNT(*) AS nb,
            COALESCE(SUM(nbr_h_c),0) AS h, COALESCE(SUM(nbr_f_c),0) AS f,
            COALESCE(SUM(nbr_enfant_inf_5_ans),0) AS enf,
            COALESCE(SUM(latr_fam),0) AS latr_fam,
            COALESCE(SUM(latr_amel),0) AS latr_amel,
            COALESCE(SUM(latr_non_amelior),0) AS latr_non_amel,
            COALESCE(SUM(latr_amel_vid_adeq),0) AS vid_adeq,
            COALESCE(SUM(latr_amel_vid_inadeq),0) AS vid_inadeq,
            COALESCE(SUM(latr_amel_vid_adeq_part),0) AS vid_adeq_part,
            COALESCE(SUM(latr_amel_vid_inadeq_part),0) AS vid_inadeq_part,
            COALESCE(SUM(ltr_endommag),0) AS endommag,
            COALESCE(SUM(latr_amelior_voisin),0) AS voisin,
            COALESCE(SUM(men_dal),0) AS dal,
            COALESCE(SUM(dlm = 'DLM Complet'),0) AS dlm_complet,
            COALESCE(SUM(dlm = 'DLM Incomplet'),0) AS dlm_incomplet,
            COALESCE(SUM(dlm IS NULL OR dlm = 'Sans DLM'),0) AS sans_dlm,
            COALESCE(SUM(latr_realise),0) AS realise,
            COALESCE(SUM(latr_auto_const),0) AS autoconst,
            COALESCE(SUM(latr_realise_macon),0) AS macon,
            COALESCE(SUM(latr_entraid),0) AS entraid,
            COALESCE(SUM(latr_subv),0) AS subv,
            COALESCE(SUM(latr_autofin),0) AS autofin,
            COALESCE(SUM(latr_en_constr),0) AS en_constr,
            COALESCE(SUM(mont_invest_men),0) AS invest,
            COALESCE(SUM(invest_men_dispo),0) AS invest_dispo,
            MAX(date_op) AS date_op
        ")->first();

        $existing = DB::table('localite_operations')->where('mobile_ref', $ref)->first();

        if (!$s || (int) $s->nb === 0) {
            if ($existing) {
                DB::table('localite_operations')->where('id', $existing->id)->delete();
            }
            return null;
        }

        $localite = DB::table('localites')->where('id', $localiteId)->first();
        $now = now()->format('Y-m-d H:i:s');

        $values = [
            'localite_id' => $localiteId,
            'commune_id' => $localite->commune_id ?? null,
            'moughata_id' => $localite->moughata_id ?? null,
            'wilaya_id' => $localite->wilaya_id ?? null,
            'operation_id' => $operationId,
            'projet_id' => $projetId,
            'operateur_id' => $operateurId ?? ($existing->operateur_id ?? null),
            'nbr_h_c' => $s->h,
            'nbr_f_c' => $s->f,
            'nb_tot_pop' => $s->h + $s->f,
            'nbr_enfant_inf_5_ans' => $s->enf,
            'nb_menages_comptabilises' => $s->nb,
            'nb_tot_latr_fam' => $s->latr_fam,
            'nb_latrine_amelior' => $s->latr_amel,
            'nb_latrine_non_amelior' => $s->latr_non_amel,
            'nbr_menages_latr_amel_adeq' => $s->vid_adeq,
            'nbr_menages_latr_amel_inadeq' => $s->vid_inadeq,
            'nbr_menages_latr_amel_vid_adeq_part' => $s->vid_adeq_part,
            'nbr_menages_latr_amel_inadeq_part' => $s->vid_inadeq_part,
            'nbr_menages_ltr_end_phm' => $s->endommag,
            'nbr_menages_ltr_vsn' => $s->voisin,
            'nbr_menages_dal' => $s->dal,
            'nbr_menages_dlm' => $s->dlm_complet + $s->dlm_incomplet,
            'nbr_menages_dlm_complet' => $s->dlm_complet,
            'nbr_menages_dlm_incomplet' => $s->dlm_incomplet,
            'nbr_menages_sans_dlm' => $s->sans_dlm,
            'nbr_latrine_realise' => $s->realise,
            'nbr_latrine_autoconst' => $s->autoconst,
            'nbr_latrine_realise_macon' => $s->macon,
            'nbr_latrine_entraid' => $s->entraid,
            'nbr_latrine_subv' => $s->subv,
            'nbr_latrine_autofin' => $s->autofin,
            'nbr_latrine_en_constr' => $s->en_constr,
            'mont_invest_men' => $s->invest,
            'nb_latr_invest_men_dispo' => $s->invest_dispo,
            'date_op' => $s->date_op ?: now()->toDateString(),
            'remarque' => "Fiche fusionnée automatiquement à partir de {$s->nb} ménage(s).",
            // Les chiffres ont changé : la fiche repasse en validation.
            'confirmed' => 0,
            'statut_coordinateur' => null,
            'coordinateur_id' => null,
            'coordinateur_valide_le' => null,
            'coordinateur_remarque' => null,
            'updated_at' => $now,
        ];

        if ($existing) {
            DB::table('localite_operations')->where('id', $existing->id)->update($values);
            return (int) $existing->id;
        }

        if (!$values['operateur_id'] || !$userId) {
            return null; // colonnes obligatoires : la fiche sera créée au prochain ménage complet
        }

        return (int) DB::table('localite_operations')->insertGetId($values + [
            'mobile_ref' => $ref,
            'user_id' => $userId,
            'created_at' => $now,
        ]);
    }
}
