import request from '@/utils/request'

export function Axios(url, data = {}, params = {}){
  return request({
    baseURL: process.env.BASE_API,
    // baseURL:"http://bss.sdober.com/api",
    url: url,
    method: 'post',
    data: JSON.stringify(data),
    params: params
  })
}
export function login(username, password) {
  return request({
    url: '/user/login',
    method: 'post',
    data: {
      username,
      password
    }
  })
}
export function getInfo(token) {
  return request({
    url: '/user/info',
    method: 'get',
    params: { token }
  })
}

export function logout() {
  return request({
    url: '/user/logout',
    method: 'post'
  })
}
