<?php
/**
 *
 *
 * @since   2018/5/28 21:45
 */


namespace App\Services;


use App\Constants\CacheKeyConstants;
use App\Constants\CacheTimeoutConstants;
use App\Functions\CacheFunction;

class BaseService
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance && (self::$_instance instanceof static)) {
            return self::$_instance;
        }

        return self::$_instance = new static();
    }

    public function getMethodCache($extraKey = '')
    {
        $calledFunc = debug_backtrace()[1]['function'];
        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::SERVICE_METHOD_CACHE_KEY, get_called_class(), $calledFunc, $extraKey);

        return \Cache::get($cacheKey);
    }

    public function setMethodCache($data, $expire = CacheTimeoutConstants::LOCAL_CACHE, $extraKey = '')
    {
        $calledFunc = debug_backtrace()[1]['function'];
        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::SERVICE_METHOD_CACHE_KEY, get_called_class(), $calledFunc, $extraKey);

        return \Cache::set($cacheKey, $data, $expire);
    }

}