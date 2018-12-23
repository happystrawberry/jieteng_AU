import { Axios, logout, getInfo } from '@/api/login'
import { getToken, setToken, removeToken,setCookie,removeCookie} from '@/utils/auth'

const user = {
  state: {
    token: getToken(),
    name: '',
    avatar: '',
    uid:"",
    roles: -1,
    routerlist:-1,
    login:0
  },

  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_NAME: (state, name) => {
      state.name = name
    },
    SET_UID: (state, avatar) => {
      state.uid = avatar
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    SET_UID: (state, roles) => {
      state.uid = roles
    },
    SET_LOGIN: (state, roles) => {
      state.login = roles
    },
    SET_ROUTERLIST:(state, routerlist) => {
      state.routerlist = routerlist
    },
    
  },

  actions: {
    // 登录
    Login({ commit }, userInfo) {
      console.log("这里开始登陆")
      return new Promise((resolve, reject) => {
        Axios('account/login', userInfo).then(response => {
          // const data = response.data
          console.log("叮叮叮",response.data.token)
          setToken(response.data.token)
          setCookie("zonetoken",response.data.token)
          console.log(getToken("zonetoken"))
          setCookie("uid",response.data.uid)
          commit('SET_TOKEN', response.data.token)
          commit('SET_TOKEN', response.data.uid)
          commit('SET_ROLES', response.data.role_id)
          commit('SET_NAME', response.data.username)
          commit('SET_LOGIN',1)
          Axios("account/getaccountinfo").then(res=>{
            setCookie("groupname",res.data.group_name)
            setCookie("roleid",res.data.role_id)
            setCookie("username",res.data.username)
            resolve()
          })
         
        }).catch(error => {
          console.log(error)
          reject(error)
        })
      })
    },

    // 获取动态路由信息
    GetRouter({ commit,state}){
      return new Promise((resolve,reject)=>{
        Axios('account/getroleslist').then(res=>{
          if(res.code==200){
            const data=res.data;
            commit('SET_ROUTERLIST', data.roles)
          }else{
            reject(err)
          }
        }).catch(err=>{
          reject(err)
        })
      })
    },
    // 获取用户信息
    GetInfo({ commit, state }) {
      return new Promise((resolve, reject) => {
        Axios('account/getaccountinfo').then(response => {
          const data = response.data
          setCookie("groupname",data.group_name)
            setCookie("roleid",data.role_id)
            setCookie("username",data.username)
          commit('SET_ROLES', data.roles)
          commit('SET_NAME', data.username)
          commit('SET_LOGIN',0)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 登出
    LogOut({ commit, state }) {
        console.log("登出")
      return new Promise((resolve, reject) => {
        Axios('account/logout').then((res) => {
          commit('SET_TOKEN', '')
          commit('SET_ROLES', [])
          removeToken()
          removeCookie("uid")
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 前端 登出
    FedLogOut({ commit }) {
      console.log("登出11")
      return new Promise(resolve => {
        commit('SET_TOKEN', '')
        removeToken()
        resolve()
      })
    }
  }
}

export default user
