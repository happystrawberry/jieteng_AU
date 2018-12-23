<template>
    <div class="zone-content details">
      <message :info="info"></message>
      <h3>回访单</h3>
      <div style="margin-bottom:40px;">
        <el-row type="flex" jusitfy="space-between" >
            <el-col class="left">
              <div>单号:{{item.order_sn}}</div>
              <img height="50px"  :src="'data:image/jpeg;base64,'+item.barcode" alt="">
            </el-col>
            <el-col class="right">
                           <el-button  @click.native="gotobeizhu" size="medium" type="primary">备注({{item.notes}})</el-button>
          </el-col>
        </el-row>
        <span> 订单状态:{{item.order_status}}</span>
      </div>
       <div style="margin:30px 0;">
        <el-row :gutter="20" class="info">
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>单号:</span>
            <span>{{item.order_sn}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>联系人:</span>
            <span>{{item.contact_name}}</span>
          </el-col>
           <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>手机号:</span>
            <span>{{item.phone}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>车牌:</span>
            <span>{{item.vehicle_number}}</span>
          </el-col>
         <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>车型:</span>
            <span>{{item.brand}} {{item.model}} {{item.vehicle_model}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>完工时间:</span>
            <span>{{item.final_endtime>0?parsedate(item.final_endtime):'完工时间未知'}}</span>
          </el-col>
           <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>客户来源:</span>
            <span>{{item.customer_source?item.customer_source:'客户来源未知'}}</span>
          </el-col>
           <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>车主年龄:</span>
            <el-input size="mini"    style="width:200px !important;"  v-model="fromlist.age"></el-input>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>车主性别:</span>
           <el-select style="width:200px !important;" size="mini" v-model="fromlist.sexy" >
            <el-option label="男" value="0"></el-option>
            <el-option label="女" value="1"></el-option>
          </el-select>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>车主地址:</span>
              <el-input  style="width:200px !important;" size="mini" v-model="fromlist.address"></el-input>
          </el-col>
        </el-row>
       </div>
        <div class="table">
          <div class="table-header">
            <h2>维修记录</h2>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="item.item_list"
            :summary-method="getSummaries"
            show-summary
            tooltip-effect="dark"
            style="width:900px"
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="order_id"
              width="100">
            </el-table-column>
            <el-table-column
              prop="op_number"
              header-align="center"
              align="center"
              label="项目编码"
              width="100">
            </el-table-column>
             <el-table-column
              prop="item_name"
              header-align="center"
              align="center"
              label="项目名称"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="item_times"
              header-align="center"
              align="center"
              label="工时/小时"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="item_cost"
              header-align="center"
              align="center"
              label="工时费"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="money"
              header-align="center"
              align="center"
              label="应收金额"
              width="100"
              >
              <template slot-scope="scope">
                  {{parseFloat(scope.row.item_cost)*parseFloat(scope.row.item_times)}}
              </template>
            </el-table-column>
             <el-table-column
              prop="discount"
              header-align="center"
              align="center"
              label="折扣"
              width="100"
              >
            </el-table-column>
            <el-table-column
              prop="final_price"
              header-align="center"
              align="center"
              label="折后金额"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="quality_status"
              header-align="center"
              align="center"
              label="业务类别"
              >
               <template slot-scope="scope">
                  <el-tag>{{typelist[scope.row.quality_status].name}}</el-tag>
              </template>
            </el-table-column>
          </el-table>
        </div>
        <div class="table">
          <div class="table-header">
            <h2>材料</h2>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="item.part_list"
            :summary-method="getSummarie"
            show-summary
            tooltip-effect="dark"
            style="width:1300px"
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="order_id"
              width="100">
            </el-table-column>
            <el-table-column
              prop="parts_sn"
              header-align="center"
              align="center"
              label="项目编码"
              width="100">
            </el-table-column>
            <el-table-column
              prop="op_type"
              header-align="center"
              align="center"
              label="业务类型"
              width="200"
              >
              <template slot-scope="scope">
                  <el-tag>{{typelist[scope.row.op_type].name}}</el-tag>
              </template>
            </el-table-column>
             <el-table-column
              prop="parts_name"
              header-align="center"
              align="center"
              label="商品名称"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="spec"
              header-align="center"
              align="center"
              label="规格"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="unit"
              header-align="center"
              align="center"
              label="单位"
              width="100"
              >
            </el-table-column>
              <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="数量"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="sell_price"
              header-align="center"
              align="center"
              label="应收金额"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="discount"
              header-align="center"
              align="center"
              label="折扣"
              width="100"
              >
            </el-table-column>
            <el-table-column
              prop="final_price"
              header-align="center"
              align="center"
              label="折后金额"
              >
            </el-table-column>
          </el-table>
        </div>
        <div class="table">

          <h3 style="margin:20px 0;">客户回访列表</h3>
          <el-form class="zonebg" label-position="top" label-width="80px">
              <el-form-item :label="(index+1)+'、'+item.question" v-for="(item,index) in item.question_list" :key="index">
                  <el-radio-group v-model="item.choice.pos">
                      <el-radio v-for="(items,indexs) in item.options" :key="indexs" :label="indexs">{{items}}</el-radio>
                    </el-radio-group>
                   
                    <el-input
                    type="textarea"
                    :rows="5"
                    style="width:400px;display:block;margin-top:10px;"
                    placeholder="请输入原因"
                    v-if="item.choice.pos!=-1&&item.need_cause[item.choice.pos]==1"
                    v-model="item.choice.causes">
                  </el-input>
              </el-form-item>
               <el-input type="textarea" v-model="item.feedback_remark" :rows="10" placeholder="还有什么想说的"></el-input>
              <el-button type="primary" class="submit" @click="submit">保存</el-button>
            </el-form>
          </div>
    </div>
</template>

<script>
 import message from '@/components/Messagelist/Messagelist'
import { create } from 'domain';
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";
export default {
  methods: {
    handleSelectionChange() {
      console.log(23123);
    },
    parsedate(time){
      return parseTime(time)
    },
    getSummarie(param){
      const { columns, data } = param;
        const sums = [];
        columns.forEach((column, index) => {
          if (index === 0) {
            sums[index] = '合计';
            return;
          }
          const values = data.map(item => Number(item[column.property]));
          if (!values.every(value => isNaN(value))&&[9].indexOf(index)>-1) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
          
            sums[index] += '';
          } else {
            sums[index] = ' ';
          }
        });

        return sums;
    },
    submit(){
      let el=this.item;
      let questions=[];
      if(this.item.question_list.some((res)=>{
         console.log(res)
        return res.choice.pos==-1
      })){
          this.$message({
            message: "请先填写完整客户回访列表",
            type: "warning"
          });
          return;
      }
      
      for(let i in this.item.question_list){
        questions.push({
           id:this.item.question_list[i].id,
           choice:this.item.question_list[i].choice.pos,
           cause:this.item.question_list[i].choice.causes
        })
      }
      Axios("service/savefeedback",{
        "order_id": el.order_id,
        age:this.fromlist.age,
        sexy:this.fromlist.sexy,
        address:this.fromlist.address,
       questions:questions,
        "remark": el.feedback_remark
      }).then(res=>{
          this.$notify({
          title: '成功',
          message: '回访成功',
          type: 'success'
        });
      })
    },
    gotobeizhu(){
      let routeData = this.$router.resolve({ name: 'remarkindex', query: {  id: this.$route.query.id } });
        window.open(routeData.href, '_blank');
    },
    getSummaries(param){
      const { columns, data } = param;
        const sums = [];
        columns.forEach((column, index) => {
          if (index === 0) {
            sums[index] = '合计';
            return;
          }
          const values = data.map(item => Number(item[column.property]));
          console.log([9].indexOf(index)>-1,index)
          if (!values.every(value => isNaN(value))&&[3,5,7].indexOf(index)>-1) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
            sums[index]=sums[index].toFixed(2);
            if(index==5||index==7){
              sums[index]="¥ "+sums[index];
            }
          } else {
            sums[index] = ' ';
          }
        });

        return sums;
    },
    checkcheck(param,checknum){
      console.log(param)
    const { columns, data } = param;
        console.log(12312312)
    }
  },
  mounted(){
     let that=this;
     let id = this.$route.query.id;
     Axios("common/ordernotelist",{
       order_id:this.$route.query.id
     }).then(res=>{
       this.info=res.data.list.length>0?res.data.list[res.data.list.length-1]:{};
     })
     Axios("common/getoptypelist").then(res=>{
       
       this.typelist=res.data.list;
     })
      Axios("service/vieworder",{
         order_id: id
      }).then(res=>{
        let list=res.data;
        list.feedback_maintenance=(list.feedback_maintenance);
        list.feedback_rest_zone=(list.feedback_rest_zone);
        list.feedback_service_attitude=(list.feedback_service_attitude);
        list.feedback_service_environment=(list.feedback_service_environment);
        for(let i in list.question_list){
          list.question_list[i].choice.pos=parseInt(list.question_list[i].choice.pos)
        }
        that.item=list;
        this.fromlist.age=list.owner_age
         this.fromlist.address=list.owner_addr
          this.fromlist.sexy=list.owner_sexy

          this.$message({
            message: "数据已更新",
            type: "success"
          });
      })
  },
  components:{
    message
  },
  data() {
    return {
      item:{},
      info:{},
      fromlist:{
        age:"",
        sexy:"",
        address:""
      },
      typelist:[],
      evaluate:[
        {
          value:"0",
          text:"极差"
        },
        {
          value:"1",
          text:"失望"
        },{
          value:"2",
          text:"一般"
        },{
          value:"3",
          text:"满意"
        },{
          value:"4",
          text:"惊喜"
        },
      ],
      otherdata:[
        {
          name:"张三",
          times:"1.4",
          payment:20
        },        {
          name:"张三",
          times:"1.4",
          payment:20
        },        {
          name:"张三",
          times:"1.4",
          payment:20
        },        {
          name:"张三",
          times:"1.4",
          payment:20
        }
      ],
      form:{
        maintenance:"",
        service_attitude:"",
        service_environment:"",
        rest_zone:"",
      },
      pay:[
        "支付宝付款",
        "微信付款",
        "现金结算",
        "会员卡结算",
        "挂账"
      ],
      remuneration: [
        
      ],
      draw: [
        
      ],
      multipleTable: [
       
      ],
      multipleTables:[
       
      ],
      formInline: {
        ordernumber: "",
        owner: "",
        plate: "",
        model: "",
        phone:"",
        counselor:"",
        account:"",
        overtime:""
      },
      checkList: [],
      data: [
        
      ]
    };
  }
};
</script>

<style scoped lang='scss'>
.submit{
    display:block;
    margin:30px auto;
}
.pay{
  text-align:center;
  .el-tag{
    margin-right:20px;
  }
}
.el-tag {
  padding: 0 15px;
}
.model {
  display: flex;
  > span {
    margin-right: 20px;
  }
}
.info {
  span {
    line-height: 20px;
    &:last-child {
      color: #aaa;
      margin-left: 10px;
    }
  }
}
.el-col {
  margin-bottom: 20px;
  &.left {
    display: flex;
    align-items: center;
    img {
      margin-left: 30px;
    }
  }
  &.right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
}
.table {
  margin-bottom: 40px;
  &-header {
    display: flex;
    align-items: center;
    div {
      margin-left: 50px;
    }
  }
}
</style>