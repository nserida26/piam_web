<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'communes';

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
    protected $fillable = ['code', 'intitule', 'intitule_fr', 'moughata_id','wilaya_id'];

    public function moughata()
    {
        return $this->belongsTo(Moughata::class);
    }
    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class);
    }
    
}
