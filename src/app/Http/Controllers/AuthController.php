<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'password' => 'required',
		]);

		if (!Auth::attempt($request->only('email', 'password'))) {
			return response()->json([
				'message' => 'Invalid login details'
			], 401);
		}

		return response()->json([
			'message' => 'Login successful',
			'token' => $request->user()->createToken('API Token')->plainTextToken,
		]);
	}

	public function logout(Request $request)
	{
		$request->user()->tokens()->delete();

		return response()->json([
			'message' => 'Logged out'
		]);
	}
}