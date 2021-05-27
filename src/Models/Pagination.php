<?php

namespace Guesl\Admin\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class Pagination
 * @package Guesl\Admin\Models
 */
class Pagination implements JsonSerializable, Jsonable, Arrayable
{
    const DEFAULT_PAGE_SIZE = 20;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * Pagination constructor.
     *
     * @param int $page
     * @param int|null $pageSize
     */
    public function __construct(int $page, int $pageSize = null)
    {
        $this->page = $page;
        $this->pageSize = $pageSize ?? static::DEFAULT_PAGE_SIZE;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return Pagination
     */
    public function setPage(int $page): Pagination
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize ?: self::DEFAULT_PAGE_SIZE;
    }

    /**
     * @param int $pageSize
     * @return Pagination
     */
    public function setPageSize(int $pageSize): Pagination
    {
        $this->pageSize = $pageSize;
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
            'page' => $this->page,
            'page_size' => $this->pageSize,
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
