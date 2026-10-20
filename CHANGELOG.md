# Changelog

## 0.2.0

- Add an authenticated chat page and CSRF-protected Ollama chat endpoint.
- Add configurable Ollama model, URL, timeout, and route middleware settings.
- Validate chat history and explicitly keep database retrieval out of scope.
- Replace placeholder package namespaces with `KyBDev\LaraAiAssistant`.
- Document Composer installation for published and local path-repository use.

## 0.1.0

- Initial package scaffold.
- Added service provider, facade, configuration, contracts, and test setup.
- Documented path-repository installation model for host app integration.
- Added metadata-only Eloquent model discovery with an explicit model allowlist.
- Added package configuration publishing and discovery facade access.
