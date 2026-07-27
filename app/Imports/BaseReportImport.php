<?php

namespace App\Imports;

use App\Models\Commune;
use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\Moughata;
use App\Models\Operateur;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

abstract class BaseReportImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    protected $count_before = 0;
    protected $operation_id;
    protected $wilaya_id;
    protected $moughatas;
    protected $communes;
    protected $localites;
    protected $projets;
    protected $operateurs;
    protected $existingReports;
    protected $skippedRows = [];
    protected $errorRows = [];
    /** @var \App\Models\Projet|null the "NR" (Non renseigné) placeholder project */
    protected $nrProjet;
    /** @var \App\Models\Operateur|null the "NR" (Non renseigné) placeholder operateur */
    protected $nrOperateur;

    public function __construct($operation_id, $wilaya_id)
    {
        $this->operation_id = $operation_id;
        $this->wilaya_id = $wilaya_id;

        // Charger les données de référence
        $this->moughatas = Moughata::select('id', 'intitule', 'wilaya_id')->get();
        $this->communes = Commune::select('id', 'intitule', 'moughata_id')->get();
        $this->localites = Localite::select('id', 'intitule', 'commune_id', 'code_ansade')->get();
        $this->operateurs = Operateur::select('id', 'intitule')->get();
        $this->projets = Projet::select('id', 'intitule_resume')->get();
        $this->nrProjet = $this->projets->first(fn ($p) => $this->normalizeString($p->intitule_resume) === 'nr');
        $this->nrOperateur = $this->operateurs->first(fn ($o) => $this->normalizeString($o->intitule) === 'nr');
        $this->count_before = LocaliteReport::count();
        
        // Charger les rapports existants pour cette opération et wilaya
        $this->existingReports = LocaliteReport::where('operation_id', $operation_id)
            ->where('wilaya_id', $wilaya_id)
            ->get();
    }

    /**
     * Nettoie une chaîne pour la comparaison
     * Supprime les accents, espaces multiples, caractères spéciaux
     */
    protected function normalizeString($string)
    {
        if (empty($string)) {
            return '';
        }

        // Convertir en minuscules
        $string = mb_strtolower(trim($string), 'UTF-8');
        
        // Remplacer les espaces multiples par un seul espace
        $string = preg_replace('/\s+/', ' ', $string);
        
        // Supprimer les caractères non alphanumériques (garde les lettres, chiffres et espaces)
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);
        
        // Remplacer les caractères accentués et spéciaux
        $search = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', 'ñ', 'œ', 'æ'];
        $replace = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c', 'n', 'oe', 'ae'];
        $string = str_replace($search, $replace, $string);
        
        return trim($string);
    }

    /**
     * Vrai pour une valeur vide ou un placeholder de type "Inconnu"/"NR"/
     * "Non renseigné" — ces lignes doivent retomber sur l'entité "NR"
     * plutôt qu'être traitées comme une vraie valeur à rechercher.
     */
    protected function isPlaceholderValue($value): bool
    {
        $normalized = $this->normalizeString((string) $value);
        return $normalized === '' || in_array($normalized, ['inconnu', 'inconnue', 'nr', 'non renseigne', 'non renseignee'], true);
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
        
        // Correspondance exacte après normalisation
        if ($normalized1 === $normalized2) {
            return true;
        }
        
        // Vérifier si l'un contient l'autre (recherche LIKE %...%)
        if (strpos($normalized1, $normalized2) !== false || strpos($normalized2, $normalized1) !== false) {
            return true;
        }
        
        // Calculer la similarité (optionnel, pour les cas difficiles)
        similar_text($normalized1, $normalized2, $percent);
        if ($percent > 80) {
            return true;
        }
        
        return false;
    }

    /**
     * Trouve les entités liées avec recherche floue
     */
    protected function findEntities(array $row)
    {
        // Vérifier Moughata
        $moughata = $this->findMoughata($row['moughataa'] ?? null);
        if (!$moughata) {
            $this->skippedRows[] = [
                'row' => $row,
                'reason' => "Moughata non trouvée: '{$row['moughataa']}' pour wilaya_id: {$this->wilaya_id}"
            ];
            \Log::warning("Moughata non trouvée: '{$row['moughataa']}' pour wilaya_id: {$this->wilaya_id}");
            return null;
        }

        // Vérifier Commune
        $commune = $this->findCommune($row['commune'] ?? null, $moughata);
        if (!$commune) {
            $this->skippedRows[] = [
                'row' => $row,
                'reason' => "Commune non trouvée: '{$row['commune']}' pour moughata: {$moughata->intitule} (ID: {$moughata->id})"
            ];
            \Log::warning("Commune non trouvée: '{$row['commune']}' pour moughata: {$moughata->intitule}");
            return null;
        }

        // Vérifier Localite
        $localite = $this->findLocalite($row['localite'] ?? null, $row['code_ansade'] ?? null, $commune, $moughata);
        if (!$localite) {
            $this->skippedRows[] = [
                'row' => $row,
                'reason' => "Localité non trouvée: '{$row['localite']}' (code_ansade: " . ($row['code_ansade'] ?? 'N/A') . ") pour commune: {$commune->intitule} (ID: {$commune->id})"
            ];
            \Log::warning("Localité non trouvée: '{$row['localite']}' pour commune: {$commune->intitule}");
            return null;
        }

        // Vérifier Projet — projet_id est NOT NULL en base (localite_operations).
        // Une valeur vide/placeholder ("Inconnu", "NR"...) OU une valeur
        // renseignée mais introuvable retombent toutes deux sur le projet
        // "NR" (Non renseigné) plutôt que de sauter la ligne.
        $projetRaw = trim((string) ($row['int_projet_resum'] ?? ''));
        $projet = $this->isPlaceholderValue($projetRaw) ? null : $this->findProjet($projetRaw);
        if (!$projet) {
            $projet = $this->nrProjet;
        }
        if (!$projet) {
            $this->skippedRows[] = [
                'row' => $row,
                'reason' => "Projet introuvable ('{$row['int_projet_resum']}') et aucun projet \"NR\" en base"
            ];
            \Log::warning("Projet introuvable et pas de NR: '{$row['int_projet_resum']}'");
            return null;
        }

        // Vérifier Opérateur — operateur_id est aussi NOT NULL en base, même logique.
        $operateurRaw = trim((string) ($row['intitule_se'] ?? ''));
        $operateur = $this->isPlaceholderValue($operateurRaw) ? null : $this->findOperateur($operateurRaw);
        if (!$operateur) {
            $operateur = $this->nrOperateur;
        }
        if (!$operateur) {
            $this->skippedRows[] = [
                'row' => $row,
                'reason' => "Opérateur introuvable ('{$row['intitule_se']}') et aucun opérateur \"NR\" en base"
            ];
            \Log::warning("Opérateur introuvable et pas de NR: '{$row['intitule_se']}'");
            return null;
        }

        return compact('moughata', 'commune', 'localite', 'projet', 'operateur');
    }

    /**
     * Recherche une Moughata avec LIKE %...%
     */
    protected function findMoughata($intitule)
    {
        if (empty($intitule)) {
            return null;
        }

        $normalized = $this->normalizeString($intitule);
        
        // 1. Recherche exacte normalisée dans la collection
        $result = $this->moughatas->first(function ($item) use ($intitule, $normalized) {
            return $this->stringsMatch($item->intitule, $intitule);
        });

        if ($result) {
            return $result;
        }

        // 2. Recherche en base avec LIKE %...%
        $result = Moughata::where('wilaya_id', $this->wilaya_id)
            ->where(function ($query) use ($intitule, $normalized) {
                $query->where('intitule', 'LIKE', '%' . $intitule . '%')
                      ->orWhere('intitule', 'LIKE', '%' . $normalized . '%')
                      ->orWhere('intitule_fr', 'LIKE', '%' . $intitule . '%')
                      ->orWhere('intitule_fr', 'LIKE', '%' . $normalized . '%');
            })
            ->first();

        if ($result) {
            // Ajouter à la collection pour les prochaines recherches
            if (!$this->moughatas->contains('id', $result->id)) {
                $this->moughatas->push($result);
            }
            return $result;
        }

        return null;
    }

    /**
     * Recherche une Commune avec LIKE %...%
     */
    protected function findCommune($intitule, $moughata)
    {
        if (empty($intitule) || !$moughata) {
            return null;
        }

        $normalized = $this->normalizeString($intitule);
        
        // 1. Recherche dans la collection
        $result = $this->communes->first(function ($item) use ($intitule, $moughata) {
            return $item->moughata_id === $moughata->id && 
                   $this->stringsMatch($item->intitule, $intitule);
        });

        if ($result) {
            return $result;
        }

        // 2. Recherche en base avec LIKE %...%
        $result = Commune::where('moughata_id', $moughata->id)
            ->where(function ($query) use ($intitule, $normalized) {
                $query->where('intitule', 'LIKE', '%' . $intitule . '%')
                      ->orWhere('intitule', 'LIKE', '%' . $normalized . '%')
                      ->orWhere('intitule_fr', 'LIKE', '%' . $intitule . '%')
                      ->orWhere('intitule_fr', 'LIKE', '%' . $normalized . '%');
            })
            ->first();

        if ($result) {
            if (!$this->communes->contains('id', $result->id)) {
                $this->communes->push($result);
            }
            return $result;
        }

        return null;
    }

    /**
     * Recherche une Localite avec LIKE %...%
     */
    protected function findLocalite($intitule, $codeAnsade, $commune, $moughata = null)
    {
        if (empty($intitule) || !$commune) {
            return null;
        }

        // Localité non codifiée dans l'Excel → code_ansade = null
        $codeAnsade = !empty($codeAnsade) ? strval(trim($codeAnsade)) : null;

        $normalized = $this->normalizeString($intitule);

        // 1. Recherche avec code_ansade si fourni
        if ($codeAnsade !== null) {
            $result = $this->localites->first(function ($item) use ($codeAnsade, $commune) {
                return $item->commune_id === $commune->id &&
                       !empty($item->code_ansade) &&
                       strval($item->code_ansade) === $codeAnsade;
            });

            if ($result) {
                return $result;
            }
        }

        // 2. Recherche par intitulé dans la collection
        $result = $this->localites->first(function ($item) use ($intitule, $commune) {
            return $item->commune_id === $commune->id &&
                   $this->stringsMatch($item->intitule, $intitule);
        });

        if ($result) {
            // Mettre à jour le code_ansade uniquement si fourni dans l'Excel et absent en base
            if ($codeAnsade !== null && empty($result->code_ansade)) {
                Localite::where('id', $result->id)->update(['code_ansade' => $codeAnsade]);
                $result->code_ansade = $codeAnsade;
            }
            return $result;
        }

        // 3. Recherche en base avec LIKE %...%
        $query = Localite::where('commune_id', $commune->id);

        if ($codeAnsade !== null) {
            $query->where('code_ansade', $codeAnsade);
        }
        
        $result = $query->where(function ($q) use ($intitule, $normalized) {
            $q->where('intitule', 'LIKE', '%' . $intitule . '%')
              ->orWhere('intitule', 'LIKE', '%' . $normalized . '%')
              ->orWhere('intitule_fr', 'LIKE', '%' . $intitule . '%')
              ->orWhere('intitule_fr', 'LIKE', '%' . $normalized . '%');
        })->first();

        if ($result) {
            if (!$this->localites->contains('id', $result->id)) {
                $this->localites->push($result);
            }
            return $result;
        }

        // 4. Repli : même moughata, commune sœur — capte les localités
        // réaffectées à une autre commune du même moughata pendant la
        // réorganisation, alors que le fichier ne connaît que l'ancien
        // découpage plus grossier (ex: toutes les localités listées sous la
        // commune "OUALATA" alors qu'une partie a depuis été isolée dans la
        // commune "NOUAWDER", toutes deux sous la moughataa OUALATA).
        // Exact match uniquement : la recherche floue habituelle serait trop
        // risquée sur un périmètre élargi à tout le moughata.
        if ($moughata) {
            $siblingCommuneIds = $this->communes->where('moughata_id', $moughata->id)->pluck('id')->all();
            $result = $this->localites->first(function ($item) use ($normalized, $siblingCommuneIds, $commune) {
                return $item->commune_id !== $commune->id
                    && in_array($item->commune_id, $siblingCommuneIds, true)
                    && $this->normalizeString($item->intitule) === $normalized;
            });
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Recherche un Projet avec LIKE %...%
     */
    protected function findProjet($intituleResume)
    {
        if (empty($intituleResume)) {
            return null;
        }

        $normalized = $this->normalizeString($intituleResume);
        
        // 1. Recherche dans la collection
        $result = $this->projets->first(function ($item) use ($intituleResume) {
            return $this->stringsMatch($item->intitule_resume, $intituleResume);
        });

        if ($result) {
            return $result;
        }

        // 2. Recherche en base avec LIKE %...% (la table `projets` n'a pas de
        // colonne `intitule`, seulement `intitule_resume`/`intitule_complet`)
        $result = Projet::where(function ($query) use ($intituleResume, $normalized) {
            $query->where('intitule_resume', 'LIKE', '%' . $intituleResume . '%')
                  ->orWhere('intitule_resume', 'LIKE', '%' . $normalized . '%')
                  ->orWhere('intitule_complet', 'LIKE', '%' . $intituleResume . '%')
                  ->orWhere('intitule_complet', 'LIKE', '%' . $normalized . '%');
        })->first();

        if ($result) {
            if (!$this->projets->contains('id', $result->id)) {
                $this->projets->push($result);
            }
            return $result;
        }

        return null;
    }

    /**
     * Recherche un Opérateur avec LIKE %...%
     */
    protected function findOperateur($intitule)
    {
        if (empty($intitule)) {
            return null;
        }

        $normalized = $this->normalizeString($intitule);
        
        // 1. Recherche dans la collection
        $result = $this->operateurs->first(function ($item) use ($intitule) {
            return $this->stringsMatch($item->intitule, $intitule);
        });

        if ($result) {
            return $result;
        }

        // 2. Recherche en base avec LIKE %...%
        $result = Operateur::where(function ($query) use ($intitule, $normalized) {
            $query->where('intitule', 'LIKE', '%' . $intitule . '%')
                  ->orWhere('intitule', 'LIKE', '%' . $normalized . '%');
        })->first();

        if ($result) {
            if (!$this->operateurs->contains('id', $result->id)) {
                $this->operateurs->push($result);
            }
            return $result;
        }

        return null;
    }

    /**
     * Vérifie si un rapport existe déjà pour cette combinaison
     */
    protected function findExistingReport($entities)
    {
        if (!$entities || !isset($entities['localite'])) {
            return null;
        }

        $query = $this->existingReports->where('localite_id', $entities['localite']->id)
            ->where('operation_id', $this->operation_id)
            ->where('wilaya_id', $this->wilaya_id);

        // Affiner par projet si fourni
        if (isset($entities['projet']) && $entities['projet']) {
            $query = $query->where('projet_id', $entities['projet']->id);
        } else {
            $query = $query->whereNull('projet_id');
        }

        // Affiner par opérateur si fourni
        if (isset($entities['operateur']) && $entities['operateur']) {
            $query = $query->where('operateur_id', $entities['operateur']->id);
        } else {
            $query = $query->whereNull('operateur_id');
        }

        return $query->first();
    }

    /**
     * Prépare les données de base communes
     */
    protected function prepareBaseData($entities)
    {
        // Always derive commune_id/moughata_id from the MATCHED localite's
        // actual parent chain, never from the separately-resolved file-based
        // commune/moughata — findLocalite()'s sibling-commune fallback can
        // return a localite that lives in a different (but same-moughata)
        // commune than the one the file's own "commune" column named, and
        // the operation record must stay consistent with where the localite
        // really is, not with what the file happened to say.
        $localite = $entities['localite'];
        $commune = $this->communes->firstWhere('id', $localite->commune_id) ?? $entities['commune'];
        $moughata = $this->moughatas->firstWhere('id', $commune->moughata_id) ?? $entities['moughata'];

        return [
            'localite_id' => $localite->id,
            'commune_id' => $commune->id,
            'moughata_id' => $moughata->id,
            'wilaya_id' => $this->wilaya_id,
            'operation_id' => $this->operation_id,
            'projet_id' => $entities['projet'] ? $entities['projet']->id : null,
            'operateur_id' => $entities['operateur'] ? $entities['operateur']->id : null,
            'user_id' => Auth::id(),
            'updated_at' => now()
        ];
    }

    /**
     * Convertit une date Excel
     */
    protected function convertDate($value, $defaultMonth = '12', $defaultDay = '31')
    {
        if (empty($value)) {
            return null;
        }

        if ($this->isExcelDate(strval($value))) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        // Essayer de parser la date
        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        // Fallback avec année seule
        $year = intval($value);
        if ($year > 1900 && $year < 2100) {
            return date("Y-{$defaultMonth}-{$defaultDay}", strtotime("{$year}-{$defaultMonth}-{$defaultDay}"));
        }

        return null;
    }

    /**
     * Vérifie si la valeur est une date Excel
     */
    protected function isExcelDate($value)
    {
        if (is_string($value) && strlen($value) > 0) {
            return $value[0] === '4';
        }
        return false;
    }

    /**
     * Retourne le nombre de lignes importées
     */
    public function getRowCount(): int
    {
        $count_after = LocaliteReport::count();
        return $count_after - $this->count_before;
    }

    /**
     * Retourne les lignes ignorées
     */
    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }

    /**
     * Retourne le nombre de lignes ignorées
     */
    public function getSkippedCount(): int
    {
        return count($this->skippedRows);
    }

    /**
     * Retourne les erreurs
     */
    public function getErrorRows(): array
    {
        return $this->errorRows;
    }

    abstract protected function getSpecificData(array $row, $entities);
}