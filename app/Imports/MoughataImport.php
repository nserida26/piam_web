<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Wilaya;
use App\Models\Moughata;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoughataImport implements ToModel, SkipsEmptyRows, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;
    
    private $wilayas;
    private $existingMoughatas;
    
    public function __construct()
    {
        $this->wilayas = Wilaya::select('id', 'intitule')->get();
        $this->existingMoughatas = Moughata::select('id', 'intitule', 'code')->get();
    }
    
    public function model(array $row)
    {
        try {
            // V�rifier les champs requis
            if (empty($row['moughataa'])) {
                return null;
            }

            $wilaya = $this->wilayas->first(function ($item) use ($row) {
                return strtolower(trim($item->intitule)) === strtolower(trim($row['wilaya']));
            });

            if (!$wilaya) {
                return null;
            }

            $created_at = now();

            // V�rifier l'unicit� par intitul� ou par code
            $existingMoughata = $this->existingMoughatas->first(function ($item) use ($row) {
                // V�rifier par intitul� (insensible � la casse)
                if (strtolower(trim($item->intitule)) === strtolower(trim($row['moughataa']))) {
                    return true;
                }
                // V�rifier par code si fourni
                if (!empty($row['code_mgt']) && $item->code === $row['code_mgt']) {
                    return true;
                }
                return false;
            });

            if ($existingMoughata) {
                // Mise � jour de la moughata existante
                Moughata::where('id', $existingMoughata->id)->update([
                    'intitule' => $row['moughataa'],
                    'intitule_fr' => $row['moughataa_fr'] ?? $row['moughataa'],
                    'code' => $row['code_mgt'] ?? $existingMoughata->code,
                    'wilaya_id' => $wilaya->id,
                    'updated_at' => $created_at
                ]);
                return null;
            }

            // Cr�ation d'une nouvelle moughata
            return new Moughata([
                'intitule' => $row['moughataa'],
                'intitule_fr' => $row['moughataa_fr'] ?? $row['moughataa'],
                'code' => $row['code_mgt'] ?? null,
                'wilaya_id' => $wilaya->id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ]);

        } catch (\Throwable $th) {
            Log::error('Erreur lors de l\'import de moughata: ' . $th->getMessage() . ' - Ligne: ' . json_encode($row));
            return null;
        }
    }
}