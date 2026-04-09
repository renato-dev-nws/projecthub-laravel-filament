<x-filament-panels::page>
    <style>
        .dark .task-board-column {
            background-color: rgb(31 41 55 / 0.72) !important;
        }

        .dark .task-board-empty {
            border-color: rgb(75 85 99) !important;
            color: rgb(156 163 175) !important;
        }
    </style>

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
                    <select wire:model.live="scope" class="block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-950">
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

        <div
            x-data="{
                draggingId: null,
                draggingAllowed: false,

                onDragStart(taskId, canMove) {
                    if (! canMove) {
                        this.draggingId = null;
                        this.draggingAllowed = false;
                        return;
                    }

                    this.draggingId = taskId;
                    this.draggingAllowed = true;
                },

                onDrop(toStatus) {
                    if (this.draggingId && this.draggingAllowed) {
                        $wire.moveTask(this.draggingId, toStatus)
                    }

                    this.draggingId = null;
                    this.draggingAllowed = false;
                },
            }"
            class="flex gap-4 overflow-x-auto pb-4"
            style="min-height: 70vh;"
        >
            @foreach ($this->boards as $status => $board)
                <div class="task-board-column w-80 flex-shrink-0 rounded-2xl bg-gray-100 p-3 dark:bg-gray-800/70" @dragover.prevent @drop="onDrop('{{ $status }}')">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $board['label'] }}</span>
                        <span class="rounded-full bg-gray-300 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ count($board['tasks']) }}</span>
                    </div>

                    <div class="flex min-h-[10rem] flex-col">
                        @forelse ($board['tasks'] as $task)
                            @php $canMove = $this->canChangeStatus($task); @endphp
                            <div
                                draggable="{{ $canMove ? 'true' : 'false' }}"
                                @dragstart="onDragStart({{ $task['id'] }}, {{ $canMove ? 'true' : 'false' }})"
                                class="mb-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
                            >
                                <button wire:click="openTask({{ $task['id'] }})" class="text-left">
                                    <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $task['title'] }}</span>
                                    <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">{{ $task['project_code'] }} - {{ $task['project_name'] }}</span>
                                </button>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset
                                        @switch($task['priority_color'])
                                            @case('warning') bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-200 @break
                                            @case('danger') bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-200 @break
                                            @default bg-gray-100 text-gray-700 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-200
                                        @endswitch
                                    ">{{ $task['priority_label'] }}</span>
                                    @if ($task['assignee_name'])
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-500/20 dark:bg-slate-800 dark:text-slate-200">{{ $task['assignee_name'] }}</span>
                                    @endif
                                </div>

                                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    Prazo: {{ $task['due_date'] ?? 'Sem prazo' }}
                                </div>
                            </div>
                        @empty
                            <div class="task-board-empty rounded-xl border-2 border-dashed border-gray-300 p-4 text-center text-xs text-gray-400 dark:border-gray-600 dark:text-gray-500">
                                Nenhuma tarefa
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
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