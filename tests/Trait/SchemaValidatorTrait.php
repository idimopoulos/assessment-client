<?php

declare(strict_types=1);

namespace AssessmentClient\Tests\Trait;

use cebe\openapi\Reader;
use JsonSchema\Validator;

/** Shared helper to validate data against an OpenAPI schema. */
trait SchemaValidatorTrait
{
    /**
     * Validate data structure against a schema defined in an OpenAPI YAML file.
     *
     * @param array $data
     *   The data to validate.
     * @param string $openApiFile
     *   The path to the OpenAPI YAML file.
     * @param string $schema
     *   The schema name to validate against.
     *
     * @return array<string, list<string>>
     *   A map of JSON pointers to lists of error messages.
     */
    private function validate(array $data, string $openApiFile, string $schema): array
    {
        $openapi = Reader::readFromYamlFile($openApiFile);

        if (!$openapi->validate()) {
            throw new \InvalidArgumentException(
                "The file $openApiFile has invalid OpenAPI schema. Reported errors:\n".
                print_r($openapi->getErrors(), true)
            );
        }

        $schemaDef = $openapi->components->schemas[$schema]->getSerializableData();

        // Convert associative arrays to objects (stdClass) where appropriate.
        $data = json_decode(json_encode($data));

        $validator = new Validator();
        $validator->validate($data, $schemaDef);

        $errors = [];
        foreach ($validator->getErrors() as $error) {
            $errors[$error['pointer']][] = $error['message'];
        }
        ksort($errors);

        return $errors;
    }
}
