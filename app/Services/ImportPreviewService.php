<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Wilaya;
use App\Models\Moughata;
use App\Models\Commune;
use App\Models\Localite;
use Illuminate\Support\Facades\Storage;

class ImportPreviewService
{
    private const TEMP_DISK = 'local';
    private const TEMP_PATH = 'imports/temp';

    // O(1) lookup indexes built once at construction
    private array $wilayaByName      = [];  // norm(name) => Wilaya
    private array $moughataByKey     = [];  // "norm(name)|wilaya_id" => Moughata
    private array $communeByKey      = [];  // "norm(name)|moughata_id" => Commune
    private array $localitesByCommune = []; // commune_id => [norm_name => Localite]
    private array $localiteNames     = [];  // norm(name) => true  (for report validation)

    public function __construct()
    {
        $this->buildIndexes();
    }

    private function buildIndexes(): void
    {
        foreach (Wilaya::all() as $w) {
            foreach ([$w->intitule, $w->intitule_fr] as $name) {
                $k = $this->norm($name);
                if ($k !== '') $this->wilayaByName[$k] = $w;
            }
        }

        foreach (Moughata::all() as $m) {
            foreach ([$m->intitule, $m->intitule_fr] as $name) {
                $k = $this->norm($name) . '|' . $m->wilaya_id;
                if (!str_starts_with($k, '|')) $this->moughataByKey[$k] = $m;
            }
        }

        foreach (Commune::all() as $c) {
            foreach ([$c->intitule, $c->intitule_fr] as $name) {
                $k = $this->norm($name) . '|' . $c->moughata_id;
                if (!str_starts_with($k, '|')) $this->communeByKey[$k] = $c;
            }
        }

        foreach (Localite::all() as $l) {
            foreach ([$l->intitule, $l->intitule_fr] as $name) {
                $n = $this->norm($name);
                if ($n === '') continue;
                $this->localitesByCommune[$l->commune_id][$n] = $l;
                $this->localiteNames[$n] = true;
            }
        }
    }

    // -------------------------------------------------------------------------
    // PUBLIC API
    // -------------------------------------------------------------------------

    public function storeTempFile($file): string
    {
        $ext  = $file->getClientOriginalExtension();
        $name = (string) \Illuminate\Support\Str::uuid() . '.' . $ext;
        $path = self::TEMP_PATH . '/' . $name;
        Storage::disk(self::TEMP_DISK)->put($path, file_get_contents($file->getPathname()));
        return $path;
    }

    public function getTempFilePath(string $relativePath): string
    {
        return Storage::disk(self::TEMP_DISK)->path($relativePath);
    }

    public function deleteTempFile(string $relativePath): void
    {
        Storage::disk(self::TEMP_DISK)->delete($relativePath);
    }

    public function previewAdministration($file, string $type): array
    {
        try {
            $rawData = Excel::toArray([], $file);
        } catch (\Throwable $e) {
            return $this->errorResult('Impossible de lire le fichier : ' . $e->getMessage());
        }

        $sheet = $rawData[0] ?? [];
        if (count($sheet) < 2) {
            return $this->errorResult('Le fichier est vide ou ne contient que les en-têtes.');
        }

        $headers  = $this->normalizeHeaders($sheet[0]);
        $dataRows = array_slice($sheet, 1);

        return $this->classifyAdminRows($headers, $dataRows, $type);
    }

    public function previewReport($file, int $operationId, int $wilayaId): array
    {
        try {
            $rawData = Excel::toArray([], $file);
        } catch (\Throwable $e) {
            return $this->errorResult('Impossible de lire le fichier : ' . $e->getMessage());
        }

        $sheet = $rawData[0] ?? [];
        if (count($sheet) < 2) {
            return $this->errorResult('Le fichier est vide ou ne contient que les en-têtes.');
        }

        $headers  = $this->normalizeHeaders($sheet[0]);
        $dataRows = array_slice($sheet, 1);

        return $this->classifyReportRows($headers, $dataRows, $operationId);
    }

    // -------------------------------------------------------------------------
    // ADMINISTRATION CLASSIFICATION
    // -------------------------------------------------------------------------

    private function classifyAdminRows(array $headers, array $dataRows, string $type): array
    {
        $stats       = ['total' => 0, 'new' => 0, 'update' => 0, 'error' => 0, 'warning' => 0];
        $previewRows = [];
        $allErrors   = [];

        foreach ($dataRows as $i => $raw) {
            $rowNum = $i + 2;
            $row    = $this->mapRow($headers, $raw);
            if ($this->isEmptyRow($row)) continue;

            $stats['total']++;
            $result = $this->validateAdminRow($row, $type);
            $stats[$result['status']]++;

            if ($result['status'] === 'error') {
                $allErrors[] = ['row' => $rowNum, 'reason' => $result['reason']];
            }

            $previewRows[] = [
                'row'    => $rowNum,
                'status' => $result['status'],
                'reason' => $result['reason'] ?? null,
                'data'   => array_slice($row, 0, 6, true),
            ];
        }

        return [
            'success'      => true,
            'stats'        => $stats,
            'headers'      => array_slice($headers, 0, 6),
            'preview_rows' => $previewRows,
            'all_errors'   => $allErrors,
        ];
    }

    private function validateAdminRow(array $row, string $type): array
    {
        switch ($type) {
            case 'wilaya':
                return $this->validateWilayaRow($row);
            case 'moughata':
                return $this->validateMoughataRow($row);
            case 'commune':
                return $this->validateCommuneRow($row);
            case 'localite':
                return $this->validateLocaliteRow($row);
            case 'infrastructure':
                return $this->validateInfrastructureRow($row);
            case 'operation':
                return $this->validateSimpleRow($row, 'operation', 'Opération');
            case 'operateur':
                return $this->validateSimpleRow($row, 'operateur', 'Opérateur');
            case 'projet':
                return $this->validateSimpleRow($row, 'projet', 'Projet');
            case 'unified':
                return $this->validateUnifiedRow($row);
            default:
                return ['status' => 'error', 'reason' => 'Type inconnu'];
        }
    }

    private function validateWilayaRow(array $row): array
    {
        $name = $row['wilaya'] ?? null;
        if (empty($name)) {
            return ['status' => 'error', 'reason' => 'Nom de wilaya manquant'];
        }
        $existing = $this->wilayaByName[$this->norm($name)] ?? null;
        if (!$existing && !empty($row['code'])) {
            $existing = Wilaya::where('code', $row['code'])->first();
        }
        return $existing
            ? ['status' => 'update', 'reason' => "Mise à jour : {$existing->intitule}"]
            : ['status' => 'new',    'reason' => 'Nouvelle wilaya'];
    }

    private function validateMoughataRow(array $row): array
    {
        $name = $row['moughataa'] ?? null;
        if (empty($name)) {
            return ['status' => 'error', 'reason' => 'Nom de moughata manquant'];
        }
        $wilaya = $this->wilayaByName[$this->norm($row['wilaya'] ?? '')] ?? null;
        if (!$wilaya) {
            return ['status' => 'warning', 'reason' => 'Wilaya introuvable — ligne ignorée à l\'import'];
        }
        $existing = $this->moughataByKey[$this->norm($name) . '|' . $wilaya->id] ?? null;
        return $existing
            ? ['status' => 'update', 'reason' => "Mise à jour : {$existing->intitule}"]
            : ['status' => 'new',    'reason' => 'Nouvelle moughata'];
    }

    private function validateCommuneRow(array $row): array
    {
        $name = $row['commune'] ?? null;
        if (empty($name)) {
            return ['status' => 'error', 'reason' => 'Nom de commune manquant'];
        }
        $wilaya   = $this->wilayaByName[$this->norm($row['wilaya'] ?? '')] ?? null;
        $moughata = $wilaya
            ? ($this->moughataByKey[$this->norm($row['moughataa'] ?? '') . '|' . $wilaya->id] ?? null)
            : null;
        if (!$moughata) {
            return ['status' => 'warning', 'reason' => 'Moughata introuvable — ligne ignorée à l\'import'];
        }
        $existing = $this->communeByKey[$this->norm($name) . '|' . $moughata->id] ?? null;
        return $existing
            ? ['status' => 'update', 'reason' => "Mise à jour : {$existing->intitule}"]
            : ['status' => 'new',    'reason' => 'Nouvelle commune'];
    }

    private function validateLocaliteRow(array $row): array
    {
        $name = $row['localite'] ?? null;
        if (empty($name)) {
            return ['status' => 'error', 'reason' => 'Nom de localité manquant'];
        }

        $wilaya   = $this->wilayaByName[$this->norm($row['wilaya'] ?? '')] ?? null;
        $moughata = $wilaya
            ? ($this->moughataByKey[$this->norm($row['moughataa'] ?? '') . '|' . $wilaya->id] ?? null)
            : null;
        $commune  = $moughata
            ? ($this->communeByKey[$this->norm($row['commune'] ?? '') . '|' . $moughata->id] ?? null)
            : null;

        if (!$commune) {
            return ['status' => 'warning', 'reason' => 'Commune introuvable — ligne ignorée à l\'import'];
        }

        // Check DB only: exact normalized name within the SAME commune
        // (commune was resolved through the wilaya → moughata → commune chain,
        //  so matching on commune_id already implies the correct moughata and wilaya)
        $normName = $this->norm($name);
        $existing = $this->localitesByCommune[$commune->id][$normName] ?? null;

        return $existing
            ? ['status' => 'update', 'reason' => "Mise à jour : {$existing->intitule}"]
            : ['status' => 'new',    'reason' => 'Nouvelle localité'];
    }


    private function validateInfrastructureRow(array $row): array
    {
        $name = $row['intitule_infra_publ'] ?? $row['nom'] ?? null;
        if (empty($name)) {
            return ['status' => 'error', 'reason' => 'Nom d\'infrastructure manquant'];
        }
        if (empty($row['localite'])) {
            return ['status' => 'warning', 'reason' => 'Localité non spécifiée'];
        }
        return ['status' => 'new', 'reason' => 'Infrastructure à importer'];
    }

    private function validateSimpleRow(array $row, string $field, string $label): array
    {
        if (empty($row[$field])) {
            return ['status' => 'error', 'reason' => "Nom de {$label} manquant"];
        }
        return ['status' => 'new', 'reason' => "{$label} à importer"];
    }

    private function validateUnifiedRow(array $row): array
    {
        if (empty($row['wilaya'])) {
            return ['status' => 'error', 'reason' => 'Wilaya manquante'];
        }
        if (empty($row['localite'])) {
            return ['status' => 'warning', 'reason' => 'Localité manquante (seuls les niveaux supérieurs seront créés)'];
        }
        return ['status' => 'new', 'reason' => 'Hiérarchie complète à importer'];
    }

    // -------------------------------------------------------------------------
    // REPORT CLASSIFICATION
    // -------------------------------------------------------------------------

    private function classifyReportRows(array $headers, array $dataRows, int $opId): array
    {
        $stats       = ['total' => 0, 'new' => 0, 'update' => 0, 'error' => 0, 'warning' => 0];
        $previewRows = [];
        $allErrors   = [];
        $required    = $this->getRequiredReportFields($opId);

        foreach ($dataRows as $i => $raw) {
            $rowNum = $i + 2;
            $row    = $this->mapRow($headers, $raw);
            if ($this->isEmptyRow($row)) continue;

            $stats['total']++;
            $result = $this->validateReportRow($row, $required);
            $stats[$result['status']]++;

            if ($result['status'] === 'error') {
                $allErrors[] = ['row' => $rowNum, 'reason' => $result['reason']];
            }

            $previewRows[] = [
                'row'    => $rowNum,
                'status' => $result['status'],
                'reason' => $result['reason'] ?? null,
                'data'   => array_slice($row, 0, 6, true),
            ];
        }

        return [
            'success'      => true,
            'stats'        => $stats,
            'headers'      => array_slice($headers, 0, 6),
            'preview_rows' => $previewRows,
            'all_errors'   => $allErrors,
        ];
    }

    private function validateReportRow(array $row, array $required): array
    {
        $missing = [];
        foreach ($required as $field) {
            if (empty($row[$field])) $missing[] = $field;
        }
        if (!empty($missing)) {
            return ['status' => 'error', 'reason' => 'Champs manquants : ' . implode(', ', $missing)];
        }

        $localiteName = $row['localite'] ?? $row['nom_localite'] ?? null;
        if ($localiteName && !isset($this->localiteNames[$this->norm($localiteName)])) {
            return ['status' => 'warning', 'reason' => "Localité introuvable : {$localiteName}"];
        }

        return ['status' => 'new', 'reason' => 'Rapport à importer'];
    }

    private function getRequiredReportFields(int $opId): array
    {
        $common = ['localite'];
        $extra  = [
            1 => [],
            2 => [],
            //'nb_menages_compt', 'nb_hom_compt', 'nb_fem_compt'
            3 => [],
            4 => [],
            //'nb_menages_compt'
            5 => [],
            6 => [],
            7 => [],
        ];
        return array_merge($common, $extra[$opId] ?? []);
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private function norm(?string $s): string
    {
        if ($s === null || $s === '') return '';
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = preg_replace('/\s+/', ' ', $s);
        static $search  = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', 'ñ', 'œ', 'æ'];
        static $replace = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c', 'n', 'oe', 'ae'];
        $s = str_replace($search, $replace, $s);
        $s = preg_replace('/[^a-z0-9 ]/', '', $s);
        return trim($s);
    }

    private function normalizeHeaders(array $row): array
    {
        return array_map(function ($cell) {
            $cell = (string)($cell ?? '');
            $cell = mb_strtolower(trim($cell), 'UTF-8');
            $cell = preg_replace('/\s+/', '_', $cell);
            $cell = preg_replace('/[^a-z0-9_]/', '', $cell);
            return $cell;
        }, $row);
    }

    private function mapRow(array $headers, array $values): array
    {
        $mapped = [];
        foreach ($headers as $i => $header) {
            if (!empty($header)) {
                $mapped[$header] = $values[$i] ?? null;
            }
        }
        return $mapped;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $v) {
            if ($v !== null && trim((string)$v) !== '') return false;
        }
        return true;
    }

    private function errorResult(string $message): array
    {
        return ['success' => false, 'error' => $message, 'stats' => [], 'preview_rows' => []];
    }
}
