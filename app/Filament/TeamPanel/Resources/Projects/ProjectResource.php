<?php

namespace App\Filament\TeamPanel\Resources\Projects;

use App\Filament\TeamPanel\Resources\Projects\Pages\CreateProject;
use App\Filament\TeamPanel\Resources\Projects\Pages\EditProject;
use App\Filament\TeamPanel\Resources\Projects\Pages\ListProjects;
use App\Filament\TeamPanel\Resources\Projects\Schemas\ProjectForm;
use App\Filament\TeamPanel\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use BackedEnum;
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
            'edit'   => EditProject::route('/{record}/edit'),
        ];
    }
}
