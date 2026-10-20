<?php

namespace Vendor\LaravelAiAssistant\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $table = 'fixture_assets';

    protected $connection = 'sqlsrv2';

    protected $fillable = ['name', 'status'];

    protected $casts = [
        'status' => 'string',
    ];
}
