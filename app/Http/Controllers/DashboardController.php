<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the dashboard based on the user's role.
     *
     * @param string $role
     * @return \Illuminate\View\View
     */
    public function showDashboard($role)
    {
        $user = Auth::user();

        // Check if the user's role matches the requested dashboard
        if ($user->role !== $role) {
            abort(403, 'Unauthorized Access');
        }

        // Map role to the respective dashboard view
        $viewMap = [
            'admin' => 'dashboards.admin',
            'donor' => 'dashboards.donor',
            'foodbank' => 'dashboards.foodbank',
        ];

        // Fallback in case an invalid role is passed
        if (!array_key_exists($role, $viewMap)) {
            abort(404, 'Dashboard not found');
        }

        // Return the appropriate view based on the user's role
        return view($viewMap[$role], ['role' => $role]);
        return view('app');

    }
}

