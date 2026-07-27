<?php

namespace App\Imports;

use App\Models\Wilaya;
use App\Models\Moughata;
use App\Models\Commune;
use App\Models\Localite;
use App\Support\Concerns\NormalizesAdministrativeNames;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

abstract class BaseAdministrationImport implements ToModel, SkipsEmptyRows, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures, NormalizesAdministrativeNames;

    protected $wilayas;
    protected $moughatas;
    protected $communes;
    protected $localites;
    
    protected $rowCount = 0;
    protected $updateCount = 0;
    protected $newCount = 0;
    protected $skippedRows = [];
    protected $errorRows = [];

    public function __construct()
    {
        $this->loadReferenceData();
    }

    protected function loadReferenceData()
    {
        set_time_limit(0);
        $this->wilayas = Wilaya::all();
        $this->moughatas = Moughata::all();
        $this->communes = Commune::all();
        // Only load columns needed for duplicate detection to reduce memory and Eloquent casting overhead
        $this->localites = Localite::select(['id', 'code_ansade', 'intitule', 'commune_id'])->get();
    }

    /**
     * Recherche une Wilaya
     */
    protected function findWilaya($name)
    {
        if (empty($name)) return null;
        
        return $this->wilayas->first(function($item) use ($name) {
            return $this->stringsMatch($item->intitule, $name) || 
                   $this->stringsMatch($item->intitule_fr, $name);
        });
    }

    /**
     * Recherche une Moughata
     */
    protected function findMoughata($name, $wilayaId = null)
    {
        if (empty($name)) return null;
        
        return $this->moughatas->first(function($item) use ($name, $wilayaId) {
            $match = $this->stringsMatch($item->intitule, $name) || 
                     $this->stringsMatch($item->intitule_fr, $name);
            
            if ($match && $wilayaId) {
                return $item->wilaya_id == $wilayaId;
            }
            return $match;
        });
    }

    /**
     * Recherche une Commune
     */
    protected function findCommune($name, $moughataId = null)
    {
        if (empty($name)) return null;
        
        return $this->communes->first(function($item) use ($name, $moughataId) {
            $match = $this->stringsMatch($item->intitule, $name) || 
                     $this->stringsMatch($item->intitule_fr, $name);
            
            if ($match && $moughataId) {
                return $item->moughata_id == $moughataId;
            }
            return $match;
        });
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getUpdateCount(): int
    {
        return $this->updateCount;
    }

    public function getNewCount(): int
    {
        return $this->newCount;
    }

    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }

    public function getErrorRows(): array
    {
        return $this->errorRows;
    }
}
