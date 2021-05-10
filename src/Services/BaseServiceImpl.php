<?php

namespace Guesl\Admin\Services;

use Guesl\Admin\Contracts\BaseService;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseServiceImpl
 * @package Guesl\Admin\Services
 */
class BaseServiceImpl implements BaseService
{
    use QueryHelper;

    /**
     * Fetch Model by id.
     * Eager Loading : Eager Loading attributes;
     *
     * @param string $modelClass
     * @param $id
     * @param array $eagerLoading
     * @return Model
     */
    public function retrieve(string $modelClass, $id, array $eagerLoading = [])
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
     * @param string $modelClass
     * @param array $data
     * @return Model
     */
    public function create(string $modelClass, array $data = [])
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
     * @param string $modelClass
     * @param $id
     * @param array $data
     * @return Model
     */
    public function update(string $modelClass, $id, array $data = [])
    {
        $result = null;

        $model = $modelClass::where((new $modelClass())->getKeyName(), $id)->first();
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
     * @param string $modelClass
     * @param $id
     */
    public function delete(string $modelClass, $id)
    {
        $model = $modelClass::where((new $modelClass())->getKeyName(), $id);
        $model->delete();
    }
}
