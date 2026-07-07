<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\Localite;
use App\Models\Wilaya;
use App\Models\LocaliteReport;
use Illuminate\Http\Request;

class MapController extends Controller
{
    //
    public function index()
    {

        $localite_reports = LocaliteReport::pluck('wilaya_id');
        $wilayas = Wilaya::whereIn('id', $localite_reports)->orderBy('intitule_fr')->get();

        return view('admin.maps.index', compact('wilayas'));
        # code...
    }
public function fech_map(Request $request)
{
    $data = array();
    
    // Construire les conditions de base
    $conditions = $this->buildConditions($request);
    
    // Cas 1: Aucun filtre (données globales)
    if (!$conditions['has_filters']) {
        $data = $this->getGlobalData();
    }
    // Cas 2: Filtre par wilaya_id seulement
    elseif ($conditions['wilaya_id'] && !$conditions['moughata_id'] && !$conditions['commune_id'] && !$conditions['projet_id'] && !$conditions['operateur_id'] && !$conditions['annee']) {
        $data = $this->getWilayaData($request->wilaya_id);
    }
    // Cas 3: Filtre par moughata_id
    elseif ($conditions['moughata_id'] && !$conditions['commune_id'] && !$conditions['projet_id'] && !$conditions['operateur_id'] && !$conditions['annee']) {
        $data = $this->getMoughataData($request->moughata_id);
    }
    // Cas 4: Filtre par commune_id
    elseif ($conditions['commune_id'] && !$conditions['projet_id'] && !$conditions['operateur_id'] && !$conditions['annee']) {
        $data = $this->getCommuneData($request->commune_id);
    }
    // Cas 5: Filtre par projet_id seulement
    elseif ($conditions['projet_id'] && !$conditions['operateur_id'] && !$conditions['annee']) {
        $data = $this->getProjetData($request->projet_id);
    }
    // Cas 6: Filtre par projet_id + operateur_id
    elseif ($conditions['projet_id'] && $conditions['operateur_id'] && !$conditions['annee']) {
        $data = $this->getProjetOperateurData($request->projet_id, $request->operateur_id);
    }
    // Cas 7: Filtre par annee seulement
    elseif ($conditions['annee'] && !$conditions['wilaya_id'] && !$conditions['moughata_id'] && !$conditions['commune_id']) {
        $data = $this->getAnneeData($request->annee);
    }
    // Cas 8: Filtre par annee + wilaya_id
    elseif ($conditions['annee'] && $conditions['wilaya_id'] && !$conditions['moughata_id'] && !$conditions['commune_id']) {
        $data = $this->getAnneeWilayaData($request->annee, $request->wilaya_id);
    }
    // Cas 9: Filtre par annee + moughata_id
    elseif ($conditions['annee'] && $conditions['moughata_id'] && !$conditions['commune_id']) {
        $data = $this->getAnneeMoughataData($request->annee, $request->moughata_id);
    }
    // Cas 10: Filtre par annee + commune_id
    elseif ($conditions['annee'] && $conditions['commune_id']) {
        $data = $this->getAnneeCommuneData($request->annee, $request->commune_id);
    }
    // Cas 11: Combinaisons complexes
    else {
        $data = $this->getComplexFilterData($request);
    }

    return response()->json($data);
}

// Méthodes auxiliaires pour chaque cas

private function buildConditions($request)
{
    return [
        'wilaya_id' => $request->wilaya_id ?? null,
        'moughata_id' => $request->moughata_id ?? null,
        'commune_id' => $request->commune_id ?? null,
        'projet_id' => $request->projet_id ?? null,
        'operateur_id' => $request->operateur_id ?? null,
        'annee' => $request->annee ?? null,
        'has_filters' => $request->wilaya_id || $request->moughata_id || $request->commune_id || 
                         $request->projet_id || $request->operateur_id || $request->annee
    ];
}

private function getGlobalData()
{
    return $this->getBaseData();
}

private function getWilayaData($wilaya_id)
{
    $data = $this->getBaseData(['wilaya_id' => $wilaya_id]);
    $data['view'] = $this->calculateView($wilaya_id, null, null);
    return $data;
}

private function getMoughataData($moughata_id)
{
    $data = $this->getBaseData(['moughata_id' => $moughata_id]);
    $data['view'] = $this->calculateView(null, $moughata_id, null);
    return $data;
}

private function getCommuneData($commune_id)
{
    $data = $this->getBaseData(['commune_id' => $commune_id]);
    $data['view'] = $this->calculateView(null, null, $commune_id);
    return $data;
}

private function getProjetData($projet_id)
{
    return $this->getBaseData(['projet_id' => $projet_id]);
}

private function getProjetOperateurData($projet_id, $operateur_id)
{
    return $this->getBaseData([
        'projet_id' => $projet_id,
        'operateur_id' => $operateur_id
    ]);
}

private function getAnneeData($annee)
{
    return $this->getBaseData(['annee' => $annee]);
}

private function getAnneeWilayaData($annee, $wilaya_id)
{
    return $this->getBaseData([
        'annee' => $annee,
        'wilaya_id' => $wilaya_id
    ]);
}

private function getAnneeMoughataData($annee, $moughata_id)
{
    return $this->getBaseData([
        'annee' => $annee,
        'moughata_id' => $moughata_id
    ]);
}

private function getAnneeCommuneData($annee, $commune_id)
{
    return $this->getBaseData([
        'annee' => $annee,
        'commune_id' => $commune_id
    ]);
}

private function getComplexFilterData($request)
{
    $filters = [];
    if ($request->wilaya_id) $filters['wilaya_id'] = $request->wilaya_id;
    if ($request->moughata_id) $filters['moughata_id'] = $request->moughata_id;
    if ($request->commune_id) $filters['commune_id'] = $request->commune_id;
    if ($request->projet_id) $filters['projet_id'] = $request->projet_id;
    if ($request->operateur_id) $filters['operateur_id'] = $request->operateur_id;
    if ($request->annee) $filters['annee'] = $request->annee;
    
    $data = $this->getBaseData($filters);
    
    // Calculer la vue si des coordonnées géographiques sont disponibles
    if ($request->wilaya_id || $request->moughata_id || $request->commune_id) {
        $data['view'] = $this->calculateView($request->wilaya_id, $request->moughata_id, $request->commune_id);
    }
    
    return $data;
}

private function getBaseData($filters = [])
{
    $data = [];
    
    // Données des écoles
    $data['ecoles_besoin_latr_satisf'] = $this->getEcolesQuery('satisf', $filters)->get();
    $data['ecoles_besoin_latr_non_satisf'] = $this->getEcolesQuery('non_satisf', $filters)->get();
    $data['ecoles_sans_latr'] = $this->getEcolesQuery('sans_latr', $filters)->get();
    
    // Données des centres de santé
    $data['santes_besoin_latr_satisf'] = $this->getSantesQuery('satisf', $filters)->get();
    $data['santes_besoin_latr_non_satisf'] = $this->getSantesQuery('non_satisf', $filters)->get();
    $data['santes_sans_latr'] = $this->getSantesQuery('sans_latr', $filters)->get();
    
    // Données des localités
    $data['non_declenches'] = $this->getNonDeclenchesQuery($filters)->get();
    $data['declenches_fdal'] = $this->getDeclenchesFDALQuery($filters)->get();
    $data['declenches_non_fdal'] = $this->getDeclenchesNonFDALQuery($filters)->get();
    $data['declenches'] = $this->getDeclenchesQuery($filters)->get();
    $data['redeclenches'] = $this->getRedeclenchesQuery($filters)->get();
    
    return $data;
}

// Méthodes de construction des requêtes (à adapter selon votre logique existante)

private function getEcolesQuery($type, $filters = [])
{
    $query = Infrastructure::join('infrastructure_operations', 'infrastructures.id', 'infrastructure_operations.infrastructure_id')
        ->join('localites', 'infrastructure_operations.localite_id', 'localites.id')
        ->select('infrastructures.*', 'localites.*')
        ->where('operation_id', 5)
        ->where('infrastructures.infra_publ', 'Ecole')
        ->whereNotNull('long_infra_publ')
        ->whereNotNull('lat_infra_publ');
    
    switch ($type) {
        case 'satisf':
            $query->where('nbr_bloc_a_const', 0)->where('nbr_cabine_a_const', 0);
            break;
        case 'non_satisf':
            $query->where('nbr_bloc_a_const', '>', 0)->where('nbr_cabine_a_const', '>', 0);
            break;
        case 'sans_latr':
            $query->where('nbr_cabine_exist', 0);
            break;
    }
    
    return $this->applyInfrastructureFilters($query, $filters);
}

private function getSantesQuery($type, $filters = [])
{
    $query = Infrastructure::join('infrastructure_operations', 'infrastructures.id', 'infrastructure_operations.infrastructure_id')
        ->join('localites', 'infrastructure_operations.localite_id', 'localites.id')
        ->select('infrastructures.*', 'localites.*')
        ->where('operation_id', 5)
        ->where('infrastructures.infra_publ', '<>', 'Ecole')
        ->whereNotNull('long_infra_publ')
        ->whereNotNull('lat_infra_publ');
    
    switch ($type) {
        case 'satisf':
            $query->where('nbr_bloc_a_const', 0)->where('nbr_cabine_a_const', 0);
            break;
        case 'non_satisf':
            $query->where('nbr_bloc_a_const', '>', 0)->where('nbr_cabine_a_const', '>', 0);
            break;
        case 'sans_latr':
            $query->where('nbr_cabine_exist', 0);
            break;
    }
    
    return $this->applyInfrastructureFilters($query, $filters);
}

private function getNonDeclenchesQuery($filters = [])
{
    $localite_id = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
        ->where('operations.code', 1);
    
    $localite_id = $this->applyLocaliteFilters($localite_id, $filters)->get()->pluck('localite_id');
    
    $query = Localite::select('localites.*')
        ->whereNotIn('id', $localite_id)
        ->whereNotNull('localites.lat_loc')
        ->whereNotNull('localites.long_loc')
        ->groupBy('localites.id');
    
    return $this->applyLocaliteFilters($query, $filters);
}

private function getDeclenchesFDALQuery($filters = [])
{
    // Récupérer d'abord toutes les localités déclenchées (operation_id = 1)
    $localitesDeclenchees = $this->getDeclencheesIds($filters);
    
    if (empty($localitesDeclenchees)) {
        return LocaliteReport::whereRaw('1 = 0'); // Retourner une requête vide
    }
    
    // Filtrer celles qui ont une opération FDAL (operation_id = 3)
    $localite_id_fdal = LocaliteReport::whereIn('localite_id', $localitesDeclenchees)
        ->where('operation_id', 3) // FDAL
        ->distinct('localite_id')
        ->pluck('localite_id');
    
    $query = Localite::whereIn('id', $localite_id_fdal)
        ->whereNotNull('lat_loc')
        ->whereNotNull('long_loc')
        ->select('localites.*');
    
    return $this->applyLocaliteFilters($query, $filters);
}

private function getDeclenchesNonFDALQuery($filters = [])
{
    // Récupérer d'abord toutes les localités déclenchées (operation_id = 1)
    $localitesDeclenchees = $this->getDeclencheesIds($filters);
    
    if (empty($localitesDeclenchees)) {
        return LocaliteReport::whereRaw('1 = 0'); // Retourner une requête vide
    }
    
    // Filtrer celles qui ont une opération FDAL (operation_id = 3)
    $localite_id_fdal = LocaliteReport::whereIn('localite_id', $localitesDeclenchees)
        ->where('operation_id', 3) // FDAL
        ->distinct('localite_id')
        ->pluck('localite_id');
    
    // Localités déclenchées mais NON FDAL
    $query = Localite::whereIn('id', $localitesDeclenchees)
        ->whereNotIn('id', $localite_id_fdal)
        ->whereNotNull('lat_loc')
        ->whereNotNull('long_loc')
        ->select('localites.*');
    
    return $this->applyLocaliteFilters($query, $filters);
}

private function getDeclencheesIds($filters = [])
{
    $query = LocaliteReport::where('operation_id', 1); // Déclenchements
    
    if (isset($filters['projet_id'])) {
        $query->where('projet_id', $filters['projet_id']);
    }
    
    if (isset($filters['operateur_id'])) {
        $query->where('operateur_id', $filters['operateur_id']);
    }
    
    if (isset($filters['wilaya_id'])) {
        $query->where('wilaya_id', $filters['wilaya_id']);
    }
    
    if (isset($filters['moughata_id'])) {
        $query->where('moughata_id', $filters['moughata_id']);
    }
    
    if (isset($filters['commune_id'])) {
        $query->where('commune_id', $filters['commune_id']);
    }
    
    if (isset($filters['annee'])) {
        $query->whereYear('date_op', $filters['annee']);
    }
    
    return $query->distinct('localite_id')->pluck('localite_id');
}

private function getDeclenchesQuery($filters = [])
{
    $localite_id_fdal = LocaliteReport::join('operations', 'localite_operations.operation_id', 'operations.id')
        ->where('operations.code', 3);
    
    $localite_id_fdal = $this->applyLocaliteFilters($localite_id_fdal, $filters)->get()->pluck('localite_id');
    
    $query = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
        ->join('operations', 'localite_operations.operation_id', 'operations.id')
        ->select('localites.*')
        ->where('operations.code', 1)
        ->whereNotIn('localites.id', $localite_id_fdal)
        ->whereNotNull('localites.lat_loc')
        ->whereNotNull('localites.long_loc')
        ->groupBy('localites.id')
        ->havingRaw("COUNT(localites.id) = 1");
    
    return $this->applyLocaliteFilters($query, $filters);
}

private function getRedeclenchesQuery($filters = [])
{
    $query = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
        ->join('operations', 'localite_operations.operation_id', 'operations.id')
        ->where('operations.code', 1)
        ->whereNotNull('localites.lat_loc')
        ->whereNotNull('localites.long_loc')
        ->select('localites.*')
        ->groupBy('localites.id')
        ->havingRaw("COUNT(localites.id) > 1");
    
    return $this->applyLocaliteFilters($query, $filters);
}

private function applyInfrastructureFilters($query, $filters = [])
{
    if (isset($filters['wilaya_id'])) {
        $query->where('infrastructure_operations.wilaya_id', $filters['wilaya_id']);
    }
    
    if (isset($filters['moughata_id'])) {
        $query->where('infrastructure_operations.moughata_id', $filters['moughata_id']);
    }
    
    if (isset($filters['commune_id'])) {
        $query->where('infrastructure_operations.commune_id', $filters['commune_id']);
    }
    
    if (isset($filters['projet_id'])) {
        $query->where('infrastructure_operations.projet_id', $filters['projet_id']);
    }
    
    if (isset($filters['operateur_id'])) {
        $query->where('infrastructure_operations.operateur_id', $filters['operateur_id']);
    }
    
    if (isset($filters['annee'])) {
        $query->whereYear('infrastructure_operations.date_op', $filters['annee']);
    }
    
    return $query;
}

private function applyLocaliteFilters($query, $filters = [])
{
    // Pour les requêtes sur localite_operations
    if (str_contains($query->toSql(), 'localite_operations')) {
        if (isset($filters['wilaya_id'])) {
            $query->where('localite_operations.wilaya_id', $filters['wilaya_id']);
        }
        
        if (isset($filters['moughata_id'])) {
            $query->where('localite_operations.moughata_id', $filters['moughata_id']);
        }
        
        if (isset($filters['commune_id'])) {
            $query->where('localite_operations.commune_id', $filters['commune_id']);
        }
        
        if (isset($filters['operateur_id'])) {
            $query->where('localite_operations.operateur_id', $filters['operateur_id']);
        }
        
        if (isset($filters['projet_id'])) {
            $query->where('localite_operations.operateur_id', $filters['projet_id']);
        }
                if (isset($filters['annee'])) {
            $query->whereYear('localite_operations.date_op', $filters['annee']);
        }
        
        
    }
    // Pour les requêtes sur localites seulement
    elseif (str_contains($query->toSql(), 'localites') && !str_contains($query->toSql(), 'localite_operations')) {
        if (isset($filters['wilaya_id'])) {
            $query->where('localites.wilaya_id', $filters['wilaya_id']);
        }
        
        if (isset($filters['moughata_id'])) {
            $query->where('localites.moughata_id', $filters['moughata_id']);
        }
        
        if (isset($filters['commune_id'])) {
            $query->where('localites.commune_id', $filters['commune_id']);
        }
    }
    
    return $query;
}
private function calculateView($wilaya_id = null, $moughata_id = null, $commune_id = null)
{
    $query = Localite::select('lat_loc', 'long_loc')
        ->whereNotNull('lat_loc')
        ->whereNotNull('long_loc');
    
    if ($wilaya_id) $query->where('wilaya_id', $wilaya_id);
    if ($moughata_id) $query->where('moughata_id', $moughata_id);
    if ($commune_id) $query->where('commune_id', $commune_id);
    
    $localites = $query->get();
    
    if ($localites->count() == 0) {
        return ["lat" => 0, "lon" => 0];
    }
    
    $lat = 0;
    $long = 0;
    foreach ($localites as $localite) {
        $lat += $localite->lat_loc;
        $long += $localite->long_loc;
    }
    
    return [
        "lat" => number_format($lat / $localites->count(), 2),
        "lon" => number_format($long / $localites->count(), 2)
    ];
}
}
