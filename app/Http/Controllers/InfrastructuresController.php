<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Wilaya;
use Illuminate\Http\Request;

class InfrastructuresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $infrastructures = Infrastructure::orderBy('infra_publ')->get();
        return view('admin.infrastructures.index', compact('infrastructures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $wilayas = Wilaya::all();
        $moughatas = Moughata::all();
        $communes = Commune::all();
        $localites = Localite::all();

        return view('admin.infrastructures.create', compact('wilayas', 'moughatas', 'communes', 'localites'));
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

        Infrastructure::create($requestData);

        return redirect('admin/infrastructures')->with('flash_message', 'Infrastructure added!');
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
        $infrastructure = Infrastructure::findOrFail($id);

        return view('admin.infrastructures.show', compact('infrastructure'));
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
        $infrastructure = Infrastructure::findOrFail($id);
        $wilayas = Wilaya::all();
        $moughatas = Moughata::all();
        $communes = Commune::all();
        $localites = Localite::all();


        return view('admin.infrastructures.edit', compact('infrastructure', 'wilayas', 'moughatas', 'communes', 'localites'));
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

        $infrastructure = Infrastructure::findOrFail($id);
        $infrastructure->update($requestData);

        return redirect('admin/infrastructures')->with('flash_message', 'Infrastructure updated!');
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
        Infrastructure::destroy($id);

        return redirect('admin/infrastructures')->with('flash_message', 'Infrastructure deleted!');
    }
}
