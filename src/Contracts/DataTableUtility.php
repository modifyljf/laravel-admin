<?php

namespace Guesl\Admin\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;

/**
 * Class DataTableUtility
 * @package Guesl\Admin\Contracts
 */
class DataTableUtility
{
    /**
     * Default page size of the combo selector.
     */
    const DEFAULT_PAGE_SIZE = 10;

    /**
     * Get pagination info from request.
     *
     * @param Request $request
     * @return array
     */
    public static function getPageInfo(Request $request)
    {
        $pagination = $request->get('pagination');

        if (isset($pagination)) {
            $pagination = json_decode($pagination, true);
            $page = $pagination['page'];
            $pageSize = $pagination['perpage'];

        } else {
            $page = 1;
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }

        return ['page' => $page, 'pageSize' => $pageSize];
    }

    /**
     * Get filter columns.
     *
     * @param Request $request
     * @return array
     */
    public static function getFilterColumns($request)
    {
        $filterColumn = [];

        $query = $request->get('query');
        if (isset($query)) {
            $query = json_decode($query, true);

            if (array_key_exists('generalSearch', $query)) {
                unset($query['generalSearch']);
                $filterColumn = $query;

            }
        }
        return $filterColumn;
    }

    /**
     * Get search columns.
     *
     * @param Request $request
     * @return array
     */
    public static function getSearchColumns(Request $request)
    {
        $searchColumns = [];
        $query = $request->get('query');
        $searchColumnsName = $request->get('search_columns');
        if (isset($query)) {
            $query = json_decode($query, true);

            if (isset($searchColumnsName)) {
                if (array_key_exists('generalSearch', $query)) {
                    $search = $query['generalSearch'];
                    foreach ($searchColumnsName as $columnName) {
                        $searchColumns[$columnName] = $search;
                    }
                }
            }
        }

        return $searchColumns;
    }

    /**
     * Get sort column and type.
     *
     * @param Request $request
     * @return array
     */
    public static function getSortColumn(Request $request)
    {
        $sortColumn = [];
        $sort = $request->get('sort');
        if (isset($sort)) {
            $sort = json_decode($sort, true);

            $sortColumn[$sort['field']] = $sort['sort'];
        }

        return $sortColumn;
    }

    /**
     * Format the result to the special page object.
     *
     * @param Paginator $result
     * @return Paginator
     */
    public static function formatPageObject(Paginator $result)
    {
        $meta = [
            'page' => $result->currentPage(),
            'pages' => $result->count(),
            'perpage' => $result->perPage(),
            'total' => $result->total(),
        ];

        $result = $result->toArray();
        $result['meta'] = $meta;

        return $result;
    }
}
