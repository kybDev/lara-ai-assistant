<?php

namespace KyBDev\LaraAiAssistant;

use Illuminate\Support\ServiceProvider;
use KyBDev\LaraAiAssistant\Chat\OllamaChatClient;
use KyBDev\LaraAiAssistant\Discovery\ModelScanner;

class AiAssistantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/ai-assistant.php',
            'ai-assistant'
        );

        $this->app->singleton(ModelScanner::class, function ($app): ModelScanner {
            return new ModelScanner(
                $app->basePath(),
                $app['config']->get('ai-assistant.model_directories', []),
                $app['config']->get('ai-assistant.enabled_models', []),
                $app['config']->get('ai-assistant.excluded_models', [])
            );
        });

        $this->app->singleton(OllamaChatClient::class, function ($app): OllamaChatClient {
            return new OllamaChatClient(
                $app['config']->get('ai-assistant.ollama')
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(dirname(__DIR__).'/routes/web.php');
        $this->loadViewsFrom(dirname(__DIR__).'/resources/views', 'lara-ai-assistant');

        $this->publishes([
            dirname(__DIR__).'/config/ai-assistant.php' => config_path('ai-assistant.php'),
        ], 'ai-assistant-config');
    }
}
