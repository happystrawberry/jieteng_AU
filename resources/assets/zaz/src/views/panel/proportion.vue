<template>
      <div class="zone-content">
          <h3>税率设置</h3>
          <div class="content">
              <span>设置税率(0-100百分制)</span>
              <el-input size="mini" type="number" v-model="value" style="width:100px;margin-left:10px;margin-right:20px;" placeholder="请输入税率">
              </el-input> <el-button  style="margin:20px auto" size="mini" type="primary"  @click="complite">确定</el-button>
          </div>
          
         
         
          
      </div>
</template>


<script>
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
export default {
    components: {
        Pageing
    },
    methods: {
        getlist(params){
            Axios("setting/gettaxrate").then(res=>{
                this.value=res.data.value;
            })
        },
        complite(res){
            if(this.value==""){
                this.$message({
               showClose: true,
               message: '税率不能设置为空',
               type: 'warning'
               });
               return;
            }
             if(this.value>100||this.value<0){
                this.$message({
               showClose: true,
               message: '税率设置有误,请重试',
               type: 'warning'
               });
               return;
            }
            Axios("setting/edittaxrate",{
                val:this.value
            }).then(res=>{
               this.$message({
               showClose: true,
               message: '修改税率成功',
               type: 'success'
               });
               this.getlist();
            })
        }
    },
    mounted(){
       this.getlist();
    },
    data() {
        return {
           value:"",

        }
    },
}
</script>


<style  lang='scss'>


</style>
