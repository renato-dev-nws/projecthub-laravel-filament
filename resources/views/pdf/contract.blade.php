<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Contrato {{ $project->code }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
    .page { padding: 30px 36px; }
    .title { font-size: 20px; color: #0e7490; font-weight: bold; margin-bottom: 8px; }
    .subtitle { color: #64748b; margin-bottom: 16px; }
    .block { margin-bottom: 16px; }
    .heading { background: #ecfeff; border-left: 4px solid #06b6d4; padding: 6px 10px; font-weight: bold; }
    .content { border: 1px solid #e2e8f0; border-top: none; padding: 10px; }
    .list { margin: 8px 0 0; padding-left: 18px; }
    .list li { margin-bottom: 4px; }
    .meta td { padding: 4px 0; }
</style>
</head>
<body>
<div class="page">
    <div class="title">CONTRATO DE PRESTAÇÃO DE SERVIÇOS</div>
    <div class="subtitle">Projeto {{ $project->code }} - {{ $project->name }}</div>

    <div class="block">
        <div class="heading">DADOS DO PROJETO</div>
        <div class="content">
            <table class="meta">
                <tr><td><strong>Cliente:</strong> {{ $project->client?->company_name }}</td></tr>
                <tr><td><strong>Gerente do Projeto:</strong> {{ $project->projectManager?->name }}</td></tr>
                <tr><td><strong>Período:</strong> {{ $project->start_date?->format('d/m/Y') }} até {{ $project->end_date?->format('d/m/Y') }}</td></tr>
            </table>
            @if($project->description)
                <p>{{ $project->description }}</p>
            @endif
        </div>
    </div>

    <div class="block">
        <div class="heading">FASES</div>
        <div class="content">
            <ul class="list">
                @foreach($project->phases as $phase)
                    <li>
                        <strong>{{ $phase->name }}</strong>
                        @if($phase->description) - {{ $phase->description }} @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="block">
        <div class="heading">ROADMAP</div>
        <div class="content">
            <ul class="list">
                @foreach($project->roadmapItems as $item)
                    <li>
                        <strong>{{ $item->title }}</strong>
                        @if($item->planned_date) - {{ $item->planned_date->format('d/m/Y') }} @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="block">
        <div class="heading">CLÁUSULAS</div>
        <div class="content">
            @forelse($clauses as $clause)
                <p><strong>{{ $clause->title }}</strong></p>
                <p>{{ $clause->content }}</p>
            @empty
                <p>Sem cláusulas cadastradas. Configure em Configurações > Cláusulas de Contrato.</p>
            @endforelse
        </div>
    </div>
</div>
</body>
</html>
