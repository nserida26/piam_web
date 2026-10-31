<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LocaliteReport extends Model
{
        /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'localite_operations';

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
        'statut_coordinateur', 'coordinateur_id', 'coordinateur_valide_le', 'coordinateur_remarque',
        'nb_tot_pop','nbr_h_c', 'nbr_f_c', 'nbr_enfant_inf_5_ans', 'nb_menages_comptabilises',
        'approv_eau', 'nb_tot_latr_fam','nb_latrine_amelior', 'nb_latrine_non_amelior',
        'nbr_menages_latr_amel_inadeq', 'nbr_menages_latr_amel_adeq','nbr_menages_ltr_vsn', 'nbr_menages_latr_amel_vid_adeq_part',
        'nbr_menages_latr_amel_inadeq_part', 'nbr_menages_ltr_end_phm','nbr_menages_dal', 'nbr_menages_dlm',
        'nbr_menages_dlm_complet', 'nbr_menages_dlm_incomplet','nbr_menages_sans_dlm',
        'nbr_latrine_realise','nbr_latrine_autoconst', 'nbr_latrine_realise_macon','nbr_latrine_entraid',
        'nbr_latrine_subv','nbr_latrine_en_constr','nbr_latrine_autofin','mont_invest_men','nb_latr_invest_men_dispo',
        'remarque','confirmed','localite_id','operation_id','user_id','wilaya_id','moughata_id','commune_id','projet_id','operateur_id','date_op'];

    protected $casts = [
        'confirmed' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('confirmed_for_reporting', function (Builder $builder) {
            if (static::shouldApplyConfirmedScope()) {
                $builder->where($builder->getModel()->getTable() . '.confirmed', true);
            }
        });
    }

    public function scopeConfirmedOnly(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.confirmed', true);
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
}
