<template>
   <div class="zone-content index">
        <comheader 
         @search="search"
     :formInline="formInline"
        ></comheader>
        <iconlist 
         :data="counts"
    :numlist="numlist"
    @numlists="numlists"
        ></iconlist>
        <div class="dashboard-container-list" v-if="!nodata">
        <el-row :gutter="20">
            <el-col 
             v-for="(item,index) in list"
              :key="index"
            shadow="always" :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                <div class="grid-content bg-purple">
                    <div class="list-name margin">
                    <span>单号: {{item.order_sn}}</span>
                    <el-tag >{{item.order_status}}</el-tag>
                    </div>
                    <div class="list-mode margin">
                    <span>到店时间：{{checkdate(item.arrive_time)}}</span>
                    </div>
                    <div class="list-mode margin">
                    <span>完工时间：{{item.end_time?checkdate(item.end_time):'未完工'}}</span>
                    </div>
                    <div class="list-counselor margin">
                    业务类别：{{item.service_items}}
                    </div>
                    <div class="list-time margin">
                    联系人：{{item.contact_name}}
                    </div>
                    <div class="list-over margin">
                    车牌：{{item.vehicle_number}}
                    </div>
                    <div class="list-over margin">
                    结算人员：{{item.bill_operator}}
                    </div>
                    <div class="list-over ">
                    <el-button @click="details(item.order_id)" size="mini" type="text">查看详情</el-button>
                    <span>总计:{{item.total_cost}}(元)</span>
                    </div>
                </div>
            </el-col>
        </el-row>
        </div>
        <div class="nodata" v-else>
          <img src="../../assets/nodata.png"  style="display:block;margin:240px auto 20px;" alt="">
          <p>暂时还没有数据哟</p>
        </div>
             <pageing   @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>

   </div>
</template>

<script>
import Iconlist from "@/components/Iconlist/Iconlist";
import Comheader from "@/components/comHeader/comheader";
import { parseTime } from "@/utils/index";
import Pageing from "@/components/Paging/Paging";
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
     
      this.$router.push({ name: "servicedetails", query: { id: index } });
    },
    checkdate(time){
      return parseTime(time)
    },
    pagechange(value){
        let el=this.formInline;
      this.getlist({
        page: value,
        vehicle_number: el.otherplate,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    search(value){
       let el=this.formInline;
      this.getlist({
        page: 1,
        vehicle_number: el.otherplate,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    numlists(value){
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
        vehicle_number: value.otherplate,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    getlist(params,type=false){
      Axios("service/list",params).then(res=>{
        let list=res.data.list;
        for(let i in res.data.list){
          list[i].plan_date=parseTime(list[i].plan_endtime);
        }
        this.list = res.data.list;
        this.nodata=res.data.list.length==0;
        if(type){
          this.numlist=[-1];
        //   for(let i in res.data.counts){
        //   this.numlist.push(res.data.counts[i].order_status)
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
      })
    }
  },
  mounted(){
    this.getlist({},true)
  },
  data() {
    return {
       numlist:[],
        counts:[],
        nodata:false,
        list:[],
        orderlist:"",
        page: {},
        checkList:[],
      formInline: {
        otherplate: "",
        time: ""
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
