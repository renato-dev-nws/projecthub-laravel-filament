<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model  = config('services.gemini.model', 'gemini-2.0-flash');
    }

    public function generateJson(string $prompt): array
    {
        $candidateModels = array_values(array_unique(array_filter([
            $this->model,
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
        ])));

        $lastErrorBody = null;

        foreach ($candidateModels as $model) {
            $response = Http::timeout(60)
                ->post("{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature'      => 0.3,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '{}');

                return json_decode($text, true) ?? [];
            }

            $lastErrorBody = $response->body();

            if ($response->status() !== 404) {
                throw new RuntimeException('Erro ao chamar API Gemini: ' . $lastErrorBody);
            }
        }

        throw new RuntimeException('Erro ao chamar API Gemini: ' . ($lastErrorBody ?? 'modelo indisponível.'));
    }
}
