<?php

namespace Guesl\Admin\Services;

use Guesl\Admin\Models\Criterion;
use Guesl\Admin\Models\EagerLoading;
use Guesl\Admin\Models\Fuzzy;
use Guesl\Admin\Models\Scope;
use Guesl\Admin\Models\Sort;
use Guesl\Admin\Utilities\DataTableQueryBuild;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Trait QueryHelper
 * @package Guesl\Admin\Services
 */
trait QueryHelper
{
    use DataTableQueryBuild;

    /**
     * Fetch page object by table's name , page size, searching info ,and ordering info;
     *
     * $modelClass : The  Class Name of eloquent model.
     *
     * @param string $modelClass
     * @param mixed $pagination
     * @param array<Criterion> $criteria
     * @param array<Sort> $sorts
     * @param array<Fuzzy> $searches
     * @param array<EagerLoading> $eagerLoadings
     * @param array<Scope> $scopes
     * @return LengthAwarePaginator|Collection
     */
    public function fetch($modelClass, $pagination, array $criteria, array $sorts, array $searches, array $eagerLoadings = [], array $scopes = [])
    {
        Log::debug(get_class($this) . '::fetch => Fetch page object by table\'s name , page size, searching info ,and ordering info.');

        $query = $modelClass::query();
        $curTable = (new $modelClass)->getTable();

        if (isset($scopes) && sizeof($scopes) > 0) {
            foreach ($scopes as $scope) {
                $parameters = $scope->getParameters();
                $scopeName = $scope->getName();
                if (!empty($parameters))
                    $query->{$scopeName}($parameters);
                else
                    $query->{$scopeName}();
            }
        }

        if (isset($criteria) && sizeof($criteria) > 0) {
            $query->where(function ($q) use ($criteria) {
                foreach ($criteria as $criterion) {
                    $name = $criterion->getName();
                    $columns = explode('.', $name);

                    $column = Str::lower(array_pop($columns));
                    $relations = implode('.', $columns);
                    $table = DataTableQueryBuild::getModelTable($relations);

                    //if relations presented in $name
                    if (strpos($name, '.') !== false && $table !== "") {
                        if ('' != $criterion->getValue()) {
                            if ($criterion->isExclusive()) {
                                $q->whereDoesntHave($relations, function ($relateQuery) use ($criterion, $column, $table) {
                                    $this->generateCriteria($relateQuery, $criterion, $column);
                                });
                            } else {
                                $q->whereHas($relations, function ($relateQuery) use ($criterion, $column, $table) {
                                    $this->generateCriteria($relateQuery, $criterion, $column);
                                });
                            }
                        } else {
                            $q->has($relations);
                        }
                    } else {
                        $q = $this->generateCriteria($q, $criterion, $name);
                    }
                }
            });
        }

        if (isset($searches) && sizeof($searches) > 0) {
            $query->where(function ($q) use ($searches, $curTable) {
                foreach ($searches as $search) {
                    $searchColumn = $search->getName();
                    $searchValue = $search->getValue();
                    if (strpos($searchColumn, '.') !== false) {
                        $columns = explode('.', $searchColumn);

                        $column = array_pop($columns);
                        $relations = implode('.', $columns);

                        $q->orWhereHas($relations, function ($relateQuery) use ($column, $searchValue) {
                            $relateQuery->where($column, 'like', '%' . $searchValue . '%');
                        });
                    } else {
                        $q->orWhere($curTable.'.'.$searchColumn, 'like', '%' . $searchValue . '%');
                    }
                }
            });
        }

        if (isset($eagerLoadings) && sizeof($eagerLoadings) > 0) {
            foreach ($eagerLoadings as $eagerLoading) {
                $query = $query->with([$eagerLoading->getName()]);
            }
        }

        if (isset($sorts) && sizeof($sorts) > 0) {
            foreach ($sorts as $sort) {
                $sortColumn = $sort->getName();
                $dir = $sort->getDirection();
                if (strpos($sortColumn, '.') !== false) {
                    $columns = explode('.', $sortColumn);

                    $column = array_pop($columns);
                    $relations = implode('.', $columns);
                    //one to many
                    $query->with([$relations => function ($relateQuery) use ($column, $dir) {
                        $relateQuery->orderBy($column, $dir);
                    }]);
                } else {
                    $query->orderBy($sortColumn, $dir);
                }
            }
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        if (isset($pagination)) { // if the page info exists , then fetch the pagination info.
            $perPage = $pagination->getPageSize();
            $page = $pagination->getPage();
            $result = $query->paginate($perPage, ['*'], 'page', $page);

        } else {
            $result = $query->get();
        }

        return $result;
    }

    /**
     * Generate criteria.
     *
     * @param $q
     * @param Criterion $criterion
     * @param $column
     * @return object
     */
    protected function generateCriteria($q, Criterion $criterion, $column)
    {
        if ('' !== $criterion->getValue()) {
            $operation = $criterion->getOperation();
            $value = $criterion->getValue();

            if ('isNull' == $operation) {
                $q->whereNull($column);
            } else if ('isNotNull' == $operation) {
                $q->whereNotNull($column);
            } else if ('in' == $operation && is_array($value)) {
                $q->whereIn($column, $value);
            } else if ('notIn' == $operation && is_array($value)) {
                $q->whereNotIn($column, $value);
            } else if ('between' == $operation && is_array($value)) {
                $q->whereBetween($column, $value);
            } else {
                $q->where($column, $operation, $value);
            }
        } else {
            $q->whereNull($column);
        }

        return $q;
    }
}
