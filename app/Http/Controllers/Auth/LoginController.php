<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/product';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // Update redirectTo berdasarkan role:
    protected function redirectTo()
    {
        if(auth()->user()->is_admin) {
            return '/admin/dashboard';
        }
        return '/product';
    }

    // Atau lebih baik override method authenticated:
    protected function authenticated(Request $request, $user)
    {
        if($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('index_product');
    }
}
