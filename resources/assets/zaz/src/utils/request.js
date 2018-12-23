import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '../store'
import { getToken,getCookie } from '@/utils/auth'

// 创建axios实例
const service = axios.create({
  baseURL: process.env.BASE_API, // api的base_url http://op.haojia.pub/api
  timeout: 31000 // 请求超时时间
})

// request拦截器
service.interceptors.request.use(config => {
  if (store.getters.token) {
    config.headers['X-Token'] = getCookie("zonetoken") // 让每个请求携带自定义token 请根据实际情况自行修改
    config.headers['X-Uid'] =getCookie("uid")
  }
  return config
}, error => {
  // Do something with request error
  console.log(error) // for debug
  Promise.reject(error)
})

// respone拦截器
service.interceptors.response.use(
  response => {
  /**
  * code为非20000是抛错 可结合自己业务进行修改
  */
    const res = response.data
    console.log(res)
    if (res.code != 200) {
      Message({
        message: res.msg,
        type: 'error',
        duration: 3 * 1000
      })
      // 401状态失效;
      console.log("啦啦啦啦",res.code)
      if (res.code == 401) {
        MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
          confirmButtonText: '重新登录',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          console.log("这里标签机")
          store.dispatch('LogOut').then(() => {
            location.reload()// 为了重新实例化vue-router对象 避免bug
          })
        })
      }
      return Promise.reject('error')
    } else {
      return response.data
    }
  },
  error => {
    console.log('err' + error)// for debug
    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
