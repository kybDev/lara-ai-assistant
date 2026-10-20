<?php

namespace Vendor\LaravelAiAssistant\Facades;

use Illuminate\Support\Facades\Facade;
use Vendor\LaravelAiAssistant\Discovery\ModelScanner;

/**
 * @method static list<\Vendor\LaravelAiAssistant\Discovery\DiscoveredModel> discover()
 */
class AiAssistant extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ModelScanner::class;
    }
}
