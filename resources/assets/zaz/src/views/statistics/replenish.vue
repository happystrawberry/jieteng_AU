<template>
    <div class="zone-content">
       <el-form :inline="true" :model="formInline" class="demo-form-inline">
           
           <el-form-item v-show="!formInline.hidetime"  v-if="(formInline.time||formInline.time=='')||formInline.time==null">
            <el-date-picker
            size="mini"
            v-model="formInline.time"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
             :default-time="['00:00:00', '23:59:59']"
            value-format="timestamp"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
        <el-form-item
          class="otherinput"
        >
          <el-select 
          size="mini"
           clearable
           filterable
           multiple
             @remove-tag="removetag"
          v-model="formInline.checkconsultant" placeholder="选择品牌">
            <el-option
            style="width:200px"
            v-for="(item,index) in formInline.consultant"
            :key="index" 
            :label="item.name" 
            :value="item.id"></el-option>
          </el-select>
        </el-form-item>
          <el-form-item   >
                        <el-button  style="transform:translateY(-1px)" size="mini" type="primary" @keyup.enter.native="onSubmit"  @click="onSubmit">搜索</el-button>
        </el-form-item>
       </el-form>  
       <div v-if="keyword&&formInline.time">
          <div class="title">
            <h3>进厂量统计图</h3>
            <el-button  style="transform:translateY(-1px)" size="mini" type="primary"  @click.native="donwload">下载数据</el-button>
          </div>
          <div>查询时间:{{formInline.time?checkdate(formInline.time[0]/1000):''+"-"+formInline.time?checkdate(formInline.time[1]/1000):''}}</div>
          <div class="content">
              <div class="ct">
                 <ve-pie :data="chartData"></ve-pie>
              </div>
              <div class="tips" style="margin-left:100px;">
                <div >
                <el-table
                :data="lists"
                border
                show-summary
                sun-text="总进厂量"
                align="center"
                style="width:200px">
                <el-table-column
                  prop="brand"
                  label="项目"
                  align="center"
                  header-align="center"
                  width="100">
                </el-table-column>
                <el-table-column
                  prop="num"
                  label="总数量"
                  align="center"
                  header-align="center"
                  >
                </el-table-column>
              </el-table>
             </div>
              </div>
          </div>
           <div class="title">
         <h3>订单列表</h3>
       </div>
        <el-table
                :data="list"
                border
                align="center"
                style="width:600px">
                <el-table-column
                  prop="order_sn"
                  label="单号"
                  align="center"
                  header-align="center"
                  >
                </el-table-column>
                <el-table-column
                  prop="brand"
                  label="品牌"
                  align="center"
                  width="80"
                  header-align="center"
                  >
                </el-table-column>
                 <el-table-column
                  label="车型"
                  prop="brand_model"
                  align="center"
                  header-align="center"
                  >
                </el-table-column>
                  <el-table-column
                  prop="num"
                  label="操作"
                  align="center"
                  header-align="center"
                  width="120">
                    <template slot-scope="scope">
                      <el-button   size="mini" type="primary"  @click="checkdetail(scope)">查看详情</el-button>
                </template>
                </el-table-column>
              </el-table>
              <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>

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
   data() {
      return {
         formInline:{
          time:"",
          checkconsultant:"",
          consultant:[],
          },
          keyword:false,
          page:{},
          list:[],
          lists:[],
          chartData:{
          columns: ['品牌', '进厂量'],
          rows:[]
        },
      }
   },
   mounted() {
      // 获取下拉列表
       Axios("models/getbrandlist ").then(res=>{
         this.formInline.consultant=res.data.list;
      })
   },
   methods: {
     checkdate(time){
       return parseTime(time).split(" ")[0]
     },
     removetag(value){
        if(this.keyword){
            this.getinfo();
         this.getlist({
            op_type:this.formInline.checkconsultant.join(","),
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
         })
        }
     },
     pagechange(value){
       this.getlist({
            op_type:this.formInline.checkconsultant.join(","),
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
             page:value
         })
     },
     donwload(){
         let ele = this.formInline;
         this.HttpDownload(`/api/stat/orderlist?page=${
          this.page.curpage
        }&export=1&op_type=${this.formInline.checkconsultant.join(",")
        }&begin_time=${
          ele.time? ele.time[0] / 1000 : ""
        }&end_time=${ele.time? ele.time[1] / 1000 : ""}`, "进厂量订单列表"+ '.xls')
     },
     getinfo(){
       Axios("stat/order",{
             op_type:this.formInline.checkconsultant.length>0?this.formInline.checkconsultant.join(","):"",
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
      }).then(res=>{
        console.log(res.data.counts)
        this.keyword=true;
         let list=res.data.counts,
              row=[],
              lists=[],
              newlist=[];
         for(let i in list){
            row.push({
                  "品牌":list[i].brand,
                  "进厂量":list[i].num
              })
         }
        this.chartData.rows=row;
        this.lists=res.data.counts;
      })
     },
     checkdetail(scope){
       let routeData = this.$router.resolve({
          name: "orrdersgoodsdetails",
          query: {
            id:scope.row.id
          }
        });
        window.open(routeData.href, "_blank");
     },
      onSubmit(value){
        // 这里需要判断必填项
         if(this.formInline.time==""){
              this.$message({
               showClose: true,
               message: '请先填写查询时间',
               type: 'warning'
            });
            return;
         }
         this.getinfo();
         this.getlist({
            op_type:this.formInline.checkconsultant.join(","),
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
         })
         
      },
      getlist(params){
         Axios("stat/orderlist",params).then(res=>{
            this.list=res.data.list;
             let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
                  this.$message({
               showClose: true,
               message: '数据已更新',
               type: 'success'
            });
         })
      }
   }
   
}
</script>


<style  lang='scss'>
 .title{
   margin:20px 0;
   display:flex;
   h3{
     margin-right:20px;
   }
 }
 .content{
   margin-top:40px;
   display:flex;
   align-items:center;
   .ct{
     flex:0 0 400px;
   }
   .tips{
      flex:1;
   }
 }


</style>
