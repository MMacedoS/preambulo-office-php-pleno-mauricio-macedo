<?php

namespace App\Traits;

trait ValidationTrait
{
    public function hasRequiredModelAttributes(object $model, array $keys): bool
    {
        if (empty($model)) {
            return false;
        }

        $attribs = $model->getAttributes();

        if (empty($attribs) || empty($keys)) {
            return false;
        }

        $required = true;
        foreach ($keys as $key) {
            if (!isset($attribs[$key])) {
                $required = false;
            }
        }

        return $required;
    }
}
