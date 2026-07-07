<?php

namespace App\Imports;

use App\Models\LocaliteReport;

class CertificationImport extends BaseReportImport
{
    protected function getSpecificData(array $row, $entities)
    {
        return [
            'date_op' => $this->convertDate($row['date_op'] ?? null, '12', '31'),
        ];
    }

    public function model(array $row)
    {
        try {
            // Validation basique des champs requis
            if (empty($row['localite']) || empty($row['moughataa']) || empty($row['commune'])) {
                $this->skippedRows[] = [
                    'row' => $row,
                    'reason' => 'Données obligatoires manquantes (localite, moughataa, commune)'
                ];
                \Log::warning('Ligne ignorée - données manquantes: ' . json_encode($row));
                return null;
            }

            // Trouver les entités liées avec recherche floue
            $entities = $this->findEntities($row);
            if (!$entities) {
                return null;
            }

            // Vérifier l'unicité
            $existingReport = $this->findExistingReport($entities);

            $baseData = $this->prepareBaseData($entities);
            $specificData = $this->getSpecificData($row, $entities);

            if ($existingReport) {
                $existingReport->update(array_merge($baseData, $specificData));
                \Log::info('Rapport certification mis à jour: ID ' . $existingReport->id . 
                          ' - Localité: ' . $entities['localite']->intitule);
                return null;
            }

            $baseData['created_at'] = now();
            \Log::info('Nouveau rapport certification créé pour localité: ' . $entities['localite']->intitule);
            
            return new LocaliteReport(array_merge($baseData, $specificData));

        } catch (\Throwable $th) {
            $this->errorRows[] = [
                'row' => $row,
                'error' => $th->getMessage()
            ];
            \Log::error('Erreur import certification: ' . $th->getMessage() . ' - Ligne: ' . json_encode($row));
            return null;
        }
    }
}