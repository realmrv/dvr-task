<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Stateless;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AuthenticatedUserController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): array
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::firstWhere('email', $validated['email']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => 'The provided credentials are incorrect.',
            ]);
        }

        $user->tokens()->where('name', 'api')->delete();

        $expiredAt = CarbonImmutable::now()->add(config('auth.defaults.api_token_expires_in'));

        return [
            'token' => $user->createToken('api', expiresAt: $expiredAt)->plainTextToken,
            'expired_at' => $expiredAt->toDateTimeString(),
        ];
    }
}
