<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    
    public function show($id)
    {
    # code...
        $user = User::find($id);
        //$user = Auth::user();
        $id = $user->id;
        if (!isset($user)) {
            # code...
            //{ error: 'no_user', status: :error }
            return [
                'status' => 'error','error' => 'no_user'
            ];
        }
        $not_fake_count = Report::join('users','users.id','reports.user_id')->where('reports.user_id',$id)->where('reports.status','!=','fake')->count();
        //$not_fake_count = $user->report()->where('status','!=','fake')->count();
        $total_count = Report::join('users','users.id','reports.user_id')->where('reports.user_id',$id)->count();
        
        $value_fake = 100;
        if ($not_fake_count >= 0) {
            # code...
            $value_fake = ($not_fake_count * 100) / $total_count;

        }
        $value = ($total_count >= 0) ? $value_fake : 0;
        return [
            'status' => 'ok','user' => $user, 'trust' => intval($value) , 'total'=> $total_count
        ];
        //status: :ok, user: user, trust: value, total: total_count
    }
    
    /**/
    //users/update
    public function update(Request $request)
    {
        //try {
            //code...
            $user = Auth::user();
            $req =  $request->all();
            if ($req['password'] == null) {
                # code...
                return [
                    'error' => 'no_password'
                    ,'status' => 'error'
                ];
            }
            //return $user->password.'current'.bcrypt($req['current_password']);
            /*if( Hash::check($req['current_password'], $user->password)){
                return [
                'error' => 'invalid_password'
                ,'status' => 'error'
                ];
            }
            */
            
            User::where('id' ,$user->id)->update([
                'password' =>  bcrypt($req['password'])
            ]);
            return [ 'status' =>'ok' ];
        //} catch (\Throwable $th) {
          //  return [
            //    'error' => $th
              //  ,'status' => 'error'
            //];
        //}
    }
    //password/forgot
    public function forgot()
    {
    # code...

    }
}
