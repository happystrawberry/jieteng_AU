<?php
/**
 *
 *
 * @since   2018/5/28 20:46
 */


namespace App\Services;


use App\Constants\CacheKeyConstants;
use App\Constants\CacheTimeoutConstants;
use App\Functions\CacheFunction;
use App\Models\Accounts;
use App\Models\AccountsGroupItems;

class AccountService extends BaseService
{
    public function getUserToken($uid, $update = false)
    {

        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::USER_TOKEN, $uid);

        if ($update) {
            $token = \Hash::make($uid . uniqid() . time());

            \Cache::set(
                CacheFunction::makeCacheKey(CacheKeyConstants::USER_TOKEN, $uid),
                $token,
                CacheTimeoutConstants::LOCAL_DAY_CACHE
            );

            return $token;
        }

        return \Cache::get($cacheKey);
    }

    public function validUserToken($uid, $token)
    {
        if (!$uid || !$token) {
            return false;
        }

        return ($this->getUserToken($uid) === $token) ? true : false;
    }

    public function getAccountUserInfo($uid)
    {
        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::ACCOUNT_USERINFO, $uid);

        $res = \Cache::get($cacheKey);

        if ($res) {
            return $res;
        }

        $res = Accounts::getInstance()->getOneByField('id', $uid);
        $res->op_type = array_unique(AccountsGroupItems::getInstance()->getAllListByOnField([
            'group_id' => explode(',', $res->groupid)
        ], 'item_type'));
        \Cache::set($cacheKey, $res, CacheTimeoutConstants::LOCAL_MIDDLE_CACHE);

        return $res;
    }

    public function syncDingTalkUser()
    {
        $users = DingTalkService::getInstance()->getUserList();

        if (empty($users)) {
            return false;
        }

        $syncTime = time();
        foreach ($users as $user) {
            if (!Accounts::getInstance()->getOneByField('phone', $user->mobile)) {
                Accounts::getInstance()->insertByFields([
                    'ding_userid' => $user->userid,
                    'ding_position' => $user->position,
                    'ding_department' => implode(',', $user->departments),
                    'ding_jobnumber' => isset($user->jobnumber) ? $user->jobnumber : '',
                    'phone' => $user->mobile,
                    'username' => $user->name,
                    'enable' => 0,
                    'display' => 1,
                    'dateline' => $syncTime
                ]);
            } else {
                Accounts::getInstance()->updateByFields([
                    'ding_userid' => $user->userid,
                    'ding_position' => $user->position,
                    'ding_department' => implode(',', $user->departments),
                    'ding_jobnumber' => isset($user->jobnumber) ? $user->jobnumber : '',
                    'username' => $user->name,
                    'dateline' => $syncTime,
                    'display' => 1,
                ], ['phone' => $user->mobile]);
            }
        }

        //关闭没有同步到的账号(管理员账号不控制)
        Accounts::getInstance()->updateByFields(['enable' => 0, 'display' => 0], ['is_admin' => 0, 'dateline<>' => $syncTime]);

        return true;
    }
}