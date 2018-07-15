<?php

namespace Guesl\Admin\Services;

use Guesl\Admin\Contracts\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2017/4/30
 * Time: 14:31
 */
class BaseServiceImpl implements BaseService
{
    /**
     * Fetch page object by table's name , page size, searching info ,and ordering info;
     *
     * $modelClass : The  Class Name of eloquent model.
     * Page Info : page num and page size.
     * Filter Columns : Key : column's name, Value : filter value.
     * Search Columns :  Key : column's name, Value : search value
     * Order Columns : Key : column's name, Value : ordering type ("asc", or "desc")
     * Eager Loading : Eager Loading attributes;
     *
     * @param $modelClass
     * @param array $pageInfo
     * @param array $filterColumn
     * @param array $orderColumn
     * @param array $searchColumn
     * @param array $eagerLoading
     * @param array $scopes
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection
     */
    public function fetch($modelClass, $pageInfo = [], $filterColumn = [], $orderColumn = [], $searchColumn = [], $eagerLoading = [], $scopes = [])
    {
        Log::debug(get_class($this) . "::fetch => Fetch page object by table's name , page size, searching info ,and ordering info.");

        $query = $modelClass::whereRaw("1=1");

        if (isset($scopes) && sizeof($scopes) > 0) {
            foreach ($scopes as $scope) {
                if (isset($scope["parameters"]))
                    $query->{$scope["scope"]}($scope["parameters"]);
                else
                    $query->{$scope["scope"]}();
            }
        }

        if (isset($filterColumn) && sizeof($filterColumn) > 0) {
            $query->where(function ($q) use ($filterColumn) {
                foreach ($filterColumn as $column => $filter) {
                    if (strpos($column, ".") !== false) {
                        $relationColumn = explode(".", $column);
                        $className = "App\\Models\\" . ucfirst($relationColumn[0]);
                        if (class_exists($className)) {
                            $relationTable = (new $className)->getTable();
                        } else {
                            $relationTable = $relationColumn[0];
                        }
                        if (isset($filter)) {
                            if (is_array($filter) && array_get($filter, "type") == BaseModel::STATUS_NEGATIVE) {
                                $q->whereDoesntHave($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $filter) {
                                    $this->generateCriteria($relateQuery, $relationTable . "." . $relationColumn[1], $filter);
                                });
                            } else {
                                $q->whereHas($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $filter) {
                                    $this->generateCriteria($relateQuery, $relationTable . "." . $relationColumn[1], $filter);
                                });
                            }
                        } else {
                            $q->has($relationColumn[0]);
                        }
                    } else {
                        $q = $this->generateCriteria($q, $column, $filter);
                    }
                }
            });
        }

        if (isset($searchColumn) && sizeof($searchColumn) > 0) {
            $query->where(function ($q) use ($searchColumn) {
                foreach ($searchColumn as $column => $search) {
                    if (strpos($column, ".") !== false) {
                        $relationColumn = explode(".", $column);
                        $className = "App\\Models\\" . ucfirst($relationColumn[0]);
                        $relationTable = (new $className)->getTable();
                        $q->orWhereHas($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $search) {
                            $relateQuery->where($relationTable . "." . $relationColumn[1], "like", "%" . $search . "%");
                        });
                    } else {
                        $q->orWhere($column, "like", "%" . $search . "%");
                    }
                }
            });
        }

        if (isset($eagerLoading) && sizeof($eagerLoading) > 0) {
            foreach ($eagerLoading as $value) {
                $query = $query->with($value);
            }
        }

        if (isset($orderColumn) && sizeof($orderColumn) > 0) {
            foreach ($orderColumn as $column => $dir) {
                if (strpos($column, ".") !== false) {
                    $relationColumn = explode(".", $column);
                    $query->with([$relationColumn[0] => function ($relateQuery) use ($relationColumn, $dir) {
                        $relateQuery->orderBy($relationColumn[1], $dir);
                    }]);

                } else {
                    $query->orderBy($column, $dir);
                }
            }
        } else {
            $query->orderBy("updated_at", "desc");
        }

        if (isset($pageInfo) && array_get($pageInfo, "pageSize")) { // if the page info exists , then fetch the pagination info.
            $pageSize = $pageInfo["pageSize"];
            $result = $query->paginate($pageSize);
        } else {
            $result = $query->get();
        }

        return $result;
    }

    /**
     * @param $q
     * @param $column
     * @param $filter
     *
     * @return object
     */
    protected function generateCriteria($q, $column, $filter)
    {
        if (is_array($filter)) {
            $operation = array_get($filter, "operation");
            $value = array_get($filter, "value");
            if ("isNull" == $operation) {
                $q->whereNull($column);
            } else if ("isNotNull" == $operation) {
                $q->whereNotNull($column);
            } else if ("in" == $operation && is_array($value)) {
                $q->whereIn($column, $value);
            } else if ("notIn" == $operation && is_array($value)) {
                $q->whereNotIn($column, $value);
            } else if ("between" == $operation && is_array($value)) {
                $q->whereBetween($column, $value);
            } else {
                $q->where($column, $operation, $value);
            }
        } else {
            if (!isset($filter)) {
                $q->whereNull($column);
            } else {
                $q->where($column, "=", $filter);
            }
        }

        return $q;
    }
}
