<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of Model
 *
 * @implements BaseRepositoryInterface<TModel>
 */
abstract class AbstractEloquentRepository implements BaseRepositoryInterface
{
    /** @var TModel */
    protected Model $model;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * @return class-string<TModel>
     */
    abstract protected function modelClass(): string;

    /**
     * @return void
     */
    protected function setModel(): void
    {
        $modelClass = $this->modelClass();
        $this->model = new $modelClass;
    }

    /**
     * @param array<int, string> $columns
     * @return Collection<int, TModel>
     */
    public function getAll(array $columns = ['*']): Collection
    {
        return $this->model->newQuery()->get($columns);
    }

    /**
     * @param int|string $id
     * @param array<int, string> $columns
     * @return TModel|null
     */
    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()->find($id, $columns);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<int, string> $columns
     * @return TModel|null
     */
    public function findBy(array $criteria, array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()->where($criteria)->first($columns);
    }

    /**
     * @param array<string, mixed> $attributes
     * @return TModel
     */
    public function create(array $attributes): Model
    {
        return $this->model->newQuery()->create($attributes);
    }

    /**
     * @param TModel $model
     * @param array<string, mixed> $attributes
     * @return TModel
     */
    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model->refresh();
    }

    /**
     * @param TModel $model
     * @param array<int, string> $relations
     * @return TModel
     */
    public function load(Model $model, array $relations): Model
    {
        return $model->load($relations);
    }

    /**
     * @param TModel $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return (bool)$model->delete();
    }
}
