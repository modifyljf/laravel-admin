<?php

namespace Guesl\Admin\Models;

use ArrayIterator;

/**
 * Class EagerLoading
 * @package Guesl\Admin\Models
 */
class QueryList extends ArrayIterator
{
    private $queryArray;

    /**
     * QueryList constructor.
     * @param $queryArray
     */
    public function __construct($queryArray = [])
    {
        $this->queryArray = $queryArray;
        parent::__construct($queryArray);
    }

    /**
     * @return array
     */
    public function toParams()
    {
        $result = [];

        while ($this->valid()) {
            $current = $this->current();
            $currentType = get_class($current);
            $paramArray = $current->toArray();

            switch ($currentType) {
                case Pagination::class:
                    $result['pagination'] = $paramArray;
                    break;
                case EagerLoading::class:
                    $result['eager_loadings'][] = $paramArray;
                    break;
                case Criterion::class:
                    $result['filters'][] = $paramArray;
                    break;
                case Fuzzy::class:
                    $result['searches'][] = $paramArray;
                    break;
                case Scope::class:
                    $result['scopes'][] = $paramArray;
                    break;
                case Sort::class:
                    $result['sorts'][] = $paramArray;
                    break;
                default:
                    $result['filters'][] = $paramArray;
                    break;
            }
            $this->next();
        }

        return $result;
    }
}
