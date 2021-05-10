<?php

namespace Guesl\Admin\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class Scope
 * @package Guesl\Admin\Models
 */
class Scope implements JsonSerializable, Jsonable, Arrayable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $parameters;

    /**
     * Scope constructor.
     * @param string $name
     * @param array $parameters
     */
    public function __construct(string $name, array $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Scope
     */
    public function setName(string $name): Scope
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return Scope
     */
    public function setParameters(array $parameters): Scope
    {
        $this->parameters = $parameters;
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
            'parameters' => $this->getParameters()
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
