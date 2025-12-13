# Assessment Client

A tiny, dependency‑free PHP model layer to construct Assessment payloads matching `openapi/assessments.openapi.yaml`.

## Requirements
- PHP 7.4+ (project code avoids newer syntax to remain compatible)
- Composer

## Installation
Install dev tools and autoloader:

```
composer install
```

If you already installed once and only need the dev tools:

```
composer update --dev
```

## Development tooling
This project includes PHP_CodeSniffer and PHPStan for quick checks.

- Run CodeSniffer (PSR-12 rules on `src/`):

```
composer cs
```

- Auto-fix coding style issues with PHP_CodeSniffer's fixer:

```
composer cbf
```

- Run PHPStan static analysis (level 6):

```
composer stan
```

- Run both lint and static analysis in one go:

```
composer qa
```

Configuration files:
- `phpcs.xml` — ruleset (PSR-12, excludes `vendor/` and `openapi/`).
- `phpstan.neon.dist` — analysis settings (paths: `src/`).

## Usage
Autoload models via Composer and build the assessment payload. See inline examples in the codebase.

## License
See the `LICENSE` file for license information.

## Contact
GitHub: `idimopoulos`