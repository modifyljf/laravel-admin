<?php

namespace Modifyljf\Admin\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class Sort
 * @package Modifyljf\Admin\Models
 */
class Sort implements JsonSerializable, Arrayable, Jsonable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $direction;

    /**
     * Sort constructor.
     * @param string $name
     * @param string $direction
     */
    public function __construct(string $name, string $direction)
    {
        $this->name = $name;
        $this->direction = $direction;
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
     * @return Sort
     */
    public function setName(string $name): Sort
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     * @return Sort
     */
    public function setDirection(string $direction): Sort
    {
        $this->direction = $direction;
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
            'direction' => $this->getDirection()
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
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
