<?php

namespace App\Services;

use App\Models\Commune;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Wilaya;
use Illuminate\Support\Facades\DB;

/**
 * Centralizes moving nodes of the wilaya -> moughata -> commune -> localite
 * hierarchy under a new parent. Neither MoughatasController nor
 * CommunesController cascades a parent change down to its children today —
 * this service is the single place that does, so both the reform tooling
 * and any future admin action stay consistent.
 */
class HierarchyReassignmentService
{
    /**
     * Move the given moughatas under a new wilaya, cascading wilaya_id down
     * to every commune and localite under them.
     */
    public function assignMoughatasToWilaya(array $moughataIds, int $wilayaId): int
    {
        $moughataIds = array_values(array_unique(array_filter($moughataIds)));
        if (empty($moughataIds)) {
            return 0;
        }

        $wilaya = Wilaya::findOrFail($wilayaId);

        return DB::transaction(function () use ($moughataIds, $wilaya) {
            $count = Moughata::whereIn('id', $moughataIds)->update([
                'wilaya_id' => $wilaya->id,
            ]);

            Commune::whereIn('moughata_id', $moughataIds)->update([
                'wilaya_id' => $wilaya->id,
            ]);

            Localite::whereIn('moughata_id', $moughataIds)->update([
                'wilaya_id' => $wilaya->id,
            ]);

            return $count;
        });
    }

    /**
     * Move the given communes under a new moughata, cascading wilaya_id to
     * the communes themselves and to every localite under them.
     */
    public function assignCommunesToMoughata(array $communeIds, int $moughataId): int
    {
        $communeIds = array_values(array_unique(array_filter($communeIds)));
        if (empty($communeIds)) {
            return 0;
        }

        $moughata = Moughata::findOrFail($moughataId);

        return DB::transaction(function () use ($communeIds, $moughata) {
            $count = Commune::whereIn('id', $communeIds)->update([
                'moughata_id' => $moughata->id,
                'wilaya_id' => $moughata->wilaya_id,
            ]);

            Localite::whereIn('commune_id', $communeIds)->update([
                'moughata_id' => $moughata->id,
                'wilaya_id' => $moughata->wilaya_id,
            ]);

            return $count;
        });
    }

    /**
     * Move the given localites under a new commune, cascading moughata_id
     * and wilaya_id from that commune.
     */
    public function assignLocalitesToCommune(array $localiteIds, int $communeId): int
    {
        $localiteIds = array_values(array_unique(array_filter($localiteIds)));
        if (empty($localiteIds)) {
            return 0;
        }

        $commune = Commune::findOrFail($communeId);

        return DB::transaction(function () use ($localiteIds, $commune) {
            return Localite::whereIn('id', $localiteIds)->update([
                'commune_id' => $commune->id,
                'moughata_id' => $commune->moughata_id,
                'wilaya_id' => $commune->wilaya_id,
            ]);
        });
    }
}
