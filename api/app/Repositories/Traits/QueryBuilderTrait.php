<?php

namespace App\Repositories\Traits;

trait QueryBuilderTrait
{
    protected function buildQuery(array $criteria, array $orderBy, array $orWhereCriteria)
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $field => $value) {
            $query->where($field, '=', $value);
        }

        if (!empty($orWhereCriteria)) {
            $query->where(function ($q) use ($orWhereCriteria) {
                foreach ($orWhereCriteria as $index => $condition) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $q->{$method}($condition['field'], $condition['operator'], $condition['value']);
                }
            });
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

    protected function makeCacheKey(...$params)
    {
        return $this->model->getTable() . '_all';
    }
}
