<?php
/**
 *
 *
 * @since   2018/5/28 21:03
 */


namespace App\Constants;


class CacheKeyConstants
{
    const USER_TOKEN = 'user_token';

    const SMS_CODE = 'sms_code';

    const ACCOUNT_USERINFO = 'account_userinfo';

    const DINGTALK_ACCESS_TOKEN = 'dingtalk_access_token';

    const SERVICE_METHOD_CACHE_KEY = 'service_method_cache_key';

    const URL_CACHE = 'url_cache';

    const API_MUTEX_LOCK = 'api_mutex_lock';

    // 支付锁，避免重复发起支付
    const PAY_LOCK_STATUS = 'pay_lock_status';
}