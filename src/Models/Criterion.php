<?php

namespace Guesl\Admin\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class Criterion
 * @package Guesl\Admin\Models
 */
class Criterion implements JsonSerializable, Arrayable, Jsonable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $operation;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $exclusive;

    /**
     * Criterion constructor.
     *
     * @param string $name
     * @param string $operation
     * @param string | array $value
     * @param bool $exclusive
     */
    public function __construct(string $name, string $operation, $value, bool $exclusive = false)
    {
        $this->name = $name;
        $this->operation = $operation;
        $this->value = $value;
        $this->exclusive = $exclusive;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Criterion
     */
    public function setName($name): Criterion
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     * @return Criterion
     */
    public function setOperation(string $operation): Criterion
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Criterion
     */
    public function setValue($value): Criterion
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    /**
     * @param bool $exclusive
     * @return Criterion
     */
    public function setExclusive($exclusive)
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'operation' => $this->getOperation(),
            'value' => $this->getValue(),
            'exclusive' => $this->exclusive,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
