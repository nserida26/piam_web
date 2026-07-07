<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Image;
use App\Models\Role;
use App\Models\User;
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
        return view('admin.users.create', compact('roles', ));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:1024',
            'role_id' => 'required|array',
        ]);

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
        return view('admin.users.edit', compact('roles', 'user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|string|max:255|unique:users,email,'.$id,
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:1024',
            'role_id' => 'required|array',

        ]);

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

}
