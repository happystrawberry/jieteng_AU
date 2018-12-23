import Cookies from 'js-cookie'
import { getToken, getCookie } from '@/utils/auth' // 验权

import {
  constantRouterMap,
  asyncRouterMap
} from '@/router'
console.log(constantRouterMap,asyncRouterMap)
import {
  Axios
} from '@/api/login'
import Layout from '@/views/layout/Layout'
import accountIndex from '@/views/account/index'
import dashboardIndex from '@/views/dashboard/index'
import dispatchIndex from '@/views/dispatch/index'
import indexIndex from '@/views/index/index'
import itemsIndex from '@/views/items/index'
import memberIndex from '@/views/member/index'
import modelsIndex from '@/views/models/index'
import modelsMotorcycle from '@/views/models/motorcycle'
import modelsType from '@/views/models/type'
import orderIndex from '@/views/order/index'
import orderDetails from '@/views/order/details'
import partsIndex from '@/views/account/index'
import peopleIndex from '@/views/people/index'
import qualityIndex from '@/views/quality/index'
import remarkIndex from '@/views/remark/index'
import serviceIndex from '@/views/service/index'
import settlementIndex from '@/views/settlement/index'
import teamIndex from '@/views/team/index'

import partskucunlist from '@/views/parts/kucun/kucunlist'
import memberrule from '@/views/member/rule/rule'
import layout from '@/views/layout/Layout'


// import teamIndex from '@/views/team/index'
// import teamIndex from '@/views/team/index'
// import teamIndex from '@/views/team/index'

// import teamIndex from '@/views/team/index'
// import partsorder from '@/views/parts/order'
// import partssupply from '@/views/parts/supply'
// import modelsbrand from '@/views/models/brand'
// import modelsvehicle from '@/views/models/vehicle'
// import modelstype from '@/views/models/type'
// import peopleindex from '@/views/people/index'
// import memberinfo from '@/views/member/info'
// import membercreditrule from '@/views/member/creditrule'

// import accountIndex from '@/views/account/index'
// import accountIndex from '@/views/account/index'
// import accountIndex from '@/views/account/index'
// import accountIndex from '@/views/account/index'

/**
 * 通过meta.role判断是否与当前用户权限匹配
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.indexOf(role) >= 0)
  } else {
    return true
  }
}

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param asyncRouterMap
 * @param roles
 */
function filterAsyncRouter(asyncRouterMap, roles) {
  if (asyncRouterMap.component == 'Layout') { }
  const accessedRouters = asyncRouterMap.filter(route => {
    if (hasPermission(roles, route)) {
      if (route.children && route.children.length) {
        route.children = filterAsyncRouter(route.children, roles)
      }
      return true
    }
    return false
  })
  // asyncRouterMap.component=='Layout'?Layout:() => import(`${asyncRouterMap.component}`)
  return accessedRouters
}

const permission = {
  state: {
    routers: constantRouterMap,
    addRouters: []
  },
  mutations: {
    SET_ROUTERS: (state, routers) => {
      state.addRouters = routers
      state.key = true
      state.routers = constantRouterMap.concat(routers)
      // this.$router.options.routes= state.routers
    },
    SET_INFO: (state, value) => {
      state.username = value
    },
    SET_ROLEID: (state, value) => {
      state.role_id = value
    }
  },
  actions: {
    GenerateRoutes({
      commit
    }, data) {
      return new Promise(resolve => {
        const {
          roles
        } = data;
        let accessedRouters;
        // 这里保险起见 本地不保存路由表
        Axios("account/getroleslist").then(res => {
          let newlist=[];
        for(let i in res.data.roles){
            newlist.push({
              path:res.data.roles[i].path,
              roles:res.data.roles[i].meta.roles
            })
        }
          let list=asyncRouterMap;
          for(let i in list){
              for(let a in newlist){
                if(newlist[a].path==list[i].path&&list[i].path!="*"){
                  list[i].meta.roles=newlist[a].roles
                }
              }
          }
          accessedRouters = filterAsyncRouter(list, roles)
          commit('SET_ROUTERS', accessedRouters)
          resolve()
        }).catch(res => {
          reject();
        })


      })
    }
  }
}

export default permission
