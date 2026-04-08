<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Orçamento {{ $quote->number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }
    .page { padding: 30px 40px; }
    .header { display: table; width: 100%; margin-bottom: 24px; border-bottom: 2px solid #0891b2; padding-bottom: 16px; }
    .header-left { display: table-cell; vertical-align: middle; width: 50%; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; }
    .header-right h1 { font-size: 20px; font-weight: bold; color: #0891b2; }
    .header-right p { color: #6b7280; font-size: 10px; margin-top: 2px; }
    .logo { height: 40px; }
    .section { margin-bottom: 20px; }
    .section-title { font-size: 12px; font-weight: bold; background: #f0f9ff; color: #0369a1; padding: 6px 10px; border-left: 4px solid #0891b2; margin-bottom: 8px; }
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table td { padding: 4px 8px; }
    .info-table td:first-child { font-weight: bold; color: #374151; width: 140px; }
    .phase-block { margin-bottom: 16px; border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden; }
    .phase-header { background: #f8fafc; padding: 8px 12px; font-weight: bold; font-size: 11px; border-bottom: 1px solid #e5e7eb; }
    .phase-desc { padding: 4px 12px 8px; color: #6b7280; font-size: 10px; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th { background: #e0f2fe; color: #0369a1; padding: 5px 8px; text-align: left; font-size: 10px; }
    .items-table td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
    .items-table tr:last-child td { border-bottom: none; }
    .phase-subtotal { background: #f0f9ff; font-weight: bold; }
    .text-right { text-align: right; }
    .totals { width: 280px; margin-left: auto; border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden; }
    .totals table { width: 100%; border-collapse: collapse; }
    .totals td { padding: 5px 12px; font-size: 11px; }
    .totals td:last-child { text-align: right; font-weight: bold; }
    .totals tr.total-row td { background: #0891b2; color: #fff; font-size: 13px; font-weight: bold; }
    .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 9px; text-align: center; }
</style>
</head>
<body>
<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('img/icon.svg') }}" class="logo" alt="Logo">
        </div>
        <div class="header-right">
            <h1>ORÇAMENTO #{{ $quote->number }}</h1>
            <p>{{ $quote->title }}</p>
            <p>Criado em: {{ $quote->created_at->format('d/m/Y') }}</p>
            @if($quote->valid_until)
            <p>Válido até: {{ $quote->valid_until->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    <!-- Client / Lead -->
    @if($quote->client)
    <div class="section">
        <div class="section-title">CLIENTE</div>
        <table class="info-table">
            <tr><td>Empresa:</td><td>{{ $quote->client->company_name }}</td></tr>
            @if($quote->client->email)<tr><td>E-mail:</td><td>{{ $quote->client->email }}</td></tr>@endif
            @if($quote->client->phone)<tr><td>Telefone:</td><td>{{ $quote->client->phone }}</td></tr>@endif
        </table>
    </div>
    @elseif($quote->lead)
    <div class="section">
        <div class="section-title">LEAD</div>
        <table class="info-table">
            <tr><td>Nome:</td><td>{{ $quote->lead->name }}</td></tr>
            @if($quote->lead->company)<tr><td>Empresa:</td><td>{{ $quote->lead->company }}</td></tr>@endif
            @if($quote->lead->email)<tr><td>E-mail:</td><td>{{ $quote->lead->email }}</td></tr>@endif
        </table>
    </div>
    @endif

    <!-- Phases -->
    @if($quote->phases->count())
    <div class="section">
        <div class="section-title">FASES E SERVIÇOS</div>
        @foreach($quote->phases as $phase)
        <div class="phase-block">
            <div class="phase-header">
                {{ $phase->name }}
                @if($phase->estimated_days) — {{ $phase->estimated_days }} dias @endif
                @if($phase->deadline_date) — Prazo: {{ $phase->deadline_date->format('d/m/Y') }} @endif
            </div>
            @if($phase->description)<div class="phase-desc">{{ $phase->description }}</div>@endif
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Serviço / Descrição</th>
                        <th class="text-right">Horas</th>
                        <th class="text-right">R$/hora</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phase->items as $item)
                    <tr>
                        <td>{{ $item->description }}@if($item->service) <span style="color:#6b7280">({{ $item->service->name }})</span>@endif</td>
                        <td class="text-right">{{ number_format($item->hours ?? $item->quantity, 1) }}h</td>
                        <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="phase-subtotal">
                        <td colspan="3" class="text-right">Subtotal da fase:</td>
                        <td class="text-right">R$ {{ number_format($phase->subtotal, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr><td>Subtotal:</td><td>R$ {{ number_format($quote->subtotal, 2, ',', '.') }}</td></tr>
            @if($quote->discount_value > 0)
            <tr><td>Desconto:</td><td>- R$ {{ number_format($quote->discount_value, 2, ',', '.') }}</td></tr>
            @endif
            @if($quote->tax_value > 0)
            <tr><td>Impostos:</td><td>+ R$ {{ number_format($quote->tax_value, 2, ',', '.') }}</td></tr>
            @endif
            <tr class="total-row"><td>TOTAL:</td><td>R$ {{ number_format($quote->total, 2, ',', '.') }}</td></tr>
        </table>
    </div>

    @if($quote->terms_conditions)
    <div class="section" style="margin-top:20px">
        <div class="section-title">TERMOS E CONDIÇÕES</div>
        <p style="font-size:10px;color:#4b5563;line-height:1.5">{{ $quote->terms_conditions }}</p>
    </div>
    @endif

    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i') }} &bull; Criado por: {{ $quote->creator?->name }}
    </div>
</div>
</body>
</html>
