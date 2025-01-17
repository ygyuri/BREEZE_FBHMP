<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users with pagination and optional filtering.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Log the incoming request for users
        Log::info('Fetching users', ['request' => $request->all()]);

        // Validate optional filters
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'role' => 'nullable|string|max:50',
        ]);

        try {
            // Fetch users with optional filtering and pagination
            $users = User::when($validated['name'] ?? null, function ($query, $name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                })
                ->when($validated['email'] ?? null, function ($query, $email) {
                    return $query->where('email', 'like', '%' . $email . '%');
                })
                ->when($validated['role'] ?? null, function ($query, $role) {
                    return $query->where('role', $role);
                })
                ->paginate(10);

            Log::info('Users fetched successfully', ['users' => $users]);

            return response()->json($users, 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch users', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created user in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Log the incoming request data
        Log::info('Creating user', ['request' => $request->all()]);

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|digits_between:10,15|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,foodbank,donor,recipient', // Roles can be extended as needed
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:255',
            'recipient_type' => 'nullable|in:individual,organization',
            'donor_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
             'status' => 'nullable|in:pending,approved,rejected', // Validate status if it's passed

        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed while creating user', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Sanitize and create the user using validated data
            $user = User::create([
                'name' => e($request->input('name')), // Sanitize output
                'email' => $request->input('email'),
                'phone' => $request->input('phone') ? preg_replace('/[^0-9]/', '', $request->input('phone')) : null,
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
                'location' => e($request->input('location')),
                'address' => e($request->input('address')),
                'organization_name' => e($request->input('organization_name')),
                'recipient_type' => $request->input('recipient_type'),
                'donor_type' => e($request->input('donor_type')),
                'notes' => e($request->input('notes')),
                'status' => $request->input('status', 'pending'),
            ]);

            Log::info('User created successfully', ['user' => $user]);

            return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create user', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create user', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        Log::info("Fetching user with ID: $id");

        try {
            $user = User::findOrFail($id);

            Log::info('User fetched successfully', ['user' => $user]);

            return response()->json($user, 200);
        } catch (\Exception $e) {
            Log::error("Failed to fetch user with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'User not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the specified user in the database.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Log the incoming request data
        Log::info("Updating user with ID: $id", ['request' => $request->all()]);

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['sometimes', 'digits_between:10,15', Rule::unique('users', 'phone')->ignore($id)],
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|string|in:admin,foodbank,donor,recipient',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:255',
            'recipient_type' => 'nullable|in:individual,organization',
            'donor_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,rejected', // Validate status if it's passed
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed while updating user with ID: $id", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Fetch user and update
            $user = User::findOrFail($id);
            $user->update($request->only(['name', 'email', 'phone', 'role', 'location', 'address', 'organization_name', 'recipient_type', 'donor_type', 'notes']));

            // If password is present, hash it and save
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            Log::info('User updated successfully', ['user' => $user]);

            return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update user with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update user', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified user from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info("Deleting user with ID: $id");

        try {
            // Soft delete the user
            $user = User::findOrFail($id);
            $user->delete();

            Log::info('User deleted successfully', ['user_id' => $id]);

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete user with ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete user', 'message' => $e->getMessage()], 500);
        }
    }


        // Approval for user roles foodbank, donor, recipient
            public function approveRejectUser(Request $request, $id, $status)
    {
        // Ensure that the authenticated user is an admin
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized action. Only admins can approve or reject users.'], 403);
        }

        // Validate that status is one of the accepted values: pending, approved, or rejected
        $validStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return response()->json(['error' => 'Invalid status. Accepted statuses are: pending, approved, rejected.'], 400);
        }

        try {
            // Fetch the user from the database
            $user = User::findOrFail($id);

            // Ensure the user role is one of the roles that require approval
            if (!in_array($user->role, ['foodbank', 'donor', 'recipient'])) {
                return response()->json(['error' => 'User does not require approval'], 400);
            }

            // Check if the status is being updated to the same status (no change needed)
            if ($user->status === $status) {
                return response()->json(['message' => "User is already in $status status."], 200);
            }

            // Update the user's status
            $user->status = $status;
            $user->save();

            // Log the status change
            Log::info("User {$user->id} status changed to $status", ['user' => $user]);

            // Return a success message
            return response()->json([
                'message' => "User status changed to $status successfully",
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            // Log any errors that occur during the process
            Log::error("Failed to change user status", ['error' => $e->getMessage()]);

            // Return an error message if something goes wrong
            return response()->json([
                'error' => 'Failed to change user status',
                'message' => $e->getMessage()
            ], 500);
        }
    }



        public function getFilteredUsersByStatus(Request $request)
    {
        // Validate the incoming request to ensure status and role are passed correctly
        $validated = $request->validate([
            'role' => 'required|string|in:foodbank,donor,recipient', // Validating the role to be one of foodbank, donor, or recipient
            'status' => 'nullable|string|in:pending,approved,rejected', // Status is optional but must be one of pending, approved, or rejected
        ]);

        // Get the role and status from the validated data
        $role = $validated['role'];
        $status = $validated['status'] ?? null;

        // Query to filter users based on role and optionally by status
        $query = User::where('role', $role);

        // If status is provided, filter by status as well
        if ($status) {
            $query->where('status', $status);
        }

        try {
            // Execute the query and get the filtered results
            $users = $query->get();

            // Return the filtered users as a JSON response
            return response()->json(['message' => 'Filtered users fetched successfully', 'data' => $users], 200);
        } catch (\Exception $e) {
            // Log any errors and return a failure response
            Log::error('Failed to fetch filtered users', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch filtered users', 'message' => $e->getMessage()], 500);
        }
    }





}