<?php

namespace App\Filament\TeamPanel\Pages;

use App\Filament\TeamPanel\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class UserProfile extends Page
{
    protected string $view = 'filament.team-panel.pages.user-profile';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Perfil do Colaborador';

    public User $profileUser;

    public function mount(): void
    {
        $userId = request()->integer('user');
        $target = User::query()->findOrFail($userId);

        $viewer = Auth::user();

        abort_unless($viewer instanceof User && $viewer->can('view', $target), 403);

        $this->profileUser = $target;
    }

    public function getHeading(): string
    {
        return (string) $this->profileUser->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editar')
                ->label('Editar usuário')
                ->icon('heroicon-o-pencil-square')
                ->url(fn () => UserResource::getUrl('edit', ['record' => $this->profileUser]))
                ->visible(function (): bool {
                    $viewer = Auth::user();

                    return $viewer instanceof User && $viewer->can('update', $this->profileUser);
                }),
        ];
    }
}
