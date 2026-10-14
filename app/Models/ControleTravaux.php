<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControleTravaux extends Model
{
    protected $table = 'controle_travaux';

    protected $primaryKey = 'id';

    protected $fillable = [
        'mobile_ref',
        'intitule_site', 'type_etablissement', 'code_ansade', 'code_mesre', 'code_ms',
        'effectif_ecole', 'nb_usagers',
        'avancement', 'form_status', 'confirmed', 'date_op',
        'marche_travaux', 'marche_controle', 'travaux_a_realiser',
        'personnel', 'equipements', 'materiaux',
        'statuts', 'implantation', 'fondation', 'elevation', 'toit', 'enduits',
        'menuiserie', 'plomberie', 'peinture', 'revetement', 'dlm', 'garde_fou',
        'pges', 'mgp', 'appreciation', 'reception_technique', 'reception_provisoire',
        'wilaya_id', 'moughata_id', 'commune_id', 'localite_id',
        'projet_id', 'operateur_id', 'user_id',
    ];

    protected $casts = [
        'avancement' => 'float',
        'confirmed' => 'boolean',
        'date_op' => 'date',
        'marche_travaux' => 'array',
        'marche_controle' => 'array',
        'travaux_a_realiser' => 'array',
        'personnel' => 'array',
        'equipements' => 'array',
        'materiaux' => 'array',
        'statuts' => 'array',
        'implantation' => 'array',
        'fondation' => 'array',
        'elevation' => 'array',
        'toit' => 'array',
        'enduits' => 'array',
        'menuiserie' => 'array',
        'plomberie' => 'array',
        'peinture' => 'array',
        'revetement' => 'array',
        'dlm' => 'array',
        'garde_fou' => 'array',
        'pges' => 'array',
        'mgp' => 'array',
        'appreciation' => 'array',
        'reception_technique' => 'array',
        'reception_provisoire' => 'array',
    ];

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'wilaya_id');
    }

    public function moughata()
    {
        return $this->belongsTo(Moughata::class, 'moughata_id');
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    public function operateur()
    {
        return $this->belongsTo(Operateur::class, 'operateur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
