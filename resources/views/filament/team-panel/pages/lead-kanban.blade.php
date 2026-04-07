<x-filament-panels::page>
    <div
        x-data="{
            draggingId: null,
            draggingFrom: null,
            isDragging: false,

            onDragStart(leadId, fromStatus) {
                this.draggingId   = leadId;
                this.draggingFrom = fromStatus;
                this.isDragging   = true;
            },

            onDrop(toStatus) {
                if (this.draggingId && this.draggingFrom !== toStatus) {
                    $wire.dispatch('lead-moved', {
                        leadId:    this.draggingId,
                        newStatus: toStatus,
                    });
                }
                this.isDragging   = false;
                this.draggingId   = null;
                this.draggingFrom = null;
            },
        }"
        class="flex gap-4 overflow-x-auto pb-4"
        style="min-height: 70vh;"
    >
        @foreach ($boards as $status => $board)
            @php
                $columnColors = [
                    'new'           => 'border-gray-400',
                    'contacted'     => 'border-blue-400',
                    'qualified'     => 'border-cyan-400',
                    'proposal_sent' => 'border-yellow-400',
                    'negotiation'   => 'border-orange-400',
                    'converted'     => 'border-green-500',
                    'lost'          => 'border-red-400',
                ];
                $headerColors = [
                    'new'           => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300',
                    'contacted'     => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                    'qualified'     => 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300',
                    'proposal_sent' => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                    'negotiation'   => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
                    'converted'     => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                    'lost'          => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                ];
                $borderColor  = $columnColors[$status]  ?? 'border-gray-300';
                $headerColor  = $headerColors[$status]   ?? 'bg-gray-100 text-gray-700';
            @endphp

            <div
                class="flex-none w-72 rounded-xl border-t-4 {{ $borderColor }} bg-white dark:bg-gray-900 shadow-sm flex flex-col"
                @dragover.prevent
                @drop="onDrop('{{ $status }}')"
            >
                {{-- Column Header --}}
                <div class="flex items-center justify-between px-4 py-3 rounded-t-lg {{ $headerColor }}">
                    <span class="font-semibold text-sm">{{ $board['label'] }}</span>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-white/60 dark:bg-black/20">
                        {{ count($board['leads']) }}
                    </span>
                </div>

                {{-- Cards --}}
                <div class="flex flex-col gap-2 p-3 flex-1 min-h-[8rem]">
                    @forelse ($board['leads'] as $lead)
                        @php
                            $priorityColors = [
                                'low'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                                'high'   => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            ];
                            $priorityLabels = [
                                'low'    => 'Baixa',
                                'medium' => 'Média',
                                'high'   => 'Alta',
                            ];
                            $priorityColor = $priorityColors[$lead['priority']] ?? $priorityColors['medium'];
                            $priorityLabel = $priorityLabels[$lead['priority']] ?? $lead['priority'];
                        @endphp

                        <div
                            draggable="true"
                            @dragstart="onDragStart({{ $lead['id'] }}, '{{ $status }}')"
                            @dragend="isDragging = false"
                            class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700
                                   cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow"
                        >
                            {{-- Lead Name --}}
                            <a
                                href="{{ \App\Filament\TeamPanel\Resources\Leads\LeadResource::getUrl('edit', ['record' => $lead['id']]) }}"
                                class="font-medium text-sm text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 line-clamp-1"
                                @click.stop
                            >
                                {{ $lead['name'] }}
                            </a>

                            {{-- Company --}}
                            @if (!empty($lead['company']))
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">
                                    {{ $lead['company'] }}
                                </p>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center justify-between mt-2">
                                <span class="inline-flex items-center text-xs font-medium px-1.5 py-0.5 rounded {{ $priorityColor }}">
                                    {{ $priorityLabel }}
                                </span>

                                @if (!empty($lead['estimated_value']))
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        R$ {{ number_format($lead['estimated_value'], 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex items-center justify-center h-16 text-xs text-gray-400 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                            Nenhum lead
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
