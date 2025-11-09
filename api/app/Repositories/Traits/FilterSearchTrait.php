<?php

namespace App\Repositories\Traits;

trait FilterSearchTrait
{
    public function prepareFilters(array $filters, array $fieldMapping = []): array
    {
        $defaultMapping = [
            'title' => 'titulo',
            'name' => 'name',
            'email' => 'email',
        ];

        $mapping = array_merge($defaultMapping, $fieldMapping);

        $criteria = [];
        $orWhere = [];

        foreach ($filters as $field => $value) {
            if (isset($mapping[$field]) && in_array($field, ['title', 'name', 'email'])) {
                $orWhere[] = [
                    'field' => $mapping[$field],
                    'operator' => 'like',
                    'value' => "%{$value}%"
                ];
            }

            if (isset($mapping[$field]) && !in_array($field, ['title', 'name', 'email'])) {
                $criteria[$mapping[$field]] = $value;
            }

            if (!isset($mapping[$field])) {
                $criteria[$field] = $value;
            }
        }

        return [
            'criteria' => $criteria,
            'orWhere' => $orWhere,
        ];
    }
}
