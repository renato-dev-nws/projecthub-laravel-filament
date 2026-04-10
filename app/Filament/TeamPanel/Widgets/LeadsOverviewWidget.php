<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\Lead;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LeadsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Leads em Andamento';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->hasPermissionTo('module.crm')
            && $user->hasPermissionTo('leads.view_any');
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        $query = Lead::query()
            ->whereNotIn('status', ['converted', 'lost'])
            ->with('assignedTo')
            ->orderByDesc('updated_at')
            ->limit(8);

        if ($user instanceof User && $user->hasRole('Account Manager')) {
            $query->where('assigned_to', $user->id);
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Lead')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('company')
                    ->label('Empresa')
                    ->default('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Novo',
                        'contacted' => 'Contactado',
                        'qualified' => 'Qualificado',
                        'proposal_sent' => 'Proposta Enviada',
                        'negotiation' => 'Negociação',
                        'converted' => 'Convertido',
                        'lost' => 'Perdido',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Responsável')
                    ->default('Não atribuído'),
            ])
            ->paginated(false);
    }
}
