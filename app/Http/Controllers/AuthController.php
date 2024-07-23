<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginPost(Request $request)
    {
        $userdata = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($userdata)) {
            if (Auth::user()->status == 0) {
                Session::flash('warning', 'Your Account is pending approval. Please contact support.');
                return redirect()->back();
            }
            return redirect()->intended('/campaigns');
        } else {
            Session::flash('warning', 'Invalid Username / Password');
            return redirect()->back();
        }
    }

    public function loginGet()
    {
        return view('login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function registerGet()
    {
        if (Auth::check()) {
            return redirect()->intended('/campaigns');
        } else {
            return view('register');
        }
    }

    public function registerPost(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
        ];

        $validator = $this->validator($data);

        if ($validator->fails()) {
            return redirect('/register')->withErrors($validator)->withInput();
        } else {
            $this->create($data);
            Session::flash('message', 'Thanks for your registration. We will let you know once we confirm your account.');
            return redirect('/login');
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'username' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'plain_pass' => $data['password'], // Assuming 'plain_pass' is a column in your users table
            'password' => bcrypt($data['password']),
        ]);
    }

    public function generatePass(Request $request)
    {
        return bcrypt($request->input('password'));
    }
}
