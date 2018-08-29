<?php

namespace Guesl\Admin\Services;

use Guesl\Admin\Contracts\BaseService;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class BaseServiceImpl
 * @package Guesl\Admin\Services
 */
class BaseServiceImpl implements BaseService
{
    /**
     * Fetch page object by table's name , page size, searching info ,and ordering info;
     *
     * $modelClass : The  Class Name of eloquent model.
     * Page Info : page num and page size.
     * Filter Columns : Key : column's name, Value : filter value(The filter could be array, like ['value' => ['1,2,3'], 'operation' => 'in']).
     * Search Columns :  Key : column's name, Value : search value.
     * Order Columns : Key : column's name, Value : ordering type ('asc', or 'desc')
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
        Log::debug(get_class($this) . '::fetch => Fetch page object by table\'s name , page size, searching info ,and ordering info.');

        $query = $modelClass::query();

        if (isset($scopes) && sizeof($scopes) > 0) {
            foreach ($scopes as $scope) {
                if (isset($scope['parameters']))
                    $query->{$scope['scope']}($scope['parameters']);
                else
                    $query->{$scope['scope']}();
            }
        }

        if (isset($filterColumn) && sizeof($filterColumn) > 0) {
            $query->where(function ($q) use ($filterColumn) {
                foreach ($filterColumn as $column => $filter) {
                    if (strpos($column, '.') !== false) {
                        $relationColumn = explode('.', $column);
                        $className = Container::getInstance()->getNamespace() . 'Models\\' . ucfirst($relationColumn[0]);
                        if (class_exists($className)) {
                            $relationTable = (new $className)->getTable();
                        } else {
                            $relationTable = $relationColumn[0];
                        }
                        if (isset($filter)) {
                            if (is_array($filter) && array_get($filter, 'type') == false) {
                                $q->whereDoesntHave($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $filter) {
                                    $this->generateCriteria($relateQuery, $relationTable . '.' . $relationColumn[1], $filter);
                                });
                            } else {
                                $q->whereHas($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $filter) {
                                    $this->generateCriteria($relateQuery, $relationTable . '.' . $relationColumn[1], $filter);
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
                    if (strpos($column, '.') !== false) {
                        $relationColumn = explode('.', $column);
                        $className = Container::getInstance()->getNamespace() . 'Models\\' . ucfirst($relationColumn[0]);
                        $relationTable = (new $className)->getTable();
                        $q->orWhereHas($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $search) {
                            $relateQuery->where($relationTable . '.' . $relationColumn[1], 'like', '%' . $search . '%');
                        });
                    } else {
                        $q->orWhere($column, 'like', '%' . $search . '%');
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
                if (strpos($column, '.') !== false) {
                    $relationColumn = explode('.', $column);
                    $query->with([$relationColumn[0] => function ($relateQuery) use ($relationColumn, $dir) {
                        $relateQuery->orderBy($relationColumn[1], $dir);
                    }]);

                } else {
                    $query->orderBy($column, $dir);
                }
            }
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        if (isset($pageInfo) && array_get($pageInfo, 'pageSize')) { // if the page info exists , then fetch the pagination info.
            $perPage = $pageInfo['pageSize'];
            $page = $pageInfo['page'];
            $result = $query->paginate($perPage, null, null, $page);
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
            $operation = array_get($filter, 'operation');
            $value = array_get($filter, 'value');
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
            if (!isset($filter)) {
                $q->whereNull($column);
            } else {
                $q->where($column, '=', $filter);
            }
        }

        return $q;
    }

    /**
     * Fetch Model by id.
     * Eager Loading : Eager Loading attributes;
     *
     * @param $modelClass
     * @param $id
     * @param array $eagerLoading
     * @return Model|null
     */
    public function retrieve($modelClass, $id, $eagerLoading = [])
    {
        $result = null;
        $query = $modelClass::where((new $modelClass())->getKeyName(), $id);
        if (isset($eagerLoading) && sizeof($eagerLoading) > 0) {
            foreach ($eagerLoading as $value) {
                $query = $query->with($value);
            }
        }
        $result = $query->first();
        return $result;
    }

    /**
     * Create a new model(Persistence data).
     *
     * @param $modelClass
     * @param $data
     * @return Model
     */
    public function create($modelClass, $data)
    {
        $model = new $modelClass();
        foreach ($data as $col => $value) {
            $model->{$col} = $value;
        }
        $model->save();

        return $model;
    }

    /**
     * Update model by id.
     * $data : attributes which should be updated.
     *
     * @param $modelClass
     * @param $id
     * @param $data
     * @return Model
     */
    public function update($modelClass, $id, $data)
    {
        $result = null;
        $model = $modelClass::find($id);
        if ($model) {
            foreach ($data as $key => $value) {
                $model->$key = $value;
            }
            $model->save();
        }
        $result = $model;

        return $result;
    }

    /**
     * Delete the model by id.
     *
     * @param $modelClass
     * @param $id
     */
    public function delete($modelClass, $id)
    {
        $modelClass::where((new $modelClass())->getKeyName(), $id)->delete();
    }
}
