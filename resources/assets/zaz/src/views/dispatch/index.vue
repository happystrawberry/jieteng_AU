<template>
    <div class="zone-content team">
         <comheader 
         :formInline="formInline"
         @search="search"
         ></comheader>
         <iconlist :data="counts"
         :numlist="numlist"
         @numlists="numlists"
         ></iconlist>
          <div class="dashboard-container-list" v-if="!nodata">
      <el-row :gutter="20">
        <el-col 
         v-for="(item,index) in list"
          :key="index"
        shadow="always" :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
          <div 
          class="grid-content bg-purple" >
            <div class="list-name margin">
              <span>单号:{{item.order_sn}}</span>
              <el-tag >{{item.status}}</el-tag>
            </div>
            <div class="list-mode margin">
              <span>到店时间: {{checkdate(item.arrive_time)}}</span>
              <span>排号: {{checkdate(item.arrive_time,'{y}-{m}-{d}')}} 第{{item.wait_number}}位</span>
            </div>
             <div class="list-time margin">
            预计完工时间: {{checkdate(item.plan_endtime)}}
            </div>
            <div class="list-time margin">
             车牌：{{item.vehicle_number}}
            </div>
            <div class="list-over margin">
              车型：{{item.vehicle_model}}
            </div>
            <div class="selcet margin">
               <h4>业务类型</h4>
                <el-form label-position="center" ref="form" :model="form" label-width="60px">
                <div v-for="(items,indexs) in item.service_groups">
                    <el-form-item :label="items.op_name" >
                    <el-select 
                    v-if="items.set=='people'"
                    size="mini"  
                     :disabled="item.order_status==2?false:true"
                    v-model="items.target_account"
                     :placeholder="items.set=='team'?'请选择班组':'请选择人员'">
                        <el-option 
                        :key="indexss"
                        v-for="(itemss,indexss) in (info[items.op_type-1].users)"
                        :label="items.set=='team'?itemss.group_name:itemss.username" 
                        :value="items.set=='team'?itemss.group_id:itemss.id"></el-option>
                        </el-select>
                    <el-select 
                    size="mini"  
                    v-else
                     :disabled="item.order_status==2?false:true"
                    v-model="items.target_team"
                     :placeholder="items.set=='team'?'请选择班组':'请选择人员'">
                        <el-option 
                        :key="indexss"
                        v-for="(itemss,indexss) in (info[items.op_type-1].teams)"
                        :label="items.set=='team'?itemss.group_name:itemss.username" 
                        :value="items.set=='team'?itemss.group_id:itemss.id">
                        </el-option>
                        </el-select>
                    </el-form-item>
                   
                </div>
                </el-form>
            </div>
            <div class="list-over" style="justify-content: flex-end;">
                <el-button size="mini" type="primary"  @click="gotodetail(item.order_id)">打印施工单</el-button>
               <el-button size="mini" type="primary"  @click="gotobeizhu(item.order_id)">备注</el-button>
               <el-popover
                    style="margin-left:20px"
                    placement="top"
                    width="160"
                    v-model="item.visible">
                    <p>您是否确定这个订单进入到下个环节</p>
                    <div style="text-align: right; margin: 0">
                        <el-button size="mini" type="text" @click="item.visible = false">取消</el-button>
                        <el-button type="primary" size="mini" @click="checksure(item)">确定</el-button>
                    </div>
                    <el-button v-show="item.order_status==2" slot="reference" size="mini" type="primary">确认</el-button>
                    </el-popover>
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
export default {
  name: "index",
  components: {
    Iconlist,
    Comheader,
    Pageing
  },
  methods: {
    pagechange(value) {
       let el=this.formInline;
     this.getlist({
        page: value,
        order_status: this.orderlis,
        order_sn:el.order_sn,
        vehicle_number:el.plate,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
      });
    },
     checkdate(time,type){
      return parseTime(time,type)
    },
    gotodetail(id){
       this.$router.push({name:"dispatchdetails",query:{id:id}})
    },
    checksure(item){
       let list=[];
       for(let i in item.service_groups){
         list.push({
           op_type:item.service_groups[i].op_type,
           target_account:item.service_groups[i].target_account,
           target_team:item.service_groups[i].target_team,
         })
       }
       // 这里判断下
       for(let i in list){
         if(list[i].target_account.length==0&&list[i].target_team.length==0){
           this.$message({
              message: "确认调度必须所有业务都分配完成",
              type: "warning"
            });
            item.visible=false;
            return;
         }
       }
        Axios("/dispatch/saveworktask",{
          order_id:item.order_id,
          dispatch:list
        }).then(res=>{
           item.visible=false;
            this.$message({
          message: "设置成功",
          type: "success"
        });
            this.getlist({},true);
        })
    },
    checkdate(time){
        return parseTime(time)
    },
    gotobeizhu(value){
       let routeData = this.$router.resolve({ name: 'remarkindex', query: {  id: value } });
        window.open(routeData.href, '_blank');
      // this.$router.push({name:"remarkindex",query:{id:this.$route.query.id}})
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
         order_sn:el.order_sn,
        order_status: this.orderlist,
        vehicle_number:el.plate,
        begin_time: el.time ? el.time[0] / 1000 : "",
        end_time: el.time ? el.time[1] / 1000 : "",
      });
    },
    getlist(params, type = false) {
      this.loading=true;
      Axios("dispatch/list", params).then(res => {
        let list = res.data.list;
        for(let i in list){
            list[i].visible=false;
        }
        if (type) {
          this.numlist=[-1];
          // for (let i in res.data.counts) {
          //   this.numlist.push(res.data.counts[i].order_status);
          // }
        }

        let newlist=res.data.list;
         for(let i in newlist){
          newlist[i].value=true;
          for(let a in newlist[i].service_groups){
            let index=newlist[i].dispatch_list.findIndex((el)=>{
              return el.op_type==newlist[i].service_groups[a].op_type
            })
            if(index>-1){
                //证明存在//证明存在
                newlist[i].dispatch_list[index].target_account=(newlist[i].dispatch_list[index].target_account+"".split(",")[0])
                  newlist[i].dispatch_list[index].target_team=(newlist[i].dispatch_list[index].target_team+"".split(",")[0])
                newlist[i].service_groups[a]=Object.assign(newlist[i].service_groups[a],newlist[i].dispatch_list[index])
            }else{
                 newlist[i].service_groups[a].target_account="";
                 newlist[i].service_groups[a].target_team="";
            }
          }
        }
        this.counts = res.data.counts;
         let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        this.list = newlist;
        this.loading=false;
        this.$message({
          message: "数据已更新",
          type: "success"
        });
      });
    },
    search(value) {
      this.getlist({
        page: 1,
        vehicle_number:value.plate,
         order_sn:value.order_sn,
        order_status: this.orderlis,
        begin_time: value.time? value.time[0] / 1000 : "",
        end_time: value.time? value.time[1] / 1000 : "",
      });
    }
  },
  mounted() {
    this.getlist({},true);
    Axios("dispatch/getservicegroup").then(res=>{
      console.log(res.data.list.length==0,"对比")
        this.nodata=res.data.list.length==0;
        this.info=res.data.list;
    })
  },
  data() {
    return {
      numlist: [-1],
      nodata:false,
      counts: [],
      list: [],
      info:{},
      orderlis:"",
      loading:true,
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
        order_sn:"",
        time: ""
      },
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
      > div {
        display: flex;
     
      }
    }
  }
}
</style>
