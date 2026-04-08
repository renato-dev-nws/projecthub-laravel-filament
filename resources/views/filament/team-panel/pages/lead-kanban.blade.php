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

            <div
                class="w-72 flex-shrink-0 rounded-xl bg-gray-100 p-3 flex flex-col"
                @dragover.prevent
                @drop="onDrop('{{ $status }}')"
            >
                {{-- Column Header --}}
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">{{ $board['label'] }}</span>
                    <span class="rounded-full bg-gray-300 px-2 py-0.5 text-xs font-medium text-gray-600">
                        {{ count($board['leads']) }}
                    </span>
                </div>

                {{-- Cards --}}
                <div class="flex flex-col flex-1 min-h-[8rem]">
                    @forelse ($board['leads'] as $lead)
                        @php
                            $priorityColors = [
                                'low'    => 'bg-gray-100 text-gray-600',
                                'medium' => 'bg-yellow-100 text-yellow-700',
                                'high'   => 'bg-red-100 text-red-700',
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
                            class="mb-2 rounded-lg bg-white p-3 shadow-sm border border-gray-200 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow"
                        >
                            {{-- Lead Name --}}
                            <a
                                href="{{ \App\Filament\TeamPanel\Resources\Leads\LeadResource::getUrl('edit', ['record' => $lead['id']]) }}"
                                class="text-sm font-medium text-gray-900 hover:text-primary-600 line-clamp-1"
                                @click.stop
                            >
                                {{ $lead['name'] }}
                            </a>

                            {{-- Company --}}
                            @if (!empty($lead['company']))
                                <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                    {{ $lead['company'] }}
                                </p>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center justify-between mt-2">
                                <span class="inline-flex items-center text-xs font-medium px-1.5 py-0.5 rounded {{ $priorityColor }}">
                                    {{ $priorityLabel }}
                                </span>

                                @if (!empty($lead['estimated_value']))
                                    <span class="text-xs text-gray-500">
                                        R$ {{ number_format($lead['estimated_value'], 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center text-xs text-gray-400">
                            Nenhum lead
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
