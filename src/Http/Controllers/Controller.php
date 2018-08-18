<?php

namespace Guesl\Admin\Http\Controllers;

use Guesl\Admin\Contracts\BaseService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * BaseController constructor.
     * @var BaseService
     */
    protected $baseService;

    /**
     * BaseController constructor.
     * @param BaseService $baseService
     */
    public function __construct(BaseService $baseService)
    {
        $this->baseService = $baseService;
    }

    /**
     * Init setting of navigator.
     *
     * @param null $module
     * @param null $menu
     */
    protected function initSetting($menu = null, $module = null)
    {
        if (isset($menu)) {
            request()->session()->flash("menu", $menu);
        }

        if (isset($module)) {
            request()->session()->flash("module", $module);
        }
    }

    /**
     * Bootstrap select combo search.
     *
     * @param Request $request
     */
    public function comboSearch(Request $request)
    {

    }
}