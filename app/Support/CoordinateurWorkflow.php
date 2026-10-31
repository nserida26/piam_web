<?php

namespace App\Support;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

/**
 * Double validation des rapports : le coordinateur de l'ONG valide d'abord
 * (statut_coordinateur = 'valide'), puis l'admin (confirmed = 1).
 *
 * La validation admin n'exige l'étape coordinateur que pour les ONG qui ont
 * un coordinateur : les données des autres ONG (et l'historique importé)
 * restent validables directement par l'admin, comme avant.
 */
class CoordinateurWorkflow
{
    /** ONG (operateur_id) qui ont au moins un coordinateur. */
    public static function coordinatedOngIds(): array
    {
        $roleIds = Role::where('name', 'like', '%coordinat%')->pluck('id');
        if ($roleIds->isEmpty()) {
            return [];
        }

        return DB::table('users')
            ->whereIn('id', DB::table('role_user')->whereIn('role_id', $roleIds)->pluck('user_id'))
            ->whereNotNull('operateur_id')
            ->distinct()
            ->pluck('operateur_id')
            ->all();
    }

    /**
     * Parmi $ids, ceux que l'admin ne peut pas encore valider : rapport d'une
     * ONG avec coordinateur, pas encore validé par celui-ci.
     */
    public static function idsAwaitingCoordinateur(string $table, array $ids): array
    {
        $ongs = self::coordinatedOngIds();
        if (empty($ongs) || empty($ids)) {
            return [];
        }
        $ongUsers = DB::table('users')->whereIn('operateur_id', $ongs)->pluck('id')->all();

        return DB::table($table)
            ->whereIn('id', $ids)
            ->where(function ($q) {
                $q->whereNull('statut_coordinateur')->orWhere('statut_coordinateur', '!=', 'valide');
            })
            ->where(function ($q) use ($ongs, $ongUsers) {
                $q->whereIn('operateur_id', $ongs)->orWhereIn('user_id', $ongUsers ?: [0]);
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public static function blockedMessage(int $count): string
    {
        return "$count ligne(s) non validée(s) : elles doivent d'abord être validées par le coordinateur de l'ONG.";
    }
}
