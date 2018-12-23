<template>
    <div class="zone-content team">
         <comheader :formInline="formInline"
          @search="search"
         ></comheader>
         <iconlist 
         :data="counts"
         :numlist="numlist"
         @numlists="numlists"
         ></iconlist>
          <div class="dashboard-container-list" v-if="!nodata">
      <el-row :gutter="20" v-loading="loading">
        <el-col
        v-for="(item,index) in list"
        :key="index"
         shadow="always" :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
          <div class="grid-content bg-purple">
            <div class="list-name margin">
              <span>单号:{{item.order_sn}}</span>
              <el-tag >{{item.status}}</el-tag>
            </div>
            <div class="list-mode margin">
              <span>到店时间: {{checkdate(item.arrive_time)}}</span>
              <span>排号:{{checkdate(item.arrive_time,'{y}-{m}-{d}')}}第 {{item.wait_number}}位置</span>
            </div>
             <div class="list-time margin">
            预计完工时间: {{checkdate(item.plan_endtime)}}
            </div>
            <div class="list-time margin">
             车牌：{{item.vehicle_number}}
            </div>
            <div class="list-over margin">
              车型:  {{item.brand}} {{item.model}}  {{item.vehicle_model}} 
            </div>
            <div class="selcet">
               <h4>业务类型</h4>
                <el-form label-position="center" ref="form" :model="form" label-width="60px">
                <div v-for="(items,indexs) in item.service_groups">
                    <el-form-item :label="items.op_name" >
                       <el-select 
                   disabled
                    size="mini" 
                     v-if="items.set=='people'"
                     v-model="items.target_account" :placeholder="items.set=='team'?'请选择班组':'请选择人员'">
                        <el-option
                        :key="indexss" 
                        v-for="(itemss,indexss) in (info[items.op_type-1].users)"
                        :label="items.set=='team'?itemss.group_name:itemss.username" 
                        :value="items.set=='team'?itemss.group_id:itemss.id"></el-option>
                        </el-select>
                    <el-select 
                    v-else
                    disabled
                    size="mini"  v-model="items.target_team" :placeholder="items.set=='team'?'请选择班组':'请选择人员'">
                        <el-option
                        :key="indexss" 
                        
                        v-for="(itemss,indexss) in (info[items.op_type-1].teams)"
                        :label="items.set=='team'?itemss.group_name:itemss.username" 
                         :value="items.set=='team'?itemss.group_id:itemss.id"></el-option>
                        </el-select>
                       
                    </el-form-item>
                </div>
                </el-form>
            </div>
            <div class="list-over ">
              <el-button type="text" @click="gotodetails(item.order_id)">查看详情</el-button>
              <div>
             <!-- <el-button  size="mini" type="primary" @click.native="setstatus(item)" v-show="item.order_status!=5&&item.order_status!=0">{{item.order_status==4?'暂停':'开工'}}</el-button>-->
               <!-- <el-button  size="mini" type="primary" v-show="item.order_status==4"  @click.native="gotonext(item)">去质检</el-button> -->
              </div>
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
/*
            0作废1已接车2已开单3已调度4已开工5已质检(已完工)6已结算7已回访 99已暂停
          */
import Iconlist from "@/components/Iconlist/Iconlist";
import Comheader from "@/components/comHeader/comheader";
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
export default {
  name: "index",
  components: {
    Iconlist,
    Comheader,
    Pageing
  },
   
  methods: {
    gotodetails(index) {
      
      this.$router.push({ name: "teamdetails", query: { id: index } });
    },
    gotonext(item){
        this.$router.push({ name: "qualityindex" });
    },
    updatelist(){
       let el=this.formInline;
       this.getlist({
                page: this.page.curpage,
                 order_sn:el.order_sn,
                order_status: this.orderlis,
                begin_time: el.time? el.time[0] / 1000 : "",
                end_time: el.time? el.time[1] / 1000 : "",
              });
    },
    checkdate(time,type){
        return parseTime(time,type)
    },
    complete(){
      Axios("team/setdonestatus",{
        id:item.service_groups[0].id
      }).then(res=>{
          this.$message({
            message: "操作成功",
            type: "success"
            }); 
           this.updatelist();
      })
    },
    setstatus(item){
      let that=this;
      console.log(item)
      let arr=[0,4];
      //这里判断下
      console.log(item)
      if(item.order_status==3){
        Axios("team/setstartstatus",{
          id:item.service_groups[0].id
        }).then(res=>{
            this.$message({
            message: "操作成功",
            type: "success"
            }); 
           that.updatelist();
        })
      }
      if(item.order_status==4){
        Axios("/team/pausetask",{
          order_id:item.order_id
        }).then(res=>{
            this.$message({
            message: "操作成功",
            type: "success"
            }); 
           that.updatelist();
        })
      }
      if(item.order_status==99){
          Axios("team/resumetask",{
          order_id:item.order_id
        }).then(res=>{
            this.$message({
            message: "操作成功",
            type: "success"
            }); 
           that.updatelist();
        })
      }
    },
   
    search(value){
       let el=this.formInline;
      this.getlist({
        page: 1,
         order_sn:el.order_sn,
         vehicle_number: el.plate,
        order_status: this.orderlist,
        begin_time: value.time? value.time[0] / 1000 : "",
        end_time: value.time? value.time[1] / 1000 : "",
      });
    },
    pagechange(value){
         let el=this.formInline;
     this.getlist({
        page: value,
         vehicle_number: el.plate,
          order_sn:el.order_sn,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    numlists(value){
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
         vehicle_number: el.plate,
         order_sn:el.order_sn,
        order_status: this.orderlist,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
    getlist(params,type=false){
        this.loading=true;
        Axios("team/list",params).then(res=>{
           let list = res.data.list;
            for(let i in list){
                list[i].visible=false;
            }
            if (type) {
              this.numlist=[-1];
            }
            let newlist=res.data.list;
         for(let i in newlist){
          newlist[i].value=true;
          for(let a in newlist[i].service_groups){
            let index=newlist[i].dispatch_list.findIndex((el)=>{
              return el.op_type==newlist[i].service_groups[a].op_type
            })
            if(index>-1){
                //证明存在
                  newlist[i].dispatch_list[index].target_account=newlist[i].dispatch_list[index].target_account
                  newlist[i].dispatch_list[index].target_team=newlist[i].dispatch_list[index].target_team
                 newlist[i].service_groups[a]=Object.assign(newlist[i].service_groups[a],newlist[i].dispatch_list[index])
            }else{
                 newlist[i].service_groups[a].target_account="";
                 newlist[i].service_groups[a].target_team="";
            }
          }
        }
        let countslist=[];

            this.counts =res.data.counts;
             let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
            this.list = newlist;
            this.nodata=newlist.length==0;
            this.loading=false;
            this.$message({
            message: "数据已更新",
            type: "success"
            }); 
        })
    },
  },
  mounted() {
    this.getlist({},true);
    Axios("dispatch/getservicegroup").then(res=>{
        this.info=res.data.list;
    })
  },
  data() {
    return {
      numlist: [],
      counts: [],
      nodata:"",
      list: [],
      info:{},
      loading:true,
      orderlis:"",
      page:{},  
      value1: "",
      value2: "",
      value3: "",
      value4: "",
      value5: "",
      value6: "",
      value7: "",
      value8: "",
      form: {
        region: ""
      },
      formInline: {
        plate: "",
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

<style  lang="scss">
.dashboard-container-list {
  .el-form-item__label{
    text-align:center;
  }
  .el-form{
    margin-right:0 !important;
    display:flex;
    flex-wrap:wrap;
    >div{
      flex: 0 0 50%;
      width:50%;
      >div{
        width:100%;
      }
    }
  }
  .el-form-item {
    margin-bottom: 0;
  }
  h4 {
    margin-bottom: 0;
  }
  .el-form {
    margin-right: 50px;
  }
  .selcet {
    display: block !important;
    height:160px;
    .el-form {
      margin-top:20px;
      > div {
        display: flex;
        align-items: center;
      }
    }
  }
  .list-over{
    .el-tag{
      margin-left:10px;
    }
  }
}
</style>
