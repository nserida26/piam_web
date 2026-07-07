<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayaUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wilaya_users';

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
    protected $fillable = ['wilaya_id', 'user_id'];

    public function wilaya()
    {
        return $this->belongsTo('App\Models\Wilaya');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
}
