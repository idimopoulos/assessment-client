<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Root model for building the assessment request payload. */
class Assessment extends BaseModel
{
    protected ?string $name = null;
    /**
     * Provider can be Organisation object or UUID string reference
     * @var Organisation|string|null
     */
    protected $provider = null;
    /** @var BindingRequirement[] */
    protected array $bindingRequirement = [];

    // results_in statements
    /** @var array{legal: ?Statement, organisational: ?Statement, semantic: ?Statement, technical: ?Statement} */
    protected array $resultsIn = [
        'legal' => null,
        'organisational' => null,
        'semantic' => null,
        'technical' => null,
    ];

    protected ?string $remainingBarriers = null;
    /** @var string[] */
    protected array $documents = [];
    protected ?string $otherComment = null;

    /**
     * Create or replace the provider organisation object.
     *
     * @return Organisation
     *   The organisation instance to populate.
     */
    public function getProvider(): Organisation
    {
        // Replace existing reference if any
        $org = new Organisation();
        $this->provider = $org;
        return $org;
    }

    /**
     * Set the provider by UUID reference.
     *
     * @param string $uuid
     *   The organisation UUID.
     *
     * @return $this
     *   The current instance for chaining.
     */
    public function setProviderId(string $uuid): self
    {
        $this->provider = $uuid;
        return $this;
    }

    /**
     * Append a top-level supporting document URL.
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
     * Create and append a new binding requirement.
     *
     * @return BindingRequirement
     *   The newly created binding requirement instance.
     */
    public function getBindingRequirement(): BindingRequirement
    {
        $br = new BindingRequirement();
        $this->bindingRequirement[] = $br;
        return $br;
    }

    /**
     * Get or create a results_in statement by dimension.
     *
     * @param string $dimension
     *   One of: legal, organisational, semantic, technical.
     *
     * @return Statement|null
     *   The statement for the given dimension, or null if dimension is invalid.
     */
    public function getResultsIn(string $dimension): ?Statement
    {
        $dim = strtolower($dimension);
        if (!array_key_exists($dim, $this->resultsIn)) {
            return null;
        }
        if ($this->resultsIn[$dim] === null) {
            $this->resultsIn[$dim] = new Statement();
        }
        return $this->resultsIn[$dim];
    }

    /**
     * Build payload for Assessment.
     *
     * @return array{
     *     name?: string,
     *     provider?: string|array<string, string>,
     *     binding_requirement?: array<string, mixed>|list<array<string, mixed>>,
     *     results_in?: array<string, array<string, string>>,
     *     remaining_barriers?: string,
     *     documents?: list<string>,
     *     other_comment?: string
     * } Associative array representation ready for JSON encoding.
     */
    public function getAsArray(): array
    {
        $out = [];
        if ($this->name !== null) {
            $out['name'] = $this->name;
        }

        // provider: string (uuid) or Organisation object
        if ($this->provider !== null) {
            if (is_string($this->provider)) {
                $out['provider'] = $this->provider;
            } elseif ($this->provider instanceof Organisation) {
                $out['provider'] = $this->provider->toArray();
            }
        }

        // binding_requirement: output single object if only one, else array
        if (!empty($this->bindingRequirement)) {
            $mapped = array_map(function ($br) {
                return $br->toArray();
            }, $this->bindingRequirement);
            if (count($mapped) === 1) {
                $out['binding_requirement'] = $mapped[0];
            } else {
                $out['binding_requirement'] = $mapped;
            }
        }

        // results_in
        $ri = [];
        foreach ($this->resultsIn as $k => $stmt) {
            if ($stmt instanceof Statement) {
                $val = $stmt->toArray();
                if (!empty($val)) {
                    $ri[$k] = $val;
                }
            }
        }
        if (!empty($ri)) {
            $out['results_in'] = $ri;
        }

        if (!empty($this->remainingBarriers)) {
            $out['remaining_barriers'] = $this->remainingBarriers;
        }
        if (!empty($this->documents)) {
            $out['documents'] = $this->documents;
        }
        if (!empty($this->otherComment)) {
            $out['other_comment'] = $this->otherComment;
        }

        return $out;
    }
}
