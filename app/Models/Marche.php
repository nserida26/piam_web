<?php

// App\Models\Marche.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marche extends Model
{
    protected $fillable = [
        'num', 'lot', 'date_sign', 'duree', 'montant',
        'nb_loc_adecl', 'nb_loc_asuiv_postfdal', 'nb_latfam_asubv',
        'nb_site_ims', 'nb_autre_cible', 'nb_bloc_a_const',
        'nb_cab_a_const', 'nb_cab_a_rehab', 'operateur_id', 'projet_id'
    ];

    protected $casts = [
        'date_sign' => 'date',
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function operateur()
    {
        return $this->belongsTo(Operateur::class);
    }

    public function wilayas()
    {
        return $this->belongsToMany(Wilaya::class, 'marche_wilayas');
    }

    public function partenaires()
    {
        return $this->belongsToMany(Partenaire::class, 'marche_partenaires');
    }
}