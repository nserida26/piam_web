<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjetWilaya extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projet_wilayas';

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
    protected $fillable = ['wilaya_id', 'projet_id'];

    public function projet()
    {
        return $this->belongsTo('App\Models\Projet');
    }
    public function wilaya()
    {
        return $this->belongsTo('App\Models\Wilaya');
    }
    
}
