@extends('layouts.app')

@section('title', 'Login - HIGH FIVE')

@section('content')
<div class="min-h-screen bg-primary-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="text-3xl font-bold">
                HIGH<span class="font-light">FIVE</span>
            </a>
            <p class="text-primary-600 mt-2">Masuk ke akun Anda</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="bg-white border border-primary-200 p-8">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-primary-700 mb-1">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2 border border-primary-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors @error('email') border-red-500 @enderror"
                        placeholder="your@email.com"
                        required
                        autofocus
                    />
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-primary-700 mb-1">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full px-4 py-2 border border-primary-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors @error('password') border-red-500 @enderror"
                        placeholder="••••••••"
                        required
                    />
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" name="remember">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Ingat saya
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-primary-600 hover:text-primary-500">
                                Lupa password?
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-primary-900 text-white py-3 px-6 hover:bg-primary-800 transition-colors mt-6 font-medium"
            >
                Masuk
            </button>
        </form>

        <p class="text-center text-primary-600 mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary-900 font-medium hover:underline">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>
@endsection
