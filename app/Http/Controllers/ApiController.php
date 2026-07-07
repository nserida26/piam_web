<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\InfrastructureReport;
use App\Models\Moughata;
use Illuminate\Http\Request;
use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\Operateur;
use App\Models\Operation;

use App\Models\Projet;


class ApiController extends Controller
{
    //
    /**

     * Write code on Method

     *

     * @return response()

     */

    public function fetchMoughata(Request $request)

    {

        $wilaya_id = LocaliteReport::where('wilaya_id', $request->wilaya_id)->pluck('wilaya_id');
        $data['moughatas'] = Moughata::whereIn("wilaya_id", $wilaya_id)->orderBy('intitule_fr')
            ->get();
        return response()->json($data);
    }
    public function fetchMoughata2(Request $request)

    {

        $wilaya_id = InfrastructureReport::where('wilaya_id', $request->wilaya_id)->pluck('wilaya_id');
        $data['moughatas'] = Moughata::whereIn("wilaya_id", $wilaya_id)->orderBy('intitule_fr')
            ->get();
        return response()->json($data);
    }

    public function fetchCommune(Request $request)

    {

        $moughata_id = LocaliteReport::where('moughata_id', $request->moughata_id)->pluck('moughata_id');
        $data['communes'] = Commune::whereIn("moughata_id", $moughata_id)->orderBy('intitule_fr')->get();

        return response()->json($data);
    }
    public function fetchCommune2(Request $request)

    {

        $moughata_id = InfrastructureReport::where('moughata_id', $request->moughata_id)->pluck('moughata_id');
        $data['communes'] = Commune::whereIn("moughata_id", $moughata_id)->orderBy('intitule_fr')->get();

        return response()->json($data);
    }

    public function fetchLocalite(Request $request)

    {

        $commune_id = LocaliteReport::where('commune_id', $request->commune_id)->pluck('commune_id');

        $data['localites'] = Localite::whereIn("commune_id", $commune_id)->orderBy('intitule_fr')->get();

        return response()->json($data);
    }
    public function fetchLocalite2(Request $request)

    {

        $commune_id = InfrastructureReport::where('commune_id', $request->commune_id)->pluck('commune_id');

        $data['localites'] = Localite::whereIn("commune_id", $commune_id)->orderBy('intitule_fr')->get();

        return response()->json($data);
    }
    public function fetchOperation(Request $request)

    {

        $operation_id = LocaliteReport::where('wilaya_id', $request->wilaya_id)->pluck('operation_id');
        $data['operations'] = Operation::whereIn("id", $operation_id)->orderBy('code')->get(["intitule", "id"]);

        return response()->json($data);
    }

    public function fetchOperation2(Request $request)

    {

        $operation_id = InfrastructureReport::where('wilaya_id', $request->wilaya_id)->pluck('operation_id');
        $data['operations'] = Operation::whereIn("id", $operation_id)->orderBy('code')->get(["intitule", "id"]);

        return response()->json($data);
    }

    public function fetchProjet(Request $request)

    {

        $projet_id = LocaliteReport::where('wilaya_id', $request->wilaya_id)->pluck('projet_id');
        $data['projets'] = Projet::whereIn("id", $projet_id)->orderBy('intitule_resume')->get(["intitule_resume", "id"]);

        return response()->json($data);
    }

    public function fetchProjet2(Request $request)

    {

        $projet_id = InfrastructureReport::where('wilaya_id', $request->wilaya_id)->pluck('projet_id');
        $data['projets'] = Projet::whereIn("id", $projet_id)->orderBy('intitule_resume')->get(["intitule_resume", "id"]);

        return response()->json($data);
    }

    public function fetchOperateur(Request $request)

    {

        $operateur_id = LocaliteReport::where('wilaya_id', $request->wilaya_id)->where('projet_id', $request->projet_id)->pluck('operateur_id');
        $data['operateurs'] = Operateur::whereIn("id", $operateur_id)->orderBy('intitule_fr')->get(["intitule", "id"]);

        return response()->json($data);
    }

    public function fetchOperateur2(Request $request)
    {
        $operateur_id = LocaliteReport::where('projet_id', $request->projet_id)->whereIn('operation_id', [1, 2, 4])->pluck('operateur_id');
        $data['operateurs'] = Operateur::whereIn("id", $operateur_id)->orderBy('intitule_fr')->get(["intitule", "id"]);
        return response()->json($data);
    }

    public function fetchOperateur3(Request $request)

    {

        $operateur_id = InfrastructureReport::where('wilaya_id', $request->wilaya_id)->where('projet_id', $request->projet_id)->pluck('operateur_id');
        $data['operateurs'] = Operateur::whereIn("id", $operateur_id)->orderBy('intitule_fr')->get(["intitule", "id"]);

        return response()->json($data);
    }
}
