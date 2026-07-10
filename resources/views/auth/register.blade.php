@extends('layouts.app')

@section('title', 'Register - HIGH FIVE')

@section('content')
<div class="min-h-screen bg-primary-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="text-3xl font-bold">
                HIGH<span class="font-light">FIVE</span>
            </a>
            <p class="text-primary-600 mt-2">Buat akun baru</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="bg-white border border-primary-200 p-8">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-primary-700 mb-1">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-primary-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors @error('name') border-red-500 @enderror"
                        placeholder="John Doe"
                        required
                        autofocus
                    />
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

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
                    />
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-primary-700 mb-1">
                        No. Telepon (opsional)
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border border-primary-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors @error('phone') border-red-500 @enderror"
                        placeholder="08xxxxxxxxxx"
                    />
                    @error('phone')
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
                        placeholder="Minimal 8 karakter"
                        required
                    />
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-primary-700 mb-1">
                        Konfirmasi Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="w-full px-4 py-2 border border-primary-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors"
                        placeholder="Ulangi password"
                        required
                    />
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-primary-900 text-white py-3 px-6 hover:bg-primary-800 transition-colors mt-6 font-medium"
            >
                Daftar
            </button>
        </form>

        <p class="text-center text-primary-600 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary-900 font-medium hover:underline">
                Masuk
            </a>
        </p>
    </div>
</div>
@endsection
