<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use App\Models\Wilaya;
use App\Models\WilayaUser;
use Illuminate\Http\Request;

class WilayaUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $wilayauser = WilayaUser::join('wilayas', 'wilaya_users.wilaya_id', 'wilayas.id')
            ->join('users', 'wilaya_users.user_id', 'users.id')
            ->select('users.username', 'users.email', 'wilayas.intitule', 'wilaya_users.id')
            ->get();

        return view('admin.wilaya-user.index', compact('wilayauser'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $wilayas = Wilaya::all();
        $users = User::all();
        return view('admin.wilaya-user.create', compact('wilayas', 'users'));
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

        WilayaUser::create($requestData);

        return redirect('admin/wilaya-user')->with('flash_message', 'WilayaUser added!');
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
        $wilayauser = WilayaUser::join('wilayas', 'wilaya_users.wilaya_id', 'wilayas.id')
            ->join('users', 'wilaya_users.user_id', 'users.id')
            ->select('users.username', 'users.email', 'wilayas.intitule', 'wilaya_users.id')
            ->where('wilaya_users.id', $id)
            ->first();

        return view('admin.wilaya-user.show', compact('wilayauser'));
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
        
        $wilayauser = WilayaUser::findOrFail($id);
        $wilayas = Wilaya::all();
        $users = User::all();

        return view('admin.wilaya-user.edit', compact('wilayauser','wilayas','users'));
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

        $wilayauser = WilayaUser::findOrFail($id);
        $wilayauser->update($requestData);

        return redirect('admin/wilaya-user')->with('flash_message', 'WilayaUser updated!');
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
        WilayaUser::destroy($id);

        return redirect('admin/wilaya-user')->with('flash_message', 'WilayaUser deleted!');
    }
}
