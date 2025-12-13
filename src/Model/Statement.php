<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Statement entity used under results_in dimensions. */
class Statement extends BaseModel
{
    protected ?string $interpretation = null; // negative|neutral|positive
    protected ?string $judgement = null;      // string

    /**
     * Build payload for Statement.
     *
     * @return array{
     *     interpretation?: string,
     *     judgement?: string
     * } Associative array representation ready for JSON encoding.
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->interpretation !== null) {
            $out['interpretation'] = $this->interpretation;
        }
        if ($this->judgement !== null) {
            $out['judgement'] = $this->judgement;
        }
        return $out;
    }
}
