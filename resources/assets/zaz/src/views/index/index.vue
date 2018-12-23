<template>
  <div class="dashboard-container">
    <carousel 
    @checkbtn="checkbtn(false)"
    :showinfokey="showinfokey"
    @getmore="getmore"
    :page="checkpage"
    :showinfo="checklist"></carousel>
    <comheader 
    @search="search"
      @checkbtn="checkbtn(true)"
    @neworder="neworder"
    :formInline="formInline"
    ></comheader>
    <iconlist 
    :data="counts"
    :numlist="numlist"
    @numlists="numlists"
    ></iconlist>
    <div class="dashboard-container-list" v-if="!nodata">
      <el-row :gutter="30">
        <el-col 
        v-for="(item,index) in list"
        :key="index"
        shadow="always" 
        :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
          <div class="grid-content bg-purple">
            <div class="list-name margin">
              <span>{{item.vehicle_number}} {{item.contact_name}} {{item.phone}}</span>
              <el-tag >{{item.order_status}}</el-tag>
            </div>
            <div class="list-mode margin">
              <span>业务类型: 
                <el-tag 
                size="mini"
                v-if="item.service_array.length>0&&itemd"
                style="margin-left:5px;padding:0 10px;"
                v-for="(itemd,indexs) in item.service_array"
                :key="indexs"
                >{{itemd}}</el-tag>
                <el-tag  size="mini" type="danger"  style="padding:0 10px;" v-if="item.service_array.length==0"> 暂无</el-tag>
              </span>
              <span>排号: {{checkdate(item.arrive_time,'{y}-{m}-{d}')}} 第{{item.wait_number}}位</span>
            </div>
             <div class="list-counselor margin">
              订单号: {{item.order_sn}}
            </div>
            <div class="list-counselor margin">
              服务顾问: {{item.service_consultant}}
            </div>
            <div class="list-time margin">
              创建时间: {{item.create_date}}
            </div>
            <div class="list-time margin">
              到店时间: {{item.arrive_date?item.arrive_date:"未确定"}}
            </div>
            <div class="list-over margin">
              预计完工时间: {{item.plan_date}}
            </div>
            <div style="line-height:32px">
              <span v-show="item.counttimes" class="tips" style="font-size:13px;">
                已经超时:
               <span v-show="item.counttimes.days>0">{{item.counttimes.days}}天</span>
               <span v-show="item.counttimes.hours>0">{{item.counttimes.hours}}小时</span>
              <span v-show="item.counttimes.minutes>0">{{item.counttimes.minutes}}分钟</span>
              </span>
              <el-tag size="samll" v-show="!item.counttimes">暂未超时</el-tag>
            </div>
            <div class="list-level " style="display:block">
              <el-progress :text-inside="true" :stroke-width="18" :percentage="parseFloat(item.progress)" color="#66b1ff"></el-progress>
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
let listtime;
import waves from "@/directive/waves"; // 水波纹指令
import Iconlist from "@/components/Iconlist/Iconlist";
import Comheader from "@/components/comHeader/comheader";
import carousel from "@/components/Carousel/carousel";
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
import { setTimeout } from 'timers';
export default {
  name: "index",
  components: {
    Iconlist,
    Comheader,
    Pageing,
    carousel
  },
  directives: {
    waves
  },
  data() {
    return {
      statuslist: [""],
      nodata:false,
      lists:[],
      formInline: {
        plate: "",
        phone: "",
        names: "",
        user: "",
        ordernum: "",
        time: "",
        neworder: "",
        showinfo:true
      },
      checkpage:{},
      page: {},
      otherpage:{},
      showinfokey:false,
      list: [],
      checklist:[],
      orderlist:"",
      numlist: [-1],
      counts: []
    };
  },
  watch:{
      list(newvalue){
        console.log("变化",newvalue)
        let lists=[];
        for(let i in newvalue){
            lists.push(newvalue[i])
        }
        this.lists=lists;
      }
  },
  beforeDestroy(){
    clearInterval(listtime)
  },
  mounted() {
    let that=this;
    this.getlist({},true,false,false,);
    this.getlist({
        page: this.otherpage.nextpage>0?this.otherpage.nextpage:1,
        order_status:"",
        vehicle_number:"",
        begin_time: "",
        end_time: "",
        order_sn:"",
        phone: "",
        vehicle_owner: "",
        contact_name:""
      },true,true,true)
    listtime=setInterval(()=>{
       this.getlist({
        page: this.otherpage.nextpage>0?this.otherpage.nextpage:1,
        order_status:"",
        vehicle_number:"",
        begin_time: "",
        end_time: "",
        order_sn:"",
        phone: "",
        vehicle_owner: "",
        contact_name:""
      },true,true,true)
    },10000)
  },
  filters: {
    capitalize: function(value) {
      var shoppingList = this.list;
      if(this.numlist.length==0){
        return shoppingList
      }else{
        return shoppingList.filter(function(item) {
        return this.numlist.indexOf(item.status_num > -1);
         });
      }
      
    }
  },
  computed:{
      capitalize: function(value) {
        let that=this;
      var shoppingList = this.list;
      if(this.numlist.length==0){
        return shoppingList
      }else{
        return shoppingList.filter(function(item) {
        return that.numlist.indexOf(item.status_num)>-1;
         });
      }
    }
  },
  methods: {
    handleFilter(e) {
    },
    checkbtn(type){
      this.showinfokey=type;
       this.$message({
        message: "切换成功",
        type: "success"
      });
    },
    checkbtns(type){
       this.showinfokey=type;
    },
    getmore(type){
      let page=type==1?parseInt(this.checkpage.curpage)-1:parseInt(this.checkpage.curpage)+1;
      this.getlist({
        page: page,
        order_status:"",
        vehicle_number:"",
        begin_time: "",
        end_time: "",
        order_sn:"",
        phone: "",
        vehicle_owner: "",
        contact_name:""
      },true,true)
    },
    checkdate(time,type){
      return parseTime(time,type)
    },
    neworder() {
      this.$router.push({ name: "orderdetails" });
    },
    counttime(item){
        if(new Date(item.plan_endtime*1000)>new Date()){
          return  false
        }else{
          let Dtime=new Date().getTime()-new Date(item.plan_endtime*1000).getTime(),
              rDate={};
          rDate.days=Math.floor(Dtime/(24*3600*1000));
        var leave1=Dtime%(24*3600*1000);
        rDate.hours=Math.floor(leave1/(3600*1000));
        var leave2=leave1%(3600*1000);
        rDate.minutes=Math.floor(leave2/(60*1000));
        var leave3=leave2%(60*1000);
        rDate.seconds=Math.round(leave3/1000);
        return rDate;
        }
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
      let el=this.formInline;
      this.getlist({
        page: 1,
        order_status:this.orderlist,
        vehicle_number: el.plate,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
        order_sn: el.ordernum,
        phone: el.phone,
        vehicle_owner: el.names,
        contact_name: el.user
      });
    },
    pagechange(value) {
      let el = this.formInline;
      this.getlist({
        page: value,
        order_status: this.orderlist,
        vehicle_number: el.plate,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
        order_sn: el.ordernum,
        phone: el.phone,
        vehicle_owner: el.names,
        contact_name: el.user
      });
    },

    getlist(params,type=false,other=false,showkey=false) {
      let that=this;
      Axios("dashboard/progress", params).then(res => {
        /*
            0作废1已接车2已开单3已调度4已开工5已质检(已完工)6已结算7已回访 99已暂停
          */
        let list = res.data.list;
        for (let i in res.data.list) {
          list[i].arrive_date = parseTime(list[i].arrive_time);
          list[i].plan_date = parseTime(list[i].plan_endtime);
          list[i].create_date = parseTime(list[i].create_time);
          list[i].service_array = list[i].service_items==""?[]:list[i].service_items.split(",");
          switch (list[i].order_status) {
            case "作废":
              list[i].status_num = 0;
              break;
            case "已接车":
              list[i].status_num = 1;
              break;
            case "已开单":
              list[i].status_num = 2;
              break;
            case "已调度":
              list[i].status_num = 3;
              break;
            case "已开工":
              list[i].status_num = 4;
              break;
            case "已质检(已完工)":
              list[i].status_num = 5;
              break;
            case "已结算":
              list[i].status_num = 6;
              break;
            case "已回访":
              list[i].status_num = 7;
              break;
            case "已暂停":
              list[i].status_num = 99;
              break;
          }
        }
        if(type){
          this.numlist=[-1];
          // for(let i in res.data.counts){
          // this.numlist.push(res.data.counts[i].order_status)
          // }
        }
        // 这里进行下排序
        let countslist=[];
        for(let i in res.data.counts){
          switch(res.data.counts[i].order_status){
              case 1 :
              countslist[0]=res.data.counts[i];
              break;
              case 3 :
              countslist[1]=res.data.counts[i];
              break;
              case 4 :
              countslist[2]=res.data.counts[i];
              break;
              case 5 :
              countslist[3]=res.data.counts[i];
              break;
              case 99 :
              countslist[4]=res.data.counts[i];
              break;
              case 5 :
              countslist[5]=res.data.counts[i];
              break;
              case 6 :
              countslist[6]=res.data.counts[i];
              break;
              case 7 :
              countslist[7]=res.data.counts[i];
              break;
              case -1 :
              countslist[8]=res.data.counts[i];
              break;
          }
        }
        for(let i in list){
          list[i].counttimes=that.counttime(list[i]);
        }
        this.nodata=list.length==0;
        if(!showkey){
            this.list = list;
            this.counts = res.data.counts;
            let page=res.data.page;
            for(let i in page){
                  page[i]=parseInt(page[i])
                }
                this.page=page;
            if(other){
              this.checklist=list;
              this.checkpage=res.data.page;
            }
        }else{
               let page=res.data.page;
            for(let i in page){
                  page[i]=parseInt(page[i])
                }
                this.otherpage=page;
              this.checklist=list;
        }
        
      });
    },
    search(value) {
      this.getlist({
        page:1,
        order_status: this.orderlist,
        vehicle_number: value.plate,
        begin_time: value.time? value.time[0] / 1000 : "",
        end_time: value.time? value.time[1] / 1000 : "",
        order_sn: value.ordernum,
        phone: value.phone,
        vehicle_owner: value.names,
        contact_name: value.user
      });
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" >
.main-container{
  background:white;
}
.dashboard {
  background:white;
  .active {
    color: #409eff;
    border-color: #c6e2ff;
    background-color: #ecf5ff;
  }
  .tips{
    font-size:12px;
  }
  &-container {
    margin: 20px;
    &-list {
      margin-top: 30px;
    }
  }
}
</style>
