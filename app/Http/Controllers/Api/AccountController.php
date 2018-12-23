<?php
/**
 *
 *
 * @since   2018/5/26 14:10
 */


namespace App\Http\Controllers\Api;


use App\Constants\BusinessConstants;
use App\Functions\UtilFunction;
use App\Http\Controllers\ApiBaseController;
use App\Models\AccountGroups;
use App\Models\Accounts;
use App\Models\AccountsGroup;
use App\Models\AccountsGroupItems;
use App\Models\WorkTaskPeople;
use App\Services\AccountService;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;


class AccountController extends ApiBaseController
{

    public function loginAction()
    {
        $userName = $this->getParameter('username');
        $passWord = $this->getParameter('password');

        $accountInfo = Accounts::getInstance()->getOneByField('phone', $userName);
        if (!$accountInfo) {
            $accountInfo = Accounts::getInstance()->getOneByField('username', $userName);
        }

        if (!$accountInfo || !$accountInfo->enable || !$accountInfo->display) {
            static::errorThrow('账号已被关闭,请联系管理员!');
        }

        if (!$accountInfo || !\Hash::check($passWord . $accountInfo->salt, $accountInfo->password)) {
            static::errorThrow('用户名或密码错误!');
        }

        Accounts::getInstance()->updateByFields([
            'lastip' => ip2long($this->request->getClientIp()),
            'lastvisit' => time()
        ], [
            'id' => $accountInfo->id
        ]);

        parent::renderSuccessJson([
            'token' => AccountService::getInstance()->getUserToken($accountInfo->id, true),
            'role_id' => array_filter(explode(',', $accountInfo->groupid)),
            'username' => $accountInfo->username,
            'phone' => $accountInfo->phone,
            'uid' => $accountInfo->id
        ]);
    }

    public function logoutAction()
    {
        $this->request->session()->flush();

        parent::renderSuccessJson();
    }

    public function captchaAction()
    {
        $builder = new CaptchaBuilder(null, new PhraseBuilder(4));
        $builder->build();
        parent::renderSuccessJson([
            'base64' => base64_encode($builder->get()),
            'captcha_code' => \Hash::make($builder->getPhrase() . $this->request->getClientIp())
        ]);
    }

    public function getAccountInfoAction()
    {
        $ret = Accounts::getInstance()->getOneByFields([
            'enable' => 1,
            'id' => $this->uid
        ]);

        if (!$ret) {
            static::errorThrow('用户不存在');
        }

        parent::renderSuccessJson([
            'username' => $ret->username,
            'role_id' => array_map(function ($i) {
                return intval($i);
            }, array_filter(explode(',', $ret->groupid))),
            'group_name' => AccountsGroup::getInstance()->getGroupNameByGroupId($ret->groupid)
        ]);
    }

    public function initAction()
    {
    }

    public function getPermsListAction()
    {
        parent::renderSuccessJson(['list' => BusinessConstants::FUNCTION_MODULE]);
    }

    public function getRolesListAction()
    {
        $groupList = AccountsGroup::getInstance()->getListByConditions(['enable' => 1], 0, BusinessConstants::MAX_RETURN_SIZE);

        $data = [];
        $roleIds = [];
        foreach ($groupList as $v) {
            $arr = explode(',', $v->perms);
            foreach ($arr as $p) {
                $data[$p][] = intval($v->id);
                $roleIds[] = intval($v->id);
            }
        }

        $roles = [];
        foreach (BusinessConstants::FUNCTION_MODULE as $m => $title) {
            $children = [];
            if (array_key_exists($m, BusinessConstants::FUNCTION_CHILDREN_MODULE)) {
                foreach (BusinessConstants::FUNCTION_CHILDREN_MODULE[$m] as $c => $f) {
                    $children[] = [
                        'path' => $m . $c,
                        'component' => '/views/' . $m . '/' . $c,
                        'name' => $m . $c,
                        'meta' => [
                            'icon' => $m,
                            'title' => $f,
                            'roles' => isset($data[$m]) ? array_unique($data[$m]) : [],
                            'noCache' => true
                        ]
                    ];
                }


            } else {
                $children = [
                    [
                        'path' => 'index',
                        'component' => '/views/' . $m . '/index',
                        'name' => $m . 'index',
                        'meta' => [
                            'icon' => $m,
                            'title' => $title,
                            'roles' => isset($data[$m]) ? array_unique($data[$m]) : [],
                            'noCache' => true
                        ]
                    ]
                ];
            }

            $roles[] = [
                'path' => '/' . $m,
                'component' => 'Layout',
                'children' => $children,
                'meta' => [
                    'title' => $title,
                    'roles' => isset($data[$m]) ? array_unique($data[$m]) : []
                ],
            ];
        }

        $roles[] = [
            'path' => '*',
            'redirect' => '/404',
            'hidden' => true,
            'meta' => array_values(array_unique($roleIds))
        ];

        parent::renderSuccessJson([
            'roles' => $roles
        ]);
    }

    public function saveAccountAction()
    {
        $id = $this->getIntegerParameter('id');
        $password = $this->getParameter('password', '');
        $enable = $this->getIntegerParameter('enable', 1);
        $groupIds = array_filter(explode(',', $this->getParameter('group_id', '')));
        $notes = $this->getParameter('notes', '');
        $isAdmin = $this->getIntegerParameter('is_admin', 0);

        $groupIds = AccountsGroup::getInstance()->getEnableGroupByIds($groupIds);

        $values = [
            'groupid' => implode(',', $groupIds),
            'enable' => $enable ? 1 : 0,
            'notes' => $notes,
            'is_admin' => $isAdmin ? 1 : 0,
            'dateline' => time()
        ];

        if ($password) {
            if (strlen($password) < 8) {
                static::errorThrow('密码长度不能小于8位!');
            }

            $values['salt'] = str_random(6);
            $values['password'] = \Hash::make($password . $values['salt']);
        }

        if (!Accounts::getInstance()->updateByFields($values, ['id' => $id])) {
            static::errorThrow('修改账号失败');
        }

        AccountGroups::getInstance()->deleteByFields(['uid' => $id]);
        $values = [];
        foreach ($groupIds as $groupId) {
            $values[] = [
                'uid' => $id,
                'group_id' => $groupId
            ];
        }
        if ($values) {
            AccountGroups::getInstance()->batchInsertByFields($values);
        }
        parent::renderSuccessJson();
    }

    public function saveGroupAction()
    {
        $id = $this->getParameter('id', 0);
        $groupName = $this->getParameter('group_name');
        $items = $this->getParameter('items', '');
        $perms = explode(',', $this->getParameter('perms'));

        $allPerms = array_keys(BusinessConstants::FUNCTION_MODULE);
        foreach ($perms as $v) {
            if (!in_array($v, $allPerms)) {
                static::errorThrow('权限设定错误');
            }
        }

        $values = [
            'dateline' => time(),
            'perms' => implode(',', $perms),
            'group_name' => $groupName
        ];

        if (($ret = AccountsGroup::getInstance()->getOneByField('group_name', $groupName)) && (!$id || ($ret->id != $id))) {
            static::errorThrow('存在重复的组名');
        }

        if ($id) {
            AccountsGroup::getInstance()->updateByFields($values, ['id' => $id]);
        } else {
            $id = AccountsGroup::getInstance()->insertByFields($values);
        }

        AccountsGroupItems::getInstance()->updateItemsByGroupId($id, explode(',', $items));

        parent::renderSuccessJson();
    }

    public function getGroupListAction()
    {
        parent::renderSuccessJson([
            'list' => AccountsGroup::getInstance()->getAllGroupNameList(true)
        ]);
    }

    public function groupListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $total = AccountsGroup::getInstance()->countListByConditions([]);
        $list = [];
        if ($total) {
            $list = AccountsGroup::getInstance()->getListByConditions([], $pageOffset, $pageSize);
            $gids = [];
            foreach ($list as $v) {
                $gids[] = $v->id;
            }
            $groupItems = AccountsGroupItems::getInstance()->getItemTypeByGroupIds($gids);
            foreach ($list as &$v) {
                $v->types = isset($groupItems[$v->id]) ? $groupItems[$v->id] : [];
            }
            unset($v);
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);

    }

    public function accountListAction()
    {
        list($curPage, $pageOffset, $pageSize) = UtilFunction::getPageAndOffset($this->getIntegerParameter('page', 1));

        $phone = $this->getParameter('phone', '');
        $userName = $this->getParameter('username', '');
        $conds = [];
        if ($phone) {
            $conds['phone'] = $phone;
        }
        if ($userName) {
            $conds['%username%'] = $userName;
        }

        $conds['display'] = 1;

        $total = Accounts::getInstance()->countListByConditions($conds);

        $list = [];
        if ($total) {
            $res = Accounts::getInstance()->getListByConditions($conds, $pageOffset, $pageSize);
            $groupNames = AccountsGroup::getInstance()->getAllGroupNameList();

            foreach ($res as $v) {
                $list[] = [
                    'id' => $v->id,
                    'username' => $v->username,
                    'phone' => $v->phone,
                    'group_name' => AccountsGroup::getInstance()->getGroupNameByGroupId($v->groupid),
                    'enable' => $v->enable,
                    'is_admin' => $v->is_admin,
                    'group_id' => array_filter(explode(',', $v->groupid)),
                    'notes' => $v->notes
                ];
            }
        }

        parent::renderSuccessJson([
            'list' => $list,
            'page' => UtilFunction::renderPage($total, $curPage)
        ]);
    }

    public function switchAccountStatusAction()
    {
        $id = $this->getParameter('id');

        $ret = Accounts::getInstance()->getOneByField('id', $id);

        if (!$ret) {
            static::errorThrow('账号不存在');
        }

        $enable = $ret->enable ? 0 : 1;

        if (!$enable && WorkTaskPeople::getInstance()->countAccountByUnFinishOrder($id)) {
            //检查这个用户是否有正在进行的订单，如果存在则禁止关闭
            static::errorThrow('当前账号有未完成的订单，禁止关闭此账号!');
        }

        if (!Accounts::getInstance()->updateByFields(['enable' => $enable], ['id' => $id])) {
            static::errorThrow('操作失败');
        }

        parent::renderSuccessJson([
            'enable' => $enable
        ]);
    }

    public function switchGroupStatusAction()
    {
        $id = $this->getParameter('id');

        $ret = AccountsGroup::getInstance()->getOneByField('id', $id);

        if (!$ret) {
            static::errorThrow('用户组不存在');
        }

        $enable = $ret->enable ? 0 : 1;
        if (!AccountsGroup::getInstance()->updateByFields(['enable' => $enable], ['id' => $id])) {
            static::errorThrow('操作失败');
        }

        parent::renderSuccessJson([
            'enable' => $enable
        ]);
    }
}