<?php

namespace App\Filament\TeamPanel\Actions;

use App\Models\Project;
use App\Services\GeminiService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class GenerateRoadmapAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generate-roadmap-ai';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Gerar com IA')
            ->icon(\Filament\Support\Icons\Heroicon::OutlinedSparkles)
            ->modalHeading('Geração de Roadmap e Tarefas com IA')
            ->schema([
                Textarea::make('instructions')
                    ->label('Instruções adicionais')
                    ->rows(3)
                    ->placeholder('Ex: foque em mobile-first, use React + Laravel API...'),
                Toggle::make('include_tasks')
                    ->label('Incluir tarefas por fase')
                    ->default(true),
                Toggle::make('skip_existing')
                    ->label('Ignorar itens já existentes')
                    ->default(true),
            ])
            ->action(function (array $data, Project $record): void {
                $existingRoadmap = $record->roadmapItems()->pluck('title')->join(', ');
                $existingTasks   = $record->tasks()->pluck('title')->join(', ');
                $prompt = $this->buildPrompt($record, $existingRoadmap, $existingTasks, $data);
                $result = app(GeminiService::class)->generateJson($prompt);
                $this->persistResult($result, $record, $data);

                \Filament\Notifications\Notification::make()
                    ->title('Roadmap gerado com sucesso!')
                    ->success()
                    ->send();
            });
    }

    private function buildPrompt(Project $project, string $roadmap, string $tasks, array $data): string
    {
        $skip = $data['skip_existing'] ? "NÃO inclua itens já existentes." : "";

        return <<<PROMPT
Projeto: {$project->name}
Descrição: {$project->description}
Roadmap atual: {$roadmap}
Tarefas atuais: {$tasks}
Instruções: {$data['instructions']}
{$skip}

Gere roadmap e tarefas no formato JSON:
{
    "roadmap_items": [
        { "title": "...", "description": "...", "type": "milestone", "planned_date": "YYYY-MM-DD" }
    ],
    "tasks": [
        { "title": "...", "description": "..." }
    ]
}
PROMPT;
    }

    private function persistResult(array $result, Project $project, array $data): void
    {
        $existingTitles = $data['skip_existing']
            ? $project->roadmapItems()->pluck('title')->map('strtolower')->toArray()
            : [];

        foreach ($result['roadmap_items'] ?? [] as $item) {
            if (in_array(strtolower($item['title']), $existingTitles)) {
                continue;
            }
            $project->roadmapItems()->create([
                'title'        => $item['title'],
                'description'  => $item['description'] ?? null,
                'type'         => $item['type'] ?? 'milestone',
                'planned_date' => $item['planned_date'] ?? now()->addDays(30)->format('Y-m-d'),
            ]);
        }

        if ($data['include_tasks']) {
            $existingTaskTitles = $data['skip_existing']
                ? $project->tasks()->pluck('title')->map('strtolower')->toArray()
                : [];

            foreach ($result['tasks'] ?? [] as $task) {
                if (in_array(strtolower($task['title']), $existingTaskTitles)) {
                    continue;
                }
                $project->tasks()->create([
                    'title'       => $task['title'],
                    'description' => $task['description'] ?? null,
                    'status'      => 'todo',
                ]);
            }
        }
    }
}
