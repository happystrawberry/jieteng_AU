<?php
/**
 *
 *
 * @since   2018/5/27 13:29
 */


namespace App\Constants;


class BusinessConstants
{
    const ACCOUNT_SESSION = 'account_session';

    const ORDER_PAY_STATUS = [
        0 => '未支付',
        1 => '挂账',
        2 => '已支付',
        3 => '已退款',
    ];

    const OP_ITEMS = [
        1 => '维修(原保养)',
        2 => '维修',
        3 => '美容',
        4 => '钣喷',
        5 => '改装',
        6 => '其它'
    ];

    const OP_ITEMS_v2 = [
        2 => '维修',
        3 => '美容',
        4 => '钣喷',
        5 => '改装',
        6 => '其它'
    ];

    const DISPATCH_OP_TIMES_TASK_SET = [
        1 => 'people',
        2 => 'team',
        3 => 'team',
        4 => 'people',
        5 => 'team',
        6 => 'people'
    ];

    const OP_ORDER_QC_STATUS = [
        0 => '未质检',
        1 => '质检合格',
        2 => '返工'
    ];

    const ORDER_STATUS = [
        0 => '作废',
        1 => '已接车',
        2 => '已开单',
        3 => '已调度',
        4 => '已开工',
        5 => '已质检',
        6 => '已结算',
        7 => '已回访',
        94 => '需返工(材料)',
        95 => '需返工(项目)',
        96 => '已增料',
        97 => '挂账',
        98 => '待洗车',
        99 => '已暂停'
    ];

    const ORDER_PICK_STATUS = [
        0 => '未领料',
        1 => '已领料',
    ];

    const ORDER_PARTS_STATUS = [
        0 => '待发货',
        1 => '已发货',
        2 => '已退货'
    ];

    const PAY_STATUS_ALIPAY = 1; //支付方式支付宝
    const PAY_STATUS_WXPAY = 2; // 支付方式微信
    const PAY_STATUS_CASH = 3;//支付方式现金
    const PAY_STATUS_VIPCARD = 4; // 支付方式会员卡
    const PAY_STATUS_HOLD = 5; //挂账
    const PAY_STATUS_CARD = 6; // 刷卡支付
    const PAY_STATUS_COUPON = 7; // 活动券
    const PAY_STATUS_OTHER = 8; //其他

    const PAY_STATUS_SET = [
        self::PAY_STATUS_ALIPAY => '支付宝',
        self::PAY_STATUS_WXPAY => '微信支付',
        self::PAY_STATUS_CASH => '现金',
        self::PAY_STATUS_VIPCARD => '会员卡',
        self::PAY_STATUS_HOLD => '挂账',
        self::PAY_STATUS_CARD => '刷卡支付',
        self::PAY_STATUS_COUPON => '活动券',
        self::PAY_STATUS_OTHER => '其它'
    ];

    const BARCODE_TYPE = 'EAN13';

    const FUNCTION_MODULE = [
        'stat' => '统计',
        'workorder' => '订单管理',
        // 接车下单
        'order' => '接车开单',
        // 配件仓库
        'parts' => '配件仓库',
        // 班组
        'team' => '班组',
        //调度
        'dispatch' => '调度',
        // 质检
        'quality' => '质检',
        // 结算
        'settlement' => '结算',
        // 客服回访
        'service' => '客服回访',
        // 维修项目
        'items' => '维修项目',
        // 车型管理
        'models' => '车型管理',
        // 人事管理
        'people' => '人事管理',
        // 会员管理
        'member' => '会员管理',
        // 备注
        'remark' => '备注',
        // 后台用户管理配置
        'account' => '后台账号管理',
        // 部门列表
        'department' => '部门列表',
        // 控制面板
        'setting' => '控制面板'
    ];

    const FUNCTION_CHILDREN_MODULE = [
        'models' => [
            'index' => '品牌列表',
            'motorcycle' => '车型列表',
            'type' => '具体车型列表'
        ],
        'member' => [
            'index' => '用户信息',
            'rule' => '积分规则'
        ],
        'parts' => [
            'kucunlist' => '库存统计'
        ]
    ];

    const VEHICLE_MODEL_CLASS = [
        'A0',
        'A',
        'B',
        'C',
        'D'
    ];

    const MAX_PAGE = 100000;

    const PAGE_SIZE = 10;

    const MAX_RETURN_SIZE = 100000000;

    const SETTING_KEY_TAXRATE = 'tax_rate';
}