<template>
      <div class="paging">
          <el-pagination
          :page-size="pageing.pagesize"
          @current-change="handleCurrentChange"
          :current-page="pageing.curpage"
          layout="total, prev, pager, next, jumper"
          :total="parseInt(pageing.totalnum)"
          >
        </el-pagination>
      </div>
</template>

<script>
export default {
    props:{
      pageing:{
        type:Object,
        default:{}
      }
    },
    methods: {
      handleSizeChange(){
        console.log(1)
      },
      handleCurrentChange(index){
         this.$emit('pagechange',index);
        
      }
    }
}
</script>

<style scoped lang='scss'>
    .paging{
      text-align:center;
      margin-top:50px;
    }
    .el-input__inner{
      width:50px !important;
    }
</style>