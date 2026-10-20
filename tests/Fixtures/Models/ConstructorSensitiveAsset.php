<?php

namespace KyBDev\LaraAiAssistant\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class ConstructorSensitiveAsset extends Model
{
    protected $table = 'constructor_sensitive_assets';

    public function __construct(array $attributes = [])
    {
        throw new RuntimeException('Model constructors must not be called during discovery.');
    }
}
