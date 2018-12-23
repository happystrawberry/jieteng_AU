<template>
  <div>
      <el-form 
    :inline="true" 
    :model="formInline" 
    class="demo-form-inline"> 
            <el-form-item >
                <el-input  size="mini" v-model="formInline.user" @keyup.enter.native="search" placeholder="请输入品牌型号"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button size="mini" type="primary" @click="search" >搜索</el-button>
            </el-form-item>
        </el-form>
  </div>
    
</template>

<script>
export default {
  props:{
    formInline:{
      type:Object
    }
  },
 data(){
   return{
     
   }
 },
 mounted(){
 },
  methods: {
    search(){
      this.$emit("search",this.formInline)
    },
   
  }

}
</script>

<style scoped lang='scss'>

</style>
