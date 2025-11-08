<?php

namespace App\Repositories\Traits;

trait ServiceTrait
{
    public function findById(int $id)
    {
        if (is_null($id)) {
            return null;
        }

        $class = $this->model();
        return $class::find($id);
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
        return $this
            ->model()::where(
                [
                    'uuid' => $uuid
                ]
            )
            ->first();
    }

    public function count()
    {
        return $this->model()::count();
    }
}
