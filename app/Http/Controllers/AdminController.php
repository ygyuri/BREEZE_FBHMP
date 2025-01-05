<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of all admins with optional filters and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('Fetching admins', ['request' => $request->all()]);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
        ]);

        try {
            $admins = User::where('role', 'admin')
                ->when($validated['name'] ?? null, function ($query, $name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                })
                ->when($validated['email'] ?? null, function ($query, $email) {
                    return $query->where('email', 'like', '%' . $email . '%');
                })
                ->paginate(10);

            Log::info('Admins fetched successfully', ['admins' => $admins]);

            return response()->json($admins, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch admins', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch admins', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created admin in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Creating admin', ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|digits_between:10,15|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while creating admin', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $admin = User::create([
                'name' => e($request->input('name')),
                'email' => $request->input('email'),
                'phone' => $request->input('phone') ? preg_replace('/[^0-9]/', '', $request->input('phone')) : null,
                'password' => Hash::make($request->input('password')),
                'role' => 'admin', // Explicitly set the role
            ]);

            Log::info('Admin created successfully', ['admin' => $admin]);

            return response()->json(['message' => 'Admin created successfully', 'data' => $admin], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create admin', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create admin', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified admin.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info("Fetching admin with ID: $id");

        try {
            $admin = User::where('role', 'admin')->findOrFail($id);

            Log::info('Admin fetched successfully', ['admin' => $admin]);

            return response()->json($admin, 200);
        } catch (\Exception $e) {
            Log::error("Failed to fetch admin with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Admin not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified admin in the database.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Log::info("Updating admin with ID: $id", ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['sometimes', 'digits_between:10,15', Rule::unique('users', 'phone')->ignore($id)],
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed while updating admin with ID: $id", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $admin = User::where('role', 'admin')->findOrFail($id);
            $admin->update($request->only(['name', 'email', 'phone']));

            if ($request->has('password')) {
                $admin->password = Hash::make($request->password);
                $admin->save();
            }

            Log::info('Admin updated successfully', ['admin' => $admin]);

            return response()->json(['message' => 'Admin updated successfully', 'data' => $admin], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update admin with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update admin', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified admin from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info("Deleting admin with ID: $id");

        try {
            $admin = User::where('role', 'admin')->findOrFail($id);
            $admin->delete();

            Log::info('Admin deleted successfully', ['admin_id' => $id]);

            return response()->json(['message' => 'Admin deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete admin with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete admin', 'message' => $e->getMessage()], 500);
        }
    }
}
