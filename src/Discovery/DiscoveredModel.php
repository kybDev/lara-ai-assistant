<?php

namespace Vendor\LaravelAiAssistant\Discovery;

final readonly class DiscoveredModel
{
    /**
     * @param  array<string, mixed>  $casts
     * @param  list<string>  $fillable
     */
    public function __construct(
        public string $class,
        public string $table,
        public ?string $connection,
        public string $primaryKey,
        public bool $timestamps,
        public bool $softDeletes,
        public array $fillable,
        public array $casts,
        public bool $enabled
    ) {}
}
