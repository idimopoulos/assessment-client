<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Participation entity identifying a stakeholder and its type. */
class Participant extends BaseModel
{
    protected ?string $participantType = null;
    protected ?string $country = null;

    /**
     * Build payload for Participation.
     *
     * @return array{
     *     country?: string,
     *     participant_type?: string
     * } Associative array representation ready for JSON encoding.
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->country !== null) {
            $out['country'] = $this->country;
        }
        if ($this->participantType !== null) {
            $out['participant_type'] = $this->participantType;
        }
        return $out;
    }
}
