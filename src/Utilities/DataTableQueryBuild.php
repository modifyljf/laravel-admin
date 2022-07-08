<?php

namespace Modifyljf\Admin\Utilities;

use Modifyljf\Admin\Models\Criterion;
use Modifyljf\Admin\Models\EagerLoading;
use Modifyljf\Admin\Models\Fuzzy;
use Modifyljf\Admin\Models\Pagination;
use Modifyljf\Admin\Models\Scope;
use Modifyljf\Admin\Models\Sort;
use Illuminate\Container\Container;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Trait QueryBuild
 * @package Modifyljf\Query\Utilities
 */
trait DataTableQueryBuild
{
    /**
     * Get pagination info from request.
     *
     * @param Request $request
     * @return Pagination
     */
    public static function getPageInfo(Request $request)
    {
        $pagination = $request->get('pagination');

        if (isset($pagination)) {
            $pagination = is_array($pagination) ? $pagination : json_decode($pagination, true);
            $page = $pagination['page'] ?? 1;
            $pageSize = $pagination['perpage'] ?? Pagination::DEFAULT_PAGE_SIZE;
        } else {
            return null;
        }

        return new Pagination($page, $pageSize);
    }

    /**
     * Get search columns from request.
     *
     * @param Request $request
     * @return array<Fuzzy>
     */
    public static function getSearches(Request $request)
    {
        $searchColumns = $request->get('search_columns');
        $query = $request->get('query');
        $query = (is_array($query) ? $query : json_decode($query, true)) ?? [];

        $fuzzyArray = [];

        if (isset($searchColumns)) {
            if (array_key_exists('generalSearch', $query) && !empty($query['generalSearch'])) {
                $searchColumns = is_array($searchColumns) ? $searchColumns : json_decode($searchColumns, true);

                foreach ($searchColumns as $searchColumn) {
                    if (str_contains($searchColumn, '.')) {
                        $columnArr = explode('.', $searchColumn);
                        //camel case relations except last column
                        for ($i = 0; $i < sizeof($columnArr) - 1; $i++) {
                            $columnArr[$i] = Str::camel($columnArr[$i]);
                        }
                        $searchColumn = implode(".", $columnArr);
                    }
                    $fuzzy = new Fuzzy($searchColumn, $query['generalSearch']);
                    array_push($fuzzyArray, $fuzzy);
                }
            }
        }

        return $fuzzyArray;
    }

    /**
     * Get sorts from request.
     *
     * @param Request $request
     * @return array<Sort>
     */
    public static function getSorts(Request $request)
    {
        $result = [];

        # Get the sort columns by 'sort' parameters.
        $sorts = $request->get('sort');
        if (isset($sorts)) {
            $sorts = is_array($sorts) ? $sorts : json_decode($sorts, true);
            $name = Arr::exists($sorts, 'field') ? $sorts['field'] : '';

            if (str_contains($name, '.')) {
                $columnArr = explode('.', $name);
                //camel case relations except last column
                for ($i = 0; $i < sizeof($columnArr) - 1; $i++) {
                    $columnArr[$i] = Str::camel($columnArr[$i]);
                }
                $name = implode(".", $columnArr);
            }

            $direction = Arr::exists($sorts, 'sort') ? $sorts['sort'] : 'desc';
            $sort = new Sort($name, $direction);
            array_push($result, $sort);
        }
        return $result;
    }

    /**
     * Get filters from request.
     *
     * @param Request $request
     * @return array<Criterion>
     */
    public static function getCriterion(Request $request)
    {
        $criteria = self::formatFilters($request);

        // Get the search columns by 'filters' parameters.
        $filterColumns = $request->get('query');
        if (isset($filterColumns)) {
            $filterColumns = is_array($filterColumns) ? $filterColumns : json_decode($filterColumns, true);

            if (array_key_exists('generalSearch', $filterColumns)) {
                unset($filterColumns['generalSearch']);
            }

            foreach ($filterColumns as $column => $filter) {
                // If select all continue next filter column.
                if (is_array($filter) && $filter[0] == null) continue;

                if (is_array($filter) && sizeof($filter) > 1) {
                    if ($column == 'created_at' || $column == 'updated_at' || $column == 'ordered_at') {
                        $operation = "between";
                    } else {
                        $operation = "in";
                    }
                } else {
                    $operation = "=";
                }

                // Multi-selector filter is always an array.
                if (is_array($filter) && sizeof($filter) == 1) {
                    $value = $filter[0];
                } else {
                    $value = $filter;
                }

                $name = $column;
                $criterion = new Criterion($name, $operation, $value, false);
                array_push($criteria, $criterion);
            }
        }

        return $criteria;
    }

    /**
     * Get the criteria from the data table filters param.
     *
     * @param Request $request
     * @return array<Criterion>
     */
    private static function formatFilters(Request $request)
    {
        $criteria = [];

        // Get the search columns by 'filters' parameters.
        if ($request->has('filters')) {
            $filters = $request->get('filters');
            foreach ($filters as $filter) {
                $name = $filter['name'];
                $operation = $filter['operation'];
                $value = $filter['value'];
                $exclusive = $filter['exclusive'];
                $criterion = new Criterion($name, $operation, $value, $exclusive);

                array_push($criteria, $criterion);
            }
        }

        return $criteria;
    }

    /**
     * Get eager loadings from request.
     *
     * @param Request $request
     * @return array<EagerLoading>
     */
    public static function getEagerLoadings(Request $request)
    {
        $result = [];

        # Get the sort columns by 'sort' parameters.
        $eagerLoadings = $request->get('eager_loadings');
        if (isset($eagerLoadings)) {
            $eagerLoadings = is_array($eagerLoadings) ? $eagerLoadings : json_decode($eagerLoadings, true);

            foreach ($eagerLoadings as $eagerLoading) {
                $name = Arr::exists($eagerLoading, 'name') ? $eagerLoading['name'] : '';
                $el = new EagerLoading($name);
                array_push($result, $el);
            }

        }

        return $result;
    }

    /**
     * Get the scopes.
     *
     * @param Request $request
     * @return array<Scope>
     */
    public static function getScopes(Request $request)
    {
        $result = [];

        $scopes = $request->get('scopes');
        if (isset($scopes)) {
            $scopes = is_array($scopes) ? $scopes : json_decode($scopes, true);

            foreach ($scopes as $scope) {
                $name = Arr::exists($scope, 'name') ? $scope['name'] : '';
                $parameters = Arr::exists($scope, 'parameters') ? $scope['parameters'] : [];
                $scope = new Scope($name, $parameters);
                array_push($result, $scope);
            }
        }

        return $result;
    }

    /**
     * Get the model related table name.
     *
     * @param string $name
     * @return string
     */
    public static function getModelTable(string $name)
    {
        if (strpos($name, '.')) {
            $names = explode('.', $name);
            $name = array_pop($names);
        }

        $modelClassName = config("query.models.$name");
        if (isset($modelClassName)) {
            $model = new $modelClassName;
            $tableName = $model->getTable();
        } else {
            $modelClassName = Container::getInstance()->getNamespace() . 'Models\\' . ucfirst(Str::singular($name));
            if (class_exists($modelClassName)) {
                $model = new $modelClassName;
                $tableName = $model->getTable();
            } else {
                $tableName = '';
            }
        }

        return $tableName;
    }

    /**
     * Get the model related connection.
     *
     * @param string $name
     * @return string
     */
    public static function getModelDB(string $name)
    {
        $defaultConnectionName = env('DB_CONNECTION', 'mysql');

        if (strpos($name, '.')) {
            $names = explode('.', $name);
            $name = array_pop($names);
        }

        $modelClassName = config("query.models.$name");

        if (isset($modelClassName)) {
            $model = new $modelClassName;
            $defaultConnectionName = $model->getConnectionName();
        }

        return config('database.connections.' . $defaultConnectionName . ".database");
    }


    /**
     * Create eagerLoadings.
     *
     * @param array<string> $relations
     * @return array<EagerLoading>
     */
    public static function createEagerLoadings(array $relations)
    {
        $eagerLoadings = [];

        if (isset($relations)) {
            foreach ($relations as $relation) {
                $el = new EagerLoading($relation);
                array_push($eagerLoadings, $el);
            }
        }

        return $eagerLoadings;
    }

    /**
     * Create Scopes.
     *
     * @param array $scopes
     * @return array<Scope>
     */
    public static function createScopes(array $scopes)
    {
        $result = [];

        if (isset($scopes)) {
            foreach ($scopes as $scope) {
                $name = Arr::exists($scope, 'name') ? $scope['name'] : '';
                $parameters = Arr::exists($scope, 'parameters') ? $scope['parameters'] : [];
                $scope = new Scope($name, $parameters);
                array_push($result, $scope);
            }
        }

        return $result;
    }

    /**
     * Format the result to the special page object.
     *
     * @param Paginator|array $paginator
     * @param bool $isPaginator
     * @return array
     */
    public static function formatPageObject($paginator, bool $isPaginator = true)
    {
        if ($isPaginator) {
            $meta = [
                'page' => $paginator->currentPage(),
                'pages' => $paginator->lastPage(),
                'perpage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ];
            $result['meta'] = $meta;
            $result['data'] = $paginator->items();

            return $result;
        } else {
            return $paginator;
        }
    }

    /**
     * @param $data
     * @return array
     */
    public static function formatRemotePagination($data)
    {
        if ($data) {
            $meta = [
                'page' => $data["current_page"],
                'pages' => $data["last_page"],
                'perpage' => $data["per_page"],
                'total' => $data["total"],
            ];
            $result['meta'] = $meta;
            $result['data'] = $data["data"];

            return $result;
        }
        return [];
    }
}
