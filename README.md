# Laravel AI Assistant

A Laravel package for safe model discovery and future authorized AI retrieval.
The current implementation provides Eloquent model metadata discovery only; it
does not query records, call an AI provider, or expose MCP tools.

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Installation

Install the package with Composer. For local development from the host
application, add a path repository pointing to this package directory and
require `vendor/laravel-ai-assistant`.

The service provider is registered through Laravel package auto-discovery.
Publish the default configuration with:

```bash
php artisan vendor:publish --tag=ai-assistant-config
```

## Model discovery

Configure the directories and PSR-4 namespaces containing Eloquent models in
`config/ai-assistant.php`. Discovery returns model class, table, connection,
primary key, timestamps, soft-delete support, fillable fields, casts, and an
`enabled` flag.

Models are disabled unless explicitly listed in `enabled_models`. Exclusions
always take precedence. This allowlist is groundwork for future retrieval; an
enabled model is not currently read or exposed to a provider.

```php
use Vendor\LaravelAiAssistant\Facades\AiAssistant;

$catalog = AiAssistant::discover();
```

Discovery does not run model constructors, query records, or invoke model
relationship methods. This avoids accidental database access or application
side effects while scanning. Relationship
metadata, authorization-aware retrieval, provider integrations, and MCP tools
are not implemented yet.
