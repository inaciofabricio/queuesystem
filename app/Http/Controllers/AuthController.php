<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login() {

        return view('auth.login_frm', ['subtitle' => 'Login']);
    }

    public function loginSubmit(Request $request) {

        $request->validate(
            [
                'username' => 'required|email',
                'password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,16}$/'
            ],
            [
                'username.required' => 'O usuário é obrigatório.',
                'username.email' => 'O usuário deve ser um e-mail válido',
                'password.required' => 'A senha é obrigatória.',
                'password.regex' => 'A senha deve conter entre 6 e 16 caracteres, ter uma maiúscula, uma minúscula e um algarismo',
            ]
        );

        $user = User::where('email', trim($request->username))
            ->where('active', true)
            ->whereNull('deleted_at')
            ->where(function($query){
                $query->whereNull('blocked_until')->orWhere('blocked_until', '<', now());
            })
            ->first();

        if($user && Hash::check(trim($request->password), $user->password))
        {
            $this->loginUser($user);
            return redirect()->route('home');
        }
        else
        {
            return redirect()->back()->withInput()->with('server_error', 'Login inválido.');
        }
    }

    private function loginUser($user) {

        $user->last_login = now();
        $user->code = null;
        $user->code_expiration = null;
        $user->blocked_until = null;
        $user->save();

        Auth::login($user);
    }

    public function logout() {

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function changePassword() {
        return view('auth.change_password_frm', ['subtitle' => 'Alterar Senha']);
    }

    public function changePasswordSubmit(Request $request) {

        $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,16}$/|confirmed',
            ],
            [
                'current_password.required' => 'A senha atual é obrigatória.',
                'new_password.required' => 'A nova senha atual é obrigatória.',
                'new_password.regex' => 'A nova senha deve conter entre 6 e 16 caracteres, ter uma maiúscula, uma minúscula e um algarismo',
                'new_password.confirmed' => 'A nova senha e a confirmação não estão iguais.',
            ]
        );

        $user = Auth::user();

        if(Hash::check($request->current_password, $user->password)) {

            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->route('home')->with('message_success', 'Senha alterada com sucesso.');

        } else {
            return redirect()->back()->with('server_error', 'Senha atual inválida.');
        }
    }

}
