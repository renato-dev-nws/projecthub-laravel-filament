<?php

namespace App\Filament\TeamPanel\Widgets;

use App\Models\SupportTicket;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SupportOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Suporte Recente';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->hasPermissionTo('module.support')
            && $user->hasPermissionTo('support.view_any');
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        $query = SupportTicket::query()
            ->whereIn('status', ['open', 'in_progress', 'waiting_client'])
            ->with(['project', 'assignee'])
            ->orderByDesc('updated_at')
            ->limit(8);

        if ($user instanceof User && ! $user->hasAnyRole(['Super Admin', 'Admin', 'Account Manager'])) {
            if ($user->hasRole('Project Manager')) {
                $query->where('project_manager_id', $user->id);
            } else {
                $query->where(function (Builder $builder) use ($user) {
                    $builder
                        ->where('assigned_to', $user->id)
                        ->orWhereHas('project.members', fn (Builder $memberQuery) => $memberQuery->where('users.id', $user->id));
                });
            }
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Assunto')
                    ->limit(45),

                Tables\Columns\TextColumn::make('project.code')
                    ->label('Projeto')
                    ->default('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Aberto',
                        'in_progress' => 'Em andamento',
                        'waiting_client' => 'Aguardando cliente',
                        'resolved' => 'Resolvido',
                        'closed' => 'Fechado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Responsável')
                    ->default('Não atribuído'),
            ])
            ->paginated(false);
    }
}
