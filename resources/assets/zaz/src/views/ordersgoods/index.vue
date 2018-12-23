<template>
  <div class="order-index">
    <comheader
    :formInline="formInline"
    @download="download"
    @search="search"
    ></comheader>
    <iconlist
    :numlist="numlist"
    @numlists="numlists"
    :checktype="true"
    :data="counst"></iconlist>
     <el-table
                v-loading="loadings"
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
                prop="order_sn"
                align="center"
              header-align="center"
                label="订单号"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                prop="status"
                align="center"
              header-align="center"
                label="订单状态"
                width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                prop="contact_name"
                label="联系人"
                align="center"
              header-align="center"
                width="120">
              </el-table-column>
              <el-table-column
                prop="phone"
                align="center"
              header-align="center"
                label="手机号"
                width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                label="车牌号"
                prop="vehicle_number"
                align="center"
              header-align="center"
                width="120">
              </el-table-column>
              <el-table-column
                prop="service_items"
                align="center"
              header-align="center"
                label="业务类型"
                
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                prop="service_consultant"
                align="center"
              header-align="center"
                label="服务顾问"
                width="120"
                show-overflow-tooltip>
              </el-table-column>
             <el-table-column
                prop="order_sn"
                align="center"
              header-align="center"
              width="120"
                label="实收金额"
                show-overflow-tooltip>
                 <template slot-scope="scope">
                 {{scope.row.pay_status!="已支付"?"":scope.row.income+"元"}}
                </template>
              </el-table-column>
               <el-table-column
                prop="pay_status"
                align="center"
              header-align="center"
              width="120"
                label="支付状态"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                label="操作"
                 align="center"
                  width="100"
                show-overflow-tooltip>
                <template slot-scope="scope">
                  <el-button size="small" slot="reference" type="danger" @click="details(scope)">查看详情</el-button>
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
import { Axios } from "@/api/login";
import Pageing from "@/components/Paging/Paging";
import Iconlist from '@/components/Iconlist/Iconlist'
import { parseTime } from "@/utils/index";
import Comheader from '@/components/comHeader/comheader'
  export default {
    name: 'orderindex',
    components:{
      Iconlist,
      Comheader,
      Pageing
    },
    data() {
      return {
        input: "12",
        nodata:false,
        orderlist:"",
        starttime: "",
        endtime: "",
        loadings:true,
        numlist:["-1"],
        formInline:{
          consultant:[],
          download:true,
          checkconsultant:"",
          time:"",
          workquery:""
        },
        list:[],
        page:{},
        counst:[]
      }
    },
    mounted(){
      this.getlist({},true);

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
    methods:{
      details(scope){
         this.$router.push({name:"orrdersgoodsdetails",query:{id:scope.row.order_id}})
      },
      download(){
          let el = this.formInline;
          this.HttpDownload(`/api/workorder/list?service_consultant=${
          el.checkconsultant.length>0?el.checkconsultant:""
        }&export=1&query=${el.workquery.length>0?el.workquery:""
        }&order_status=${this.orderlist}&begin_time=${
          el.time? el.time[0] / 1000 : ""
        }&end_time=${el.time? el.time[1] / 1000 : ""}`, "订单列表"+ '.xls')
      },
      checkdate(time,type){
        return parseTime(time,type)
      },
      numlists(value){
          this.numlist = value;
      //这里做下转换
      if(this.numlist.indexOf("-1")>-1){
        //即为全部
        this.orderlist="";
      }else{
        this.orderlist=this.numlist.join(",")
      }
        this.orderlist=this.orderlist==-1?"":this.orderlist;
      let el=this.formInline;
      this.getlist({
        page:1,
         service_consultant:el.checkconsultant,
          query:el.workquery,
           order_status:this.orderlist,
         begin_time: el.time? el.time[0] / 1000 : "",
          end_time: el.time? el.time[1] / 1000 : "",
      })
      },
      neworder(){
          this.$router.push({name:"orderdetails"})
      },
      getlist(params,type=false){
        let that=this;
        Axios("workorder/list",params).then(res=>{
            this.loadings=false;
           let list=[];
           for(let i in res.data.sc_list){
             list.push({
               label:res.data.sc_list[i],
               id:res.data.sc_list[i]
             })
           }
           this.formInline.consultant=list;
            this.nodata=res.data.list.length==0;
            this.list=res.data.list;
            let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
            this.counst = res.data.counts;
           
        if(type){
           this.numlist=["-1"];
          // for(let i in res.data.counts){
          // this.numlist.push(res.data.counts[i].order_status)
          // }
        }
        this.$message({
          showClose: true,
          message: '数据已更新',
          type: 'success'
        });
        })
      },
       pagechange(value){
         let el=this.formInline;
         this.getlist({
        page:value,
        query:el.workquery,
         order_status:this.orderlist,
        service_consultant:el.checkconsultant,
          begin_time: el.time? el.time[0] / 1000 : "",
          end_time: el.time? el.time[1] / 1000 : "",
      })

        // this.$router.push({name:"orderdetails",query:{id:index}})
      },
      search(value){
        console.log(value)
         let el=this.formInline;
        this.getlist({
          page:1,
           query:el.workquery,
            order_status:this.orderlist,
          service_consultant:el.checkconsultant,
          begin_time: value.time? value.time[0] / 1000 : "",
          end_time: value.time? value.time[1] / 1000 : "",
        })
      }
    }
  }
</script>

<style scoped lang="scss">
  .order {
    &-index {
      margin: 20px;
      &-list {
        margin-top: 30px;
        display:block;
        width:100%;
        overflow: hidden;
        .grid-content {
          padding: 10px 14px;
          box-sizing: border-box;
          position: relative;
          border: 1px solid rgba(187, 187, 187, 1);
          margin-bottom: 20px;
          .margin {
            margin-bottom: 20px;
          }
          .list-level {
            position: relative;
            padding-top: 10px;
            width: 95%;
            p {
              position: absolute;
              color: rgb(102, 177, 255);
              top: -30px;
              right: 0;
            }
          }
          .list-info {
            position: absolute;
            width: 70px;
            top: 10px;
            right: 14px;
            &-type {
              width: 70px;
              height: 70px;
              line-height: 70px;
              font-size: 12px;
              text-align: center;
              border: 1px solid rgba(187, 187, 187, 1);
              border-radius: 70px;
              margin-bottom: 14px;
            }
            &-num {
              text-align: center;
              display: block;
            }
          }
          .list-bottom{
            display:flex;
            justify-content: space-between;
          }
        }
      }
      &-header {
        display: flex;
        div{
          margin-right:10px;
        }
      }
    }
  }
</style>
