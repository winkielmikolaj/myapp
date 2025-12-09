@extends('layouts.admin', ['title' => 'Dodaj nowy quiz'])

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-semibold text-white">Dodaj nowy quiz</h1>
            <p class="text-slate-400 mt-1">Wypełnij formularz, aby utworzyć nowy quiz</p>
        </div>

        <form action="{{ route('admin.quizzes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-200 mb-2">
                        Tytuł quizu <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        placeholder="Np. Quiz o historii Polski"
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-200 mb-2">
                        Opis quizu <span class="text-rose-400">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                        class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        placeholder="Opisz quiz, jego tematykę i cel..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="time_limit" class="block text-sm font-semibold text-slate-200 mb-2">
                        Limit czasu (w minutach)
                    </label>
                    <input
                        type="number"
                        id="time_limit"
                        name="time_limit"
                        value="{{ old('time_limit') }}"
                        min="1"
                        max="300"
                        class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-base text-white placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none"
                        placeholder="Opcjonalnie - pozostaw puste, jeśli bez limitu"
                    >
                    @error('time_limit')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-5 w-5 accent-indigo-500"
                    >
                    <label for="is_active" class="text-sm font-semibold text-slate-200">
                        Quiz aktywny (widoczny dla użytkowników)
                    </label>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-base font-semibold text-white hover:bg-white/10 transition">
                    Anuluj
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    Utwórz quiz
                </button>
            </div>
        </form>
    </div>
@endsection

