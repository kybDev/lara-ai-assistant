<?php

namespace KyBDev\LaraAiAssistant\Chat;

use RuntimeException;
use Throwable;

final class OllamaChatException extends RuntimeException
{
    public static function connectionFailure(Throwable $previous): self
    {
        return new self(
            'Unable to connect to Ollama. Check that the Ollama service is running and the configured URL is correct.',
            previous: $previous
        );
    }

    public static function requestFailure(int $status): self
    {
        return new self("Ollama returned an unsuccessful response (HTTP {$status}).");
    }

    public static function invalidResponse(): self
    {
        return new self('Ollama returned an invalid chat response.');
    }
}
