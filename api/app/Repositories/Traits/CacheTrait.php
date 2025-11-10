<?php

namespace App\Repositories\Traits;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

trait CacheTrait
{
    public function getFromCacheOrFetch(string $key, \Closure $callback, int $seconds = 0)
    {
        $cached = Cache::get($key);

        if (!is_null($cached)) {
            return $cached;
        }

        $data = $callback();

        if (!is_null($data)) {
            $this->setCachedItem($key, $data, $seconds);
        }

        return $data;
    }

    public function deleteFromCacheByIdAndModel(string $modelTable, int $id)
    {
        $this->removeCachedItem(
            $modelTable . "_" . $id
        );
    }

    public function deleteFromCacheAllObjects()
    {
        $this->forgetClassObjects($this->model);
    }

    public function removeCachedItem(string $key)
    {
        Cache::forget($key);
    }

    public function removeCachedObject(object $model)
    {
        Cache::forget($this->setKeyName($model));
    }

    public function removeAllCacheByTable(string $tableName)
    {
        Cache::forget($tableName . '_all');
        Cache::forget($tableName . '_cached_ids');

        $cacheDriver = config('cache.default');
        if ($cacheDriver === 'redis') {
            $redis = Cache::connection('redis');
            $pattern = $tableName . '_*';
            $keys = $redis->connection()->keys($pattern);

            foreach ($keys as $key) {
                $cleanKey = str_replace(config('cache.prefix'), '', $key);
                Cache::forget($cleanKey);
            }
        }
    }

    private function forgetClassObjects(object $model)
    {
        $keys = $this->readObjectKeys($model);

        if (count($keys) == 0) {
            return [];
        }

        $prefix = $model->getTable() . '_';

        foreach ($keys as $key) {
            Cache::forget($prefix . $key);
        }

        $this->forgetObjectKeys($model);
    }

    private function setKeyName(object $model)
    {
        $key = $model->getTable();

        if (!is_null($model->id)) {
            $key .= '_' . $model->id;
        }

        return $key;
    }

    private function storeObjectKey(object $model): void
    {
        $id = (int)$model->id;
        $keys = $this->readObjectKeys($model);

        if (in_array($id, $keys)) {
            return;
        }

        $keys[] = $id;
        $this->storeObjectKeys($model, $keys);
    }

    private function readObjectKeys(object $model): array
    {
        $cached_ids_key = $model->getTable() . '_cached_ids';
        $keys = Cache::get($cached_ids_key);

        return is_null($keys) ? [] : $keys;
    }

    private function storeObjectKeys(object $model, array $keys): void
    {
        $cached_ids_key = $model->getTable() . '_cached_ids';
        Cache::forever($cached_ids_key, $keys);
    }

    private function forgetObjectKeys($model): void
    {
        $cached_ids_key = $model->getTable() . '_cached_ids';
        Cache::forget($cached_ids_key);
    }

    public function getCachedObject(object $object)
    {
        return Cache::get($this->setKeyName($object));
    }

    public function setCachedObject(object $model, int $seconds = 0)
    {
        $this->storeObjectKey($model);

        if ($seconds != 0) {
            $expiresAt = Carbon::now()->addSeconds($seconds);
            Cache::put($this->setKeyName($model), $model, $expiresAt);
        } else {
            Cache::forever($this->setKeyName($model), $model);
        }
    }

    public function hasCachedObject(object $object)
    {
        return Cache::has($this->setKeyName($object));
    }

    public function getCachedItem(string $key)
    {
        return Cache::get($key);
    }

    public function setCachedItem($key, $value, int $seconds = 0)
    {
        if ($seconds != 0) {
            $expiresAt = Carbon::now()->addSeconds($seconds);
            Cache::put($key, $value, $expiresAt);
        } else {
            Cache::forever($key, $value);
        }
    }

    public function hasCachedItem($key)
    {
        return Cache::has($key);
    }
}
