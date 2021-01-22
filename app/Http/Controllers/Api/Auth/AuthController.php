<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
	/**
	 * Create a new AuthController instance.
	 *
	 * @return void
	 */
	public function __construct() {
	    $this->middleware('auth:api', ['except' => ['login', 'register']]);
	}

	/**
	 * Get a JWT via given credentials.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function login(Request $request){
		$validatedData = $request->validate([
	        'email' => 'required|email',
	        'password' => 'required|string|min:6',
	    ]);

	    if (! $token = auth()->attempt($validatedData)) {
	        return response()->json(['error' => 'Incorrect username or password'], 401);
	    }

	    return $this->createNewToken($token);
	}

	/**
	 * Register a Customer.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function register(Request $request) {
	    $validatedData = $request->validate([
	        'name' => 'required|string|max:255',
	        'email' => 'required|email|unique:users,email',
	        'password' => 'required|string|confirmed|min:6',
	        // 'dob' => 'required',
	        // 'country' => 'required|string|max:255',
	        // 'profession' => 'required|string|max:255',
	        // 'role' => 'required|in:customer,admin',
	    ]);

	    $user = User::create(array_merge(
	        $validatedData,
	        ['password' => bcrypt($request->password), 'role' => 'admin']
	    ));

	    return response()->json([
	        'message' => 'Successfully registered',
	        'user' => $user
	    ], 201);
	}


	/**
	 * Log out. 
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function logout() {
	    auth()->logout();

	    return response()->json(['message' => 'Successfully signed out']);
	}

	/**
	 * Refresh token.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function refresh() {
	    return $this->createNewToken(auth()->refresh());
	}

	/**
	 * Get authenticated User.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function userProfile() {
	    return response()->json(auth()->user());
	}

	/**
	 * Helper method (contruct response data with token)
	 *
	 * @param  string $token
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function createNewToken($token){
	    return response()->json([
	        'access_token' => $token,
	        'token_type' => 'bearer',
	        'expires_in' => auth()->factory()->getTTL() * 60,
	        'user' => auth()->user()
	    ]);
	}
}
