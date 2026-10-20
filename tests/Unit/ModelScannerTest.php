<?php

namespace KyBDev\LaraAiAssistant\Tests\Unit;

use InvalidArgumentException;
use KyBDev\LaraAiAssistant\Discovery\ModelScanner;
use KyBDev\LaraAiAssistant\Tests\Fixtures\Models\Asset;
use KyBDev\LaraAiAssistant\Tests\Fixtures\Models\ConstructorSensitiveAsset;
use PHPUnit\Framework\TestCase;

class ModelScannerTest extends TestCase
{
    public function test_it_discovers_model_metadata_without_enabling_models_by_default(): void
    {
        $scanner = new ModelScanner(
            basePath: dirname(__DIR__, 2),
            modelDirectories: [[
                'path' => 'tests/Fixtures/Models',
                'namespace' => 'KyBDev\\LaraAiAssistant\\Tests\\Fixtures\\Models',
            ]]
        );

        $models = $scanner->discover();

        self::assertCount(2, $models);
        self::assertSame([Asset::class, ConstructorSensitiveAsset::class], array_column($models, 'class'));

        $asset = $models[0];
        self::assertSame('fixture_assets', $asset->table);
        self::assertSame('sqlsrv2', $asset->connection);
        self::assertSame('id', $asset->primaryKey);
        self::assertTrue($asset->timestamps);
        self::assertTrue($asset->softDeletes);
        self::assertSame(['name', 'status'], $asset->fillable);
        self::assertArrayHasKey('status', $asset->casts);
        self::assertFalse($asset->enabled);
        self::assertSame('constructor_sensitive_assets', $models[1]->table);
    }

    public function test_it_enables_only_explicitly_allowlisted_models_and_honors_exclusions(): void
    {
        $scanner = new ModelScanner(
            basePath: dirname(__DIR__, 2),
            modelDirectories: [[
                'path' => 'tests/Fixtures/Models',
                'namespace' => 'KyBDev\\LaraAiAssistant\\Tests\\Fixtures\\Models',
            ]],
            enabledModels: [Asset::class]
        );

        $models = $scanner->discover();
        self::assertTrue($models[0]->enabled);
        self::assertFalse($models[1]->enabled);

        $scannerWithExclusion = new ModelScanner(
            basePath: dirname(__DIR__, 2),
            modelDirectories: [[
                'path' => 'tests/Fixtures/Models',
                'namespace' => 'KyBDev\\LaraAiAssistant\\Tests\\Fixtures\\Models',
            ]],
            enabledModels: [Asset::class],
            excludedModels: [Asset::class]
        );

        $excludedModels = $scannerWithExclusion->discover();
        self::assertCount(1, $excludedModels);
        self::assertSame(ConstructorSensitiveAsset::class, $excludedModels[0]->class);
        self::assertFalse($excludedModels[0]->enabled);
    }

    public function test_it_fails_explicitly_for_a_missing_model_directory(): void
    {
        $scanner = new ModelScanner(
            basePath: dirname(__DIR__, 2),
            modelDirectories: [[
                'path' => 'missing-models',
                'namespace' => 'KyBDev\\LaraAiAssistant\\Tests\\Fixtures\\Models',
            ]]
        );

        $this->expectException(InvalidArgumentException::class);
        $scanner->discover();
    }
}
