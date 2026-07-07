<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOperateur extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_operateurs';

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
    protected $fillable = ['date_valid', 'operateur_id', 'projet_id', 'operation_id', 'user_id'];

    public function operateur()
    {
        return $this->belongsTo('App\Models\Operateur');
    }
    public function projet()
    {
        return $this->belongsTo('App\Models\Projet');
    }
    public function operation()
    {
        return $this->belongsTo('App\Models\Operation');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
