<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Public service entity for affected service areas. */
class AffectedService extends BaseModel
{
    protected ?string $name = null;
    protected ?string $thematicArea = null;

    /**
     * Build payload for PublicService.
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
        if ($this->thematicArea !== null) {
            $out['thematic_area'] = $this->thematicArea;
        }
        return $out;
    }
}
