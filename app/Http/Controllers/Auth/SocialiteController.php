<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // If user exists, just update provider if not set
                if (!$user->provider) {
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_token' => $socialUser->token,
                    ]);
                }
                Auth::login($user);
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Create new user
            $newUser = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'role' => 'pengunjung',
            ]);

            Auth::login($newUser);
            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Socialite {$provider} login error: ", ['error' => (string) $e]);
            return redirect()->route('login')->with('error', "Gagal login dengan {$provider}. Silakan coba lagi.");
        }
    }
}
