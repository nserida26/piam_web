<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilaya extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wilayas';

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
    protected $fillable = ['code', 'intitule', 'intitule_fr'];

    public function marches()
    {
        return $this->belongsToMany(Marche::class, 'marche_wilayas');
    }
    public function moughatas()
    {
        return $this->hasMany(Moughata::class);
    }
    
}
