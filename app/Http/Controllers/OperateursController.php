<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Operateur;
use App\Models\Projet;
use App\Models\Marche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperateursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $operateur = DB::table('operateurs as o')
            ->join('marches as m', 'o.id', '=', 'm.operateur_id')
            ->join('projets as p', 'm.projet_id', '=', 'p.id')
            ->select(
                'o.id as operateur_id',
                'o.intitule as operateur_nom',
                'o.intitule_fr as operateur_nom_fr',
                'o.type_struct as operateur_type',
                'p.id as projet_id',
                'p.intitule_resume as projet_resume',
                'p.intitule_complet as projet_complet',
                'p.periode as projet_periode',
                DB::raw('COUNT(m.id) as nombre_marches'),
                DB::raw('SUM(m.montant) as total_montant'),
                DB::raw('MAX(m.created_at) as dernier_marche')
            )
            ->groupBy('o.id', 'p.id')
            ->orderBy('o.intitule')
            ->get();

        return view('admin.operateurs.index', compact('operateur'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.operateurs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $requestData = $request->all();

        Operateur::create($requestData);

        return redirect('admin/operateurs')->with('flash_message', 'Operateur added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
public function show($id, $projet_id = null)
{
    $operateur = Operateur::with([
        'marches' => function($query) use ($projet_id) {
            if ($projet_id) {
                $query->where('projet_id', $projet_id);
            }
            $query->with(['projet', 'wilayas', 'partenaires']);
        }
    ])->findOrFail($id);

    // Si un projet_id est sp�cifi�, on peut aussi charger le projet
    $projet = null;
    if ($projet_id) {
        $projet = Projet::find($projet_id);
    }

    return view('admin.operateurs.show', compact('operateur', 'projet'));
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $operateur = Operateur::findOrFail($id);

        return view('admin.operateurs.edit', compact('operateur'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->all();

        $operateur = Operateur::findOrFail($id);
        $operateur->update($requestData);

        return redirect('admin/operateurs')->with('flash_message', 'Operateur updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Operateur::destroy($id);

        return redirect('admin/operateurs')->with('flash_message', 'Operateur deleted!');
    }
}
