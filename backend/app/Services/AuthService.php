<?php

namespace App\Services;

use App\Enum\StatusEnum;
use App\Exceptions\InvalidOperationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login($request): string
    {
        $emailOrName = $request['emailOrName'] ?? null;
        $password = $request['password'] ?? null;

        $user = User::where(function ($query) use ($emailOrName) {
            $query->where('email', $emailOrName)
                ->orWhere('name', $emailOrName);
        })
            ->where('status', StatusEnum::ACTIVE->value)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new InvalidOperationException('Invalid credentials.');
        }

        // Create access token
        $tokenResult = $user->createToken('crm-token');

        return $tokenResult->plainTextToken;
    }
}
