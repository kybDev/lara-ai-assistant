<?php

namespace KyBDev\LaraAiAssistant\Chat;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

final class OllamaChatClient
{
    /**
     * @param  array{base_url: string, model: string, timeout: int, think?: bool, system_prompt: string}  $config
     */
    public function __construct(private readonly array $config) {}

    /**
     * @param  list<array{role: 'user'|'assistant', content: string}>  $messages
     */
    public function reply(array $messages): string
    {
        if ($messages === [] || end($messages)['role'] !== 'user') {
            throw new InvalidArgumentException('The last chat message must be from the user.');
        }

        $requestMessages = [
            [
                'role' => 'system',
                'content' => $this->config['system_prompt'],
            ],
            ...$messages,
        ];

        try {
            $response = Http::acceptJson()
                ->timeout($this->config['timeout'])
                ->post(rtrim($this->config['base_url'], '/').'/api/chat', [
                    'model' => $this->config['model'],
                    'messages' => $requestMessages,
                    'stream' => false,
                    'think' => $this->config['think'] ?? false,
                ]);
        } catch (ConnectionException $exception) {
            throw OllamaChatException::connectionFailure($exception);
        }

        if (! $response->successful()) {
            throw OllamaChatException::requestFailure($response->status());
        }

        $content = $response->json('message.content');
        if (! is_string($content) || trim($content) === '') {
            throw OllamaChatException::invalidResponse();
        }

        return trim($content);
    }
}
