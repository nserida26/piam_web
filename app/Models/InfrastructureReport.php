<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfrastructureReport extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('confirmed_for_reporting', function (Builder $builder) {
            if (static::shouldApplyConfirmedScope()) {
                $builder->where($builder->getModel()->getTable() . '.confirmed', true);
            }
        });
    }

    public static function shouldApplyConfirmedScope(): bool
    {
        if (!app()->bound('request')) {
            return false;
        }
        $request = request();
        return $request->is('*/admin')
            || $request->is('*/admin/home')
            || $request->is('*/admin/fetch_general_wilaya')
            || $request->is('*/admin/fech_infrastructures')
            || $request->is('*/admin/reporting*');
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'infrastructure_operations';

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
        'mobile_ref',
        'effectif_garcon', 'effectif_fille', 'effectif', 'dist_approv_eau',
        'approv_eau', 'nbr_bloc', 'nb_dlm', 'nb_dlm_complet',
        'nb_dlm_incomplet', 'nb_sans_dlm', 'type_latr_publ', 'nbr_cabine_exist',
        'nbr_cabine_fonct', 'dist_cab_hom_fem', 'nbr_cabine_fem_hyg', 'gest_dechet',
        'gest_dechet_biomed', 'nbr_cabine_pmr', 'nbr_cabine_a_rehab', 'nbr_bloc_a_const',
        'nbr_cabine_a_const', 'localite_id', 'operation_id', 'user_id', 'wilaya_id',
        'moughata_id', 'commune_id', 'projet_id', 'operateur_id', 'date_op',
        'infrastructure_id', 'remarque', 'confirmed',
        'an_recept', 'an_program', 'nb_dlm_instal', 'nbr_bloc_const', 'nbr_cabine_const', 'nbr_cabine_rehab',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'approv_eau' => 'boolean',
        'gest_dechet' => 'boolean',
        'gest_dechet_biomed' => 'boolean',
        'confirmed' => 'boolean',
        'date_op' => 'date',
        'effectif' => 'integer',
        'effectif_garcon' => 'integer',
        'effectif_fille' => 'integer',
        'dist_approv_eau' => 'float',
        'nbr_bloc' => 'integer',
        'nb_dlm' => 'integer',
        'nb_dlm_complet' => 'integer',
        'nb_dlm_incomplet' => 'integer',
        'nb_sans_dlm' => 'integer',
        'nbr_cabine_exist' => 'integer',
        'nbr_cabine_fonct' => 'integer',
        'nbr_cabine_fem_hyg' => 'integer',
        'nbr_cabine_pmr' => 'integer',
        'nbr_cabine_a_rehab' => 'integer',
        'nbr_bloc_a_const' => 'integer',
        'nbr_cabine_a_const' => 'integer',
    ];

    /**
     * Get the wilaya that owns the report.
     */
    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'wilaya_id');
    }

    /**
     * Get the moughata that owns the report.
     */
    public function moughata()
    {
        return $this->belongsTo(Moughata::class, 'moughata_id');
    }

    /**
     * Get the commune that owns the report.
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    /**
     * Get the localite that owns the report.
     */
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'localite_id');
    }

    /**
     * Get the operation that owns the report.
     */
    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }

    /**
     * Get the projet that owns the report.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    /**
     * Get the operateur that owns the report.
     */
    public function operateur()
    {
        return $this->belongsTo(Operateur::class, 'operateur_id');
    }

    /**
     * Get the infrastructure that owns the report.
     */
    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class, 'infrastructure_id');
    }

    /**
     * Get the user that created the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor for effectif_garcon - returns null if value is 0 or empty
     */
    public function getEffectifGarconAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    /**
     * Accessor for effectif_fille - returns null if value is 0 or empty
     */
    public function getEffectifFilleAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    /**
     * Accessor for effectif - returns null if value is 0 or empty
     */
    public function getEffectifAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    /**
     * Accessor for dist_approv_eau - returns null if value is 0 or empty
     */
    public function getDistApprovEauAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    /**
     * Accessor for numeric fields - returns null if value is 0 or empty
     */
    public function getNbrBlocAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbDlmAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbDlmCompletAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbDlmIncompletAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbSansDlmAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbrCabineExistAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbrCabineFonctAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbrCabineFemHygAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbrCabinePmrAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbrCabineARehabAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbrBlocAConstAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    public function getNbrCabineAConstAttribute($value)
    {
        return !is_null($value) && $value != 0 ? $value : null;
    }

    /**
     * Check if a field has a value
     */
    public function hasValue($field)
    {
        $value = $this->getAttribute($field);
        return !is_null($value) && $value !== '' && $value !== 0;
    }
}