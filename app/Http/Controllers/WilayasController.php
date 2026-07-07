<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Wilaya;
use Illuminate\Http\Request;

class WilayasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $wilayas = Wilaya::orderBy('code')->get();
        return view('admin.wilayas.index', compact('wilayas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.wilayas.create');
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
        
        Wilaya::create($requestData);

        return redirect('admin/wilayas')->with('flash_message', 'wilaya added!');
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
        $wilaya = Wilaya::findOrFail($id);

        return view('admin.wilayas.show', compact('wilaya'));
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
        $wilaya = Wilaya::findOrFail($id);

        return view('admin.wilayas.edit', compact('wilaya'));
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
        
        $wilaya = Wilaya::findOrFail($id);
        $wilaya->update($requestData);

        return redirect('admin/wilayas')->with('flash_message', 'wilaya updated!');
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
        Wilaya::destroy($id);

        return redirect('admin/wilayas')->with('flash_message', 'wilaya deleted!');
    }
}
