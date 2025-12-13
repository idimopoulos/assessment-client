<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Public Organisation entity used as provider in an Assessment. */
class Organisation extends BaseModel
{
    protected ?string $name = null;

    protected ?string $organisationType = null;

    protected ?string $country = null; // ISO-3166 alpha-2 or null

    /**
     * Build payload for Organisation.
     *
     * @return array<string, string>
     *   Associative array representation ready for JSON encoding.
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->name !== null) {
            $out['name'] = $this->name;
        }
        if ($this->organisationType !== null) {
            $out['organisation_type'] = $this->organisationType;
        }
        if ($this->country !== null) {
            $out['country'] = $this->country;
        }

        return $out;
    }
}
