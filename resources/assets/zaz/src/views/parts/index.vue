<template>
    <div class="partsindex zone-content">
    <comheader 
      @search="search"
     :formInline="formInline"></comheader>
    <iconlist 
    :data="counts"
    :numlist="numlist"
    @numlists="numlists"
    ></iconlist>
    <div class="partsindex-list" v-if="!nodata">
         <el-row :gutter="20">
             <el-col 
              v-for="(item,index) in list"
              :key="index"
             :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
              <div 
             
              class="grid-content bg-purple">
                <div class="list-name margin">
                <span>单号: {{item.order_sn}}</span>
                <el-tag >{{item.order_status}}</el-tag>
                    </div>
                    <div class="list-mode margin">
                      <span>到店时间: {{item.arrive_date}}</span>
                      <span>排号:{{checkdate(item.arrive_time,'{y}-{m}-{d}')}}第 {{item.wait_number}}位</span>
                    </div>
                    <div class="list-time margin">
                    预计完工时间: {{item.plan_endtime>0?checkdate(item.plan_endtime):'完工时间未知'}}
                    </div>
                    <div class="list-counselor margin">
                    车牌号:{{item.vehicle_number}}
                    </div>
                    <div class="list-time margin">
                    VIN码:{{item.vehicle_vin}}
                    </div>
                    <div class="radio margin">
                    <span>业务类别</span>
                        <el-checkbox-group v-model="item.service_items">
                            <el-checkbox  label="维修">维修</el-checkbox>
                            <el-checkbox  label="保养">保养</el-checkbox>
                            <el-checkbox  label="钣喷">钣喷</el-checkbox>
                            <el-checkbox  label="美容">美容</el-checkbox>
                            <el-checkbox  label="改装">改装</el-checkbox>
                            <el-checkbox  label="其他">其他</el-checkbox>
                        </el-checkbox-group>
                    </div>
                    <div>
                        <el-button @click="gotodetails(item.order_id)" type="text">查看详情</el-button>
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
  name: "index",
  components: {
    Iconlist,
    Comheader,
    Pageing
  },
  methods: {
    gotodetails(index){
        this.$router.push({name:"partsdetails",query:{id:index}})
    },
     checkdate(time) {
      return parseTime(time);
    },
    pagechange(value){
      let el=this.formInline;
     this.getlist({
        page: value,
        order_sn:el.order_sn,
        vehicle_number:el.plate,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    search(value){
       let el=this.formInline;
      this.getlist({
        page: 1,
        order_sn:el.order_sn,
        vehicle_number:value.plate,
        order_status: this.orderlist,
        begin_time: value.time? value.time[0] / 1000 : "",
        end_time: value.time? value.time[1] / 1000 : "",
      });
    },
    numlists(value) {
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
        vehicle_number:el.plate,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    getlist(params,type=false){
      Axios("/parts/list",params).then(res=>{
        let list=res.data.list;
        for(let i in res.data.list){
          list[i].plan_date=parseTime(list[i].plan_endtime);
        list[i].arrive_date=parseTime(list[i].arrive_time);
         
        }
        this.nodata=res.data.list.length==0;
        this.list = res.data.list;
        
        if(type){
          this.numlist=[-1];
        //   for(let i in res.data.counts){
        //   this.numlist.push(res.data.counts[i].order_status)
        // }
        }
        let counts=res.data.counts;
        for(let i in counts){
          counts[i].order_status=counts[i].pick_status
        }
        this.counts = counts;
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
    this.getlist({},true);
  },
  data(){
       return {
        formInline:{
          plate:"",
          time:"",
          order_sn:""
        },
        nodata:false,
        numlist:[-1],
        counts:[],
        list:[],
        orderlist:"",
        page: {},
        checkList:[],
        data: [{
          name: "我的任务",
          num: 1111,
        }, {
          name: "我的任务",
          num: 1,
        }, {
          name: "我的任务",
          num: 1,
        },]
      }
  },
};
</script>

<style scoped>
</style>
