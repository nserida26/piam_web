<?php

namespace App\Imports;

use App\Models\Wilaya;
use App\Models\Moughata;
use App\Models\Commune;
use Illuminate\Support\Facades\Log;

class CommuneImport extends BaseAdministrationImport
{
    public function model(array $row)
    {
        $this->rowCount++;

        try {
            if (empty($row['commune'])) {
                $this->skippedRows[] = ['row' => $row, 'reason' => 'Nom de commune manquant'];
                return null;
            }

            $wilayaName = $row['wilaya'] ?? null;
            $moughataName = $row['moughataa'] ?? $row['moughata'] ?? null;

            if (empty($wilayaName) || empty($moughataName)) {
                $this->skippedRows[] = ['row' => $row, 'reason' => 'Wilaya ou Moughataa manquante'];
                return null;
            }

            $wilaya = $this->findWilaya($wilayaName);
            if (!$wilaya) {
                $this->skippedRows[] = ['row' => $row, 'reason' => "Wilaya non trouvée: '{$wilayaName}'"];
                return null;
            }

            $moughata = $this->findMoughata($moughataName, $wilaya->id);
            if (!$moughata) {
                $this->skippedRows[] = ['row' => $row, 'reason' => "Moughata non trouvée: '{$moughataName}' dans wilaya {$wilaya->intitule}"];
                return null;
            }

            $existing = $this->findCommune($row['commune'], $moughata->id);

            if ($existing) {
                $existing->update([
                    'intitule' => $row['commune'],
                    'intitule_fr' => $row['commune_fr'] ?? $row['commune'],
                    'code' => $row['code_com'] ?? $row['code'] ?? $existing->code,
                    'moughata_id' => $moughata->id,
                    'wilaya_id' => $wilaya->id,
                ]);
                $this->updateCount++;
                return null;
            }

            $this->newCount++;
            $commune = new Commune([
                'intitule' => $row['commune'],
                'intitule_fr' => $row['commune_fr'] ?? $row['commune'],
                'code' => $row['code_com'] ?? $row['code'] ?? 0,
                'moughata_id' => $moughata->id,
                'wilaya_id' => $wilaya->id,
            ]);
            
            $this->communes->push($commune);
            return $commune;

        } catch (\Throwable $th) {
            $this->errorRows[] = ['row' => $row, 'error' => $th->getMessage()];
            Log::error('CommuneImport Error: ' . $th->getMessage() . ' row: ' . json_encode($row));
            return null;
        }
    }
}
