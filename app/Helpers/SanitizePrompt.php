<?php

declare(strict_types=1);

namespace App\Helpers;

final class SanitizePrompt
{
    private const INJECTION_PATTERNS = [
        '/ignore\s+(previous|all\s+)?(instructions?|orders?|commands?|rules?)/i',
        '/(disregard|forget)\s+(previous|all\s+)?(instructions?|orders?|commands?)/i',
        '/new\s+(system|prompt|instructions?)/i',
        '/<system>/i',
        '/\[\s*SYSTEM\s*\]/i',
        '/you\s+are\s+now\s+(a\s+)?(different|new)/i',
        '/pretend\s+(you\s+are|to\s+be)/i',
        '/as\s+a\s+(different|new|another)\s+(AI|model|assistant)/i',
        '/switch\s+(to|in\s+)?(a\s+)?(different|new)/i',
        '/override/i',
        '/jailbreak/i',
        '/dan\s+(prompt)?/i',
        '/roleplay.*without.*rules/i',
        '/ignore.*policy/i',
    ];

    private const MAX_MESSAGE_LENGTH = 1000;

    public function sanitize(string $input): string
    {
        $sanitized = trim($input);

        $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $sanitized);

        $sanitized = stripcslashes($sanitized);

        if (strlen($sanitized) > self::MAX_MESSAGE_LENGTH) {
            $sanitized = substr($sanitized, 0, self::MAX_MESSAGE_LENGTH);
        }

        $sanitized = preg_replace('/\s+/', ' ', $sanitized);

        foreach (self::INJECTION_PATTERNS as $pattern) {
            if (preg_match($pattern, $sanitized)) {
                $sanitized = preg_replace($pattern, '[filtered]', $sanitized);
            }
        }

        return $sanitized;
    }

    public function isClean(string $input): bool
    {
        foreach (self::INJECTION_PATTERNS as $pattern) {
            if (preg_match($pattern, $input)) {
                return false;
            }
        }

        return true;
    }
}
