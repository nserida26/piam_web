<?php

namespace App\Imports;

use App\Models\Wilaya;
use App\Models\Moughata;
use App\Models\Commune;
use App\Models\Localite;
use Illuminate\Support\Facades\Log;

class UnifiedHierarchyImport extends BaseAdministrationImport
{
    public function model(array $row)
    {
        $this->rowCount++;

        try {
            // 1. Wilaya
            $wilayaName = $row['wilaya'] ?? null;
            if (empty($wilayaName)) return null;

            $wilaya = $this->findWilaya($wilayaName);
            if (!$wilaya) {
                $wilaya = Wilaya::create([
                    'intitule' => $wilayaName,
                    'intitule_fr' => $row['wilaya_fr'] ?? $wilayaName,
                    'code' => $row['code_wilaya'] ?? 0
                ]);
                $this->wilayas->push($wilaya);
                $this->newCount++;
            }

            // 2. Moughata
            $moughataName = $row['moughata'] ?? $row['moughataa'] ?? null;
            if (empty($moughataName)) return null;

            $moughata = $this->findMoughata($moughataName, $wilaya->id);
            if (!$moughata) {
                $moughata = Moughata::create([
                    'intitule' => $moughataName,
                    'intitule_fr' => $row['moughata_fr'] ?? $moughataName,
                    'wilaya_id' => $wilaya->id,
                    'code' => $row['code_moughata'] ?? 0
                ]);
                $this->moughatas->push($moughata);
                $this->newCount++;
            }

            // 3. Commune
            $communeName = $row['commune'] ?? null;
            if (empty($communeName)) return null;

            $commune = $this->findCommune($communeName, $moughata->id);
            if (!$commune) {
                $commune = Commune::create([
                    'intitule' => $communeName,
                    'intitule_fr' => $row['commune_fr'] ?? $communeName,
                    'moughata_id' => $moughata->id,
                    'wilaya_id' => $wilaya->id,
                    'code' => $row['code_commune'] ?? 0
                ]);
                $this->communes->push($commune);
                $this->newCount++;
            }

            // 4. Localite
            $localiteName = $row['localite'] ?? null;
            if (empty($localiteName)) return null;

            $existingLocalite = $this->localites->first(function($item) use ($localiteName, $commune) {
                return $item->commune_id == $commune->id && $this->stringsMatch($item->intitule, $localiteName);
            });

            $localiteData = [
                'intitule' => $localiteName,
                'intitule_fr' => $row['localite_fr'] ?? $localiteName,
                'commune_id' => $commune->id,
                'moughata_id' => $moughata->id,
                'wilaya_id' => $wilaya->id,
                'code_ansade' => $row['code_ansade'] ?? null,
                'lat_loc' => $row['latitude'] ?? null,
                'long_loc' => $row['longitude'] ?? null,
            ];

            if ($existingLocalite) {
                $existingLocalite->update($localiteData);
                $this->updateCount++;
            } else {
                $localiteData['code'] = $row['code_localite'] ?? $this->generateLocaliteCode($wilaya, $moughata, $commune);
                $localite = Localite::create($localiteData);
                $this->localites->push($localite);
                $this->newCount++;
            }

            return null;

        } catch (\Throwable $th) {
            $this->errorRows[] = ['row' => $row, 'error' => $th->getMessage()];
            Log::error('UnifiedHierarchyImport Error: ' . $th->getMessage());
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
