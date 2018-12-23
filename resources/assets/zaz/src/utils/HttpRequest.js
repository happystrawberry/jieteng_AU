import axios from 'axios'
import router from '../router'
import Cookies from 'js-cookie'

export default {
  install (Vue, options) {
    Vue.prototype.HttpRequest = function (url, params, upload = false) {
      let data = new FormData()
      if (upload) {
        for (var k in params) {
          data.append(k, params[k])
        }
      }

      let userId = this.$store.state.user.uid
      let userTOKEN = this.$store.state.user.token
      return axios.request({
        baseURL: Vue.prototype.globalConfig.api_prefix,
        url: url,
        method: 'post',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': upload ? 'multipart/form-data' : 'application/json',
          'uid': userId || Cookies.get('uid'),
          'token': userTOKEN || Cookies.get('token')
        },
        data: upload ? data : JSON.stringify(params),
        responseType: 'json'
      }).then(function (response) {
        if (response.data.code === 200) {
          if (response.data.msg !== 'ok' && response.data.msg !== 'back') {
            Vue.prototype.$notify({
              title: response.data.msg,
              type: 'success',
              duration: 1000
            })
          }
          return response.data.data
        } else if (response.data.code === 312) {
          Vue.prototype.$message.error(response.data.msg)
          Cookies.remove('uid')
          Cookies.remove('token')
          setTimeout(function () {
            router.replace({
              path: 'login'
            })
          }, 250)
          return false
        } else {
          Vue.prototype.$message.error(response.data.msg)
          return false
        }
      }).catch(function (error) {
        if (error) {
          Vue.prototype.$message(error)
        } else {
          Vue.prototype.$message('请求异常')
        }
        return false
      })
    }

    Vue.prototype.HttpDownload = function (url, fileName) {
      let userId = this.$store.state.user.uid
      let userTOKEN = this.$store.state.user.token
      
      return axios.request({
        baseURL: "/",
        url: url,
        method: 'post',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-Uid': Cookies.get('uid'),
          'X-Token': Cookies.get('zonetoken')
        },
        responseType: 'blob'
      }).then(function (response) {
        let url = window.URL.createObjectURL(new Blob([response.data]))
        let link = document.createElement('a')
        link.style.display = 'none'
        link.href = url
        link.setAttribute('download', fileName)
        document.body.appendChild(link)
        link.click()
      }).catch(function (error) {
        if (error) {
          Vue.prototype.$message(error)
        } else {
          Vue.prototype.$message('请求异常')
        }
        return false
      })
    }
  }
}
