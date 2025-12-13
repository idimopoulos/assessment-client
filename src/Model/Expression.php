<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Expression entity within a Binding Requirement representing a legal or policy expression. */
class Expression extends BaseModel
{
    protected ?string $name = null;
    protected ?string $description = null;
    protected ?string $documentedIn = null; // uri

    /**
     * Build payload for Expression.
     *
     * @return array<string, string> Associative array representation ready for JSON encoding.
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->name !== null) {
            $out['name'] = $this->name;
        }
        if ($this->description !== null) {
            $out['description'] = $this->description;
        }
        if ($this->documentedIn !== null) {
            $out['documented_in'] = $this->documentedIn;
        }
        return $out;
    }
}
