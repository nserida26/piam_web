<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Notifications des facilitateurs (table user_notifications) : lues par
 * l'appli mobile (GET /api/notifications) et dans le panneau web (cloche).
 *
 * Envoyées quand un rapport est rejeté par le coordinateur, par l'admin
 * agissant à sa place, ou par l'admin lors de sa propre validation.
 */
class Notifier
{
    const LIBELLES = [
        'localite_operations' => 'Rapport localité',
        'infrastructure_operations' => 'Rapport infrastructure',
        'menages' => 'Fiche ménage',
    ];

    public static function available(): bool
    {
        return Schema::hasTable('user_notifications');
    }

    /**
     * Prévient le(s) facilitateur(s) de chaque rapport rejeté.
     *
     * @param string      $table  localite_operations | infrastructure_operations | menages
     * @param int[]       $ids
     * @param string|null $motif
     * @param object|null $par    utilisateur qui rejette
     * @param string      $role   « le coordinateur » / « l'administrateur »
     */
    public static function rejet(string $table, array $ids, ?string $motif, $par, string $role): int
    {
        if (!self::available() || !$ids) {
            return 0;
        }

        $query = DB::table($table)
            ->leftJoin('localites', 'localites.id', '=', "$table.localite_id")
            ->leftJoin('operations', 'operations.id', '=', "$table.operation_id")
            ->whereIn("$table.id", $ids)
            ->select("$table.id", "$table.user_id", "$table.mobile_ref", "$table.localite_id",
                "$table.operation_id", 'localites.intitule as localite', 'operations.intitule as operation');
        if ($table !== 'menages') {
            $query->addSelect("$table.date_op", "$table.projet_id");
        } else {
            $query->addSelect("$table.nom_chef_menage", "$table.created_at as date_op");
        }
        if ($table === 'infrastructure_operations') {
            $query->leftJoin('infrastructures', 'infrastructures.id', '=', "$table.infrastructure_id")
                ->addSelect('infrastructures.intitule_infra_publ as infrastructure');
        }

        $now = now();
        $rows = [];
        foreach ($query->get() as $r) {
            $objet = trim(($r->operation ?: (self::LIBELLES[$table] ?? 'Rapport'))
                . (!empty($r->infrastructure) ? ' – ' . $r->infrastructure : '')
                . (!empty($r->nom_chef_menage) ? ' – ménage ' . $r->nom_chef_menage : '')
                . ' – ' . ($r->localite ?: 'localité ?')
                . ($r->date_op ? ' (' . substr($r->date_op, 0, 10) . ')' : ''));
            $message = "$objet : rejeté par $role" . ($par ? ' ' . ($par->username ?? $par->name ?? '') : '') . '.'
                . ($motif ? "\nMotif : $motif" : '')
                . "\nCorrigez la fiche dans l'application puis synchronisez-la à nouveau.";

            // Fiche localité fusionnée à partir des ménages : on prévient
            // chaque facilitateur qui a saisi un ménage de cette fiche.
            $destinataires = [$r->user_id];
            $mobileRef = $r->mobile_ref;
            if ($table === 'localite_operations' && strpos((string) $r->mobile_ref, 'menages:') === 0) {
                $destinataires = DB::table('menages')->where('localite_id', $r->localite_id)
                    ->where('operation_id', $r->operation_id)->where('projet_id', $r->projet_id)
                    ->distinct()->pluck('user_id')->all();
                $mobileRef = null; // pas de fiche locale correspondante dans l'appli
                $message = str_replace("Corrigez la fiche", "Corrigez les fiches ménage concernées", $message);
            }

            foreach (array_unique(array_filter($destinataires)) as $userId) {
                $rows[] = [
                    'user_id' => $userId,
                    'type' => 'rejet',
                    'titre' => 'Rapport rejeté',
                    'message' => $message,
                    'report_table' => $table,
                    'report_id' => $r->id,
                    'mobile_ref' => $mobileRef,
                    'par_user_id' => $par->id ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($rows) {
            DB::table('user_notifications')->insert($rows);
        }
        return count($rows);
    }

    public static function forUser(int $userId, int $limit = 50)
    {
        if (!self::available()) {
            return collect();
        }
        return DB::table('user_notifications')->where('user_id', $userId)
            ->orderByDesc('id')->limit($limit)->get();
    }

    public static function unreadCount(int $userId): int
    {
        if (!self::available()) {
            return 0;
        }
        return DB::table('user_notifications')->where('user_id', $userId)->whereNull('lu_le')->count();
    }

    /** Marque comme lues : toutes, ou seulement $ids. */
    public static function markRead(int $userId, ?array $ids = null): int
    {
        if (!self::available()) {
            return 0;
        }
        $q = DB::table('user_notifications')->where('user_id', $userId)->whereNull('lu_le');
        if ($ids !== null) {
            $q->whereIn('id', array_map('intval', $ids));
        }
        return $q->update(['lu_le' => now(), 'updated_at' => now()]);
    }
}
