<x-filament-panels::page>
<style>
    .dark .prequote-summary {
        background-color: rgb(30 41 59 / 0.6) !important;
        border-color: rgb(51 65 85) !important;
        color: rgb(203 213 225) !important;
    }
    .dark .prequote-phase-wrap {
        border-color: rgb(51 65 85) !important;
    }
    .dark .prequote-phase-header {
        background-color: rgb(30 41 59 / 0.8) !important;
        border-color: rgb(51 65 85) !important;
    }
    .dark .prequote-phase-desc {
        border-color: rgb(51 65 85 / 0.5) !important;
        color: rgb(148 163 184) !important;
    }
    .dark .prequote-thead {
        background-color: rgb(15 23 42 / 0.7) !important;
        border-color: rgb(51 65 85) !important;
    }
    .dark .prequote-th {
        color: rgb(148 163 184) !important;
    }
    .dark .prequote-tr {
        border-color: rgb(51 65 85) !important;
    }
    .dark .prequote-tr:hover {
        background-color: rgb(30 41 59 / 0.4) !important;
    }
    .dark .prequote-td-main {
        color: rgb(226 232 240) !important;
    }
    .dark .prequote-td-sub {
        color: rgb(148 163 184) !important;
    }
    .dark .prequote-tr-total {
        background-color: rgb(15 23 42 / 0.5) !important;
        border-color: rgb(51 65 85) !important;
    }
    .dark .prequote-tr-total td {
        color: rgb(203 213 225) !important;
    }
    .dark .prequote-grand-total {
        border-color: rgb(51 65 85) !important;
    }
    .dark .prequote-grand-label {
        color: rgb(226 232 240) !important;
    }
    .dark .prequote-success-box {
        background-color: rgb(20 83 45 / 0.3) !important;
        border-color: rgb(22 101 52) !important;
    }
    .dark .prequote-success-text {
        color: rgb(134 239 172) !important;
    }
    .dark .prequote-success-link {
        color: rgb(74 222 128) !important;
    }
    .dark label.prequote-label {
        color: rgb(148 163 184) !important;
    }
</style>

    <div class="mx-auto w-full px-4 py-6 space-y-6 lg:min-w-[70vw] lg:max-w-[85vw]">

        {{-- Step 1: Input --}}
        <x-filament::section>
            <x-slot name="heading">Descreva o Projeto</x-slot>
            <div class="space-y-4">
                <div>
                    <label class="prequote-label block text-sm font-medium text-gray-700 mb-1">Lead (opcional)</label>
                    <select wire:model="lead_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                        <option value="">— Sem lead —</option>
                        @foreach(\App\Models\Lead::whereNotIn('status', ['converted','lost'])->orderBy('name')->get() as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->name }} @if($lead->company)({{ $lead->company }})@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="prequote-label block text-sm font-medium text-gray-700 mb-1">Descrição do Projeto <span class="text-red-500">*</span></label>
                    <textarea wire:model="project_description" rows="6"
                        placeholder="Descreva o projeto em detalhes: funcionalidades, tecnologias, objetivos, público-alvo..."
                        class="w-full min-h-[140px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400"></textarea>
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
            <div class="prequote-summary rounded-lg bg-gray-50 border border-gray-200 p-4 text-sm text-gray-800 mb-4 italic">
                {{ $aiResult['project_summary'] }}
            </div>
            @endif

            @foreach($aiResult['phases'] ?? [] as $phase)
            <div class="prequote-phase-wrap mb-6 rounded-xl border border-gray-200 overflow-hidden">
                <div class="prequote-phase-header bg-gray-50 px-4 py-3 flex justify-between items-center border-b border-gray-200">
                    <span class="prequote-td-main font-semibold text-sm text-gray-900">{{ $phase['name'] }}</span>
                    @if($phase['estimated_days'] ?? null)
                    <span class="prequote-td-sub text-xs text-gray-500">{{ $phase['estimated_days'] }} dias</span>
                    @endif
                </div>
                @if($phase['description'] ?? null)
                <p class="prequote-phase-desc px-4 py-2 text-xs text-gray-500 italic border-b border-gray-100">{{ $phase['description'] }}</p>
                @endif
                <table class="w-full text-sm">
                    <thead class="prequote-thead bg-gray-50 text-xs border-b border-gray-200">
                        <tr>
                            <th class="prequote-th text-left px-4 py-2 text-gray-700 font-medium">Serviço / Descrição</th>
                            <th class="prequote-th text-right px-4 py-2 text-gray-700 font-medium">Horas</th>
                            <th class="prequote-th text-right px-4 py-2 text-gray-700 font-medium">R$/h</th>
                            <th class="prequote-th text-right px-4 py-2 text-gray-700 font-medium">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($phase['items'] ?? [] as $item)
                        <tr class="prequote-tr border-b border-gray-200">
                            <td class="prequote-td-main px-4 py-2 text-gray-800">{{ $item['description'] }}</td>
                            <td class="prequote-td-sub text-right px-4 py-2 text-gray-700">{{ $item['hours'] }}h</td>
                            <td class="prequote-td-sub text-right px-4 py-2 text-gray-700">R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</td>
                            <td class="prequote-td-sub text-right px-4 py-2 text-gray-700">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="prequote-tr-total bg-gray-50 border-t border-gray-200 font-semibold">
                            <td colspan="3" class="text-right px-4 py-2 text-xs uppercase tracking-wide text-gray-600">Subtotal da fase:</td>
                            <td class="text-right px-4 py-2 text-gray-800">R$ {{ number_format($phase['subtotal'], 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endforeach

            <div class="prequote-grand-total flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                <span class="prequote-grand-label font-bold text-lg text-gray-900">Total estimado:</span>
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
            <div class="prequote-success-box rounded-lg bg-green-50 border border-green-200 p-4 flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <div>
                    <p class="prequote-success-text font-semibold text-green-800">Orçamento gerado com sucesso!</p>
                    <a href="{{ route('filament.admin.resources.quotes.edit', $generatedQuoteId) }}"
                       class="prequote-success-link text-sm font-medium text-green-600 hover:text-green-700 underline">
                        Abrir orçamento →
                    </a>
                </div>
            </div>
        </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>
