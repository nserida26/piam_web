<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Photos + positions GPS d'un rapport, pour les écrans de validation
 * (coordinateur, admin) :
 *   ['photos' => [['url', 'label'], …], 'gps' => [['label', 'lat', 'lng', 'url'], …]]
 *
 * Une fiche localité fusionnée à partir des ménages (mobile_ref
 * « menages:… ») montre les photos et positions de chacun de ses ménages.
 */
class ReportMedia
{
    public static function forReport(string $table, $report): array
    {
        $photos = [];
        $gps = [];

        foreach (ReportPhotos::paths($table, (int) $report->id) as $i => $path) {
            $photos[] = ['url' => ReportPhotos::url($path), 'label' => 'Photo ' . ($i + 1)];
        }
        if ($p = self::point($report->latlng ?? null, 'Position du facilitateur')) {
            $gps[] = $p;
        }

        if ($table === 'infrastructure_operations' && !empty($report->infrastructure_id)) {
            $infra = DB::table('infrastructures')->where('id', $report->infrastructure_id)
                ->first(['intitule_infra_publ', 'lat_infra_publ', 'long_infra_publ']);
            if ($infra && ($p = self::point($infra->lat_infra_publ . ',' . $infra->long_infra_publ,
                    'Infrastructure ' . $infra->intitule_infra_publ))) {
                $gps[] = $p;
            }
        }

        if ($table === 'localite_operations' && strpos((string) ($report->mobile_ref ?? ''), 'menages:') === 0) {
            [$photosMenages, $gpsMenages] = self::menagesDeLaFiche($report);
            $photos = array_merge($photos, $photosMenages);
            $gps = array_merge($gps, $gpsMenages);
        }

        return ['photos' => $photos, 'gps' => $gps];
    }

    /** Photos et positions des ménages fusionnés dans une fiche localité. */
    private static function menagesDeLaFiche($report): array
    {
        $cols = ['id', 'latlng'];
        foreach (['nom_chef_menage', 'photo'] as $c) {
            if (Schema::hasColumn('menages', $c)) {
                $cols[] = $c;
            }
        }
        $menages = DB::table('menages')
            ->where('localite_id', $report->localite_id)
            ->where('operation_id', $report->operation_id)
            ->where('projet_id', $report->projet_id)
            ->orderBy('id')
            ->get($cols);

        $parMenage = ReportPhotos::pathsFor('menages', $menages->pluck('id')->all());
        $photos = [];
        $gps = [];
        foreach ($menages as $m) {
            $nom = 'Ménage ' . (!empty($m->nom_chef_menage) ? $m->nom_chef_menage : '#' . $m->id);
            $paths = $parMenage[$m->id] ?? (!empty($m->photo) ? [$m->photo] : []);
            foreach ($paths as $i => $path) {
                $photos[] = ['url' => ReportPhotos::url($path), 'label' => $nom . (count($paths) > 1 ? ' – ' . ($i + 1) : '')];
            }
            if ($p = self::point($m->latlng, $nom)) {
                $gps[] = $p;
            }
        }
        return [$photos, $gps];
    }

    /** « lat,lng » valide → point avec lien Google Maps ; null sinon. */
    public static function point(?string $latlng, string $label): ?array
    {
        if (!$latlng || !preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/', $latlng, $m)) {
            return null;
        }
        [$lat, $lng] = [(float) $m[1], (float) $m[2]];
        if (($lat == 0.0 && $lng == 0.0) || abs($lat) > 90 || abs($lng) > 180) {
            return null;
        }
        return [
            'label' => $label,
            'lat' => $lat,
            'lng' => $lng,
            'url' => "https://www.google.com/maps?q=$lat,$lng",
        ];
    }
}
