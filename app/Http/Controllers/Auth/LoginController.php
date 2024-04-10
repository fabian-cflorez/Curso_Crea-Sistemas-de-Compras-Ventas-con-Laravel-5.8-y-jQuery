<?php

namespace App\Http\Controllers\Auth;


// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Redirect;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if(Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password, 'condicion' => true]))
        {
            // return redirect()->route('main');
            return redirect('/home');
        }

        return back()->withErrors(['usuario' => trans('auth.failed')])
        ->withInput(request(['usuario']));
    }

    public function validateLogin(Request $request)
    {
        $this->validate($request,
        [
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}