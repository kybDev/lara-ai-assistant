<?php

namespace KyBDev\LaraAiAssistant\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use KyBDev\LaraAiAssistant\Chat\OllamaChatClient;
use KyBDev\LaraAiAssistant\Chat\OllamaChatException;

final class ChatController
{
    public function index(): View
    {
        return view('lara-ai-assistant::chat', [
            'model' => config('ai-assistant.ollama.model'),
            'messageUrl' => route('ai-assistant.chat.message'),
        ]);
    }

    public function message(Request $request, OllamaChatClient $client): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:40'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:8000'],
        ]);

        $messages = array_values($validated['messages']);
        if ($messages[array_key_last($messages)]['role'] !== 'user') {
            return response()->json([
                'message' => 'The last message must be from you.',
            ], 422);
        }

        try {
            return response()->json([
                'message' => $client->reply($messages),
                'model' => config('ai-assistant.ollama.model'),
            ]);
        } catch (OllamaChatException $exception) {
            Log::warning('AI assistant Ollama request failed.', [
                'user_id' => $request->attributes->get('user')?->user_id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }
    }
}
