<?php

namespace Guesl\Admin\Http\Controllers;

use Guesl\Admin\Contracts\BaseService;
use Guesl\Admin\Contracts\DataTableUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class ComboController
 * @package App\Http\Controllers\System
 */
class ComboController extends Controller
{
    /**
     * @var BaseService $service
     */
    protected $service;

    /**
     * Controller constructor.
     * @param BaseService $service
     */
    public function __construct(BaseService $service)
    {
        $this->service = $service;
    }

    /**
     * Bootstrap selector search method.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function comboSearch(Request $request)
    {
        if ($request->expectsJson()) {
            $modelClass = $request->get('model_class');
            $pageInfo = DataTableUtility::getPageInfo($request);
            $filterColumns = DataTableUtility::getFilterColumns($request);
            $searchColumns = DataTableUtility::getSearchColumns($request);
            $sortColumn = DataTableUtility::getSortColumn($request);

            $result = $this->service->fetch($modelClass, $pageInfo, $filterColumns, $sortColumn, $searchColumns);

            return response()->json($result);
        }
    }
}
