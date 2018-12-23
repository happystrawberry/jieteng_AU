import router from './router'
import store from './store'
import NProgress from 'nprogress' // Progress 进度条
import 'nprogress/nprogress.css'// Progress 进度条样式
import {Message} from 'element-ui'
import {getToken,getCookie} from '@/utils/auth' // 验权

const whiteList = ['/login'] // 不重定向白名单
router.beforeEach((to, from, next) => {
  NProgress.start()
  if (getCookie("zonetoken")) {
    if (to.path == '/login') {
      next({path: '/'})
      NProgress.done() // if current page is dashboard will not trigger	afterEach hook, so manually handle it
    } else {
      // 这里检测本地权限判断
      console.log(store.getters.name.length,store.getters.login)
      // next();
      // return;
      if (store.getters.login==1||store.getters.name.length==0) {
        store.dispatch('GetInfo').then(res=>{
          const roles = res.data.role_id;
          store.dispatch('GenerateRoutes', {
            roles
          }).then(res => {
            console.log("zzzone",store.getters.addRouters)
            router.addRoutes(store.getters.addRouters) // 动态添加可访问路由表
            // console.log("router",router)
            // next({path: '/'})
            // return;
            next({...to, replace: true})
          }).catch((err) => {
            store.dispatch('FedLogOut').then(() => {
              next({path: '/'})
            })
          })
        }).catch(res=>{
        })

      } else {
        //如果存在路由表 直接next();
        next()
      }
    }
  } else {
    if (whiteList.indexOf(to.path) !== -1) {
      next()
    } else {
      next('/login')
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  NProgress.done() // 结束Progress
})
