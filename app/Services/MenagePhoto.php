<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Photo de la latrine d'un ménage (colonne menages.photo).
 *
 * Fichiers enregistrés dans public/uploads/menages/AAAA/MM/ (disque « public »
 * de config/filesystems.php) ; la colonne garde le chemin relatif
 * « menages/AAAA/MM/xxx.jpg », affiché avec MenagePhoto::url().
 *
 * L'appli mobile envoie la photo en base64 (champ photo_base64, dans le même
 * JSON que la fiche : pas de multipart, compatible avec l'envoi hors ligne
 * différé) ; le formulaire web envoie un fichier (champ photo).
 */
class MenagePhoto
{
    const MAX_BYTES = 8 * 1024 * 1024;

    const TYPES = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];

    /** Enregistre la photo reçue ; null si aucune photo ou photo invalide. */
    public static function fromRequest(Request $request, $userId): ?string
    {
        $bytes = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $bytes = file_get_contents($request->file('photo')->getRealPath());
        } elseif (is_string($request->photo_base64) && $request->photo_base64 !== '') {
            $data = $request->photo_base64;
            if (strpos($data, ',') !== false && stripos($data, 'base64') !== false) {
                $data = substr($data, strpos($data, ',') + 1); // data:image/jpeg;base64,...
            }
            $bytes = base64_decode($data, true) ?: null;
        }

        if (!$bytes || strlen($bytes) > self::MAX_BYTES) {
            return null;
        }
        $info = @getimagesizefromstring($bytes);
        if (!$info || !isset(self::TYPES[$info[2]])) {
            return null; // pas une image : ignorée, la fiche est quand même enregistrée
        }

        $dir = 'menages/' . date('Y') . '/' . date('m');
        $abs = public_path('uploads/' . $dir);
        if (!is_dir($abs)) {
            mkdir($abs, 0755, true);
        }
        $name = 'u' . (int) $userId . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . self::TYPES[$info[2]];
        file_put_contents($abs . '/' . $name, $bytes);

        return $dir . '/' . $name;
    }

    public static function url(?string $path): ?string
    {
        return $path ? asset('uploads/' . $path) : null;
    }

    public static function delete(?string $path): void
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
