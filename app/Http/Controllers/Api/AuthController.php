<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController

{

    public function login(Request $request)
    {



        // Identifiant : nom d'utilisateur ou email (le web se connecte par
        // email ; les utilisateurs saisissent souvent l'un pour l'autre).
        $login = trim((string) $request->username);
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $request->password])
            || ($field === 'username' && Auth::attempt(['email' => $login, 'password' => $request->password]))) {
            $user = Auth::user();
            if (!$user->isActive()) {
                return $this->sendError(__("auth.disabled"), ['error' => 'disabled']);
            }
            $id = Auth::id();
            $today = date('Y-m-d');
            $user_operateur = DB::table('user_operateurs')->where('user_id', $id)->whereDate('date_valid', '>=', $today)->first();
            
            //if (isset($user_operateur) && !empty($user_operateur) && $user_operateur != null) {

                $success['result'] =  $user;
                $success['status'] =  'ok';
                $success['headers'] =  ['authorization' => $user->createToken('MyApp')->plainTextToken];
                return $this->sendResponse($success, __("auth.success"));
            //} else {
              //  return $this->sendError(__("auth.blocked"), ['error' => $request->all()]);
            //}
            # code...

        } else {
            return $this->sendError(__("auth.blocked"), ['error' => "error"]);
        }
    }
    public function logout()
    {
        //Auth::logout();
        $user = Auth::user();
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['username'] =  $user->username;
        $success['status'] =  'ok';
        $success['result'] = request()->user()->tokens()->delete();
        return $this->sendResponse(
            $success,
            __("auth.logout_success"),
        );
    }
}
