<?php
/**
 *
 *
 * @since   2018/5/27 13:11
 */


namespace App\Constants;


class ExceptionConstants
{
    const NORMAL_CODE = 200;

    const INTERNAL_ERROR = 300;

    // 登录失败
    const LOGIN_FAILED = 401;
    // 权限失败
    const LOGIN_PERM_FAILED = 402;

    // http 404
    const HTTP_NOTFOUND = 404;
}