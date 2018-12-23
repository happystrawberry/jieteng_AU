<template>
  <div class="zone-content datalist">
      <comheader 
      :formInline="formInline"
      @search="search"
      ></comheader>
      <div class="datalist-table">
          <div class="table-header">
            <h3>入库出库数据一览表</h3>
            <el-col style="margin-left:0;margin-top:12px;">
              <el-button   size="mini" type="primary" @click="checkexport">导出数据</el-button>
            </el-col>
          </div>
          <div class="table-ct">
              <el-table
            ref="multipleTable"
            border
            :data="tabledata"
            tooltip-effect="dark"
            style="width: 100%"
            stripe
            >
            <el-table-column
              label="订单编号"
              header-align="center"
              align="center"
              prop="order_sn"
              width="180"
              >
               <template slot-scope="scope">
                <span style="color:#409EFF;cursor:pointer" @click="gotoorder(scope.row.order_id)">{{scope.row.order_sn=='-'?"":scope.row.order_sn}}</span>
               </template> 
            </el-table-column>
             <el-table-column
              label="车牌号"
              header-align="center"
              align="center"
              prop="vehicle_number"
              >
            </el-table-column>
            <el-table-column
              prop="parts_sn"
              header-align="center"
              align="center"
              label="材料编码"
              width="100">
            </el-table-column>
            <el-table-column
              prop="parts_name"
              header-align="center"
              align="center"
              label="材料名称"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="target_model"
              header-align="center"
              align="center"
              label="对应车型"
              >
            </el-table-column>
             <el-table-column
              prop="spec"
              header-align="center"
              align="center"
              label="规格"
              width="70"
              >
            </el-table-column>
            <el-table-column
              prop="unit"
              header-align="center"
              align="center"
              label="单位"
              width="70"
              >
            </el-table-column>
             <el-table-column
              prop="origin_amount"
              header-align="center"
              align="center"
              label="变动前数量"
              width="80"
              >
              </el-table-column>
             <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="变动数量"
              width="80"
              >
            </el-table-column>
             <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="变动后数量"
              width="80"
              >
               <template slot-scope="scope">
                 {{scope.row.status=="出库"?parseFloat(scope.row.origin_amount)-parseFloat(scope.row.amount):parseFloat(scope.row.origin_amount)+parseFloat(scope.row.amount)}}
               </template> 
            </el-table-column>
             <el-table-column
              prop="purchase_price_tax"
              label="进货单价(含税)"
              header-align="center"
              align="center"
              width="90"
              >
            </el-table-column>
             <el-table-column
              prop="purchase_price_notax"
              header-align="center"
              align="center"
              label="进货单价(不含税)"
              width="90"
              >
            </el-table-column>
            <el-table-column
              prop="sell_price"
              header-align="center"
              align="center"
              label="出货单价"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="status"
              header-align="center"
              align="center"
              label="状态"
              width="80"
              >
               <template slot-scope="scope">
                 <el-tag>{{scope.row.status}}</el-tag>
               </template> 
            </el-table-column>
            <el-table-column
              prop="sell_price"
              header-align="center"
              align="center"
              label="时间"
              width="180"
              >
               <template slot-scope="scope">
                 {{checkdate(scope.row.dateline)}}
               </template> 
            </el-table-column>
             <el-table-column
              header-align="center"
              align="center"
              label="操作"
              >
            <template slot-scope="scope" >
                <div style="display:flex; justify-content:space-around">
                    <el-button type="primary" size="mini" @click="gotodetails(scope.row.parts_id)">查看详情</el-button>
                </div>
            </template>
            </el-table-column>
          </el-table>
          </div>
      </div>
      <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
  </div>
</template>

<script>
import Comheader from "@/components/comHeader/comheader";
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";

export default {
  name: "index",
  components: {
    Comheader,
    Pageing
  },
  methods: {
     gotodetails(index) {
      this.$router.push({ name: "kucuntdetails", query: { id: index } });
    },
    pagechange(value) {
      let el=this.formInline;
      this.getlist({
        page:value,
        parts_id: "0",
        parts_name: el.accessories,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
        is_out: el.types
      });
    },
    checkexport() {
    let el=this.formInline;
    window.open(`/api/parts/getpartsloglist?export=1&page=${this.page.curpage}&parts_id=0&is_out=${el.types}&begin_time=${el.time.length>0?ele.time[0]/1000:""}&end_time=${el.time.length>0?ele.time[1]/1000:""}`, '_blank');
    },
    gotoorder(item){
      console.log(item)
      let routeData = this.$router.resolve({ name: 'datalistdetails', query: {  id: item } });
        window.open(routeData.href, '_blank');
    },
    search(value) {
      let el=this.formInline;
      this.getlist({
        page:this.page.curpage,
        parts_id: "0",
        parts_name: el.accessories,
        begin_time: el.time? el.time[0] / 1000 : "",
        end_time: el.time? el.time[1] / 1000 : "",
        is_out: el.types
      });
    },
    checkdate(time) {
      return parseTime(time);
    },
    getlist(params) {
      Axios("parts/getpartsloglist", params).then(res => {
         let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        this.tabledata = res.data.list;
      });
    }
  },
 mounted() {
  
     this.getlist();
      
  },
  data() {
    return {
      page: {},
      formInline: {
        accessories: "",
        time: "",
        types: "",
        type: [
          {
            label: "出库",
            value: 1
          },
          {
            label: "入库",
            value: 0
          }
        ]
      },
      tabledata: [
      ]
    };
  }
};
</script>

<style scoped lang='scss'>
.table-header {
  margin-top: 30px;
  margin-bottom: 30px;
  display: flex;
  h3 {
    margin: 0 40px 0 0px;
    line-height: 40px;
    display: inline-block;
    flex: 0 0 200px;
    line-height: 28px;
  }
  > div {
    display: inline-block;
  }
}
</style>
