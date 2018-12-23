<template>
    <div class="imgupload">
             <el-upload
                    class="upload-demo"
                    :on-success="getimg"
                    :before-upload="beforeimg"
                    drag
                    action="/api/common/upload"
                    >
                    <i class="el-icon-upload"></i>
                    <img v-if="data" :src="data" class="avatar">
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    </el-upload>
    </div>
</template>

<script>
export default {
  name: "iconlist",
  props: {
    data: {
      type:Object,
      default:{}
    },
  
  },
  methods: {
      getimg(res){
          let that=this;
          console.log(this)
          this.$emit("checkimg",{
              item:that.data,
              image:res.data.url
          })
      },
      beforeimg(){
          let that=this;
          console.log(this)
           this.$emit("checkimg",{
              item:that.data,
              image:""
          })
      }
  }
};
</script>

<style scoped lang="scss">

</style>
