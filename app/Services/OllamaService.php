<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->baseUrl = env('OLLAMA_URL', 'http://localhost:11434');
        $this->model = env('OLLAMA_MODEL', 'llama3.2');
    }

    public function chat(string $prompt): string
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/api/chat", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['message']['content'] ?? 'I am not sure how to respond.';
            }

            Log::error('Ollama error', ['status' => $response->status()]);
            return 'I am having trouble thinking right now.';
        } catch (\Exception $e) {
            Log::error('Ollama exception', ['error' => $e->getMessage()]);
            return 'I am having trouble thinking right now.';
        }
    }

    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/api/tags");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
