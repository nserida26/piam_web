<?php

namespace App\Imports;

use App\Models\Commune;
use App\Models\InfrastructureReport;
use App\Models\Localite;
use App\Models\Infrastructure;
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
class ProgrammationTraveauxImport implements ToModel, WithHeadingRow
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
        $this->localites = Localite::select('id', 'code_ansade', 'intitule');
        $this->operateurs = Operateur::select('id', 'intitule')->get();
        $this->projets = Projet::select('id', 'intitule_resume')->get();
        $this->infrastructures = Infrastructure::select('id', 'intitule_infra_publ')->get();
        $this->count_before = InfrastructureReport::count();

        # code...
    }

    public function model(array $row)
    {


       /// print_r($row);

        try {
            $user = Auth::id();
            $created_at = date('Y-m-d');
            $projet_id = $this->projets->where('intitule_resume', $row['int_projet_resum'])->first();
            $operateur_id = $this->operateurs->where('intitule', $row['intitule_se'])->first();


            $wilaya_id = $this->wilaya_id;
            $operation_id = $this->operation_id;
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
                        'user_id' => $user,
                        'date_op' => $this->isDate(strval($row['date_op'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_op'])->format('Y-m-d') : (empty($row['date_op'])  ? date("Y-12-31", strtotime($row['date_op'] . "-12-31")) : NULL),
                        'infrastructure_id' => $infrastructure_id->id,
                        'nbr_cabine_a_rehab' => $row['nb_cab_a_rehab'],
                        'nbr_bloc_a_const' => $row['nb_bloc_a_const'],
                        'nbr_cabine_a_const' => $row['nb_cab_a_const'],
                        'an_program' => $row['an_program'],
                        'created_at' => $created_at,
                        'updated_at' => $created_at
                    ]

                );
            //}
            //code...
        } catch (\Throwable $th) {
            //throw $th;
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
