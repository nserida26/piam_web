<?php

namespace App\Imports;

use App\Models\Wilaya;
use App\Models\Moughata;
use App\Models\Commune;
use App\Models\Localite;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

abstract class BaseAdministrationImport implements ToModel, SkipsEmptyRows, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;

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
     * Nettoie une chaîne pour la comparaison
     */
    protected function normalizeString($string)
    {
        if (empty($string)) {
            return '';
        }

        $string = mb_strtolower(trim($string), 'UTF-8');
        $string = preg_replace('/\s+/', ' ', $string);
        
        $search = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', 'ñ', 'œ', 'æ'];
        $replace = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c', 'n', 'oe', 'ae'];
        $string = str_replace($search, $replace, $string);
        
        // Remove special characters but keep spaces
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);

        return trim($string);
    }

    /**
     * Vérifie si deux chaînes correspondent (recherche floue)
     */
    protected function stringsMatch($value1, $value2)
    {
        $normalized1 = $this->normalizeString($value1);
        $normalized2 = $this->normalizeString($value2);
        
        if (empty($normalized1) || empty($normalized2)) {
            return false;
        }
        
        if ($normalized1 === $normalized2) {
            return true;
        }
        
        if (strpos($normalized1, $normalized2) !== false || strpos($normalized2, $normalized1) !== false) {
            return true;
        }
        
        similar_text($normalized1, $normalized2, $percent);
        return $percent > 85;
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
