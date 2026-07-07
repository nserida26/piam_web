<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Moughata;
use App\Models\Wilaya;
use Illuminate\Http\Request;

class MoughatasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $moughata = Moughata::with('wilaya')->orderBy('code')->get();
        return view('admin.moughatas.index', compact('moughata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $wilayas = Wilaya::all();
        return view('admin.moughatas.create', compact('wilayas'));
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

        Moughata::create($requestData);

        return redirect('admin/moughatas')->with('flash_message', 'Moughata added!');
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
        $moughatum = Moughata::findOrFail($id);

        return view('admin.moughatas.show', compact('moughatum'));
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
        $moughatum = Moughata::findOrFail($id);
        $wilayas = Wilaya::all();

        return view('admin.moughatas.edit', compact('moughatum', 'wilayas'));
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

        $moughatum = Moughata::findOrFail($id);
        $moughatum->update($requestData);

        return redirect('admin/moughatas')->with('flash_message', 'Moughatum updated!');
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
        Moughata::destroy($id);

        return redirect('admin/moughatas')->with('flash_message', 'Moughatum deleted!');
    }
}
