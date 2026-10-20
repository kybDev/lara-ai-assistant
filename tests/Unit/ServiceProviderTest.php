<?php

namespace KyBDev\LaraAiAssistant\Tests\Unit;

use KyBDev\LaraAiAssistant\AiAssistantServiceProvider;
use KyBDev\LaraAiAssistant\Facades\AiAssistant;
use KyBDev\LaraAiAssistant\Tests\Fixtures\Models\Asset;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [AiAssistantServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('ai-assistant.model_directories', [[
            'path' => dirname(__DIR__, 2).'/tests/Fixtures/Models',
            'namespace' => 'KyBDev\\LaraAiAssistant\\Tests\\Fixtures\\Models',
        ]]);
    }

    public function test_it_registers_model_discovery_through_the_facade(): void
    {
        $models = AiAssistant::discover();

        self::assertSame(Asset::class, $models[0]->class);
        self::assertFalse($models[0]->enabled);
    }
}
