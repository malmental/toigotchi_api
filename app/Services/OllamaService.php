<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    private string $baseUrl;

    private string $model;

    private const BREAKER_PREFIX = "ollama_circuit:";

    private const FAILURE_THRESHOLD = 5;

    private const RECOVERY_TIMEOUT = 60;

    public function __construct()
    {
        $this->baseUrl = config(
            "services.ollama.url",
            "http://localhost:11434",
        );
        $this->model = config("services.ollama.model", "phi3:latest");
    }

    public function chat(string $prompt): string
    {
        if ($this->isCircuitOpen()) {
            return "AI service temporarily unavailable. Please try again later.";
        }

        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/api/chat", [
                "model" => $this->model,
                "messages" => [["role" => "user", "content" => $prompt]],
                "stream" => false,
            ]);

            if ($response->successful()) {
                $this->recordSuccess();
                $data = $response->json();

                return $data["message"]["content"] ??
                    "I am not sure how to respond.";
            }

            $this->recordFailure();
            Log::error("Ollama error", ["status" => $response->status()]);

            return "I am having trouble thinking right now.";
        } catch (\Exception $e) {
            $this->recordFailure();
            Log::error("Ollama exception", ["error" => $e->getMessage()]);

            return "I am having trouble thinking right now.";
        }
    }

    public function chatStream(string $prompt, callable $onChunk): void
    {
        if ($this->isCircuitOpen()) {
            $onChunk(" [AI service temporarily unavailable]");

            return;
        }

        try {
            $response = Http::timeout(60)
                ->withOptions(["stream" => true])
                ->post("{$this->baseUrl}/api/chat", [
                    "model" => $this->model,
                    "messages" => [["role" => "user", "content" => $prompt]],
                    "stream" => true,
                ]);

            if (!$response->successful()) {
                $this->recordFailure();
                Log::error("Ollama stream error", [
                    "status" => $response->status(),
                ]);
                $onChunk(" [AI service unavailable]");

                return;
            }

            $this->recordSuccess();
            $buffer = "";
            $body = $response->toPsrResponse()->getBody();

            while (!$body->eof()) {
                $chunk = $body->read(1024);
                if ($chunk === "") {
                    usleep(10000);

                    continue;
                }

                $buffer .= $chunk;

                while (($newlinePos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $newlinePos);
                    $buffer = substr($buffer, $newlinePos + 1);

                    if (trim($line) === "") {
                        continue;
                    }

                    $data = json_decode($line, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (isset($data["message"]["content"])) {
                            $onChunk($data["message"]["content"]);
                        }
                        if (isset($data["done"]) && $data["done"] === true) {
                            break;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->recordFailure();
            Log::error("Ollama stream exception", [
                "error" => $e->getMessage(),
            ]);
            $onChunk(" [Connection lost]");
        }
    }

    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function isCircuitOpen(): bool
    {
        return Cache::get(self::BREAKER_PREFIX . "open", false);
    }

    private function recordFailure(): void
    {
        $key = self::BREAKER_PREFIX . "failures";
        $failures = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $failures, self::RECOVERY_TIMEOUT);

        if ($failures >= self::FAILURE_THRESHOLD) {
            Cache::put(
                self::BREAKER_PREFIX . "open",
                true,
                self::RECOVERY_TIMEOUT,
            );
            Log::warning("Ollama circuit breaker opened", [
                "failures" => $failures,
            ]);
        }
    }

    private function recordSuccess(): void
    {
        Cache::put(
            self::BREAKER_PREFIX . "failures",
            0,
            self::RECOVERY_TIMEOUT,
        );
    }
}
