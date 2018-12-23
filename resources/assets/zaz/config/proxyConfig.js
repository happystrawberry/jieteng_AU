module.exports = {
  proxy: {
    '*': { //将www.exaple.com印射为/apis
      target: 'http://op.haojia.pub/api/account/', // 接口域名
      changeOrigin: true, //是否跨域
      pathRewrite: {
        '^/apis': '' //需要rewrite的,
      }
    }
  }
}
