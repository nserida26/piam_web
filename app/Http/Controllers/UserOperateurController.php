<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Operation;
use App\Models\Projet;
use App\Models\User;
use App\Models\UserOperateur;
use Illuminate\Http\Request;

class UserOperateurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $useroperateur = UserOperateur::join('projets', 'user_operateurs.projet_id', 'projets.id')
            ->join('operateurs', 'user_operateurs.operateur_id', 'operateurs.id')
            ->join('operations', 'user_operateurs.operation_id', 'operations.id')
            ->join('users', 'user_operateurs.user_id', 'users.id')
            ->select('users.username', 'users.email', 'projets.intitule_resume', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'date_valid', 'user_operateurs.id')->get();


        return view('admin.user-operateur.index', compact('useroperateur'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projets = Projet::all();
        $operations = Operation::all();
        $users = User::all();
        return view('admin.user-operateur.create', compact('projets', 'operations', 'users'));
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
        $operations = $requestData['operation_id'];
        $users = $requestData['user_id'];

        foreach ($operations as $operation_id) {
            # code...
            $requestData['operation_id'] = $operation_id;
            foreach ($users as $user_id) {
                # code...
                $requestData['user_id'] = $user_id;
                UserOperateur::create(
                    $requestData
                );
            }
        }



        return redirect('admin/user-operateur')->with('flash_message', 'UserOperateur added!');
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
        //$useroperateur = UserOperateur::findOrFail($id);
        $useroperateur = UserOperateur::join('projets', 'user_operateurs.projet_id', 'projets.id')
            ->join('operateurs', 'user_operateurs.operateur_id', 'operateurs.id')
            ->join('operations', 'user_operateurs.operation_id', 'operations.id')
            ->join('users', 'user_operateurs.user_id', 'users.id')
            ->select('users.username', 'users.email', 'projets.intitule_resume', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'date_valid', 'user_operateurs.id')
            ->where('user_operateurs.id', $id)
            ->first();



        return view('admin.user-operateur.show', compact('useroperateur'));
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
        $useroperateur = UserOperateur::findOrFail($id);
        $projets = Projet::all();
        $operations = Operation::all();

        return view('admin.user-operateur.edit', compact('useroperateur', 'projets', 'operations'));
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

        $useroperateur = UserOperateur::findOrFail($id);
        $useroperateur->update($requestData);

        return redirect('admin/user-operateur')->with('flash_message', 'UserOperateur updated!');
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
        UserOperateur::destroy($id);

        return redirect('admin/user-operateur')->with('flash_message', 'UserOperateur deleted!');
    }
}
