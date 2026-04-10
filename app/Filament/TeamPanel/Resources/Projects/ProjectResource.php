<?php

namespace App\Filament\TeamPanel\Resources\Projects;

use App\Filament\TeamPanel\Resources\Projects\Pages\CreateProject;
use App\Filament\TeamPanel\Resources\Projects\Pages\EditProject;
use App\Filament\TeamPanel\Resources\Projects\Pages\ListProjects;
use App\Filament\TeamPanel\Resources\Projects\Pages\ViewProject;
use App\Filament\TeamPanel\Resources\Projects\Schemas\ProjectForm;
use App\Filament\TeamPanel\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use App\Models\User;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string | UnitEnum | null $navigationGroup = 'Projetos';

    protected static ?string $navigationLabel = 'Projetos';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Projeto';

    protected static ?string $pluralModelLabel = 'Projetos';

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['client', 'projectManager']);

        $user = Auth::user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Financial']) || $user->hasPermissionTo('projects.view_all')) {
            return $query;
        }

        if ($user->hasRole('Project Manager')) {
            return $query->where('project_manager_id', $user->id);
        }

        return $query->whereHas('members', fn (Builder $builder) => $builder->where('users.id', $user->id));
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PhasesRelationManager::class,
            RelationManagers\TasksRelationManager::class,
            RelationManagers\RoadmapItemsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\TimeLogsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view'   => ViewProject::route('/{record}'),
            'edit'   => EditProject::route('/{record}/edit'),
        ];
    }
}
