<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Asset entity used inside BindingRequirement. */
class Asset extends BaseModel
{
    // Support both naming variants seen in examples/schemas
    protected ?string $name = null;        // sometimes used in examples

    protected ?string $title = null;       // used in schemas

    protected ?string $description = null;

    protected ?string $landingPage = null; // uri in BindingRequirement example

    protected ?string $url = null;         // uri in Asset schema

    /**
     * Build payload for Asset.
     * Supports either name/landing_page (example) or title/url (schema).
     *
     * @return array<string, string>
     *   Associative array representation ready for JSON encoding.
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->name !== null) {
            $out['name'] = $this->name;
        } elseif ($this->title !== null) {
            $out['title'] = $this->title;
        }
        if ($this->description !== null) {
            $out['description'] = $this->description;
        }
        if ($this->landingPage !== null) {
            $out['landing_page'] = $this->landingPage;
        } elseif ($this->url !== null) {
            $out['url'] = $this->url;
        }

        return $out;
    }
}
