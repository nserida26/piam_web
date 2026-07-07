<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Commune;
use App\Models\Moughata;
use App\Models\Wilaya;
use Illuminate\Http\Request;

class CommunesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $commune = Commune::with(['moughata', 'wilaya'])->orderBy('code')->get();
        return view('admin.communes.index', compact('commune'));
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
        return view('admin.communes.create',compact('wilayas','moughatas'));
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

        Commune::create($requestData);

        return redirect('admin/communess')->with('flash_message', 'Commune added!');
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
        $commune = Commune::findOrFail($id);

        return view('admin.communes.show', compact('commune'));
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
        $commune = Commune::findOrFail($id);
        $wilayas = Wilaya::all();
        $moughatas = Moughata::all();
        return view('admin.communes.edit', compact('commune','wilayas','moughatas'));
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

        $commune = Commune::findOrFail($id);
        $commune->update($requestData);

        return redirect('admin/communes')->with('flash_message', 'Commune updated!');
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
        Commune::destroy($id);

        return redirect('admin/communes')->with('flash_message', 'Commune deleted!');
    }
}
