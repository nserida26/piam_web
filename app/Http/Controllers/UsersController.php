<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Image;
use App\Models\Role;
use App\Models\User;
use App\Models\Localite;
use App\Models\Wilaya;
use App\Models\Operateur;
use App\Models\Projet;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }



    /**
     * Admin Profile Update
     */
    public function userProfileUpdate(Request $request)
    {
        $userId = Auth::user()->id;

        $request->validate([
            'name' => 'required|string|max:60',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:1024',
        ]);

        $user = User::find($userId);

        $user->username = $request->name;

         // image upload
        $path = 'default.png';
        if ($request->has('image')) {
            //Storage::delete($user->photo);
            $extension = "." . $request->image->getClientOriginalExtension();
            $name = basename($request->image->getClientOriginalName(), $extension) . time();
            $name = $name . $extension;
            $path = $request->image->storeAs('assets/admin/imgs', $name, 'public');
        }

        $user->photo = $path;
        $update = $user->save();

        if($update) {
            $notification = array(
                'message' => 'Your profile has been updated successfully.',
                'alert-type' => 'success'
            );

            return redirect()->route('user.profile')->with($notification);
        } else {
            $notification = array(
                'message' => 'Oops! Something went wrong',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }

    }

   /**
    * Return users
    *
    * @return array
    */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {

        $roles = Role::all();
        $wilayas = Wilaya::orderBy('intitule')->get();
        $assignedLocalites = Localite::whereIn('id', (array) old('localite_ids', []))->get();
        $operateurs = Operateur::orderBy('intitule')->get();
        $projets = Projet::orderBy('intitule_resume')->get();
        return view('admin.users.create', compact('roles', 'wilayas', 'assignedLocalites', 'operateurs', 'projets'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:1024',
            'role_id' => 'required|array',
            'localite_ids' => 'nullable|array',
            'localite_ids.*' => 'integer|exists:localites,id',
            'operateur_id' => 'nullable|integer|exists:operateurs,id',
            'wilaya_ids' => 'nullable|array',
            'wilaya_ids.*' => 'integer|exists:wilayas,id',
            'projet_ids' => 'nullable|array',
            'projet_ids.*' => 'integer|exists:projets,id',
            'operateur_ids' => 'nullable|array',
            'operateur_ids.*' => 'integer|exists:operateurs,id',
        ]);
        $this->validateFacilitateurLocalites($request);
        $this->validateCoordinateurOng($request);

        $user = new User;

        $user->username = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
         //image upload
                $path = 'default.png';
        
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $extension = "." . $request->image->getClientOriginalExtension();
            $name = basename($request->image->getClientOriginalName(), $extension) . time();
            $name = $name . $extension;
            $path = $request->image->storeAs('assets/admin/imgs', $name, 'public');
        }

        $user->photo = $path;
        $user->operateur_id = $request->operateur_id ?: null;
        $save = $user->save();

        if($save) {

            // save roles for this user
            $roleAssigns = [];
            foreach($request->role_id as $role){
               $roleAssigns[] = [
                   'role_id' => $role,
                   'user_id' => $user->id
               ];
            }
            $save = DB::table('role_user')->insert($roleAssigns);

            $this->syncLocalites($user, $request);
            $user->wilayas()->sync(array_map('intval', (array) $request->wilaya_ids));
            $user->projets()->sync(array_map('intval', (array) $request->projet_ids));
            $user->operateursAutorises()->sync(array_map('intval', (array) $request->operateur_ids));

            $notification = array(
                'message' => 'Your profile has been updated successfully.',
                'alert-type' => 'success'
            );

            return redirect()->route('admin.users.index')->with($notification);
        } else {
            $notification = array(
                'message' => 'Oops! Something went wrong',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }

    }

    public function show($id)
    {
        $roles = Role::all();
        $user = User::find($id);
        return view('admin.users.show', compact('roles', 'user'));
    }

    public function edit($id)
    {
        $roles = Role::all();

        $user = User::find($id);
        $wilayas = Wilaya::orderBy('intitule')->get();
        $assignedLocalites = old('localite_ids') !== null
            ? Localite::whereIn('id', (array) old('localite_ids'))->get()
            : $user->localites()->get();
        $operateurs = Operateur::orderBy('intitule')->get();
        $projets = Projet::orderBy('intitule_resume')->get();
        return view('admin.users.edit', compact('roles', 'user', 'wilayas', 'assignedLocalites', 'operateurs', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|string|max:255|unique:users,email,'.$id,
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:1024',
            'role_id' => 'required|array',
            'localite_ids' => 'nullable|array',
            'localite_ids.*' => 'integer|exists:localites,id',
            'operateur_id' => 'nullable|integer|exists:operateurs,id',
            'wilaya_ids' => 'nullable|array',
            'wilaya_ids.*' => 'integer|exists:wilayas,id',
            'projet_ids' => 'nullable|array',
            'projet_ids.*' => 'integer|exists:projets,id',
            'operateur_ids' => 'nullable|array',
            'operateur_ids.*' => 'integer|exists:operateurs,id',
        ]);
        $this->validateFacilitateurLocalites($request);
        $this->validateCoordinateurOng($request);

        $user = User::find($id);

        $user->username = $request->name;
        $user->email = $request->email;

        // profile picture update
        $path = 'default.png';
        if ($request->has('image')) {
            //Storage::delete($user->photo);
            $extension = "." . $request->image->getClientOriginalExtension();
            $name = basename($request->image->getClientOriginalName(), $extension) . time();
            $name = $name . $extension;
            $path = $request->image->storeAs('assets/admin/imgs', $name, 'public');
        }

        $user->photo = $path;
        $user->operateur_id = $request->operateur_id ?: null;
        $save = $user->save();

        if($save) {

            // delete roles for this user
            if($request->role_id) {
                // delete old role
                $userRoles = DB::table('role_user')->where('user_id', '=', $id)->get();
                foreach($userRoles as $userRole) {
                    DB::table('role_user')->where('id', '=', $userRole->id)->delete();
                }
            }


            // store new roles
            $roleAssigns = [];
            foreach($request->role_id as $role) {
                $roleAssigns[] = [
                    'role_id' => $role,
                    'user_id' => $id
                ];
            }

            $save = DB::table('role_user')->insert($roleAssigns);

            $this->syncLocalites($user, $request);
            $user->wilayas()->sync(array_map('intval', (array) $request->wilaya_ids));
            $user->projets()->sync(array_map('intval', (array) $request->projet_ids));
            $user->operateursAutorises()->sync(array_map('intval', (array) $request->operateur_ids));

            $notification = array(
                'message' => 'Your has been updated successfully.',
                'alert-type' => 'success'
            );

            return redirect()->route('admin.users.index')->with($notification);
        } else {
            $notification = array(
                'message' => 'Oops! Something went wrong',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if(!is_null($user)) {
            //Storage::delete($user->photo);
            $delete = $user->delete();

            if($delete) {

                // save roles for this user
                $userRoles = DB::table('role_user')->where('user_id', '=', $id)->get();
                foreach($userRoles as $role){
                    $role->delete();
                }

                $notification = array(
                    'message' => 'User has been deleted successfully.',
                    'alert-type' => 'success'
                );

                return redirect()->route('admin.users.index')->with($notification);

            } else {

                $notification = array(
                    'message' => 'Oops! Something went wrong',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification);

            }

        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function indexRole(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $roles = Role::where('name', 'LIKE', "%$keyword%")

                ->latest()->paginate($perPage);
        } else {
            $roles = Role::latest()->paginate($perPage);
        }

        return view('admin.users.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function createRole()
    {
        return view('admin.users.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeRole(Request $request)
    {

        $requestData = $request->all();

        Role::create($requestData);

        return redirect('admin/users/roles')->with('flash_message', 'Role added!');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.users.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateRole(Request $request, $id)
    {


        $requestData = $request->all();

        $role = Role::findOrFail($id);
        $role->update($requestData);

        return redirect('admin/users/roles')->with('flash_message', 'Role updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroyRole($id)
    {
        Role::destroy($id);

        return redirect('admin/users/roles')->with('flash_message', 'Role deleted!');
    }

    /**
     * Un facilitateur doit avoir au moins une localité assignée.
     */
    private function validateFacilitateurLocalites(Request $request)
    {
        if ($this->isFacilitateur($request->role_id) && empty($request->localite_ids)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'localite_ids' => __('user.localites_required'),
            ]);
        }
    }

    /**
     * Enregistre les localités assignées. Elles ne concernent que le rôle
     * Facilitateur : sans ce rôle, l'attribution est vidée (pas de restriction).
     */
    private function syncLocalites(User $user, Request $request)
    {
        $ids = $this->isFacilitateur($request->role_id)
            ? array_map('intval', (array) $request->localite_ids)
            : [];
        $user->localites()->sync($ids);
    }

    private function isFacilitateur($roleIds): bool
    {
        // Le rôle est enregistré « Faciltateur » en base : on compare sur « facil ».
        return Role::whereIn('id', (array) $roleIds)
            ->where('name', 'like', '%facil%')
            ->exists();
    }

    /**
     * Un coordinateur doit être rattaché à une ONG : il ne voit que les
     * données de celle-ci.
     */
    private function validateCoordinateurOng(Request $request)
    {
        $isCoordinateur = Role::whereIn('id', (array) $request->role_id)
            ->where('name', 'like', '%coordinat%')
            ->exists();

        if ($isCoordinateur && !$request->operateur_id) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'operateur_id' => "Choisissez l'ONG du coordinateur.",
            ]);
        }
        if ($isCoordinateur && empty($request->wilaya_ids)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'wilaya_ids' => 'Choisissez la wilaya du coordinateur.',
            ]);
        }
    }
}
