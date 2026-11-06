<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Photos des rapports (ménage, localité, infrastructure) : table
 * report_photos (report_table + report_id), fichiers dans
 * public/uploads/rapports/<table>/AAAA/MM/.
 *
 * Envoi : l'appli mobile met les photos en base64 dans le même JSON que la
 * fiche (photos_base64 = liste, ou photo_base64 = une seule) ; le web envoie
 * des fichiers (photos[] ou photo). Quand une fiche est renvoyée AVEC des
 * photos, elles remplacent les précédentes ; renvoyée sans photo, elle garde
 * les siennes.
 */
class ReportPhotos
{
    const MAX_BYTES = 8 * 1024 * 1024;
    const MAX_PHOTOS = 10;
    const TYPES = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];

    public static function available(): bool
    {
        return Schema::hasTable('report_photos');
    }

    /** Images valides reçues (contenu binaire), au plus MAX_PHOTOS. */
    public static function received(Request $request): array
    {
        $raw = [];
        foreach ((array) $request->file('photos', []) as $file) {
            if ($file && $file->isValid()) {
                $raw[] = file_get_contents($file->getRealPath());
            }
        }
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $raw[] = file_get_contents($request->file('photo')->getRealPath());
        }
        $b64 = $request->input('photos_base64');
        $b64 = is_array($b64) ? $b64 : [];
        if (is_string($request->input('photo_base64')) && $request->input('photo_base64') !== '') {
            $b64[] = $request->input('photo_base64');
        }
        foreach ($b64 as $data) {
            if (!is_string($data) || $data === '') {
                continue;
            }
            if (stripos($data, 'base64,') !== false) {
                $data = substr($data, stripos($data, 'base64,') + 7); // data:image/jpeg;base64,...
            }
            $raw[] = base64_decode($data, true) ?: null;
        }

        $images = [];
        foreach ($raw as $bytes) {
            if (!$bytes || strlen($bytes) > self::MAX_BYTES) {
                continue;
            }
            $info = @getimagesizefromstring($bytes);
            if ($info && isset(self::TYPES[$info[2]])) {
                $images[] = [$bytes, self::TYPES[$info[2]]];
            }
            if (count($images) >= self::MAX_PHOTOS) {
                break;
            }
        }
        return $images;
    }

    /**
     * Enregistre les photos reçues pour ce rapport (remplace les anciennes).
     * Renvoie les chemins du rapport (inchangés si aucune photo reçue).
     */
    public static function syncFromRequest(string $table, int $reportId, Request $request, $userId): array
    {
        if (!self::available()) {
            return [];
        }
        $images = self::received($request);
        if ($images) {
            self::deleteAll($table, $reportId);
            $dir = 'rapports/' . $table . '/' . date('Y') . '/' . date('m');
            $abs = public_path('uploads/' . $dir);
            if (!is_dir($abs)) {
                mkdir($abs, 0755, true);
            }
            $now = now();
            foreach ($images as [$bytes, $ext]) {
                $name = $reportId . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                file_put_contents($abs . '/' . $name, $bytes);
                DB::table('report_photos')->insert([
                    'report_table' => $table, 'report_id' => $reportId, 'path' => $dir . '/' . $name,
                    'user_id' => $userId, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
        return self::paths($table, $reportId);
    }

    public static function paths(string $table, int $reportId): array
    {
        if (!self::available()) {
            return [];
        }
        return DB::table('report_photos')->where('report_table', $table)->where('report_id', $reportId)
            ->orderBy('id')->pluck('path')->all();
    }

    /** Chemins par rapport : [report_id => [path, …]] */
    public static function pathsFor(string $table, array $ids): array
    {
        if (!self::available() || !$ids) {
            return [];
        }
        $out = [];
        foreach (DB::table('report_photos')->where('report_table', $table)->whereIn('report_id', $ids)
                     ->orderBy('id')->get(['report_id', 'path']) as $p) {
            $out[$p->report_id][] = $p->path;
        }
        return $out;
    }

    public static function url(?string $path): ?string
    {
        return $path ? asset('uploads/' . $path) : null;
    }

    public static function urls(string $table, int $reportId): array
    {
        return array_map([self::class, 'url'], self::paths($table, $reportId));
    }

    public static function deleteAll(string $table, int $reportId): void
    {
        if (!self::available()) {
            return;
        }
        $rows = DB::table('report_photos')->where('report_table', $table)->where('report_id', $reportId);
        foreach ($rows->pluck('path') as $path) {
            self::deleteFile($path);
        }
        $rows->delete();
    }

    public static function deleteFile(?string $path): void
    {
        if (!$path || strpos($path, '..') !== false) {
            return;
        }
        $abs = public_path('uploads/' . $path);
        if (is_file($abs)) {
            @unlink($abs);
        }
    }
}
