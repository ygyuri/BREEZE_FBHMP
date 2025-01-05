<?php

namespace App\Http\Controllers;

use App\Models\FoodbankRequest;
use Illuminate\Http\Request;

class FoodbankController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:foodbank',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json(['message' => 'Foodbank registered', 'user' => $user], 201);
    }

    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'food_type' => 'required|string',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $request = FoodbankRequest::create([
            'user_id' => auth()->id(),
            'food_type' => $validated['food_type'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'],
        ]);

        return response()->json(['message' => 'Request submitted', 'request' => $request], 201);
    }
}
