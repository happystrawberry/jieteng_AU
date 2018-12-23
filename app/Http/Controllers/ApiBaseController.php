<?php
/**
 *
 *
 * @since   2018/5/26 14:10
 */


namespace App\Http\Controllers;

use App\Constants\CacheKeyConstants;
use App\Constants\CacheTimeoutConstants;
use App\Constants\ExceptionConstants;
use App\Functions\CacheFunction;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ApiBaseController extends BaseController
{
    protected $request;

    protected $jsonData;

    protected $uid = 0;

    protected $accountName = '';

    protected $isAdmin = false;

    protected $groupOpTypes = [];

    protected static $_mutexKey = '';

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->jsonData = $request->json();
        $this->uid = $this->request->header('X-Uid') ? $this->request->header('X-Uid') : 1;

        $accountInfo = AccountService::getInstance()->getAccountUserInfo($this->uid);

        $this->accountName = $accountInfo ? $accountInfo->username : '';
        $this->isAdmin = ($accountInfo && $accountInfo->is_admin) ? true : false;
        $this->groupOpTypes = ($accountInfo && isset($accountInfo->op_type)) ? $accountInfo->op_type : [];
    }

    public static function integerToString($arr)
    {
        foreach ($arr as &$v) {
            if (is_array($v) || is_object($v)) {
                $v = self::integerToString($v);
            } else {
                $v = strval($v);
            }
        }

        return $arr;
    }

    public static function renderSuccessJson($data = [], $msg = 'ok', $json = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
    {
        static::mutexUnlock();

        return response()->json([
            'code' => ExceptionConstants::NORMAL_CODE,
            'data' => self::integerToString($data),
            'msg' => $msg
        ], 200, [], $json)->send();
    }

    public static function errorThrow($err, $code = ExceptionConstants::INTERNAL_ERROR)
    {
        throw new \Exception($err, $code);
    }

    public function getParameter($key, $default = null)
    {
        $val = $this->jsonData->get($key, $this->request->has($key) ? $this->request->get($key) : $default);

        if (is_null($default) && !$val && !is_numeric($val)) {
            static::errorThrow($key . ' param empty');
        }

        return is_null($val) ? $default : ((is_string($val) || is_numeric($val)) ? strval(trim($val)) : $val);
    }

    public function getIntegerParameter($key, $default = null)
    {
        $val = $this->jsonData->get($key, $this->request->has($key) ? $this->request->get($key) : $default);

        if (is_null($default) && !is_numeric($val)) {
            static::errorThrow($key . ' param empty');
        }

        return (is_null($val) || !is_numeric($val)) ? $default : intval($val);
    }

    public function getFloatParameter($key, $default = null)
    {
        $val = $this->jsonData->get($key, $this->request->has($key) ? $this->request->get($key) : $default);
        if (is_null($default) && !is_numeric($val)) {
            static::errorThrow($key . ' param empty');
        }

        return is_null($val) ? $default : floatval($val);
    }

    public function getUrlCache($extra = '')
    {
        $cacheKey = CacheFunction::makeCacheKey(
            CacheKeyConstants::URL_CACHE,
            $this->request->fullUrlWithQuery([]),
            $this->request->getContent(),
            $extra
        );

        return \Cache::get($cacheKey);
    }

    public function setUrlCache($data, $expire = CacheTimeoutConstants::LOCAL_CACHE, $extra = '')
    {
        $cacheKey = CacheFunction::makeCacheKey(
            CacheKeyConstants::URL_CACHE,
            $this->request->fullUrlWithQuery([]),
            $this->request->getContent(),
            $extra
        );

        return \Cache::set($cacheKey, $data, $expire);
    }

    public function mutexLock($extra = '', $timeOut = 0.5)
    {
        static::$_mutexKey = CacheFunction::makeCacheKey(CacheKeyConstants::API_MUTEX_LOCK, $this->request->fullUrlWithQuery([]),
            $this->request->getContent(), $extra);

        if (!\Cache::add(static::$_mutexKey, 1, $timeOut)) {
            static::errorThrow('请勿重复请求!');
        }
    }

    public static function mutexUnlock()
    {
        if (static::$_mutexKey) {
            \Cache::delete(static::$_mutexKey);
        }
    }

}