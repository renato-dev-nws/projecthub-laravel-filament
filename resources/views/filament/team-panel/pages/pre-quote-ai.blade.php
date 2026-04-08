<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Step 1: Input --}}
        <x-filament::section>
            <x-slot name="heading">Descreva o Projeto</x-slot>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Lead (opcional)</label>
                    <select wire:model="lead_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                        <option value="">— Sem lead —</option>
                        @foreach(\App\Models\Lead::whereNotIn('status', ['converted','lost'])->orderBy('name')->get() as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->name }} @if($lead->company)({{ $lead->company }})@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição do Projeto <span class="text-red-500">*</span></label>
                    <textarea wire:model="project_description" rows="6"
                        placeholder="Descreva o projeto em detalhes: funcionalidades, tecnologias, objetivos, público-alvo..."
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500"></textarea>
                    @error('project_description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <x-filament::button wire:click="analyze" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="analyze">✨ Analisar com IA</span>
                    <span wire:loading wire:target="analyze">Analisando...</span>
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Step 2: AI Result --}}
        @if($aiResult)
        <x-filament::section>
            <x-slot name="heading">Resultado da Análise</x-slot>
            @if($aiResult['project_summary'] ?? null)
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 italic">{{ $aiResult['project_summary'] }}</p>
            @endif

            @foreach($aiResult['phases'] ?? [] as $phase)
            <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex justify-between items-center">
                    <span class="font-semibold text-sm">{{ $phase['name'] }}</span>
                    @if($phase['estimated_days'] ?? null)
                    <span class="text-xs text-gray-500">{{ $phase['estimated_days'] }} dias</span>
                    @endif
                </div>
                @if($phase['description'] ?? null)
                <p class="px-4 py-2 text-xs text-gray-500 italic">{{ $phase['description'] }}</p>
                @endif
                <table class="w-full text-sm">
                    <thead class="bg-primary-50 dark:bg-primary-900/20 text-xs">
                        <tr>
                            <th class="text-left px-4 py-2">Serviço / Descrição</th>
                            <th class="text-right px-4 py-2">Horas</th>
                            <th class="text-right px-4 py-2">R$/h</th>
                            <th class="text-right px-4 py-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($phase['items'] ?? [] as $item)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $item['description'] }}</td>
                            <td class="text-right px-4 py-2">{{ $item['hours'] }}h</td>
                            <td class="text-right px-4 py-2">R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</td>
                            <td class="text-right px-4 py-2">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600 font-semibold">
                            <td colspan="3" class="text-right px-4 py-2 text-xs uppercase tracking-wide">Subtotal da fase:</td>
                            <td class="text-right px-4 py-2">R$ {{ number_format($phase['subtotal'], 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endforeach

            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="font-bold text-lg">Total estimado:</span>
                <span class="font-bold text-xl text-primary-600">R$ {{ number_format($aiResult['total'] ?? 0, 2, ',', '.') }}</span>
            </div>

            @if(!$generatedQuoteId)
            <div class="mt-4">
                <x-filament::button wire:click="generateQuote" color="success">
                    Gerar Orçamento
                </x-filament::button>
            </div>
            @endif
        </x-filament::section>
        @endif

        {{-- Step 3: Generated --}}
        @if($generatedQuoteId)
        <x-filament::section>
            <div class="flex items-center gap-3 text-success-600">
                <span class="text-2xl">✅</span>
                <div>
                    <p class="font-semibold">Orçamento gerado com sucesso!</p>
                    <a href="{{ route('filament.admin.resources.quotes.edit', $generatedQuoteId) }}"
                       class="text-sm text-primary-600 underline">
                        Abrir orçamento →
                    </a>
                </div>
            </div>
        </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>
