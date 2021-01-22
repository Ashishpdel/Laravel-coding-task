<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::where('role', 'customer')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'dob' => 'required|date',
            'country' => 'required|string|max:255',
            'profession' => 'required|string|max:255',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|in:customer,admin',
        ]);

        $user = User::create(array_merge(
            $validatedData,
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'Successfully registered',
            'user' => new UserResource($user)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'dob' => 'date',
            'country' => 'string|max:255',
            'profession' => 'string|max:255',
        ]);

        if($request->filled('new_password')) {
            $request->validate([
                'new_password' => 'required|string|confirmed|min:6'
            ]);

            $validatedData['password'] = bcrypt($request->new_password);
        }

        $user->update($validatedData);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 200);
    }
}
