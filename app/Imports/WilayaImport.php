<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use App\Models\Wilaya;
use Illuminate\Support\Facades\DB;

class WilayaImport extends BaseAdministrationImport
{
    public function model(array $row)
    {
        $this->rowCount++;

        try {
            if (empty($row['wilaya'])) {
                $this->skippedRows[] = ['row' => $row, 'reason' => 'Nom de wilaya manquant'];
                return null;
            }

            $existing = $this->findWilaya($row['wilaya']);
            
            if (!$existing && !empty($row['code'])) {
                $existing = $this->wilayas->where('code', $row['code'])->first();
            }

            if ($existing) {
                $existing->update([
                    'intitule' => $row['wilaya'],
                    'intitule_fr' => $row['wilaya_fr'] ?? $row['wilaya'],
                    'code' => $row['code'] ?? $existing->code,
                ]);
                $this->updateCount++;
                return null;
            }

            $this->newCount++;
            $wilaya = new Wilaya([
                'intitule' => $row['wilaya'],
                'intitule_fr' => $row['wilaya_fr'] ?? $row['wilaya'],
                'code' => $row['code'] ?? 0,
            ]);
            
            $this->wilayas->push($wilaya);
            return $wilaya;

        } catch (\Throwable $th) {
            $this->errorRows[] = ['row' => $row, 'error' => $th->getMessage()];
            \Log::error('WilayaImport Error: ' . $th->getMessage());
            return null;
        }
    }
}
