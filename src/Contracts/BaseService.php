<?php

namespace Guesl\Admin\Contracts;

use Guesl\Admin\Models\Criterion;
use Guesl\Admin\Models\EagerLoading;
use Guesl\Admin\Models\Fuzzy;
use Guesl\Admin\Models\Scope;
use Guesl\Admin\Models\Sort;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface BaseService
 * @package Guesl\Admin\Contracts
 */
interface BaseService
{
    /**
     * Fetch page object by table's name , page size, searching info ,and ordering info;
     *
     * $modelClass : The  Class Name of eloquent model.
     *
     * @param string $modelClass
     * @param mixed $pagination
     * @param array<Criterion> $criteria
     * @param array<Sort> $sorts
     * @param array<Fuzzy> $searches
     * @param array<EagerLoading> $eagerLoadings
     * @param array<Scope> $scopes
     * @return LengthAwarePaginator|Collection
     */
    public function fetch($modelClass, $pagination, array $criteria, array $sorts, array $searches, array $eagerLoadings = [], array $scopes = []);

    /**
     * Fetch Model by id.
     * Eager Loading : Eager Loading attributes;
     *
     * @param string $modelClass
     * @param $id
     * @param array $eagerLoading
     * @return Model
     */
    public function retrieve(string $modelClass, $id, array $eagerLoading = []);

    /**
     * Create a new model(Persistence data).
     *
     * @param string $modelClass
     * @param array $data
     * @return Model
     */
    public function create(string $modelClass, array $data = []);

    /**
     * Update model by id.
     * $data : attributes which should be updated.
     *
     * @param string $modelClass
     * @param $id
     * @param array $data
     * @return Model
     */
    public function update(string $modelClass, $id, array $data = []);

    /**
     * Delete the model by id.
     *
     * @param string $modelClass
     * @param $id
     */
    public function delete(string $modelClass, $id);
}
