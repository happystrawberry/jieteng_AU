<template>
     <div class="zone-content">
       <el-form @keyup.enter.native="onSubmit" :inline="true" :model="formInline" class="demo-form-inline">
           <el-form-item v-show="!formInline.hidetime"  v-if="(formInline.time||formInline.time=='')||formInline.time==null">
          <el-date-picker
            size="mini"
            v-model="formInline.time"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            value-format="timestamp"
            :picker-options="pickerOptions0"
            :default-time="['00:00:00', '23:59:59']"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
          <el-form-item>
                        <el-button  style="transform:translateY(-1px)" size="mini" type="primary" @keyup.enter.native="onSubmit"  @click="onSubmit">搜索</el-button>
        </el-form-item>
       </el-form>  
       <div class="content-ct" v-if="multipleTable.length>0">
         <div class="title">
            <h3>薪酬核算</h3>
            <el-button   size="mini" type="primary"   @click="donwload">下载数据</el-button>
            <el-button   size="mini" type="primary"  @click="checksure">确定提成奖励</el-button>
         </div>
         <div class="ct-show">
           <p>查询时间:{{checkdate(formInline.time[0]/1000)}}-{{checkdate(formInline.time[1]/1000)}}</p>
           <p>总营业额:{{turnover}}</p>
           <p>提成比例:{{rate*100}}%</p>
           <p>总提成金额:{{turnover*rate}}</p>
         </div>
          <h3 class="title">业绩列表</h3>
       <div>
          <el-table
                    ref="multipleTable"
                    border
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width: 1400px"
                    stripe
                    >
                    <el-table-column
                    label="单号"
                    header-align="center"
                    align="center"
                    prop="order_sn"
                    width="200">
                    </el-table-column>
                    <el-table-column
                    label="结算时间"
                    header-align="center"
                    align="center"
                    prop="order_sn"
                    >
                     <template slot-scope="scope">
                       {{checkdate(scope.row.pay_time)}}
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="username"
                    header-align="center"
                    align="center"
                    label="维修人员"
                    width="200">
                      <template slot-scope="scope">
                       <div  v-for="(item,index) in scope.row.commission_list" :key="index">{{item.operator}}</div>
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="total_times"
                    header-align="center"
                    align="center"
                    label="总工时"
                    width="100"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="order_cost"
                    header-align="center"
                    align="center"
                    label="总工时费"
                    width="100"
                    >
                    </el-table-column>
                      <el-table-column
                    prop="order_cost"
                    header-align="center"
                    align="center"
                    label="核算状态"
                    width="100"
                    >
                     <template slot-scope="scope">
                       {{scope.row.is_check==1?'已核算':'未核算'}}
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="commission_cost"
                    header-align="center"
                    align="center"
                    label="提成工时费(元)"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="wallet_birthday"
                    header-align="center"
                    align="center"
                    label="提成(元)"
                    width="200"
                    >
                    <template slot-scope="scope">
                       <div  v-for="(item,index) in scope.row.commission_list" :key="index">{{item.commission}}</div>
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="specification"
                    header-align="center"
                    align="center"
                    label="操作"
                    >
                    <template slot-scope="scope">
                        <el-button size="mini" type="primary" @click="gotodetails(scope.row.id)">查看详情</el-button>
                    </template>
                    </el-table-column>
                </el-table>
                <pageing  @pagechange="pagechange"  :pageing="page"></pageing>
       </div>
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
    data(){
      return {
        formInline:{
          time:""
        },
         pickerOptions0:{
          disabledDate(time) {
            return time.getTime() > Date.now()-86400000;
          }
      },
        rate:"",
        turnover:"",
        multipleTable:[],
        page:{}
      }
    },
    mounted () {
        this.$message({
          showClose: true,
          message:"请点击日历查询",
        });
    },
    methods: {
      checkdate(time){
        return parseTime(time);
      },
      getlist(params){
        Axios("settlement/accountlist",params).then(res=>{
          this.$message({
          showClose: true,
          message:"数据请求成功",
          type: "success"
        });
          this.multipleTable=res.data.list;
          let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
            this.rate=res.data.rate;
            this.turnover=res.data.turnover;
        })
      },
      pagechange(value){
         this.getlist({
          begin_time:this.formInline.time[0]/1000,
          end_time:this.formInline.time[1]/1000,
          page:value
        });
      },
      gotodetails(id){
         let routeData = this.$router.resolve({
          name: "settlemendetails",
          query: {
            id:id
          }
        });
        window.open(routeData.href, "_blank");
      },
      donwload(){
         let ele = this.formInline;
          this.HttpDownload(`/api/settlement/accountlist?page=${
          this.page.curpage
        }&export=1&begin_time=${
          ele.time? ele.time[0] / 1000 : ""
        }&end_time=${ele.time? ele.time[1] / 1000 : ""}`, "业绩列表"+ '.xls')
      },
      checksure(){
        Axios("settlement/accounting",{
          begin_time: this.formInline.time? this.formInline.time[0] / 1000 : "",
          end_time: this.formInline.time? this.formInline.time[1] / 1000 : "",
        }).then(res=>{
            this.$message({
          showClose: true,
          message:"确认提成奖励成功",
          type: "success"
        });
        })
      },
      onSubmit(value){
       if(!this.formInline.time){
         this.$message({
          showClose: true,
          message:"请选先选择时间",
          type: "warning"
        });
       }else{
         this.getlist({
          begin_time:this.formInline.time[0]/1000,
          end_time:this.formInline.time[1]/1000,
          page:1
        });
       }
      }
    }

}
</script>

<style scoped lang='scss'>
    .content-ct{
      margin-top:40px;
      .title{
        display:flex;
        margin:30px 0;
         align-items: center;
        h3{
          margin-right:30px;
        }
      }
      .ct-show{
        display:flex;
        margin-top:20px;
        p{
          flex:0 0 300px;
          &:first-of-type{
            flex:0 0 500px;

          }
        }
      }
    }
</style>