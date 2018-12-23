<?php
/**
 *
 * User: lang@vip.deyi.com
 * Date: 2018/6/11
 * Time: 13:40
 */

namespace App\Services;


use App\Constants\CacheKeyConstants;
use App\Constants\CacheTimeoutConstants;
use App\Functions\CacheFunction;
use App\Functions\HttpFunctions;

class DingTalkService extends BaseService
{
    public function getAccessToken()
    {
        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::DINGTALK_ACCESS_TOKEN, date('Y-m-d'));

        $token = \Cache::get($cacheKey);

        if ($token) {
            return $token;
        }

        $ret = HttpFunctions::get('https://oapi.dingtalk.com/gettoken', [
            'corpid' => env('DINGTALK_CORPID'),
            'corpsecret' => env('DINGTALK_CORPSECRET')
        ]);

        if ($ret && ($ret = json_decode($ret)) && !$ret->errcode) {
            $token = $ret->access_token;
            \Cache::set($cacheKey, $token, CacheTimeoutConstants::LOCAL_MIDDLE_CACHE);
        } else {
            $token = false;
        }

        return $token;
    }

    public function getDepartmentIds()
    {
        $res = $this->getMethodCache();

        if ($res) {
            return $res;
        }

        $ret = HttpFunctions::get('https://oapi.dingtalk.com/auth/scopes', [
            'access_token' => self::getAccessToken()
        ]);
        $depts = [];
        if ($ret && ($ret = json_decode($ret)) && !$ret->errcode) {
            $ret = $ret->auth_org_scopes->authed_dept;
            $depts = $ret;
            foreach ($ret as $rootId) {
                $subRet = HttpFunctions::get('https://oapi.dingtalk.com/department/list', [
                    'access_token' => self::getAccessToken(),
                    'id' => $rootId,
                    'fetch_child' => true
                ]);

                if ($subRet && ($subJson = json_decode($subRet)) && $subJson->department) {
                    foreach ($subJson->department as $subDept) {
                        $depts[] = $subDept->id;
                    }
                }
            }

            $ret = array_unique($depts);
            $this->setMethodCache($ret);

            return $ret;
        }

        return false;
    }

    public function getDepartmentInfo($id)
    {
        $res = $this->getMethodCache($id);

        if ($res) {
            return $res;
        }

        $ret = HttpFunctions::get('https://oapi.dingtalk.com/department/get', [
            'access_token' => self::getAccessToken(),
            'id' => $id
        ]);

        if ($ret && ($ret = json_decode($ret)) && !$ret->errcode) {
            $this->setMethodCache($ret, CacheTimeoutConstants::LOCAL_MAX_CACHE, $id);

            return $ret;
        }

        return false;
    }

    public function getUserList()
    {

        $ids = self::getDepartmentIds();

        if (!$ids) {
            return [];
        }

        $users = [];
        foreach ($ids as $id) {
            $ret = HttpFunctions::get('https://oapi.dingtalk.com/user/simplelist', [
                'access_token' => self::getAccessToken(),
                'department_id' => $id
            ]);

            if ($ret && ($ret = json_decode($ret)) && !$ret->errcode) {
                foreach ($ret->userlist as $v) {
                    if ($user = self::getUserDetail($v->userid)) {
                        $user->departments = array_map(function ($departId) {
                            return $this->getDepartmentInfo($departId)->name;
                        }, $user->department);
                        $users[$v->userid] = $user;
                    } else {
                        return [];
                    }
                }
            } else {
                return [];
            }
        }

        return $users;
    }

    public function getUserDetail($userId)
    {
        $res = $this->getMethodCache($userId);

        if ($res) {
            return $res;
        }

        $res = HttpFunctions::get('https://oapi.dingtalk.com/user/get', [
            'access_token' => self::getAccessToken(),
            'userid' => $userId
        ]);

        if ($res && ($res = json_decode($res)) && !$res->errcode) {
            $this->setMethodCache($res, CacheTimeoutConstants::LOCAL_CACHE, $userId);

            return $res;
        }

        return false;
    }
}