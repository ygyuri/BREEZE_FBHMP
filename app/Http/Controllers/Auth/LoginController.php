<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle user authentication and redirect based on role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        $dashboardRoutes = [
            'admin' => 'dashboard.admin',
            'donor' => 'dashboard.donor',
            'foodbank' => 'dashboard.foodbank',
        ];

        if (array_key_exists($user->role, $dashboardRoutes)) {
            return redirect()->route($dashboardRoutes[$user->role]);
        }

        // Fallback route for undefined roles
        return redirect()->route('dashboard');
    }
}
