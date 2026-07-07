<?php
namespace App\Imports;

use App\Models\Infrastructure;
use App\Models\Moughata;
use App\Models\Wilaya;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Commune;
use App\Models\Localite;

class InfrastructureImport implements ToModel, SkipsEmptyRows, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;
    
    private $localites;
    private $communes;
    private $moughatas;
    private $wilayas;
    private $counter;
    
    public function __construct()
    {
        $this->localites = Localite::select('id', 'intitule', 'code')->get();
        $this->communes = Commune::select('id', 'intitule')->get();
        $this->moughatas = Moughata::select('id', 'intitule')->get();
        $this->wilayas = Wilaya::select('id', 'intitule')->get();
        $this->counter = Infrastructure::count(); // Commencer par le nombre existant
    }
    
    public function model(array $row)
    {
        try {
            // Vérifier les champs requis
            if (empty($row['intitule_infra_publ']) || empty($row['localite'])) {
                return null;
            }

            $localite = $this->localites->first(function ($item) use ($row) {
                return strtolower(trim($item->intitule)) === strtolower(trim($row['localite']));
            });

            if (!$localite) {
                return null;
            }

            $commune = $this->communes->first(function ($item) use ($row) {
                return strtolower(trim($item->intitule)) === strtolower(trim($row['commune']));
            });

            $moughata = $this->moughatas->first(function ($item) use ($row) {
                return strtolower(trim($item->intitule)) === strtolower(trim($row['moughata']));
            });

            $wilaya = $this->wilayas->first(function ($item) use ($row) {
                return strtolower(trim($item->intitule)) === strtolower(trim($row['wilaya']));
            });

            if (!$commune || !$moughata || !$wilaya) {
                return null;
            }

            // Vérifier l'unicité par intitulé et localisation
            $existingInfrastructure = Infrastructure::where(function($query) use ($row, $localite) {
                $query->whereRaw('LOWER(intitule_infra_publ) = ?', [strtolower(trim($row['intitule_infra_publ']))])
                      ->where('localite_id', $localite->id);
                
                // Vérifier aussi par coordonnées si fournies
                if (!empty($row['lat_infra_publ']) && !empty($row['long_infra_publ'])) {
                    $query->orWhere(function($q) use ($row) {
                        $q->where('lat_infra_publ', $row['lat_infra_publ'])
                          ->where('long_infra_publ', $row['long_infra_publ']);
                    });
                }
                
                // Vérifier par code MEN si fourni
                if (!empty($row['code_men'])) {
                    $query->orWhere('code_men', $row['code_men']);
                }
            })->first();

            if ($existingInfrastructure) {
                // Mise à jour de l'infrastructure existante
                $existingInfrastructure->update([
                    'infra_publ' => $row['infra_publ'] ?? $existingInfrastructure->infra_publ,
                    'intitule_infra_publ' => $row['intitule_infra_publ'],
                    'lat_infra_publ' => $row['lat_infra_publ'] ?? $existingInfrastructure->lat_infra_publ,
                    'long_infra_publ' => $row['long_infra_publ'] ?? $existingInfrastructure->long_infra_publ,
                    'code_men' => $row['code_men'] ?? $existingInfrastructure->code_men,
                    'commune_id' => $commune->id,
                    'moughata_id' => $moughata->id,
                    'wilaya_id' => $wilaya->id,
                    'updated_at' => now()
                ]);
                return null;
            }

            $this->counter++;
            $created_at = now();

            return new Infrastructure([
                'infra_publ' => $row['infra_publ'] ?? null,
                'intitule_infra_publ' => $row['intitule_infra_publ'],
                'code_infra_publ' => $localite->code . '-INF-' . str_pad($this->counter, 6, '0', STR_PAD_LEFT),
                'lat_infra_publ' => $row['lat_infra_publ'] ?? null,
                'long_infra_publ' => $row['long_infra_publ'] ?? null,
                'code_men' => $row['code_men'] ?? null,
                'localite_id' => $localite->id,
                'commune_id' => $commune->id,
                'moughata_id' => $moughata->id,
                'wilaya_id' => $wilaya->id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ]);

        } catch (\Throwable $th) {
            \Log::error('Erreur lors de l\'import d\'infrastructure: ' . $th->getMessage() . ' - Ligne: ' . json_encode($row));
            return null;
        }
    }
}