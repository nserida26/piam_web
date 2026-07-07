<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projets';

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
    protected $fillable = ['code', 'intitule_resume', 'intitule_resume_fr', 'intitule_complet', 'intitule_complet_fr', 'milieu_cible', 'periode'];
    public function marches()
    {
        return $this->hasMany(Marche::class);
    }
    
}
