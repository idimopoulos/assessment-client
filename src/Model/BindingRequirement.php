<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Binding requirement aggregating expressions, services, participations, documents, and assets. */
class BindingRequirement extends BaseModel
{
    protected ?string $description = null;
    /** @var string[] */
    protected array $documents = [];
    /** @var Expression[] */
    protected array $expression = [];
    /** @var AffectedService[] */
    protected array $affects = [];
    /** @var Participant[] */
    protected array $hasParticipation = [];
    /** @var array<int, Asset|string> list of Asset objects and/or UUID strings */
    protected array $asset = [];

    /**
     * Add a supporting document URL.
     *
     * @param string $url
     *   The document URL.
     *
     * @return $this
     *   The current instance for chaining.
     */
    public function addDocument(string $url): self
    {
        $this->documents[] = $url;
        return $this;
    }

    /**
     * Create and append a new Expression.
     *
     * @return Expression
     *   The newly created expression instance.
     */
    public function getExpression(): Expression
    {
        $expr = new Expression();
        $this->expression[] = $expr;
        return $expr;
    }

    /**
     * Create and append a new affected public service.
     *
     * @return AffectedService
     *   The newly created affected service instance.
     */
    public function getAffects(): AffectedService
    {
        $svc = new AffectedService();
        $this->affects[] = $svc;
        return $svc;
    }

    /**
     * Create and append a new participant.
     *
     * @return Participant
     *   The newly created participant instance.
     */
    public function getHasParticipation(): Participant
    {
        $p = new Participant();
        $this->hasParticipation[] = $p;
        return $p;
    }

    /**
     * Create and append a new asset object.
     *
     * @return Asset
     *   The newly created asset instance.
     */
    public function getAsset(): Asset
    {
        $a = new Asset();
        $this->asset[] = $a;
        return $a;
    }

    /**
     * Append an existing asset by UUID reference.
     *
     * @param string $uuid
     *   The asset UUID.
     *
     * @return $this
     *   The current instance for chaining.
     */
    public function addAssetId(string $uuid): self
    {
        $this->asset[] = $uuid;
        return $this;
    }

    /**
     * Build payload for BindingRequirement.
     *
     * @return array{
     *     expression?: list<array<string, string>>,
     *     description?: string,
     *     documents?: list<string>,
     *     affects?: list<array<string, string>>,
     *     has_participation?: list<array<string, string>>,
     *     asset?: list<string|array<string, string>>
     * } Associative array representation ready for JSON encoding.
     */
    public function toArray(): array
    {
        $out = [];
        if ($this->expression) {
            $out['expression'] = array_map(function ($e) {
                return $e->toArray();
            }, $this->expression);
        }
        if ($this->description !== null) {
            $out['description'] = $this->description;
        }
        if ($this->documents) {
            $out['documents'] = $this->documents;
        }
        if ($this->affects) {
            $out['affects'] = array_map(function ($s) {
                return $s->toArray();
            }, $this->affects);
        }
        if ($this->hasParticipation) {
            $out['has_participation'] = array_map(function ($p) {
                return $p->toArray();
            }, $this->hasParticipation);
        }
        if ($this->asset) {
            $out['asset'] = array_map(function ($a) {
                if (is_string($a)) {
                    return $a;
                }
                return $a->toArray();
            }, $this->asset);
        }
        return $out;
    }
}
