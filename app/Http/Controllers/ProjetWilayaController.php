<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Projet;
use App\Models\ProjetWilaya;
use App\Models\Wilaya;
use Illuminate\Http\Request;

class ProjetWilayaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $projetwilaya = ProjetWilaya::join('projets', 'projet_wilayas.projet_id', 'projets.id')
            ->join('wilayas', 'projet_wilayas.wilaya_id', 'wilayas.id')
            ->select('wilayas.intitule', 'projets.intitule_resume', 'projet_wilayas.id')
            ->get();

        return view('admin.projet-wilaya.index', compact('projetwilaya'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $wilayas = Wilaya::all();
        $projets = Projet::all();
        return view('admin.projet-wilaya.create', compact('projets', 'wilayas'));
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

        ProjetWilaya::create($requestData);

        return redirect('admin/projet-wilaya')->with('flash_message', 'ProjetWilaya added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $projetwilaya = ProjetWilaya::join('projets', 'projet_wilayas.projet_id', 'projets.id')
            ->join('wilayas', 'projet_wilayas.wilaya_id', 'wilayas.id')
            ->select('wilayas.intitule', 'projets.intitule_resume', 'projet_wilayas.id')
            ->where('projet_wilayas.id', $id)
            ->first();

        return view('admin.projet-wilaya.show', compact('projetwilaya'));
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
        $projetwilaya = ProjetWilaya::findOrFail($id);
        $wilayas = Wilaya::all();
        $projets = Projet::all();

        return view('admin.projet-wilaya.edit', compact('projetwilaya', 'wilayas', 'projets'));
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

        $projetwilaya = ProjetWilaya::findOrFail($id);
        $projetwilaya->update($requestData);

        return redirect('admin/projet-wilaya')->with('flash_message', 'ProjetWilaya updated!');
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
        ProjetWilaya::destroy($id);

        return redirect('admin/projet-wilaya')->with('flash_message', 'ProjetWilaya deleted!');
    }
}
