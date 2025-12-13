<?php

declare(strict_types=1);

namespace AssessmentClient\Tests\Unit;

use AssessmentClient\Model\Assessment;
use AssessmentClient\Tests\Trait\SchemaValidatorTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AssessmentPayloadTest extends TestCase
{
    use SchemaValidatorTrait;

    #[Test]
    public function buildsCompleteExamplePayload(): void
    {
        $assessment = new Assessment();
        $assessment->setValue('name', 'Legislation waste management');

        $provider = $assessment->getProvider();
        $provider->setValue('name', 'City of Brussels')
            ->setValue('organisation_type', 'local_public_sector_body')
            ->setValue('country', 'BE');

        $bindingRequirement = $assessment->getBindingRequirement();
        $bindingRequirement->setValue('description', 'Article 5');
        $bindingRequirement->addDocument('https://example.com/law/123')
            ->addDocument('https://example.com/regulation/eu-1655467-s');

        $expr = $bindingRequirement->getExpression();
        $expr->setValue('name', 'Legal Act 2024')
            ->setValue('description', 'Reference to an EU legal expression')
            ->setValue('documented_in', 'https://example.com/eli/act');

        $bindingRequirement->getAffects()
            ->setValue('name', 'Digital Monitoring Unit')
            ->setValue('thematic_area', 'digital_and_innovation_services');
        $bindingRequirement->getAffects()->setValue('name', 'Training Centre')
            ->setValue('thematic_area', 'education_and_research');

        // Use a valid EU country code (schema enum) without trailing comma.
        $bindingRequirement->getHasParticipation()->setValue('country', 'BE')
            ->setValue('participant_type', 'national_public_sector_body');
        $bindingRequirement->getHasParticipation()->setValue('country', 'IT')
            ->setValue('participant_type', 'private_businesses');

        $asset0 = $bindingRequirement->getAsset();
        // Per schema, object form of asset requires: title, description, landing_page.
        $asset0->setValue('title', 'Portal EU')
            ->setValue('description', 'An EU portal interoperable solution')
            ->setValue('landing_page', 'https://portal.example.eu');
        $bindingRequirement->addAssetId('b21e876e-8ad0-4057-ad57-de45c51d374f');
        $bindingRequirement->addAssetId('a0e76022-863e-4f8f-8eb0-8227cd080be4');

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
        $assessment->addDocument(
            'https://files.example.com/documents/req-1.pdf'
        )
            ->addDocument(
                'https://files.example.com/documents/regulation.2025.pdf'
            );
        $assessment->setValue(
            'other_comment',
            'Still work to be done. Waiting for a new iteration.'
        );

        $payload = $assessment->getAsArray();

        // Validate the produced payload against the OpenAPI schema (Assessment).
        $openApiFile = __DIR__.'/../../openapi/assessments.openapi.yaml';
        $errors = $this->validate($payload, $openApiFile, 'Assessment');

        $this->assertSame(
            [],
            $errors,
            'Payload must validate against the Assessment schema. Errors: '.print_r($errors, true)
        );
    }

    #[Test]
    public function failsWhenNameIsMissing(): void
    {
        $a = new Assessment();
        // Omit name on purpose.

        // Provide valid provider object.
        $a->getProvider()
            ->setValue('name', 'City of Brussels')
            ->setValue('organisation_type', 'local_public_sector_body')
            ->setValue('country', 'BE');

        // Provide a minimal valid binding requirement.
        $br = $a->getBindingRequirement();
        $br->setValue('description', 'Minimal description');
        $br->getExpression()
            ->setValue('name', 'Act')
            ->setValue('description', 'Ref');
        $br->getAffects()->setValue('name', 'Service');

        $errors = $this->validate($a->getAsArray(), __DIR__.'/../../openapi/assessments.openapi.yaml', 'Assessment');

        $this->assertNotSame([], $errors, 'Validation should fail when name is missing.');
        $all = strtolower(print_r($errors, true));
        $this->assertStringContainsString('name', $all);
        $this->assertStringContainsString('required', $all);
    }

    #[Test]
    public function failsWhenProviderIsMissing(): void
    {
        $a = new Assessment();
        $a->setValue('name', 'No Provider');

        // Provide a minimal valid binding requirement.
        $br = $a->getBindingRequirement();
        $br->setValue('description', 'Minimal description');
        $br->getExpression()
            ->setValue('name', 'Act')
            ->setValue('description', 'Ref');
        $br->getAffects()->setValue('name', 'Service');

        $errors = $this->validate($a->getAsArray(), __DIR__.'/../../openapi/assessments.openapi.yaml', 'Assessment');

        $this->assertNotSame([], $errors, 'Validation should fail when provider is missing.');
        $all = strtolower(print_r($errors, true));
        $this->assertStringContainsString('provider', $all);
        $this->assertStringContainsString('required', $all);
    }

    #[Test]
    public function failsWhenBindingRequirementIsMissing(): void
    {
        $a = new Assessment();
        $a->setValue('name', 'No BR');
        $a->getProvider()
            ->setValue('name', 'City of Brussels')
            ->setValue('organisation_type', 'local_public_sector_body');

        // Do not add any binding requirement.
        $errors = $this->validate($a->getAsArray(), __DIR__.'/../../openapi/assessments.openapi.yaml', 'Assessment');

        $this->assertNotSame([], $errors, 'Validation should fail when binding_requirement is missing.');
        $all = strtolower(print_r($errors, true));
        $this->assertStringContainsString('binding_requirement', $all);
        $this->assertStringContainsString('required', $all);
    }

    #[Test]
    public function failsWhenBindingRequirementFieldsMissing(): void
    {
        $a = new Assessment();
        $a->setValue('name', 'Invalid BR');
        $a->getProvider()->setValue('name', 'Org')->setValue('organisation_type', 'local_public_sector_body');

        // Add an empty binding requirement missing required fields.
        $a->getBindingRequirement()->setValue('description', 'desc only');

        $errors = $this->validate($a->getAsArray(), __DIR__.'/../../openapi/assessments.openapi.yaml', 'Assessment');

        $this->assertNotSame([], $errors, 'Validation should fail when BR fields are missing.');
        $all = strtolower(print_r($errors, true));
        $this->assertStringContainsString('expression', $all);
        $this->assertStringContainsString('affects', $all);
        $this->assertStringContainsString('required', $all);
    }
}
