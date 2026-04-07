<?php

namespace App\Filament\ClientPanel\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ClientProjectsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Meus Projetos';

    public function table(Table $table): Table
    {
        $clientId = auth('client_portal')->user()?->client_id;

        return $table
            ->query(
                Project::query()
                    ->where('client_id', $clientId)
                    ->where('client_portal_enabled', true)
                    ->with(['projectManager', 'phases', 'tasks'])
                    ->orderByDesc('updated_at')
            )
            ->columns([
                Tables\Columns\ColorColumn::make('color')
                    ->label('')
                    ->width('4px'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Projeto')
                    ->weight('semibold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'    => 'success',
                        'planning'  => 'warning',
                        'on_hold'   => 'gray',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active'    => 'Ativo',
                        'planning'  => 'Planejamento',
                        'on_hold'   => 'Em Pausa',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('projectManager.name')
                    ->label('Gerente')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->suffix('%')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 40 => 'warning',
                        default      => 'primary',
                    }),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Prazo')
                    ->date('d/m/Y'),
            ])
            ->paginated(false);
    }
}
