<template>
   <div class="zone-content index">
        <comheader :formInline="formInline"  @search="search"></comheader>
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
                <span>到店时间:{{checkdate(item.arrive_time)}}</span>
                </div>
                <div class="list-mode margin">
                <span>完工时间：{{checkdate(item.plan_endtime)}}</span>
                </div>
                <div class="list-counselor margin">
                业务类别：{{item.service_items}}
                </div>
                <div class="list-time margin">
                车牌：{{item.vehicle_number}}
                </div>
                <div class="list-over margin">
                车型：{{item.vehicle_model}}
                </div>
                <div class="list-over margin" >
                洗车进度:{{item.need_wash==0?'不需要洗车':item.wash_status==0?'洗车未开始':'已完成洗车'}}
                </div>
                <div class="list-over ">
                 <el-button type="text" @click="gotodetails(item.order_id)">查看详情</el-button>   
                 <el-button v-show="item.need_wash==1&&item.wash_status==0" @click="wash(item)" size="mini" type="primary">完成洗车</el-button>
                <!-- <el-tag >打印增料单</el-tag> -->
                </div>
            </div>
            </el-col>
        </el-row>
        </div>
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
import { setTimeout } from 'timers';
export default {
  name: "zhijian",
  components: {
    Iconlist,
    Comheader,
    Pageing
  },
  methods: {
    gotodetails(index) {
      this.$router.push({ name: "qualitydetails", query: { id: index } });
    },
    wash(item){
      Axios("quality/setwashdone",{
        order_id:item.order_id
      }).then(res=>{
          this.$message({
            message: "洗车成功",
            type: "success"
            }); 
      })
      let el=this.formInline;
      let that=this;
      setTimeout(()=>{
         this.getlist({
           page:that.page.curpage,
           order_sn:el.order_sn,
           order_status: this.orderlist,
           vehicle_number: el.otherplate,
           begin_time: el.time? el.time[0] / 1000 : "",
           end_time: el.time? el.time[1] / 1000 : "",
       })
      },500)
    
    },
    checkdate(time) {
      return parseTime(time);
    },
    search(value){
        let el=this.formInline;
       this.getlist({
           page:1,
           order_sn:el.order_sn,
           order_status: this.orderlist,
           vehicle_number: el.otherplate,
           begin_time: el.time? el.time[0] / 1000 : "",
           end_time: el.time? el.time[1] / 1000 : "",
       })
    },
    getlist(params,type=false){
        Axios("quality/list",params).then(res=>{
            let list = res.data.list;
            if (type) {
                this.numlist=[-1];
            // for (let i in res.data.counts) {
            //     this.numlist.push(res.data.counts[i].order_status);
            // }
            }
            this.counts = res.data.counts;
            let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
            this.list = res.data.list;
            this.nodata=res.data.list.length==0;
            this.$message({
            message: "数据已更新",
            type: "success"
            }); 
        })
    },
    pagechange(value) {
        let el=this.formInline;
        this.getlist({
           page:value,
           order_sn:el.order_sn,
           order_status: this.orderlist,
           vehicle_number: el.otherplate,
           begin_time: el.time? el.time[0] / 1000 : "",
           end_time: el.time? el.time[1] / 1000 : "",
       })
    },
    numlists(value) {
           this.numlist = value;
      console.log("value",value)
      //这里做下转换
      if(this.numlist.indexOf(-1)>-1){
        //即为全部
        this.orderlist="";
      }else{
        this.orderlist=this.numlist.join(",")
      }
        this.orderlist=this.orderlist==-1?"":this.orderlist;
      console.log(value)
      let el=this.formInline;
      this.getlist({
          page: 1,
          order_sn:el.order_sn,
         vehicle_number: el.plate,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });

    }
  },
  mounted () {
      this.getlist({},true);
  },
  data() {
    return {
      formInline: {
        otherplate: "",
        time: "",
        order_sn:""
      },
      nodata:false,
      orderlist:"",
      numlist: [],
      counts: [],
      list: [],
      page:{}, 
    };
  }
};
</script>

<style scoped>
</style>
