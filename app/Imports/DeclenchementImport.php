<?php

namespace App\Imports;

use App\Models\LocaliteReport;

class DeclenchementImport extends BaseReportImport
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
                return null; // Déjà loggé dans findEntities
            }

            // Vérifier l'unicité - mise à jour si existe déjà
            $existingReport = $this->findExistingReport($entities);

            $baseData = $this->prepareBaseData($entities);
            $specificData = $this->getSpecificData($row, $entities);

            if ($existingReport) {
                // Mise à jour du rapport existant
                $existingReport->update(array_merge($baseData, $specificData));
                \Log::info('Rapport déclenchement mis à jour: ID ' . $existingReport->id . 
                          ' - Localité: ' . $entities['localite']->intitule);
                return null;
            }

            // Création d'un nouveau rapport
            $baseData['created_at'] = now();
            \Log::info('Nouveau rapport déclenchement créé pour localité: ' . $entities['localite']->intitule . 
                      ' (ID: ' . $entities['localite']->id . ')');
            
            return new LocaliteReport(array_merge($baseData, $specificData));

        } catch (\Throwable $th) {
            $this->errorRows[] = [
                'row' => $row,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ];
            \Log::error('Erreur import déclenchement: ' . $th->getMessage() . 
                       ' - Ligne: ' . json_encode($row) . 
                       ' - Trace: ' . $th->getTraceAsString());
            return null;
        }
    }
}