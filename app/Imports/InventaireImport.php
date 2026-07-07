<?php

namespace App\Imports;

use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\InfrastructureReport;
use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\Moughata;
use App\Models\Operateur;
use App\Models\Projet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
//, SkipsOnFailure
//, SkipsEmptyRows
//, SkipsFailures
class InventaireImport implements ToModel, WithHeadingRow
{
    use Importable;


    private $count_before = 0;
    private $operation_id;
    private $wilaya_id;
    private $moughatas;

    private $communes;
    private $localites;

    private $projets;
    private $operateurs;

    private $infrastructures;

    public function __construct($operation_id, $wilaya_id)
    {
        $this->operation_id = $operation_id;
        $this->wilaya_id = $wilaya_id;
        $this->moughatas = Moughata::select('id', 'intitule', 'wilaya_id')->get();
        $this->communes = Commune::select('id', 'intitule', 'moughata_id')->get();
        $this->localites = Localite::select('id', 'code_ansade', 'intitule', 'commune_id')->get();
        $this->operateurs = Operateur::select('id', 'intitule')->get();
        $this->projets = Projet::select('id', 'intitule_resume')->get();
        $this->infrastructures = Infrastructure::select('id', 'intitule_infra_publ')->get();
        $this->count_before = InfrastructureReport::count();

        # code...
    }

    public function model(array $row)
    {


        //print_r($row);

        try {
            $user = Auth::id();
            $created_at = date('Y-m-d');



            $wilaya_id = $this->wilaya_id;
            $operation_id = $this->operation_id;

            $projet_id = $this->projets->where('intitule_resume', $row['int_projet_resum'])->first();
            $operateur_id = $this->operateurs->where('intitule', $row['intitule_se'])->first();



            $moughata_id = $this->moughatas->where('wilaya_id', $wilaya_id)->where('intitule', $row['moughataa'])->first();
            $commune_id = $this->communes->where('moughata_id', $moughata_id->id)->where('intitule', $row['commune'])->first();
            $localite_id = Localite::select('id', 'code_ansade', 'intitule', 'commune_id')->where('commune_id', $commune_id->id)->where('intitule', $row['localite'])->orWhere('code_ansade', $row['code_ansade'])->first();
            $infrastructure_id = Infrastructure::select('id', 'intitule_infra_publ', 'localite_id')->where('localite_id', $localite_id->id)->where('intitule_infra_publ', $row['int_infra_publ'])->first();


            //if (isset($infrastructure_id) && isset($localite_id)) {

            return new InfrastructureReport(
                [
                    'localite_id' => $localite_id->id,
                    'commune_id' => $commune_id->id,
                    'moughata_id' => $moughata_id->id,
                    'wilaya_id' => $wilaya_id,
                    'operation_id' => $operation_id,
                    'projet_id' => $projet_id->id,
                    'operateur_id' => $operateur_id->id,
                    'infrastructure_id' => $infrastructure_id->id,
                    'user_id' => $user,
                    'date_op' => $this->isDate(strval($row['date_op'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_op'])->format('Y-m-d') : (empty($row['date_op'])  ? date("Y-12-31", strtotime($row['date_op'] . "-12-31")) : NULL),
                    'effectif_garcon' => intval($row["effectif_garcon"]),
                    'effectif_fille' => intval($row["effectif_fille"]),
                    'effectif' => intval($row["effectif_tot"]),
                    'dist_approv_eau' => intval($row["dist_approv_eau"]),
                    'approv_eau' => $row['approv_eau_infra_publ'],
                    'nbr_bloc' => $row['nb_bloc_latr'],
                    'nb_dlm' => $row['nb_dlm_infra_publ'],
                    'nb_dlm_complet' => $row['nb_infra_publ_dlm_complet'],
                    'nb_dlm_incomplet' => $row['nb_infra_publ_dlm_incomplet'],
                    'nb_sans_dlm' => $row['nb_infra_publ_sans_dlm'],
                    'type_latr_publ' => $row['type_latr_publ'],
                    'nbr_cabine_exist' => intval($row['nb_cab_latr_exist']),
                    'nbr_cabine_fonct' => intval($row['nb_cab_latr_fonct']),
                    'nbr_cabine_fem' => intval($row['nb_cab_latr_fem']),
                    'dist_cab_hom_fem' => intval($row['dist_cab_hom_fem']),
                    'nbr_cabine_fem_hyg' => intval($row['nb_cab_fem_hyg_menstr']),
                    'gest_dechet' => intval($row['nb_ecole_gest_dechet']),
                    'gest_dechet_biomed' => intval($row['nb_struct_sante_gest_dechet_biomed']),
                    'nbr_cabine_pmr' => intval($row['nb_cab_pmr']),
                    'nbr_cabine_a_rehab' => intval($row['nb_cab_a_rehab']),
                    'nbr_bloc_a_const' => intval($row['nb_bloc_a_const']),
                    'nbr_cabine_a_const' => intval($row['nb_cab_a_const']),
                    'created_at' => $created_at,
                    'updated_at' => $created_at
                ]
            );
            // }
        } catch (\Throwable $th) {
            //dd($th);
            //return redirect()->back()->with(['errors' => $th]);
        }
    }
    function isDate($value)
    {
        if (is_string($value) && strlen($value) > 0) {
            return $value[0] === '4';
        }
        return false;
    }
    public function getRowCount(): int
    {
        $count_after = InfrastructureReport::count();
        return $count_after - $this->count_before;
    }
}
