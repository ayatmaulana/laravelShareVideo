<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
 	public function showLogin()
 	{
 		return view('pages.logins.login');
 	}

 	public function doLogin(Request $request)
 	{
 		$dataLogin 										= $request->only('email', 'password');

 		if(\Auth::attempt($dataLogin))
 			return \Redirect::to('admin/manage/dashboard')->with('sc_msg', 'Login Successfuly');

 		return \Redirect::to('login')
 				->with('err_msg', 'Login Failed, Username or Password Wrong')
 				->withInput($dataLogin);
 	}

 	public function doLogout()
 	{
 		\Auth::logout();

 		return \Redirect::to('login')
 				->with('sc_msg', 'Logout Successfuly');
 	}
}
