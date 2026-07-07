<?php

namespace App\Imports;

use App\Models\LocaliteReport;

class DernierSuiviImport extends BaseReportImport
{
    protected function getSpecificData(array $row, $entities)
    {
        return [
            'date_op' => $this->convertDate($row['date_op'] ?? null, '12', '31'),
            'nb_tot_latr_fam' => $row['nb_tot_latr_fam'] ?? null,
            'nb_latrine_amelior' => $row['nbr_latr_amel'] ?? null,
            'nb_latrine_non_amelior' => $row['nb_latrine_non_amelior'] ?? null,
            'nbr_menages_latr_amel_inadeq' => $row['nb_men_latr_amel_vid_inadeq'] ?? null,
            'nbr_menages_latr_amel_adeq' => $row['nb_men_latr_amel_vid_adeq'] ?? null,
            'nbr_menages_ltr_vsn' => $row['nbr_men_latr_voisin'] ?? null,
            'nbr_menages_latr_amel_inadeq_part' => $row['nb_men_latr_amel_vid_inadeq_part'] ?? null,
            'nbr_menages_latr_amel_vid_adeq_part' => $row['nb_men_latr_amel_vid_adeq_part'] ?? null,
            'nbr_menages_ltr_end_phm' => $row['nb_men_latr_endommag'] ?? null,
            'nbr_menages_dal' => $row['nb_men_dal'] ?? null,
            'nbr_menages_dlm' => $row['nb_latr_dlm'] ?? null,
            'nbr_menages_dlm_complet' => $row['nb_men_dlm_complet'] ?? null,
            'nbr_menages_dlm_incomplet' => $row['nb_men_dlm_incomplet'] ?? null,
            'nbr_menages_sans_dlm' => $row['nb_men_sans_dlm'] ?? null,
            'nbr_latrine_realise' => $row['nb_latr_realise'] ?? null,
            'nbr_latrine_autoconst' => $row['nb_latr_autoconst'] ?? null,
            'nbr_latrine_realise_macon' => $row['nb_latr_realise_macon'] ?? null,
            'nbr_latrine_entraid' => $row['nb_latr_entraid'] ?? null,
            'nbr_latrine_subv' => $row['nb_latr_subv'] ?? null,
            'nbr_latrine_en_constr' => $row['nb_latr_en_constr'] ?? null,
            'nbr_latrine_autofin' => $row['nb_latr_autofin'] ?? null,
            'mont_invest_men' => $row['mont_invest_men'] ?? null,
            'nb_latr_invest_men_dispo' => $row['nb_latr_invest_men_dispo'] ?? null,
        ];
    }

    public function model(array $row)
    {
        try {
            // Validation basique
            if (empty($row['localite']) || empty($row['moughataa']) || empty($row['commune'])) {
                \Log::warning('Ligne ignor�e - donn�es manquantes: ' . json_encode($row));
                return null;
            }

            // Trouver les entit�s li�es
            $entities = $this->findEntities($row);
            if (!$entities) {
                \Log::warning('Entit�s non trouv�es pour: ' . json_encode($row));
                return null;
            }

            // V�rifier l'unicit�
            $existingReport = $this->findExistingReport($entities);

            $baseData = $this->prepareBaseData($entities);
            $specificData = $this->getSpecificData($row, $entities);

            if ($existingReport) {
                // Mise � jour du rapport existant
                $existingReport->update(array_merge($baseData, $specificData));
                \Log::info('Rapport suivi mis � jour: ID ' . $existingReport->id);
                return null;
            }

            // Cr�ation d'un nouveau rapport
            $baseData['created_at'] = now();
            return new LocaliteReport(array_merge($baseData, $specificData));
        } catch (\Throwable $th) {
            \Log::error('Erreur import suivi: ' . $th->getMessage() . ' - Ligne: ' . json_encode($row));
            return null;
        }
    }
}
