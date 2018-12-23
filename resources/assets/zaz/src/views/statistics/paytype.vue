<template>
    <div class="zone-content">
       <el-form  :inline="true" :model="formInline" class="demo-form-inline">
           <el-form-item
          class="otherinput"
        >
          <el-select 
          size="mini"
           clearable
           filterable
           multiple
           @remove-tag="removetag"
          v-model="formInline.checkconsultant" placeholder="选择支付类型类型">
            <el-option
            style="width:200px"
            v-for="(item,index) in formInline.consultant"
            :key="index" 
            :label="item.name" 
            :value="item.id"></el-option>
          </el-select>
        </el-form-item>
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
          <el-form-item   >
                        <el-button  style="transform:translateY(-1px)" size="mini" type="primary"   @click="onSubmit">搜索</el-button>
        </el-form-item>
       </el-form>  
       <div v-if="keyword&&formInline.time">
          <div class="title">
            <h3>支付方式统计图</h3>
            <el-button  style="transform:translateY(-1px)" size="mini" type="primary"  @click="donwload">下载数据</el-button>
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
                align="center"
                border
                style="width:200px">
                <el-table-column
                  prop="pay_way"
                  label="支付方式"
                  align="center"
                  header-align="center"
                  width="100">
                </el-table-column>
                <el-table-column
                  prop="amount"
                  label="金额"
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
                style="width:1400px">
                <el-table-column
                  prop="order_sn"
                  label="单号"
                  align="center"
                  header-align="center"
                  width="200"
                  >
                </el-table-column>
                <el-table-column
                  prop="pay_time"
                  label="结算时间"
                  align="center"
                  width="180"
                  header-align="center"
                  >
                </el-table-column>
                 <el-table-column
                  label="支付宝"
                   prop="out_time"
                  align="center"
                  header-align="center"
                  >
                  <template slot-scope="scope">
                    {{scope.row.pay_ways[1].cost}}
                </template>
                </el-table-column>
                <el-table-column
                  label="现金"
                   prop="out_time"
                  align="center"
                  header-align="center"
                  >
                    <template slot-scope="scope">
                    {{scope.row.pay_ways[2].cost}}
                </template>
                </el-table-column>
                <el-table-column
                  label="银行卡"
                   prop="out_time"
                  align="center"
                  header-align="center"
                  >
                    <template slot-scope="scope">
                    {{scope.row.pay_ways[3].cost}}
                </template>
                </el-table-column>
                <el-table-column
                  label="挂账"
                   prop="out_time"
                  align="center"
                  header-align="center"
                  >
                    <template slot-scope="scope">
                    {{scope.row.pay_ways[4].cost}}
                </template>
                </el-table-column>
                <el-table-column
                  label="活动券"
                   prop="out_time"
                  align="center"
                  header-align="center"
                  >
                    <template slot-scope="scope">
                    {{scope.row.pay_ways[5].cost}}
                </template>
                </el-table-column>
                <el-table-column
                  label="其他"
                   prop="out_time"
                  align="center"
                  header-align="center"
                  >
                <template slot-scope="scope">
                    {{scope.row.pay_ways[6].cost}}
                </template>
                </el-table-column>
                <el-table-column
                  label="合计"
                   prop="out_time"
                  align="center"
                  header-align="center"
                  >
                    <template slot-scope="scope">
                      {{totalnum(scope.row)}}
                  </template>
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
          consultant:[{id:"1",name:"支付宝"},{id:"2",name:"微信"},{id:"3",name:"现金"},{id:"4",name:"会员卡"},{id:"5",name:"挂账"},{id:"6",name:"刷卡支付"},{id:"7",name:"活动券"},{id:"8",name:"其他"}],
          },
          keyword:false,
          page:{},
          list:[],
          lists:[],
          chartData:{
          columns: ['支付方式', "金额"],
          rows:[]
        },
      }
   },
   mounted() {
    
   },
   methods: {
     checkdate(time){
         return parseTime(time).split(" ")[0]
     },
     totalnum(res){
        let num=0;
        for(let i in res.way_cost){
           num+=parseInt(res.way_cost[i])
        }
        return num;
     },
     removetag(value){
        if(this.keyword){
            this.getinfo();
         this.getlist({
            pay_way:this.formInline.checkconsultant.join(","),
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
         })
        }
     },
     donwload(){
         let ele = this.formInline;
           this.HttpDownload(`/api/stat/paywaylist?page=${
          this.page.curpage
        }&export=1&pay_way=${ele.checkconsultant.join(",")}&begin_time=${
          ele.time? ele.time[0] / 1000 : ""
        }&end_time=${ele.time? ele.time[1] / 1000 : ""}`, "支付方式列表"+ '.xls')
      },
     getSummaries(){
       const { columns, data } = param;
        const sums = [];
        columns.forEach((column, index) => {
          if (index === 0) {
            sums[index] = '总进厂量';
            return;
          }
          const values = data.map(item => Number(item[column.property]));
          if (!values.every(value => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
           
          } else {
           
          }
        });

        return sums;
      },
     pagechange(value){
        this.getlist({
            pay_way:this.formInline.checkconsultant.join(","),
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
             page:value
         })
     },
     getinfo(){
       Axios("stat/payway",{
              pay_way:this.formInline.checkconsultant.join(","), 
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
      }).then(res=>{
        this.keyword=true;
         let list=res.data.list,
              row=[],
              lists=[],
              newlist=[];
         for(let i in list){
            row.push({
                  "支付方式":list[i].pay_way,
                  "金额":list[i].amount
              })
         }
        this.chartData.rows=row;
        this.lists=res.data.list;
      })
     },
     checkdetail(scope){
        let routeData = this.$router.resolve({
          name: "orrdersgoodsdetails",
          query: {
            id:scope.row.order_id
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
            pay_way:this.formInline.checkconsultant.join(","),
             begin_time: this.formInline.time ? this.formInline.time[0] / 1000 : "",
             end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
         })
         
      },
      getlist(params){
         Axios("stat/paywaylist",params).then(res=>{
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
