<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'infrastructures';

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
    protected $fillable = ['long_infra_publ', 'lat_infra_publ', 'infra_publ', 'code_infra_publ','intitule_infra_publ','code_men', 'localite_id','wilaya_id','moughata_id','commune_id'];

    public function localite()
    {
        return $this->hasOne('App\Models\Localite');
    }


}
