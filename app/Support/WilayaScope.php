<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Compte DRHA (délégation régionale) connecté au panneau web : toutes les
 * données géographiques (wilayas, moughataas, communes, localités,
 * infrastructures, rapports) sont limitées à ses wilayas (table wilaya_users).
 *
 * Enregistré comme scope global sur les modèles dans AppServiceProvider ;
 * les requêtes DB::table() passent par WilayaScope::on().
 */
class WilayaScope implements Scope
{
    /** @var array<int, array|null> wilayas autorisées, par id d'utilisateur */
    private static $cache = [];

    /**
     * Wilayas autorisées pour l'utilisateur web connecté :
     * null = aucune restriction, [] = DRHA sans wilaya (ne voit rien).
     */
    public static function ids(): ?array
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return null;
        }

        if (!array_key_exists($user->id, self::$cache)) {
            self::$cache[$user->id] = $user->isDrha()
                ? DB::table('wilaya_users')->where('user_id', $user->id)
                    ->pluck('wilaya_id')->map(function ($id) { return (int) $id; })->unique()->values()->all()
                : null;
        }

        return self::$cache[$user->id];
    }

    public static function forget(): void
    {
        self::$cache = [];
    }

    /** Limite une requête à la colonne wilaya donnée (tableau vide => aucun résultat). */
    public static function restrict($query, string $column)
    {
        $ids = self::ids();
        if ($ids !== null) {
            $query->whereIn($column, $ids);
        }
        return $query;
    }

    /**
     * Pour les requêtes DB::table('localite_operations as x') :
     * ->tap(WilayaScope::on('x'))
     */
    public static function on(string $alias = 'localite_operations'): \Closure
    {
        return function ($query) use ($alias) {
            self::restrict($query, $alias . '.wilaya_id');
        };
    }

    public function apply(Builder $builder, Model $model)
    {
        $column = $model->getTable() === 'wilayas' ? 'id' : 'wilaya_id';
        self::restrict($builder, $model->qualifyColumn($column));
    }
}
