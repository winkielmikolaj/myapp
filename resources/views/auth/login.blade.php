@extends('layouts.app', ['title' => 'Logowanie Administratora'])

@section('content')
    <div class="max-w-md mx-auto space-y-6">
        <div class="text-center">
            <h1 class="text-3xl font-semibold text-white">Logowanie Administratora</h1>
            <p class="text-slate-400 mt-2">Zaloguj się, aby uzyskać dostęp do panelu administracyjnego</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-200 mb-2">
                    Adres e-mail <span class="text-rose-400">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none @error('email') border-rose-500/50 @enderror"
                    placeholder="admin@example.com"
                >
                @error('email')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-200 mb-2">
                    Hasło <span class="text-rose-400">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none @error('password') border-rose-500/50 @enderror"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="h-5 w-5 accent-indigo-500"
                >
                <label for="remember" class="text-sm text-slate-300">
                    Zapamiętaj mnie
                </label>
            </div>

            <button
                type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-4 text-base font-semibold text-white hover:bg-indigo-500 transition"
            >
                Zaloguj się
            </button>
        </form>

        <div class="text-center text-sm text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-white transition">
                ← Powrót do strony głównej
            </a>
        </div>
    </div>
@endsection

