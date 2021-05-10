<?php

namespace Guesl\Admin\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class EagerLoading
 * @package Guesl\Admin\Models
 */
class EagerLoading implements JsonSerializable, Jsonable, Arrayable
{
    /**
     * @var string
     */
    private $name;

    /**
     * EagerLoading constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
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
     * @return EagerLoading
     */
    public function setName(string $name): EagerLoading
    {
        $this->name = $name;
        return $this;
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
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName()
        ];
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
