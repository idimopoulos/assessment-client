<?php

declare(strict_types=1);

use AssessmentClient\Model\Assessment;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AssessmentPayloadTest extends TestCase
{

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

        $bindingRequirement->getHasParticipation()->setValue('country', 'BE,')
            ->setValue('participant_type', 'national_public_sector_body');
        $bindingRequirement->getHasParticipation()->setValue('country', 'IT')
            ->setValue('participant_type', 'private_businesses');

        $asset0 = $bindingRequirement->getAsset();
        $asset0->setValue('name', 'Portal EU')
            ->setValue('description', 'An EU portal interoperable solution')
            ->setValue('landing_page', 'https://portal.example.eu');
        $bindingRequirement->addAssetId('b21e876e-8ad0-4057-ad57-de45c51d374f');
        $bindingRequirement->addAssetId('a0e76022-863e-4f8f-8eb0-8227cd080be4');

        $assessment->getResultsIn('organisational')
            ->setValue('interpretation', 'positive')
            ->setValue('judgement', 'Compliant with conditions');
        $assessment->getResultsIn('technical')->setValue('judgement', 'neutral')
            ->setValue('interpretation', 'Requires follow-up');

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

        $expected = [
            'name' => 'Legislation waste management',
            'provider' => [
                'name' => 'City of Brussels',
                'organisation_type' => 'local_public_sector_body',
                'country' => 'BE',
            ],
            // Note: single binding requirement serialized as an object (not a list),
            // matching the example provided.
            'binding_requirement' => [
                'expression' => [
                    [
                        'name' => 'Legal Act 2024',
                        'description' => 'Reference to an EU legal expression',
                        'documented_in' => 'https://example.com/eli/act',
                    ],
                ],
                'description' => 'Article 5',
                'documents' => [
                    'https://example.com/law/123',
                    'https://example.com/regulation/eu-1655467-s',
                ],
                'affects' => [
                    [
                        'name' => 'Digital Monitoring Unit',
                        'thematic_area' => 'digital_and_innovation_services',
                    ],
                    [
                        'name' => 'Training Centre',
                        'thematic_area' => 'education_and_research',
                    ],
                ],
                'has_participation' => [
                    [
                        'country' => 'BE,',
                        'participant_type' => 'national_public_sector_body',
                    ],
                    [
                        'country' => 'IT',
                        'participant_type' => 'private_businesses',
                    ],
                ],
                'asset' => [
                    [
                        'name' => 'Portal EU',
                        'description' => 'An EU portal interoperable solution',
                        'landing_page' => 'https://portal.example.eu',
                    ],
                    'b21e876e-8ad0-4057-ad57-de45c51d374f',
                    'a0e76022-863e-4f8f-8eb0-8227cd080be4',
                ],
            ],
            'results_in' => [
                'organisational' => [
                    'interpretation' => 'positive',
                    'judgement' => 'Compliant with conditions',
                ],
                'technical' => [
                    'interpretation' => 'Requires follow-up',
                    'judgement' => 'neutral',
                ],
            ],
            'remaining_barriers' => 'The city has not yet implemented the new regulations.',
            'documents' => [
                'https://files.example.com/documents/req-1.pdf',
                'https://files.example.com/documents/regulation.2025.pdf',
            ],
            'other_comment' => 'Still work to be done. Waiting for a new iteration.',
        ];

        $this->assertSame(
            $expected,
            $payload,
            'Assessment payload matches the provided example.'
        );
    }

}
