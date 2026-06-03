<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of Model
 */
interface BaseRepositoryInterface
{
    /**
     * @return Collection<int, TModel>
     */
    public function getAll(array $columns = ['*']): Collection;

    /**
     * @return TModel|null
     */
    public function find(int|string $id, array $columns = ['*']): ?Model;

    /**
     * @return TModel|null
     */
    public function findBy(array $criteria, array $columns = ['*']): ?Model;

    /**
     * @return TModel
     */
    public function create(array $attributes): Model;

    /**
     * @param TModel $model
     * @return TModel
     */
    public function update(Model $model, array $attributes): Model;

    /**
     * @param TModel $model
     * @return TModel
     */
    public function load(Model $model, array $relations): Model;

    /**
     * @param TModel $model
     */
    public function delete(Model $model): bool;
}
