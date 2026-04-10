<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($members as $member)
        @php
            $user = $member->user;
            $initials = collect(explode(' ', (string) $user?->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
            $profileUrl = $user ? \App\Filament\TeamPanel\Pages\UserProfile::getUrl(['user' => $user->id], panel: 'admin') : null;
        @endphp

        <a
            href="{{ $profileUrl }}"
            class="group rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
        >
            <div class="flex items-start gap-3">
                @if ($user?->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                @else
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-100 text-sm font-semibold text-cyan-700 ring-1 ring-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-200 dark:ring-cyan-800">
                        {{ $initials ?: '--' }}
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-gray-900 group-hover:text-cyan-700 dark:text-gray-100 dark:group-hover:text-cyan-300">
                        {{ $user?->name ?? 'Membro removido' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $user?->position ?: 'Cargo não informado' }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Papel no projeto: <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst((string) $member->role) }}</span>
                    </p>
                </div>
            </div>

            @if (! empty($user?->job_titles))
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach (array_slice((array) $user->job_titles, 0, 3) as $tag)
                        <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-medium text-sky-700 ring-1 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-200">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            @if (! empty($user?->skills))
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @foreach (array_slice((array) $user->skills, 0, 4) as $skill)
                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-200">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            @endif
        </a>
    @empty
        <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-400">
            Nenhum membro vinculado a este projeto.
        </div>
    @endforelse
</div>
