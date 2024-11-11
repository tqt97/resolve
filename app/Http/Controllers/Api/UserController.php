<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'positions' => 'required|array', // Expecting an array of position IDs
            'positions.*' => 'exists:positions,id', // Ensure each position ID exists
        ]);

        // Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Attach the provided positions to the user
        $user->positions()->attach($validatedData['positions']);

        return response()->json([
            'message' => 'User created and positions attached successfully.',
            'user' => new UserResource($user->load('positions.department')), // Load positions with department info
        ], 201);
    }

    public function show($id)
    {
        $user = User::with('positions.department')->findOrFail($id);

        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {

        // $validatedData = $request->validate([
        //     // 'name' => 'sometimes|string|max:255',
        //     // 'email' => 'sometimes|email|unique:users,email,' . $id,
        //     'positions' => 'required|array|empty', // Expecting an array of position IDs
        //     'positions.*' => 'exists:positions,id', // Ensure each position ID exists
        // ]);
        // return response()->json([
        //     'message'=> 'oke',
        // ],200);

        // Find the user
        $user = User::findOrFail($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        // Update user details if provided
        // $user->update($validatedData);

        // Sync positions (attach new and detach old ones)
        // $user->positions()->sync($request->positions);

        if (isset($request->positions)) {
            if (empty($request->positions)) {
                // If `positions` is an empty array, remove all positions
                $user->positions()->sync([]);
            } else {
                // Sync positions with `created_by` pivot value
                $user->positions()->syncWithPivotValues($request->positions, ['created_by' => Auth::id()]);
            }
        }

        if (isset($request->positions)) {
            $user->positions()->syncWithPivotValues(
                $request->positions ?: [],  // If positions is empty, pass an empty array
                ['created_by' => Auth::id()]
            );
        }

        // if (array_key_exists('positions', $request->positions)) {
        //     $user->positions()->syncWithPivotValues(
        //         $request->positions ?: [],
        //         ['created_by' => Auth::id()]
        //     );
        // }

        return response()->json([
            'message' => 'User updated and positions synced successfully.',
            'user' => new UserResource($user->load('positions.department')),
        ], 200);
    }

}
