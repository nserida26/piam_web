<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Partenaire;
use Illuminate\Http\Request;

class PartenairesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $partenaires = Partenaire::orderBy('code')->get();
        return view('admin.partenaires.index', compact('partenaires'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.partenaires.create');
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
        
        Partenaire::create($requestData);

        return redirect('admin/partenaires')->with('flash_message', 'Partenaire added!');
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
        $partenaire = Partenaire::findOrFail($id);

        return view('admin.partenaires.show', compact('partenaire'));
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
        $partenaire = Partenaire::findOrFail($id);

        return view('admin.partenaires.edit', compact('partenaire'));
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
        
        $partenaire = Partenaire::findOrFail($id);
        $partenaire->update($requestData);

        return redirect('admin/partenaires')->with('flash_message', 'Partenaire updated!');
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
        Partenaire::destroy($id);

        return redirect('admin/partenaires')->with('flash_message', 'Partenaire deleted!');
    }
}
