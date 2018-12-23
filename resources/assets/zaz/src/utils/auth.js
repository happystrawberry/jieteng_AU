import Cookies from 'js-cookie'

const TokenKey = 'zonetoken'

export function getToken() {
  return Cookies.get(TokenKey)
}
export function getCookie(name){
   return Cookies.get(name)
}

export function setToken(token) {
  return Cookies.set(TokenKey, token)
}
export function setCookie(name,value){
  return Cookies.set(name, value)
}
export function removeCookie(name){
  return Cookies.remove(name)
}
export function removeToken() {
  return Cookies.remove(TokenKey)
}
