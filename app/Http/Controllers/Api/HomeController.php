<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App;
use App\Models\Commune;
use App\Models\Moughata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Operation;
use App\Models\Projet;
use App\Models\Operateur;
use App\Models\Wilaya;
use App\Models\Localite;

class HomeController extends BaseController
{
    //
    public function index(Request $request)
    {
        $current_user = Auth::user();
        $id = Auth::id();
        //date_debut date_fin
        $user_operateur = DB::table('user_operateurs')->where('user_id', $id)->get();
        $operation_id = $user_operateur->pluck('operation_id');
        $projet_id = $user_operateur->pluck('projet_id');
        $operateur_id = $user_operateur->pluck('operateur_id');
        //$wilaya_id = DB::table('projet_wilayas')->whereIn('projet_id', $projet_id)->pluck('wilaya_id');
        $result['user'] = $current_user;
        //$result['operations'] = Operation::select('operations.*')->whereIn('id', $operation_id)->get();
        $result['operations'] = Operation::all();
        //$result['projets'] = Projet::select('projets.*')->whereIn('id', $projet_id)->get();
        $result['projets'] = Projet::all();
        //$result['operateurs'] = Operateur::select('operateurs.*')->whereIn('id', $operateur_id)->get();
        $result['operateurs'] = Operateur::all();
        //$result['wilayas'] = Wilaya::whereIn('id', $wilaya_id)->get();
        $result['wilayas'] = Wilaya::all();
        //$result['moughatas'] = Moughata::whereIn('wilaya_id', $wilaya_id)->get();
        $result['moughatas'] = Moughata::all();
        //$result['communes'] = Commune::whereIn('wilaya_id', $wilaya_id)->get();
        $result['communes'] = Commune::all();
        //$result['localites'] = Localite::whereIn('wilaya_id', $wilaya_id)->get();
        $result['localites'] = Localite::orderBy('intitule_fr')->get();

        // Facilitateur : seulement ses localités assignées (et la hiérarchie
        // qui les contient), pour qu'il ne puisse enquêter que là.
        $allowed = $current_user->allowedLocaliteIds();
        $result['localites_restreintes'] = $allowed !== null;
        if ($allowed !== null) {
            $localites = Localite::whereIn('id', $allowed)->orderBy('intitule_fr')->get();
            $result['localites'] = $localites;
            $result['wilayas'] = Wilaya::whereIn('id', $localites->pluck('wilaya_id')->unique())->get();
            $result['moughatas'] = Moughata::whereIn('id', $localites->pluck('moughata_id')->unique())->get();
            $result['communes'] = Commune::whereIn('id', $localites->pluck('commune_id')->unique())->get();
        }

        
        return $this->sendResponse($result, __("auth.success"));
    }
}
