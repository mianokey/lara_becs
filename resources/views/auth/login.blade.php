@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            <div class="bg-becs-navy text-white text-center py-4">
                <h2 class="text-2xl font-bold">BECS Consultancy</h2>
                <p class="text-sm opacity-90">Task & Project Management System</p>
            </div>

            <div class="p-6 space-y-4">
                <h3 class="text-xl font-semibold text-becs-navy text-center mb-4">Login to Your Account</h3>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Email Address') }}
                        </label>
                        <input id="email" type="email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-becs-navy focus:border-becs-navy @error('email') border-red-500 @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Password') }}
                        </label>
                        <input id="password" type="password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-becs-navy focus:border-becs-navy @error('password') border-red-500 @enderror"
                            name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center mb-4">
                        <input class="h-4 w-4 text-becs-navy border-gray-300 rounded" type="checkbox"
                            name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="ml-2 block text-sm text-gray-700" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-between">
                        <button type="submit"
                            class="w-full bg-becs-navy hover:bg-becs-navy/90 text-white font-semibold py-2 rounded-lg transition">
                            {{ __('Login') }}
                        </button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="text-sm text-becs-gray hover:underline" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
