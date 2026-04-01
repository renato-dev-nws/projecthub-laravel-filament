<?php

namespace App\Filament\ClientPanel\Resources\Projects;

use App\Filament\ClientPanel\Resources\Projects\Pages\CreateProject;
use App\Filament\ClientPanel\Resources\Projects\Pages\EditProject;
use App\Filament\ClientPanel\Resources\Projects\Pages\ListProjects;
use App\Filament\ClientPanel\Resources\Projects\Schemas\ProjectForm;
use App\Filament\ClientPanel\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Meus Projetos';

    public static function getEloquentQuery(): Builder
    {
        $clientId = auth('client_portal')->user()?->client_id;

        return parent::getEloquentQuery()
            ->where('client_id', $clientId)
            ->where('client_portal_enabled', true);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
        ];
    }
}
