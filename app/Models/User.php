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
}
