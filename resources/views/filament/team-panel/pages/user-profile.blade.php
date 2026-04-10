<x-filament-panels::page>
    @php
        $user = $this->profileUser;
        $initials = collect(explode(' ', (string) $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
        $activeProjects = $user->projectMemberships()->with('project')->latest()->limit(6)->get();
        $assignedTasks = $user->assignedTasks()->with('project')->latest()->limit(8)->get();
    @endphp

    <div class="space-y-6">
        <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                <div class="flex items-start gap-4">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-2xl object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-cyan-100 text-2xl font-semibold text-cyan-700 ring-1 ring-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-200 dark:ring-cyan-800">
                            {{ $initials ?: '--' }}
                        </div>
                    @endif

                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $user->name }}</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->position ?: 'Cargo não informado' }} • {{ $user->department ?: 'Departamento não informado' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->city ?: 'Cidade não informada' }}</p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($user->roles as $role)
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-700 ring-1 ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-200">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm dark:border-gray-800 dark:bg-gray-950/50">
                    <p class="text-gray-600 dark:text-gray-300"><span class="font-medium">E-mail:</span> {{ $user->email }}</p>
                    <p class="text-gray-600 dark:text-gray-300"><span class="font-medium">Telefone:</span> {{ $user->phone ?: 'Não informado' }}</p>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 xl:col-span-2">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Sobre</h3>
                <p class="mt-2 whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $user->bio ?: 'Sem bio cadastrada.' }}</p>

                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">LinkedIn</h4>
                        @if ($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" target="_blank" class="mt-1 inline-block text-sm text-cyan-700 hover:underline dark:text-cyan-300">Abrir perfil</a>
                        @else
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Não informado</p>
                        @endif
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">GitHub</h4>
                        @if ($user->github_url)
                            <a href="{{ $user->github_url }}" target="_blank" class="mt-1 inline-block text-sm text-cyan-700 hover:underline dark:text-cyan-300">Abrir perfil</a>
                        @else
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Não informado</p>
                        @endif
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Portfólio</h4>
                        @if ($user->portfolio_url)
                            <a href="{{ $user->portfolio_url }}" target="_blank" class="mt-1 inline-block text-sm text-cyan-700 hover:underline dark:text-cyan-300">Abrir site</a>
                        @else
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Não informado</p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Cargos</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse ((array) $user->job_titles as $tag)
                            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 ring-1 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-200">{{ $tag }}</span>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sem cargos adicionais.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Skills</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse ((array) $user->skills as $skill)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-200">{{ $skill }}</span>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sem skills cadastradas.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Projetos Vinculados</h3>
                <div class="mt-3 space-y-2">
                    @forelse ($activeProjects as $membership)
                        <a href="{{ route('filament.admin.resources.projects.view', $membership->project_id) }}" class="block rounded-xl border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/60">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $membership->project?->code }} - {{ $membership->project?->name }}</span>
                            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ ucfirst((string) $membership->role) }})</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sem projetos vinculados.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Tarefas Recentes</h3>
                <div class="mt-3 space-y-2">
                    @forelse ($assignedTasks as $task)
                        <a href="{{ \App\Filament\TeamPanel\Clusters\Tasks\Pages\TaskList::getUrl(panel: 'admin') }}" class="block rounded-xl border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/60">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</span>
                            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $task->project?->code ? '• ' . $task->project->code : '' }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sem tarefas atribuídas.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-filament-panels::page>
