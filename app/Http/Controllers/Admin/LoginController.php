<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
class LoginController extends Controller
{
public function show_login_view(){
return view('admin.auth.login');
}
public function login(LoginRequest $request){
    //dd($request->all());
if(auth()->guard('admin')->attempt(['email'=>$request->input('email'),'password'=>$request->input('password')]))
{
if (!auth()->guard('admin')->user()->isActive()) {
    auth()->guard('admin')->logout();
    return redirect()->route('admin.showlogin')->with(['error' => __('auth.disabled')]);
}
return redirect()->route('admin.dashboard');
}else{
 
return redirect()->route('admin.showlogin')->with(['error' => 'عفوا بيانات تسجيل الدخول غير صحيحة !!']);
}
}
public function logout(){
auth()->logout();
return redirect()->route('admin.showlogin');
}

}
