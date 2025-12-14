# Assessment Client

A tiny, dependency‑free PHP model layer to construct Assessment payloads matching `openapi/assessments.openapi.yaml`.

## Requirements
- PHP 8.3+ (as defined in `composer.json`).
- Composer.

## Installation
Add the following to your `repositories` section of your project's `composer.json`:

```json
{
    "type": "vcs",
    "url": "https://github.com/idimopoulos/assessment-client.git"
}
```

Then require the package:

```
composer require idimopoulos/assessment-client
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

## Testing
Basic PHPUnit tests are configured.

- Run the test suite:

```
composer test
```

The default configuration file is `phpunit.xml.dist`. Tests live under the `tests/` directory.

## Usage
Autoload models via Composer and build the assessment payload. Below is a complete example that mirrors the one used in the tests and validates against the OpenAPI schema.

```php
<?php

declare(strict_types=1);

use AssessmentClient\Model\Assessment;

require __DIR__ . '/vendor/autoload.php';

$assessment = new Assessment();
$assessment->setValue('name', 'Legislation waste management');

// Provider (new Organisation object)
$provider = $assessment->getProvider();
$provider->setValue('name', 'City of Brussels')
    ->setValue('organisation_type', 'local_public_sector_body')
    ->setValue('country', 'BE');

// Binding requirement
$bindingRequirement = $assessment->getBindingRequirement();
$bindingRequirement->setValue('description', 'Article 5');
$bindingRequirement->addDocument('https://example.com/law/123')
    ->addDocument('https://example.com/regulation/eu-1655467-s');

// Expression
$expr = $bindingRequirement->getExpression();
$expr->setValue('name', 'Legal Act 2024')
    ->setValue('description', 'Reference to an EU legal expression')
    ->setValue('documented_in', 'https://example.com/eli/act');

// Affects (PublicService)
$bindingRequirement->getAffects()
    ->setValue('name', 'Digital Monitoring Unit')
    ->setValue('thematic_area', 'digital_and_innovation_services');
$bindingRequirement->getAffects()
    ->setValue('name', 'Training Centre')
    ->setValue('thematic_area', 'education_and_research');

// Participation
$bindingRequirement->getHasParticipation()->setValue('country', 'BE')
    ->setValue('participant_type', 'national_public_sector_body');
$bindingRequirement->getHasParticipation()->setValue('country', 'IT')
    ->setValue('participant_type', 'private_businesses');

// Assets: one object (created) + two references (UUIDs)
$asset0 = $bindingRequirement->getAsset();
$asset0->setValue('title', 'Portal EU')
    ->setValue('description', 'An EU portal interoperable solution')
    ->setValue('landing_page', 'https://portal.example.eu');
$bindingRequirement->addAssetId('b21e876e-8ad0-4057-ad57-de45c51d374f');
$bindingRequirement->addAssetId('a0e76022-863e-4f8f-8eb0-8227cd080be4');

// Results
$assessment->getResultsIn('organisational')
    ->setValue('interpretation', 'positive')
    ->setValue('judgement', 'Compliant with conditions');
$assessment->getResultsIn('technical')
    ->setValue('interpretation', 'neutral')
    ->setValue('judgement', 'Requires follow-up');

$assessment->setValue(
    'remaining_barriers',
    'The city has not yet implemented the new regulations.'
);
$assessment->addDocument('https://files.example.com/documents/req-1.pdf')
    ->addDocument('https://files.example.com/documents/regulation.2025.pdf');
$assessment->setValue('other_comment', 'Still work to be done. Waiting for a new iteration.');

// Generate payload array (binding_requirement is always an array per schema)
$payload = $assessment->getAsArray();

// Optionally, JSON encode for sending over HTTP
echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
```

If you want to validate this payload against the OpenAPI schema, run the test suite (see below) or reuse the validator logic from `tests/Support/SchemaValidatorTrait.php` in your own scripts.
