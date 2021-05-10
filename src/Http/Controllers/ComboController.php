<?php

namespace Guesl\Admin\Http\Controllers;

use Guesl\Admin\Contracts\BaseService;
use Guesl\Admin\Utilities\DataTableQueryBuild;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class ComboController
 * @package App\Http\Controllers\System
 */
class ComboController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, DataTableQueryBuild;

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
     * @return JsonResponse
     */
    public function comboSearch(Request $request)
    {
        if ($request->expectsJson()) {
            $modelClass = $request->get('model_class');

            $pageInfo = DataTableQueryBuild::getPageInfo($request);
            $criteria = DataTableQueryBuild::getCriterion($request);
            $searches = DataTableQueryBuild::getSearches($request);
            $sorts = DataTableQueryBuild::getSorts($request);
            $eagerLoadings = DataTableQueryBuild::getEagerLoadings($request);

            $result = $this->service->fetch($modelClass, $pageInfo, $criteria, $sorts, $searches, $eagerLoadings);
            return response()->json($result);
        }
    }
}
