<?php

namespace App\Repositories\Traits;

trait ServiceTrait
{
    public function findById(int $id)
    {
        if (is_null($id)) {
            return null;
        }

        $cacheKey = $this->model->getTable() . '_' . $id;

        return $this->getFromCacheOrFetch(
            $cacheKey,
            fn() => $this->model->find($id),
            3600
        );
    }

    private function model()
    {
        $path_service_class = explode('\\', get_class($this));

        if (empty($path_service_class)) {
            return null;
        }

        $model_class = str_replace(
            'Service',
            '',
            $path_service_class[count($path_service_class) - 1]
        );

        $model_class = str_replace(
            'Repository',
            '',
            $model_class
        );

        if (count($path_service_class) == 4) {
            $model_class = $path_service_class[2] . "\\" . $model_class;
        }

        if (count($path_service_class) == 5) {
            $model_class = $path_service_class[3] . "\\" . $model_class;
        }

        return "App\Models\\" . $model_class;
    }

    public function findByUuid(string $uuid)
    {
        $cacheKey = $this->model->getTable() . '_uuid_' . $uuid;

        return $this->getFromCacheOrFetch(
            $cacheKey,
            fn() => $this->model->where('uuid', $uuid)->first(),
            3600
        );
    }

    public function count()
    {
        return $this->model->count();
    }
}
