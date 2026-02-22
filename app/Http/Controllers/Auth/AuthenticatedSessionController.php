<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->status == 0) {
            Auth::logout();
            return redirect()->back()->withErrors(['message' => __('auth.account_inactive')]);
        }

        // User login through frontend
        if ($request->login == 'user_login' && $user->user_type === 'user') {
            return redirect(RouteServiceProvider::FRONTEND)
                ->with('success', __('auth.login_success'));
        } 
        // User is trying to login from the wrong portal
        elseif ($request->login == 'user_login' && $user->user_type !== 'user') {
            Auth::logout();
            return redirect()->back()->withErrors(['message' => __('auth.invalid_user_type')]);
        }

        // Admin or other login
        return redirect(RouteServiceProvider::HOME)
            ->with('success', __('auth.login_success'));
    }

    /**
     * Logout the user.
     */
    public function destroy(Request $request)
    {
     
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(RouteServiceProvider::FRONTEND)
            ->with('success', __('auth.logout_success'));
    }
}
