# Laravel AI Assistant

A Laravel package that provides an authenticated chat UI backed by local Ollama
and metadata-only Eloquent model discovery. Chat responses do not currently
retrieve application database records.

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Installation

Install the package with Composer:

```bash
composer require kybdev/lara-ai-assistant
```

For local development from a host application in a sibling directory, add this
repository to the host application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../lara-ai-assistant",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "kybdev/lara-ai-assistant": "^0.1"
    }
}
```

Then run `composer update kybdev/lara-ai-assistant`.

The service provider is registered through Laravel package auto-discovery.
Publish the default configuration with:

```bash
php artisan vendor:publish --tag=ai-assistant-config
```

## Chat UI

The package registers an authenticated chat page at `/ai-assistant` and a
CSRF-protected message endpoint. It sends chat history to Ollama's `/api/chat`
endpoint and does not persist conversations.

Configure the local model in `.env`:

```dotenv
OLLAMA_BASE_URL=http://127.0.0.1:11434
OLLAMA_MODEL=qwen3:4b
OLLAMA_TIMEOUT=180
```

The package defaults to the `web` and `auth` middleware. Host applications
should add their own authorization middleware to `route.middleware`. The chat
interface renders plain text and does not accept model-generated HTML.

## Model discovery

Configure the directories and PSR-4 namespaces containing Eloquent models in
`config/ai-assistant.php`. Discovery returns model class, table, connection,
primary key, timestamps, soft-delete support, fillable fields, casts, and an
`enabled` flag.

Models are disabled unless explicitly listed in `enabled_models`. Exclusions
always take precedence. An enabled model is not currently read or exposed to a provider.

```php
use KyBDev\LaraAiAssistant\Facades\AiAssistant;

$catalog = AiAssistant::discover();
```

Discovery does not run model constructors, query records, or invoke model
relationship methods. This avoids accidental database access or application
side effects while scanning. Relationship metadata, authorization-aware
database retrieval, and MCP tools are not implemented yet.
