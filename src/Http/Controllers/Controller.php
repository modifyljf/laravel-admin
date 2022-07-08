<?php

namespace Modifyljf\Admin\Http\Controllers;

use Modifyljf\Admin\Contracts\Constant;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 * @package Modifyljf\Admin\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Init setting of navigator.
     *
     * @param null $module
     * @param null $menu
     */
    protected function initSetting($menu = null, $module = null)
    {
        if (isset($menu)) {
            request()->session()->flash('menu', $menu);
        }

        if (isset($module)) {
            request()->session()->flash('module', $module);
        }
    }

    /**
     * Session hint after updating.
     *
     * @param string $hint
     */
    protected function afterUpdated($hint = 'Updated Successfully.')
    {
        request()->session()->flash(Constant::SESSION_KEY_SUCCESS, $hint);
    }

    /**
     * Session hint after storing.
     *
     * @param string $hint
     */
    protected function afterStored($hint = 'Created Successfully.')
    {
        request()->session()->flash(Constant::SESSION_KEY_SUCCESS, $hint);
    }

    /**
     * Session hint after destroying.
     *
     * @param string $hint
     */
    protected function afterDestroyed($hint = 'Destroyed Successfully.')
    {
        request()->session()->flash(Constant::SESSION_KEY_SUCCESS, $hint);
    }

    /**
     * Session hint after listing.
     *
     * @param string $hint
     */
    protected function afterQueried($hint = 'Listing records Successfully.')
    {
        request()->session()->flash(Constant::SESSION_KEY_SUCCESS, $hint);
    }

    /**
     * Session hint after updating.
     *
     * @param string $hint
     */
    protected function afterFailed($hint = 'Updated Failed.')
    {
        request()->session()->flash(Constant::SESSION_KEY_ERROR, $hint);
    }
}
