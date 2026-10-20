<?php

namespace Vendor\LaravelAiAssistant\Discovery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

final class ModelScanner
{
    /**
     * @param  list<array{path: string, namespace: string}>  $modelDirectories
     * @param  list<class-string<Model>>  $enabledModels
     * @param  list<class-string<Model>>  $excludedModels
     */
    public function __construct(
        private readonly string $basePath,
        private readonly array $modelDirectories,
        private readonly array $enabledModels = [],
        private readonly array $excludedModels = []
    ) {}

    /**
     * Discover model metadata without running model constructors, querying
     * records, or invoking relationship methods.
     *
     * @return list<DiscoveredModel>
     */
    public function discover(): array
    {
        $models = [];

        foreach ($this->modelDirectories as $directory) {
            $path = $this->resolvePath($directory['path']);
            if (! is_dir($path)) {
                throw new InvalidArgumentException("Model discovery directory does not exist: {$path}");
            }

            $namespace = trim($directory['namespace'], '\\');
            if ($namespace === '') {
                throw new InvalidArgumentException('A model discovery namespace cannot be empty.');
            }

            $root = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if (! $file instanceof SplFileInfo || ! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $relativePath = substr($file->getPathname(), strlen($root));
                $relativeClass = substr($relativePath, 0, -4);
                $class = $namespace.'\\'.str_replace(
                    [DIRECTORY_SEPARATOR, '/'],
                    '\\',
                    $relativeClass
                );

                if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
                    continue;
                }

                $reflection = new ReflectionClass($class);
                if ($reflection->isAbstract() || in_array($class, $this->excludedModels, true)) {
                    continue;
                }

                /** @var Model $model */
                $model = $reflection->newInstanceWithoutConstructor();

                $models[$class] = new DiscoveredModel(
                    class: $class,
                    table: $model->getTable(),
                    connection: $model->getConnectionName(),
                    primaryKey: $model->getKeyName(),
                    timestamps: $model->usesTimestamps(),
                    softDeletes: in_array(SoftDeletes::class, class_uses_recursive($class), true),
                    fillable: $model->getFillable(),
                    casts: $model->getCasts(),
                    enabled: in_array($class, $this->enabledModels, true)
                );
            }
        }

        ksort($models);

        return array_values($models);
    }

    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1) {
            return $path;
        }

        return $this->basePath.DIRECTORY_SEPARATOR.str_replace(
            ['/', '\\'],
            DIRECTORY_SEPARATOR,
            $path
        );
    }
}
