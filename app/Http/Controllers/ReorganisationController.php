<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Wilaya;
use App\Services\HierarchyReassignmentService;
use Illuminate\Http\Request;

class ReorganisationController extends Controller
{
    public function __construct(private HierarchyReassignmentService $reassignment)
    {
    }

    public function localitesIndex()
    {
        $wilayas = Wilaya::orderBy('intitule')->get();

        return view('admin.reorganisation.localites', compact('wilayas'));
    }

    public function searchLocalites(Request $request)
    {
        $query = Localite::with(['wilaya:id,intitule', 'moughata:id,intitule', 'commune:id,intitule']);

        if ($request->filled('wilaya_id')) {
            $query->where('wilaya_id', $request->wilaya_id);
        }
        if ($request->filled('moughata_id')) {
            $query->where('moughata_id', $request->moughata_id);
        }
        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('intitule', 'LIKE', "%{$q}%")
                  ->orWhere('intitule_fr', 'LIKE', "%{$q}%")
                  ->orWhere('code_ansade', 'LIKE', "%{$q}%");
            });
        }

        if (!$request->filled('wilaya_id') && !$request->filled('q')) {
            return response()->json(['data' => [], 'message' => 'Choisissez au moins une wilaya ou une recherche.']);
        }

        $localites = $query->orderBy('intitule')->limit(300)->get()->map(fn($l) => [
            'id' => $l->id,
            'intitule' => $l->intitule,
            'code_ansade' => $l->code_ansade,
            'wilaya' => $l->wilaya->intitule ?? '—',
            'moughata' => $l->moughata->intitule ?? '—',
            'commune' => $l->commune->intitule ?? '—',
        ]);

        return response()->json(['data' => $localites]);
    }

    public function assignLocalites(Request $request)
    {
        $validated = $request->validate([
            'localite_ids' => 'required|array|min:1',
            'localite_ids.*' => 'integer|exists:localites,id',
            'commune_id' => 'required|integer|exists:communes,id',
        ]);

        $count = $this->reassignment->assignLocalitesToCommune($validated['localite_ids'], $validated['commune_id']);

        return redirect()->route('admin.reorganisation.localites')
            ->with('success', "{$count} localité(s) affectée(s) avec succès.");
    }

    public function communesIndex()
    {
        $wilayas = Wilaya::orderBy('intitule')->get();

        return view('admin.reorganisation.communes', compact('wilayas'));
    }

    public function searchCommunes(Request $request)
    {
        $query = Commune::with(['wilaya:id,intitule', 'moughata:id,intitule']);

        if ($request->filled('wilaya_id')) {
            $query->where('wilaya_id', $request->wilaya_id);
        }
        if ($request->filled('moughata_id')) {
            $query->where('moughata_id', $request->moughata_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('intitule', 'LIKE', "%{$q}%")
                  ->orWhere('intitule_fr', 'LIKE', "%{$q}%");
            });
        }

        if (!$request->filled('wilaya_id') && !$request->filled('q')) {
            return response()->json(['data' => [], 'message' => 'Choisissez au moins une wilaya ou une recherche.']);
        }

        $communes = $query->orderBy('intitule')->limit(300)->get()->map(fn($c) => [
            'id' => $c->id,
            'intitule' => $c->intitule,
            'code' => $c->code,
            'wilaya' => $c->wilaya->intitule ?? '—',
            'moughata' => $c->moughata->intitule ?? '—',
        ]);

        return response()->json(['data' => $communes]);
    }

    public function assignCommunes(Request $request)
    {
        $validated = $request->validate([
            'commune_ids' => 'required|array|min:1',
            'commune_ids.*' => 'integer|exists:communes,id',
            'moughata_id' => 'required|integer|exists:moughatas,id',
        ]);

        $count = $this->reassignment->assignCommunesToMoughata($validated['commune_ids'], $validated['moughata_id']);

        return redirect()->route('admin.reorganisation.communes')
            ->with('success', "{$count} commune(s) affectée(s) avec succès (localités rattachées mises à jour).");
    }

    public function moughatasIndex()
    {
        $wilayas = Wilaya::orderBy('intitule')->get();

        return view('admin.reorganisation.moughatas', compact('wilayas'));
    }

    public function searchMoughatas(Request $request)
    {
        $query = Moughata::with(['wilaya:id,intitule']);

        if ($request->filled('wilaya_id')) {
            $query->where('wilaya_id', $request->wilaya_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('intitule', 'LIKE', "%{$q}%")
                  ->orWhere('intitule_fr', 'LIKE', "%{$q}%");
            });
        }

        if (!$request->filled('wilaya_id') && !$request->filled('q')) {
            return response()->json(['data' => [], 'message' => 'Choisissez au moins une wilaya ou une recherche.']);
        }

        $moughatas = $query->orderBy('intitule')->limit(300)->get()->map(fn($m) => [
            'id' => $m->id,
            'intitule' => $m->intitule,
            'code' => $m->code,
            'wilaya' => $m->wilaya->intitule ?? '—',
        ]);

        return response()->json(['data' => $moughatas]);
    }

    public function assignMoughatas(Request $request)
    {
        $validated = $request->validate([
            'moughata_ids' => 'required|array|min:1',
            'moughata_ids.*' => 'integer|exists:moughatas,id',
            'wilaya_id' => 'required|integer|exists:wilayas,id',
        ]);

        $count = $this->reassignment->assignMoughatasToWilaya($validated['moughata_ids'], $validated['wilaya_id']);

        return redirect()->route('admin.reorganisation.moughatas')
            ->with('success', "{$count} moughataa(s) affectée(s) avec succès (communes et localités rattachées mises à jour).");
    }
}
