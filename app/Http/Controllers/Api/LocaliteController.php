<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LocaliteStoreRequest;
use App\Models\Commune;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Wilaya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocaliteController extends Controller
{
    
    public function store(LocaliteStoreRequest $request): JsonResponse
    {
        // Un facilitateur est limité à ses localités assignées : il ne peut pas
        // en créer de nouvelles depuis l'application.
        if ($request->user() && $request->user()->allowedLocaliteIds() !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez enquêter que sur les localités qui vous sont assignées.',
            ], 403);
        }

        // Compte rattaché à des wilayas : nouvelle localité seulement dans celles-ci.
        $wilayas = $request->user() ? $request->user()->allowedWilayaIds() : null;
        if ($wilayas !== null && !in_array((int) $request->wilaya_id, $wilayas, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez ajouter une localité que dans votre wilaya.',
            ], 403);
        }

        try {
            // Valider et créer la localité
            $validatedData = $request->validated();
            
            // Par défaut, les localités ajoutées depuis mobile ne sont pas validées
            if (!isset($validatedData['valider'])) {
                $validatedData['valider'] = false;
            }
            
            $localite = Localite::create($validatedData);
            
            // Charger les relations pour la réponse
            $localite->load(['wilaya', 'moughata', 'commune']);
            
            return response()->json([
                'success' => true,
                'message' => __('localite.api.created_success'),
                'data' => $this->formatLocaliteResponse($localite)
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('localite.api.creation_error'),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Formater la réponse de la localité.
     */
    private function formatLocaliteResponse(Localite $localite): array
    {
        return [
            'id' => $localite->id,
            'code' => $localite->code,
            'intitule' => $localite->intitule,
            'intitule_fr' => $localite->intitule_fr,
            'code_ansade' => $localite->code_ansade,
            'nb_pop_ansade' => $localite->nb_pop_ansade,
            'lat_loc' => $localite->lat_loc,
            'long_loc' => $localite->long_loc,
            'valider' => (bool)$localite->valider,
            'created_at' => $localite->created_at->toISOString(),
            'wilaya' => $localite->wilaya ? [
                'id' => $localite->wilaya->id,
                'code' => $localite->wilaya->code,
                'intitule' => $localite->wilaya->intitule,
                'intitule_fr' => $localite->wilaya->intitule_fr
            ] : null,
            'moughata' => $localite->moughata ? [
                'id' => $localite->moughata->id,
                'code' => $localite->moughata->code,
                'intitule' => $localite->moughata->intitule,
                'intitule_fr' => $localite->moughata->intitule_fr
            ] : null,
            'commune' => $localite->commune ? [
                'id' => $localite->commune->id,
                'code' => $localite->commune->code,
                'intitule' => $localite->commune->intitule,
                'intitule_fr' => $localite->commune->intitule_fr
            ] : null
        ];
    }
}