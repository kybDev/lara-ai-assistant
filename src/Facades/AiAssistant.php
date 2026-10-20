<?php

namespace KyBDev\LaraAiAssistant\Facades;

use Illuminate\Support\Facades\Facade;
use KyBDev\LaraAiAssistant\Discovery\ModelScanner;

/**
 * @method static list<\KyBDev\LaraAiAssistant\Discovery\DiscoveredModel> discover()
 */
class AiAssistant extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ModelScanner::class;
    }
}
