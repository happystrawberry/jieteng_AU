<?php

namespace App\Http\Middleware;

use App\Constants\ExceptionConstants;
use App\Services\AccountService;
use Closure;

class AccountAuthMiddleware
{
    const skipUrl = [
        'api/account/login',
        'api/account/logout',
        'api/account/captcha',
        'api/common/getallparts',
        'api/common/getallopitems',
        'api/common/searchbyvehiclenumber',
        'api/common/ordernotelist',
        'api/common/saveordernote',
        'api/common/searchaccountname',
        'api/common/getmodelclasslist',
        'api/common/getoptypelist',
        'api/common/upload',
        'api/common/getcodebar',
        'api/common/dopassword',
        'api/common/resetorder',
        'api/common/returnorder',
        'api/common/getallconsultantlist',
        'api/common/getalloperators',
        'api/account/init',
        'api/parts/supplylist',
        'api/member/list',
        'api/parts/getpartsloglist',
        'api/parts/import',
        'api/items/import',
        'api/models/getbrandlist'
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Expose-Headers: *");
        if (!in_array($request->path(), self::skipUrl)) {
            if (!AccountService::getInstance()->validUserToken($request->header('X-Uid', 0), $request->header('X-Token', ''))) {
                throw new \Exception('尚未登录,请重新登录!', ExceptionConstants::LOGIN_FAILED);
            }
        }

        return $next($request);
    }
}
