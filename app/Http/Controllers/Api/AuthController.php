<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request): JsonResponse
    {

        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'User already registered with this email address.'], 409);
        }

        $user = $this->userRepository->create($request->validated());

        return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();
            
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'User logged in successfully!',
                'user' => $user,
                'token' => $token
            ], 200);
        }
        
        return response()->json(['error' => 'Invalid credentials, or the User does not exists.'], 401);
    }
}
