<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** 
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'photo',
        'status',
        'password',
        'operateur_id',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Every user has many roles 
     *
     * @return void
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class); 
    }

    /**
     * Every user has many permissions
     *
     * @return void
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasRole(string $name): bool
    {
        return $this->roles->pluck('name')->contains($name);
    }

    /**
     * Localités attribuées (facilitateur) : l'utilisateur ne peut enquêter
     * que sur celles-ci.
     */
    public function localites()
    {
        return $this->belongsToMany(Localite::class, 'user_localites')->withTimestamps();
    }

    /**
     * Ids des localités autorisées, ou null si l'utilisateur n'a aucune
     * attribution (pas de restriction).
     */
    public function allowedLocaliteIds(): ?array
    {
        $ids = $this->localites()->pluck('localites.id')->all();
        return empty($ids) ? null : $ids;
    }

    public function canWorkOnLocalite($localiteId): bool
    {
        $allowed = $this->allowedLocaliteIds();
        return $allowed === null || in_array((int) $localiteId, $allowed, true);
    }

    /** ONG (table operateurs) de l'utilisateur. */
    public function operateur()
    {
        return $this->belongsTo(Operateur::class);
    }

    /** Projets assignés (facilitateur). */
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'user_projets')->withTimestamps();
    }

    /** Les noms de rôles en base ne sont pas homogènes (« Faciltateur »). */
    private function hasRoleLike(string $fragment): bool
    {
        return $this->roles->contains(fn ($r) => stripos($r->name, $fragment) !== false);
    }

    public function isCoordinateur(): bool
    {
        return $this->hasRoleLike('coordinat');
    }

    public function isFacilitateur(): bool
    {
        return $this->hasRoleLike('facil');
    }

    /**
     * Ids des utilisateurs de la même ONG (coordinateur compris). Sert à
     * rattacher à l'ONG les rapports dont operateur_id n'est pas renseigné.
     */
    public function ongUserIds(): array
    {
        if (!$this->operateur_id) {
            return [];
        }
        return static::where('operateur_id', $this->operateur_id)->pluck('id')->all();
    }

    /**
     * Restreint une requête de rapports (localite_operations /
     * infrastructure_operations) aux données de l'ONG de l'utilisateur.
     */
    public function restrictReportsToOng($query, string $table)
    {
        $ong = $this->operateur_id;
        $users = $this->ongUserIds();

        return $query->where(function ($q) use ($table, $ong, $users) {
            $q->where("$table.operateur_id", $ong ?: 0)
              ->orWhereIn("$table.user_id", $users ?: [0]);
        });
    }

    /** Projets de l'ONG : ceux de ses marchés. */
    public function ongProjets()
    {
        if (!$this->operateur_id) {
            return Projet::whereRaw('0 = 1');
        }
        $ids = \Illuminate\Support\Facades\DB::table('marches')
            ->where('operateur_id', $this->operateur_id)
            ->pluck('projet_id')->unique();
        return Projet::whereIn('id', $ids);
    }
}
