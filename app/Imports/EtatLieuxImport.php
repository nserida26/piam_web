<?php

namespace App\Imports;

use App\Models\LocaliteReport;
use Illuminate\Support\Facades\Log;

class EtatLieuxImport extends BaseReportImport
{
    protected function getSpecificData(array $row, $entities)
    {
        return [
            'date_op' => $this->convertDate($row['date_op'] ?? null, '01', '01'),
            'nbr_h_c' => $row['nb_hom_compt'] ?? null,
            'nbr_f_c' => $row['nb_fem_compt'] ?? null,
            'nbr_enfant_inf_5_ans' => $row['nb_enf_inf_5_ans_compt'] ?? null,
            'nb_tot_pop' => $row['nb_tot_pop_compt'] ?? null,
            'nb_menages_comptabilises' => $row['nb_menages_compt'] ?? null,
            'approv_eau' => $row['approv_eau_loc'] ?? null,
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
        ];
    }

    public function model(array $row)
    {
        try {
            if (empty($row['localite']) || empty($row['moughataa']) || empty($row['commune'])) {
                $this->skippedRows[] = [
                    'row' => $row,
                    'reason' => 'Donn�es obligatoires manquantes (localite, moughataa, commune)'
                ];
                Log::warning('Ligne ignor�e - donn�es manquantes: ' . json_encode($row));
                return null;
            }

            $entities = $this->findEntities($row);
            if (!$entities) {
                return null;
            }

            $existingReport = $this->findExistingReport($entities);

            $baseData = $this->prepareBaseData($entities);
            $specificData = $this->getSpecificData($row, $entities);

            if ($existingReport) {
                $existingReport->update(array_merge($baseData, $specificData));
                Log::info('Rapport �tat des lieux mis � jour: ID ' . $existingReport->id .
                    ' - Localit�: ' . $entities['localite']->intitule);
                return null;
            }

            $baseData['created_at'] = now();
            Log::info('Nouveau rapport �tat des lieux cr�� pour localit�: ' . $entities['localite']->intitule);

            return new LocaliteReport(array_merge($baseData, $specificData));
        } catch (\Throwable $th) {
            $this->errorRows[] = [
                'row' => $row,
                'error' => $th->getMessage()
            ];
            Log::error('Erreur import �tat des lieux: ' . $th->getMessage() . ' - Ligne: ' . json_encode($row));
            return null;
        }
    }
}
