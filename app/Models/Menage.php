<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Fiche ménage envoyée par l'app mobile (État des lieux / Dernier suivi,
 * niveau ménage). Validée dans « Gest. Validations > Ménages ».
 * Le scope WilayaScope (DRHA) est ajouté dans AppServiceProvider.
 */
class Menage extends Model
{
    protected $table = 'menages';

    protected $guarded = ['id'];

    protected $casts = [
        'confirmed' => 'boolean',
        'valide_le' => 'datetime',
    ];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class);
    }

    public function moughata()
    {
        return $this->belongsTo(Moughata::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function localite()
    {
        return $this->belongsTo(Localite::class);
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
