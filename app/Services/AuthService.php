<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function register(array $data): array
    {
        $userData = [
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'name' => $data['name'] ?? null,
        ];

        $user = $this->users->create($userData);
        $token = $user->createToken('API Token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): ?string
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();

        return $user?->createToken('TaskManagerApp')->plainTextToken;
    }
}

