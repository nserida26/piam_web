<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\SendCodeResetPassword;
//use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class ForgotPasswordController extends Controller
{

    public function forgot(Request $request)
    {
        //ResetCodePassword::where('email', $request->email)->delete();
        $validation = Validator::make($request->all(),[
            'email' => 'required|email'
        ]);
    
        if ($validation->fails()) {
            # code...
            $array = [
                'error'  => 'no_email'
            ];
            return $array;

        }else{
            $user_count = User::where('email',$request->email)->count();
            if ($user_count == 0) {
                $array = [
                    'error'  => 'no_email'
                ];
                return $array;
                # code...
            }
            $user = User::where('email',$request->email)->first();
            $code = mt_rand(100000, 999999);
            $codeData = DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $code,
                'created_at' => now()
            ]);
            $user->password = bcrypt($code);
            $user->save();
            Mail::to($request->email)->send(new SendCodeResetPassword($user->phone,$code));
            $array = [
                'status'  => 'ok'
            ];
            return $array;

        }
        

       

        
    }
}

