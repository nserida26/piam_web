<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localite extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'localites';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 
        'intitule', 
        'intitule_fr', 
        'nb_pop_ansade', 
        'code_ansade', 
        'long_loc', 
        'lat_loc', 
        'moughata_id', 
        'wilaya_id', 
        'commune_id',
        'valider',
        'ancien_code_ansade'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'valider' => 'boolean',
        'nb_pop_ansade' => 'integer'
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

    /**
     * Scope pour les localités validées
     */
    public function scopeValide($query)
    {
        return $query->where('valider', true);
    }

    /**
     * Scope pour les localités non validées
     */
    public function scopeNonValide($query)
    {
        return $query->where('valider', false);
    }

    /**
     * Scope pour rechercher par code ou intitulé
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('intitule', 'LIKE', "%{$search}%")
                    ->orWhere('intitule_fr', 'LIKE', "%{$search}%");
    }
}