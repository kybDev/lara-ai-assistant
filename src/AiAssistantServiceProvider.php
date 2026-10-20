<?php

namespace KyBDev\LaraAiAssistant;

use Illuminate\Support\ServiceProvider;
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
    }

    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__).'/config/ai-assistant.php' => config_path('ai-assistant.php'),
        ], 'ai-assistant-config');
    }
}
