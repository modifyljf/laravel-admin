<?php

namespace Guesl\Admin\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2018/07/23
 */
class DataTableUtility
{
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
            $page = $pagination['page'];
            $pageSize = $pagination['perpage'];
        } else {
            $page = 1;
            $pageSize = Constant::DEFAULT_PAGE_SIZE;
        }

        return [$page, $pageSize];
    }

    /**
     * Get filter columns.
     *
     * @param Request $request
     * @return array
     */
    public static function getFilterColumns($request)
    {
        $query = $request->get('query');
        if (isset($query) && array_key_exists('generalSearch', $query)) {
            unset($query['generalSearch']);
        }

        return $query;
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
        if (isset($query) && isset($searchColumnsName)) {
            $search = $query['generalSearch'];
            foreach ($searchColumnsName as $columnName) {
                $searchColumns[$columnName] = $search;
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
