<?php

namespace App\Imports;

use App\Models\Localite;
use Illuminate\Support\Facades\Log;

class LocaliteImport extends BaseAdministrationImport
{
    public function model(array $row)
    {
        $this->rowCount++;

        try {
            if (empty($row['localite'])) {
                $this->skippedRows[] = ['row' => $row, 'reason' => 'Nom de localité manquant'];
                return null;
            }

            $wilayaName = $row['wilaya'] ?? null;
            $moughataName = $row['moughataa'] ?? $row['moughata'] ?? null;
            $communeName = $row['commune'] ?? null;

            if (empty($wilayaName) || empty($moughataName) || empty($communeName)) {
                $this->skippedRows[] = ['row' => $row, 'reason' => 'Informations géographiques (Wilaya/Moughata/Commune) manquantes'];
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

            $commune = $this->findCommune($communeName, $moughata->id);
            if (!$commune) {
                $this->skippedRows[] = ['row' => $row, 'reason' => "Commune non trouvée: '{$communeName}' dans moughata {$moughata->intitule}"];
                return null;
            }

            // A localite is a duplicate only when EXACT same name in the SAME commune
            // (which implies same moughata and same wilaya).
            // We do NOT use str_contains / fuzzy matching here because "Dar Naim" would
            // incorrectly match "Dar Naim 2", "Dar Naim Est", etc.
            $normTarget = $this->normalizeString($row['localite']);
            $existing = $this->localites->first(function($item) use ($normTarget, $commune) {
                if ($item->commune_id != $commune->id) return false;
                return $this->normalizeString($item->intitule ?? '') === $normTarget
                    || $this->normalizeString($item->intitule_fr ?? '') === $normTarget;
            });

            $data = [
                'intitule' => $row['localite'],
                'intitule_fr' => $row['localite_fr'] ?? $row['localite'],
                'commune_id' => $commune->id,
                'moughata_id' => $moughata->id,
                'wilaya_id' => $wilaya->id,
                'code_ansade' => $row['code_ansade'] ?? null,
                'lat_loc' => $row['latitude'] ?? $row['lat'] ?? null,
                'long_loc' => $row['longitude'] ?? $row['long'] ?? null,
                'nb_pop_ansade' => $row['population'] ?? $row['pop'] ?? null,
            ];

            if ($existing) {
                $existing->update($data);
                $this->updateCount++;
                return null;
            }

            $this->newCount++;
            if (empty($row['code'])) {
                $data['code'] = $this->generateLocaliteCode($wilaya, $moughata, $commune);
            } else {
                $data['code'] = $row['code'];
            }

            return new Localite($data);

        } catch (\Throwable $th) {
            $this->errorRows[] = ['row' => $row, 'error' => $th->getMessage()];
            Log::error('LocaliteImport Error: ' . $th->getMessage() . ' row: ' . json_encode($row));
            return null;
        }
    }

    private function generateLocaliteCode($wilaya, $moughata, $commune)
    {
        $prefix = str_pad($wilaya->code ?? '0', 2, '0', STR_PAD_LEFT) . 
                  str_pad($moughata->code ?? '0', 2, '0', STR_PAD_LEFT) . 
                  str_pad($commune->code ?? '0', 2, '0', STR_PAD_LEFT);
        
        $count = Localite::where('code', 'LIKE', $prefix . '%')->count();
        return $prefix . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}
