<?php

declare(strict_types=1);

namespace AssessmentClient\Model;

/** Base model for all value objects in the Assessment client. */
abstract class BaseModel
{
    protected ?string $id = null;

    /**
     * Set the UUID identifier for the entity.
     *
     * @param string $id
     *   The UUID string.
     *
     * @return $this
     *   The current instance for chaining.
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the UUID identifier if present.
     *
     * @return string|null
     *   UUID or null if not set.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Generic setter that accepts either camelCase or snake_case keys.
     * If the normalized property exists on the concrete class, it is set.
     *
     * @param string $key
     *   Property name in camelCase or snake_case.
     * @param mixed $value
     *   Value to assign.
     *
     * @return $this
     *   The current instance for chaining.
     */
    public function setValue(string $key, mixed $value): self
    {
        $prop = $this->normalizeKeyToProperty($key);
        if (property_exists($this, $prop)) {
            $this->$prop = $value;
        }
        return $this;
    }

    /**
     * Convert a possibly snake_cased key to a camelCase property name.
     *
     * @param string $key
     *   The input key to normalize.
     *
     * @return string
     *   Normalized camelCase property name.
     */
    protected function normalizeKeyToProperty(string $key): string
    {
        if (str_contains($key, '_')) {
            $segments = explode('_', $key);
            $camel = array_shift($segments);
            foreach ($segments as $seg) {
                $camel .= ucfirst($seg);
            }
            return $camel;
        }
        return $key;
    }

    /**
     * Helper for converting camelCase to snake_case keys.
     *
     * @param string $key
     *   The camelCase key.
     *
     * @return string
     *   The snake_cased version of the key.
     */
    protected function camelToSnake(string $key): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
    }
}
