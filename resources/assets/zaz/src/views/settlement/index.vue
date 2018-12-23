<template>
   <div class="zone-content index">
        <comheader  
        @search="search"
      :formInline="formInline"></comheader>
        <iconlist
        :data="counts"
    :numlist="numlist"
    @numlists="numlists" 
        ></iconlist>
        <el-table
              v-if="!nodata"
              type="index"
              :stripe=true
              ref="multipleTable"
              :data="list"
              border
              class="printprimary"
              tooltip-effect="dark"
              style="width:100%"
              >
              <el-table-column
                label="单号"
                prop="order_sn"
                align="center"
              header-align="center"
                >
              </el-table-column>
             
              <el-table-column
                prop="unit"
                align="center"
              header-align="center"
                label="到店时间"
                show-overflow-tooltip>
                <template slot-scope="scope">
                {{scope.row.arrive_time>0?checkdate(scope.row.arrive_time):''}}
                </template>
              </el-table-column>
              <el-table-column
                prop="amount"
                align="center"
              header-align="center"
                label="完工时间"
                show-overflow-tooltip>
                 <template slot-scope="scope">
                {{scope.row.plan_endtime>0?checkdate(scope.row.plan_endtime):''}}
                </template>
              </el-table-column>
              
               <el-table-column
                label="业务类别"
                prop="service_items"
                align="center"
              header-align="center"
                >
              </el-table-column>
               <el-table-column
                label="联系人"
                prop="contact_name"
                align="center"
              header-align="center"
                width="120">
              </el-table-column>
              <el-table-column
                label="联系电话"
                prop="phone"
                align="center"
              header-align="center"
                width="120">
              </el-table-column>
               <el-table-column
                label="车牌"
                prop="vehicle_number"
                align="center"
              header-align="center"
                width="120">
              </el-table-column>
              <el-table-column
                label="结算人员"
                prop="bill_operator"
                align="center"
              header-align="center"
                width="120">
              </el-table-column>
              <el-table-column
                label="总计金额"
                prop="total_cost"
                align="center"
              header-align="center"
                width="120">
              </el-table-column>
               <el-table-column
                prop="order_status"
                align="center"
              header-align="center"
                label="订单状态"
                width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                label="操作"
                 align="center"
                  width="100"
                show-overflow-tooltip>
                <template slot-scope="scope">
                  <el-button size="small" slot="reference" type="danger" @click="details(scope.row.order_id)">查看详情</el-button>
                </template>
              </el-table-column>
            </el-table>
        <div class="nodata" v-else>
          <img src="../../assets/nodata.png"  style="display:block;margin:240px auto 20px;" alt="">
          <p>暂时还没有数据哟</p>
        </div>
     <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
   </div>
</template>

<script>
import Iconlist from "@/components/Iconlist/Iconlist";
import Comheader from "@/components/comHeader/comheader";
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
export default {
  name: "zhijian",
  components: {
    Iconlist,
    Comheader,
    Pageing
  },
  methods: {
    details(index) {
      // vehicle_number
      this.$router.push({ name: "settlemendetails", query: { id: index } });
    },
    checkdate(time){
      return parseTime(time)
    },
    pagechange(value){
      let el=this.formInline;
     this.getlist({
        page: value,
        order_sn:el.order_sn,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    numlists(value){
      console.log("value",value)
        this.numlist = value;
      //这里做下转换
      if(this.numlist.indexOf(-1)>-1){
        //即为全部
        this.orderlist="";
      }else{
        this.orderlist=this.numlist.join(",")
      }
        this.orderlist=this.orderlist==-1?"":this.orderlist;
      let el=this.formInline;
      this.getlist({
        page: 1,
        order_sn:el.order_sn,
         order_status: this.orderlist,
        vehicle_number: el.otherplate,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    search(value){
       let el=this.formInline;
      this.getlist({
        page: 1,
        order_sn:el.order_sn,
        order_status: this.orderlist,
        vehicle_number:value.otherplate,
        begin_time: value.time? value.time[0] / 1000 : "",
        end_time: value.time? value.time[1] / 1000 : "",
      });
    },
    
    getlist(params, type = false) {
      Axios("settlement/list", params).then(res => {
        let list = res.data.list;
        this.list = res.data.list;
          this.nodata=res.data.list.length==0;
        if (type) {
          this.numlist=[-1];
          // for (let i in res.data.counst) {
          //   this.numlist.push(res.data.counst[i].order_status);
          // }
        }
        this.counts = res.data.counts;
         let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        this.$message({
          message: "数据已更新",
          type: "success"
        });
      });
    }
  },
  mounted(){
    this.getlist({},true)
  },
  data() {
    return {
      numlist: [],
      nodata:false,
      counts: [],
      list: [],
      orderlist: "",
      page: {},
      formInline: {
        otherplate: "",
        time: "",
        order_sn:""
      },
      data: [
        {
          name: "我的任务",
          num: 1111
        },
        {
          name: "我的任务",
          num: 1
        },
        {
          name: "我的任务",
          num: 1
        }
      ]
    };
  }
};
</script>

<style scoped>
</style>
