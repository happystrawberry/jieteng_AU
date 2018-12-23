import Vue from 'vue'
import Router from 'vue-router'

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
import Layout from '../views/layout/Layout'

/**
 * hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
 *                                if not set alwaysShow, only more than one route under the children
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noredirect           if `redirect:noredirect` will no redirct in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
  }
 **/
export const constantRouterMap = [
  {path: '/login', component: () => import('@/views/login/index'), hidden: true},
  {path: '/404', component: () => import('@/views/404'), hidden: true},
  {
    path: '',
    component: Layout,
    redirect: 'index',
    children: [{
      path: 'index',
      component: () => import('@/views/index/index'),
      name: 'dashboard',
      meta: {title: '主看板', icon: 'kanban', noCache: true}
    }]
  },

]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({y: 0}),
  routes: constantRouterMap
})
export const asyncRouterMap =[
  {
    "path": "/remark",
    "component": Layout,
    hidden:true,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/remark/index"),
        "name": "remarkindex",
        "meta": {
          "title": "备注",
          icon:"beizhu",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "备注",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/order",
    "component": Layout,
    redirect: '/order/index',
    name:"接车开单",
    "children": [
      {
        "path": "index",
        "component": () => import('@/views/order/index'),
        "name": "orderindex",
        "meta": {
          "title": "接车开单",
          "icon": 'kaidan',
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "接车列表",
      "icon": 'kaidan',
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/order",
    "component": Layout,
    name:"接车列表",
    hidden:true,
    "children": [
      {
        "path": "details",
        "component": () => import('@/views/order/details'),
        "name": "orderdetails",
        "meta": {
          "title": "接车",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "接车列表",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/order",
    "component": Layout,
    name:"接车列表",
    hidden:true,
    "children": [
      {
        "path": "settlemen",
        "component": () => import("@/views/settlement/details"),
        "name": "othersettlemen",
        "meta": {
          "title": "结算详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "开车接单",
      "roles": [
        1,
        2,
        3
      ]
    }
  },

  {
    "path": "/parts",
    "component": Layout,
    hidden:true,
     name:"配件仓库",
    "children": [
      {
        "path": "index/details",
        "component": () => import('@/views/parts/kucun/index'),
        "name": "datalistdetails",
        "meta": {
          "title": "配件详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "配件仓库",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/parts",
    "component": Layout,
    hidden:true,
     name:"配件仓库",
    "children": [
      {
        "path": "datalist/details",
        "component": () => import('@/views/parts/kucun/details'),
        "name": "kucuntdetails",
        "meta": {
          "title": "配件详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "入库出库数据",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/parts",
    "component": Layout,
    hidden:true,
     name:"配件仓库",
    "children": [
      {
        "path": "kucunlist/details",
        "component": () => import('@/views/parts/kucun/details'),
        "name": "kuncuns",
        "meta": {
          "title": "配件详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "库存表",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/parts",
    "component": Layout,
    hidden:true,
     name:"配件仓库",
    "children": [
      {
        "path": "index/details",
        "component": () => import('@/views/parts/kucun/index'),
        "name": "partsdetails",
        "meta": {
          "title": "配件详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "配件仓库",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/order",
    "component": Layout,
    name:"接车列表",
    hidden:true,
    "children": [
      {
        "path": "details",
        "component": () => import('@/views/order/details'),
        "name": "orderdetails",
        "meta": {
          "title": "接车",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "接车列表",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/dispatch",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/dispatch/index"),
        "name": "dispatchindex",
        "meta": {
          "title": "调度",
          icon: 'diaodu',
          "noCache": true
        }
      },
      {
        "path": "details",
        "component": () => import("@/views/dispatch/details"),
        "name": "dispatchdetails",
        "hidden":true,
        "meta": {
          "title": "施工单",
          icon: 'diaodu',
          "noCache": true
        }
      },
    ],
    "meta": {
      "title": "调度",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/team",
    "component": Layout,
    "title": "班组",
    redirect: '/team/index',
    name:"班组",
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/team/index"),
        "name": "teamindex",
        "meta": {
          "title": "班组列表",
          icon: 'banzu',
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "班组",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/team",
    "component": Layout,
    name:"班组",
     hidden:true,
    "children": [
      {
        "path": "details",
        "component": () => import("@/views/team/details"),
        "name": "teamdetails",
        "meta": {
          "title": "班组详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "班组",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/parts",
    "component": Layout,
     name:"配件仓库",
     redirect: '/parts/index',
      "children": [
      {
        "path": "index",
        "component": () => import('@/views/parts/index'),
        "name": "partsindex",
        "meta": {
          "title": "配件列表",
          "noCache": true
        }
      },
      {
        "path": "kucunlist",
        "component": () => import('@/views/parts/kucun/kucunlist'),
        "name": "partskucunlist",
        "meta": {
          "title": "库存表",
          "noCache": true
        }
      },{
        "path": "datalist",
        "component": () => import('@/views/parts/datalist/index'),
        "name": "partsdatalist",
        "meta": {
          "title": "入库出库数据",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "配件仓库",
      icon: 'cangku',
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/quality",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/quality/index"),
        "name": "qualityindex",
        "meta": {
          "title": "质检",
          icon:"zhijian",
          "noCache": true
        }
      },
      

    ],
    "meta": {
      "title": "质检",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/quality",
    "component": Layout,
    name:"质检",
    redirect: '/quality/index',
    hidden:true,
    "children": [
      {
        "path": "details",
        "component": () => import("@/views/quality/details"),
        "name": "qualitydetails",
        "meta": {
          "title": "质检详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "质检",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/settlement",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/settlement/index"),
        "name": "settlementindex",
        "meta": {
          "title": "结算列表",
          
          "noCache": true
        }
      },{
        "path": "payment",
        "component": () => import("@/views/settlement/payment"),
        "name": "settlementpayment",
        "meta": {
          "title": "薪酬核算",
          
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "结算",
      icon:"jiesuan",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/settlement",
    "component": Layout,
    name:"结算",
    redirect: '/settlement/index',
    hidden:true,
    "children": [
      {
        "path": "details",
        "component": () => import("@/views/settlement/details"),
        "name": "settlemendetails",
        "meta": {
          "title": "结算详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "结算",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/workorder",
    "component": Layout,
    redirect: '/workorder/index',
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/ordersgoods/index"),
        "name": "orrdersgoodsindex",
        "meta": {
          "title": "订单管理",
          icon:"dingdan",
          "noCache": true
        }
      },{
        "path": "details",
        "hidden":true,
        "component": () => import("@/views/ordersgoods/details"),
        "name": "orrdersgoodsdetails",
        "meta": {
          "title": "订单详情",
          icon:"dingdan",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "订单管理",
      icon:"dingdan",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/service",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/service/index"),
        "name": "serviceindex",
        "meta": {
          "title": "客服回访",
       
          "noCache": true
        }
      },{
        "path": "management",
        "component": () => import("@/views/service/management"),
        "name": "servicemanagement",
        "meta": {
          "title": "回访问题管理",
          
          "noCache": true
        }
      },{
        "path": "management/managedetails",
        hidden:true,
        "component": () => import("@/views/service/managedetails"),
        "name": "servicemanagedetails",
        "meta": {
          "title": "回访问题详情",
          "noCache": true
        }
      }
      
    ],
    "meta": {
      "title": "客服",
      icon:"huifang",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/service",
    "component": Layout,
    name:"客服回访",
    redirect: '/service/index',
    hidden:true,
    "children": [
      {
        "path": "index/details",
        "component": () => import("@/views/service/details"),
        "name": "servicedetails",
        "meta": {
          "title": "回访详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "客服回访",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/member",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/member/index"),
        "name": "memberindex",
        "meta": {
          "title": "会员管理",
          "noCache": true
        }
      },{
        "path": "rule",
        "component": () => import("@/views/member/rule/rule"),
        "name": "memberrule",
        "meta": {
          "title": "积分管理",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "会员管理",
      icon:"huiyuan",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/people",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/people/index"),
        "name": "peopleindex",
        "meta": {
          "title": "人事管理",
          icon:"renshi",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "人事管理",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/models",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/models/index"),
        "name": "modelsindex",
        "meta": {
          "title": "品牌列表",
          "noCache": true
        }
      },
      {
        "path": "motorcycle",
        "component": () => import("@/views/models/motorcycle"),
        "name": "modelsmotorcycle",
        "meta": {
          "title": "车型列表",
          "noCache": true
        }
      },
      {
        "path": "type",
        "component": () => import("@/views/models/type"),
        "name": "modelstype",
        "meta": {
          "title": "型号列表",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "车型管理",
      icon:"chexing",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/items",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/items/index"),
        "name": "itemsindex",
        "meta": {
          "title": "维修项目",
          icon:"weixiu",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "维修项目",
      "roles": [
        1,
        2,
        3
      ]
    }
  },

  {
    "path": "/people",
    "component": Layout,
    name:"人事管理",
    redirect: '/settlement/index',
    hidden:true,
    "children": [
      {
        "path": "details",
        "component": () => import("@/views/people/details"),
        "name": "peopledetails",
        "meta": {
          "title": "人员详情",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "人事管理",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  
  {
    "path": "/member",
    "component": Layout,
    name:"会员管理",
    redirect: '/member/index',
    hidden:true,
    "children": [
      {
        "path": "details",
        "component": () => import("@/views/member/details"),
        "name": "memberdetails",
        "meta": {
          "title": "会员信息",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "会员管理",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/member",
    "component": Layout,
    name:"会员管理",
    redirect: '/member/index',
    hidden:true,
    "children": [
      {
        "path": "record",
        "component": () => import("@/views/member/record"),
        "name": "memberrecord",
        "meta": {
          "title": "会员积分变动记录",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "会员管理",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/member",
    "component": Layout,
    name:"会员管理",
    redirect: '/member/index',
    hidden:true,
    "children": [
      {
        "path": "remaining",
        "component": () => import("@/views/member/remaining"),
        "name": "memberremain",
        "meta": {
          "title": "消费积分余额记录",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "会员管理",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/department",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/department/department"),
        "name": "departmentindex",
        "meta": {
          "title": "部门列表",
          icon:"bumen",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "部门列表",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/stat",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/statistics/shipment"),
        "name": "statisticsshipment",
        "meta": {
          "title": "出货量统计",
          "noCache": true
        }
      },
      {
        "path": "replenish",
        "component": () => import("@/views/statistics/replenish"),
        "name": "statisticsreplenish",
        "meta": {
          "title": "进厂量统计",
          "noCache": true
        }
      },
      {
        "path": "paytype",
        "component": () => import("@/views/statistics/paytype"),
        "name": "statisticspaytype",
        "meta": {
          "title": "支付方式统计",
          "noCache": true
        }
      },
      {
        "path": "total",
        "component": () => import("@/views/statistics/total"),
        "name": "statisticstotal",
        "meta": {
          "title": "统计总销售额",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "统计",
      icon:"tongji",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/setting",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/panel/index"),
        "name": "panelindex",
        "meta": {
          "title": "提成设置",
          "noCache": true
        }
      },
      {
        "path": "proportion",
        "component": () => import("@/views/panel/proportion"),
        "name": "panelpropo",
        "meta": {
          "title": "税率设置",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "控制面板",
       icon:"mianban",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  {
    "path": "/account",
    "component": Layout,
    "children": [
      {
        "path": "index",
        "component": () => import("@/views/account/index"),
        "name": "accountindex",
        "meta": {
          "title": "后台账号管理",
          icon:"zhanghao",
          "noCache": true
        }
      }
    ],
    "meta": {
      "title": "后台账号管理",
      "roles": [
        1,
        2,
        3
      ]
    }
  },
  // {path: '*', redirect: '/404', hidden: true}
]
/**,
*/
