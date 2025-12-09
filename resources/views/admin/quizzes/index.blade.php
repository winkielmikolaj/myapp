@extends('layouts.admin', ['title' => 'Zarządzanie Quizami'])

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-white">Zarządzanie Quizami</h1>
                <p class="text-slate-400 mt-1">Lista wszystkich quizów w systemie</p>
            </div>
            <a href="{{ route('admin.quizzes.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Dodaj nowy quiz
            </a>
        </div>

        @if($quizzes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="pb-4 text-sm font-semibold text-slate-300">Tytuł</th>
                            <th class="pb-4 text-sm font-semibold text-slate-300">Pytania</th>
                            <th class="pb-4 text-sm font-semibold text-slate-300">Punkty</th>
                            <th class="pb-4 text-sm font-semibold text-slate-300">Status</th>
                            <th class="pb-4 text-sm font-semibold text-slate-300">Utworzono</th>
                            <th class="pb-4 text-sm font-semibold text-slate-300 text-right">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($quizzes as $quiz)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-4">
                                    <a href="{{ route('admin.quizzes.show', $quiz) }}" class="font-medium text-white hover:text-indigo-400">
                                        {{ $quiz->title }}
                                    </a>
                                    <p class="text-sm text-slate-400 mt-1 line-clamp-1">{{ \Illuminate\Support\Str::limit($quiz->description, 60) }}</p>
                                </td>
                                <td class="py-4 text-slate-300">{{ $quiz->questions_count }}</td>
                                <td class="py-4 text-slate-300">{{ $quiz->total_points ?? 0 }}</td>
                                <td class="py-4">
                                    @if($quiz->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300">
                                            Aktywny
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-500/20 text-slate-300">
                                            Nieaktywny
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 text-sm text-slate-400">{{ $quiz->created_at->format('d.m.Y H:i') }}</td>
                                <td class="py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="p-2 text-slate-400 hover:text-white transition" title="Szczegóły">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="p-2 text-slate-400 hover:text-indigo-400 transition" title="Edytuj">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="inline" onsubmit="return confirm('Czy na pewno chcesz usunąć ten quiz? Ta operacja jest nieodwracalna.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 transition" title="Usuń">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $quizzes->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-slate-400 mb-4">Brak quizów w systemie.</p>
                <a href="{{ route('admin.quizzes.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500/90 px-6 py-3 text-base font-semibold text-white hover:bg-indigo-500 transition">
                    Dodaj pierwszy quiz
                </a>
            </div>
        @endif
    </div>
@endsection

