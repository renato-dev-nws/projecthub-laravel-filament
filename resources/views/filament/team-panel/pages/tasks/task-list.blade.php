<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Projeto</label>
                <select wire:model.live="projectId" class="fi-select-input block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-950">
                    <option value="">Todos os projetos</option>
                    @foreach ($this->projectOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if ($this->canSeeTeamScope())
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Escopo</label>
                    <select wire:model.live="scope" class="fi-select-input block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-950">
                        <option value="mine">Minhas tarefas</option>
                        <option value="team">Tarefas da equipe</option>
                    </select>
                </div>
            @endif

            @if ($this->canFilterMembers())
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Membro da equipe</label>
                    <select wire:model.live="memberId" class="fi-select-input block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-950">
                        <option value="">Todos os membros</option>
                        @foreach ($this->memberOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tarefa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Projeto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Responsável</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Prioridade</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Prazo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($this->tasks as $task)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/50">
                                <td class="px-4 py-4">
                                    <button wire:click="openTask({{ $task['id'] }})" class="text-left">
                                        <span class="block font-medium text-gray-900 dark:text-gray-100">{{ $task['title'] }}</span>
                                        @if ($task['phase_name'])
                                            <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">Fase: {{ $task['phase_name'] }}</span>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <span class="block font-medium">{{ $task['project_code'] }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $task['project_name'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $task['assignee_name'] ?? 'Não atribuída' }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset
                                        @switch($task['priority_color'])
                                            @case('warning') bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-200 @break
                                            @case('danger') bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-200 @break
                                            @default bg-gray-100 text-gray-700 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-200
                                        @endswitch
                                    ">{{ $task['priority_label'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $task['due_date'] ?? 'Sem prazo' }}</td>
                                <td class="px-4 py-4">
                                    @if ($this->canChangeStatus($task))
                                        <select
                                            x-data
                                            @change="$wire.updateTaskStatus({{ $task['id'] }}, $event.target.value)"
                                            class="fi-select-input block w-full rounded-xl border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-950"
                                        >
                                            @foreach ($this->statusOptions() as $value => $label)
                                                <option value="{{ $value }}" @selected($task['status'] === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset
                                            @switch($task['status_color'])
                                                @case('info') bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-200 @break
                                                @case('warning') bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-200 @break
                                                @case('success') bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-200 @break
                                                @case('danger') bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-200 @break
                                                @default bg-gray-100 text-gray-700 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-200
                                            @endswitch
                                        ">{{ $task['status_label'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma tarefa encontrada para os filtros selecionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-data="{ open: @entangle('isTaskModalOpen') }" x-show="open" x-cloak class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-gray-950/60" @click="$wire.closeTask()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-3xl rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900">
                @if ($this->selectedTask)
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $this->selectedTask['title'] }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->selectedTask['project_code'] }} - {{ $this->selectedTask['project_name'] }}</p>
                        </div>
                        <button wire:click="closeTask" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">Fechar</button>
                    </div>

                    <div class="grid gap-6 px-6 py-5 md:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->selectedTask['status_label'] }}</p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Prioridade</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->selectedTask['priority_label'] }}</p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Responsável</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->selectedTask['assignee_name'] ?? 'Não atribuída' }}</p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Prazo</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->selectedTask['due_date'] ?? 'Sem prazo' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Fase</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->selectedTask['phase_name'] ?? 'Sem fase' }}</p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Horas Estimadas</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->selectedTask['estimated_hours'] ?? '0' }}h</p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Horas Lançadas</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $this->selectedTask['logged_hours'] ?? '0' }}h</p>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Descrição</h3>
                            <p class="mt-1 whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $this->selectedTask['description'] ?: 'Sem descrição.' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>