<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operateur extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'operateurs';

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
    protected $fillable = ['intitule', 'intitule_fr', 'type_struct'];

    public function marches()
    {
        return $this->hasMany(Marche::class);
    }
}
