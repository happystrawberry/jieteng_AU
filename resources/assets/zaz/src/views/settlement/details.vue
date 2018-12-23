<template>
    <div class="zone-content details">
      <div class="print-ct"  id="form1">
        <h1 style="text-align:center">结算单</h1>
       <div v-if="info.message">
         <p v-if="info.message">当前留言:{{info?info.account_name:''}}:{{info?info.message:''}}</p>
         <p v-else>暂无留言</p>
       </div>
       <div class="print-ct-header" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;" >
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">单号:{{item.order_sn}}</div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;"></div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">打印时间:{{printtime}}</div>
              <!-- <img style="margin-left:40px;max-width:400px;" :src="'data:image/jpeg;base64,'+item.barcode" alt=""> -->
          </div>
         <h3 class="bold" style="margin:10px auto;">车辆信息</h3> 
        <div class="ul" style="width:700px;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
              <!-- <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                单号 :{{item.order_sn}}
              </p> -->
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                车主 : {{item.vehicle_owner?item.vehicle_owner:"未填写"}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                车型 : {{item.vehicle_model}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                完工时间 :{{item.final_endtime?checkdate(item.final_endtime):'未完工'}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                车牌 : {{item.vehicle_number}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                结算人员 : {{item.bill_operator}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                服务顾问 : {{item.service_consultant}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                顾问手机号 : {{item.consultant_phone}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                VIN号码 : {{item.vehicle_vin}}
              </p>
          </div>
          <div class="printitem"  v-if="item.item_list&&item.item_list.length>0" >
            <h3 style="margin:20px auto">项目与工时</h3>
            <table border="1" width="600" height="30" cellspacing="0"  style="margin:0 auto;border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="102" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">项目编码</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">业务类型</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">项目名称</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">工时/小时</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">单价</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">应收金额</td>
          </tr>
          <tr v-for="(items,index) in item.item_list" :key="index">
            <td width="102" height="30" align="center" style="border:solid 1px black">{{items.order_id}}</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">{{items.op_number}}</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">{{items.op_type}}</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">{{items.item_name}}</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">{{items.item_times}}</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">{{items.item_cost}}</td>
            <td width="102" height="30" align="center" style="border:solid 1px black">{{items.total_price}}</td>
          </tr>
           <tr>
              <td width="102" height="30" align="center" style="border:solid 1px black">合计:</td>
              <td width="102" height="30" align="center" style="border:solid 1px black"></td>
              <td width="102" height="30" align="center" style="border:solid 1px black"></td>
              <td width="102" height="30" align="center" style="border:solid 1px black"></td>
              <td width="102" height="30" align="center" style="border:solid 1px black"></td>
              <td width="102" height="30" align="center" style="border:solid 1px black"></td>
              <td width="102" height="30" align="center" style="border:solid 1px black">{{totaltimes()}}元</td>
          </tr>
        </table>
          </div>
            <div class="printitem" v-if="item.part_list.length>0" >
            <h3 style="margin:20px auto">材料</h3>
               <table border="1" width="700" height="106" cellspacing="0"  style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="60" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="110" height="30" align="center" style="border:solid 1px black">项目编码</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">业务类型</td>
            <td width="100" height="30" align="center" style="border:solid 1px black">材料名称</td>
            <td width="110" height="30" align="center" style="border:solid 1px black">规格</td>
            <td width="50" height="30" align="center" style="border:solid 1px black">单位</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">单价</td>
            <td width="50" height="30" align="center" style="border:solid 1px black">数量</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">应收金额</td>
          </tr>
          <tr v-for="(items,index) in item.part_list" :key="index">
            <td width="60" height="30" align="center" style="border:solid 1px black">{{items.id}}</td>
            <td width="110" height="30" align="center" style="border:solid 1px black">{{items.parts_sn}}</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">{{items.op_type}}</td>
            <td width="100" height="30" align="center" style="border:solid 1px black">
              {{items.parts_name.split(" ")[0]}}
            </td>
            <td width="110" height="30" align="center" style="border:solid 1px black">{{items.spec}}</td>
            <td width="50" height="30" align="center" style="border:solid 1px black">{{items.unit}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.sell_price}}</td>
            <td width="50" height="30" align="center" style="border:solid 1px black">{{items.amount}}</td>
             <td width="80" height="30" align="center" style="border:solid 1px black">{{items.total_price}}</td>
          </tr>
           <tr>
              <td width="60" height="30" align="center" style="border:solid 1px black">合计:</td>
              <td width="110" height="30" align="center" style="border:solid 1px black"></td>
              <td width="70" height="30" align="center" style="border:solid 1px black"></td>
              <td width="130" height="30" align="center" style="border:solid 1px black"></td>
              <td width="50" height="30" align="center" style="border:solid 1px black"></td>
              <td width="50" height="30" align="center" style="border:solid 1px black"></td>
              <td width="80" height="30" align="center" style="border:solid 1px black"></td>
              <td width="50" height="30" align="center" style="border:solid 1px black"></td>
              <td width="80" height="30" align="center" style="border:solid 1px black">{{totalpart()}}元</td>
          </tr>
        </table>
          </div>
          <h3 class="bold" style="margin:20px auto;">费用信息</h3> 
        <div class="ul" style="width:700px;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                工时数合计(小时) :{{item.total_times}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                材料数量合计 : {{item.total_parts}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                工时费用(元) : {{item.times_cost}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                材料费用(元) :{{item.parts_cost}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                其他费用 :{{item.other_cost}}
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                费用合计(元) : {{item.total_cost}}
              </p>
          </div>
          <h3 class="bold" v-if="item.pay_list.length>0" style="margin:20px auto;">结算信息</h3> 
        <div class="ul" v-if="item.pay_list.length>0" style="width:700px;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
              <p v-for="(item,index) in item.pay_list" :key="index" class="list" style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;">
                <span>{{item.way}}: {{item.cost}}</span>
              </p>
          </div>
          <div style="text-align:right;margin-top:80px;margin-right:200px">
              客户签字:
          </div>
      </div>
     
      <div class="noprint" >
         <message :info="info" ></message>
      <div style="margin-bottom:40px;">
        <el-row type="flex" jusitfy="space-between" >
            <el-col class="left">
              <div>单号:{{item.order_sn}}</div>
              <img height="50px" @click="onecode" style="max-width:400px" :src="'data:image/jpeg;base64,'+item.barcode" alt="">
            </el-col>
            <el-col class="right">
               <el-button size="medium" type="primary"  @click="gotobeizhu">备注 ({{item.notes}})</el-button>
              <el-button size="medium" type="primary"  @click="print">打印</el-button>
              <el-button v-if="item.pay_status==2" size="medium" type="primary" @click="refund">退款</el-button>
              <el-button v-if="item.pay_status!=3" size="medium" type="primary"  @click="returns">返回到开单</el-button>
          </el-col>
        </el-row>
        <span> 订单状态:{{item.pay_state}}</span>
      </div>
       <div style="margin:30px 0;">
          <h2 >车辆信息</h2>
        <el-row :gutter="20" class="info">
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>单号:</span>
            <span>{{item.order_sn}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>车主:</span>
            <span>{{item.vehicle_owner?item.vehicle_owner:"未填写"}}</span>
          </el-col>
            <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:8}>
            <span>车型:</span>
            <span>{{item.vehicle_model}}</span>
          </el-col>
           <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>完工时间:</span>
            <span>{{item.final_endtime?checkdate(item.final_endtime):'未完工'}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>车牌:</span>
            <span>{{item.vehicle_number}}</span>
          </el-col>
         <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>结算人员:</span>
            <span>{{item.bill_operator}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>服务顾问:</span>
            <span>{{item.service_consultant}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>顾问手机号:</span>
            <span>{{item.consultant_phone}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:8} :md={span:8} :lg={span:8}>
            <span>VIN号码:</span>
            <span>{{item.vehicle_vin}}</span>
          </el-col>
         
        </el-row>
       </div>
        <div class="table">
          <div class="table-header">
            <h2>项目与工时</h2>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="item.item_list"
            :summary-method="getSummaries"
            show-summary
            tooltip-effect="dark"
            style="width:100%"
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
             >
            </el-table-column>
            <el-table-column
              prop="op_type"
              header-align="center"
              align="center"
              label="业务类型"
             
              >
            </el-table-column>
             <el-table-column
              prop="item_name"
              header-align="center"
              align="center"
              label="项目名称"
              >
            </el-table-column>
             <el-table-column
              prop="item_times"
              header-align="center"
              align="center"
              label="工时/小时"
              >
            </el-table-column>
             <el-table-column
              prop="item_cost"
              header-align="center"
              align="center"
              label="单价"
              >
            </el-table-column>
             <el-table-column
              prop="total_price"
              header-align="center"
              align="center"
              label="应收金额"
              >
            </el-table-column>
             <el-table-column
              prop="discount"
              header-align="center"
              align="center"
              label="折扣"
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
          <div class="table-header">
            <h2>材料</h2>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="item.part_list"
            :summary-method="getSummaries"
            show-summary
            tooltip-effect="dark"
            style="width:100%"
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="id"
             >
            </el-table-column>
            <el-table-column
              prop="parts_sn"
              header-align="center"
              align="center"
              label="项目编码"
              >
            </el-table-column>
            <el-table-column
              prop="op_type"
              header-align="center"
              align="center"
              label="业务类型"
              >
            </el-table-column>
             <el-table-column
              prop="parts_name"
              header-align="center"
              align="center"
              label="材料名称"
              >
            </el-table-column>
             <el-table-column
              prop="spec"
              header-align="center"
              align="center"
              label="规格"
              >
            </el-table-column>
             <el-table-column
              prop="unit"
              header-align="center"
              align="center"
              label="单位"
              >
            </el-table-column>
              <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="数量"
              >
            </el-table-column>
             <el-table-column
              prop="total_price"
              header-align="center"
              align="center"
              label="应收金额"
              >
            </el-table-column>
             <el-table-column
              prop="discount"
              header-align="center"
              align="center"
              label="折扣"
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
        <div class="settlement-info">
             <div class="table">
          <div class="table-header">
            <h2>费用信息</h2>
          </div>
          <el-form ref="form" label-position="left" :model="form" label-width="140px">
             <el-form-item label="工时数合计(小时)">
                 <span>{{item.total_times}}</span>
            </el-form-item>
            <el-form-item label="材料数量合计(块)">
                <span>{{item.total_parts}}</span>
            </el-form-item>
            <el-form-item label="工时费用(元)">
                <span>{{item.times_cost}}</span>
            </el-form-item>
            <el-form-item label="材料费用(元)">
              <span>{{item.parts_cost}}</span>
            </el-form-item>
            <el-form-item label="其他费用(元)">
                <span>{{item.other_cost}}</span>
            </el-form-item>
            <el-form-item label="费用合计(元)">
               <span>{{item.total_cost}}</span>
            </el-form-item>
          </el-form>
          </div>
               <div class="table">
          <div class="table-header">
            <h2>付款明细</h2>
          </div>
          <el-form ref="form"  v-if="item.pay_list.length==0&&item.pay_status!=2" label-position="left" :model="form" label-width="140px">
             <el-form-item label="支付宝">
               <el-input v-model="pay_list.alipay"></el-input>
            </el-form-item>
            <el-form-item label="微信">
                <el-input v-model="pay_list.wxpay"></el-input>
            </el-form-item>
            <el-form-item label="现金">
                <el-input v-model="pay_list.cash"></el-input>
            </el-form-item>
            <el-form-item label="会员卡">
              <el-input v-model="pay_list.vipcard"></el-input>
            </el-form-item>
            <el-form-item label="挂账">
                <el-input v-model="pay_list.hold"></el-input>
            </el-form-item>
            <el-form-item label="刷卡支付">
                <el-input v-model="pay_list.card"></el-input>
            </el-form-item>
            <el-form-item label="活动券">
               <el-input v-model="pay_list.coupon"></el-input>
            </el-form-item>
            <el-form-item label="其他">
               <el-input v-model="pay_list.other"></el-input>
            </el-form-item>
          </el-form>
          <el-form ref="form"  v-else label-position="left" :model="form" label-width="140px">
            <el-form-item :label="item.way" v-for="(item,index) in item.pay_list" :key="index">
              <span>{{item.cost}}</span>
            </el-form-item>
          </el-form>
          </div>
          <div class="table" v-if="item.refund_list.length>0">
            <div class="table-header">
            <h2>退款明细</h2>
          </div>
            <el-form ref="form" label-position="left" :model="form" label-width="140px">
            <el-form-item :label="item.way" v-for="(item,index) in item.refund_list" :key="index">
              <span>{{item.amount}}</span>
            </el-form-item>
          </el-form>
          </div>
          
         </div>
           <div>
           <div>
             <h2>施工人员信息</h2>
        <el-table
            ref="multipleTable"
            border
            :data="item.operators"
            tooltip-effect="dark"
            style="width:800px"
            stripe>
            <el-table-column
              label="施工人员"
              header-align="center"
              align="center"
              prop="username"
             >
            </el-table-column>
            <el-table-column
              prop="op_type"
              header-align="center"
              align="center"
              label="项目类型"
              width="100">
            </el-table-column>
             <el-table-column
              prop="item_name"
              header-align="center"
              align="center"
              label="项目"
              width="100">
            </el-table-column>
             <el-table-column
              prop="team"
              header-align="center"
              align="center"
              label="维修小组"
           
              >
            </el-table-column>
             <el-table-column
              prop="times"
              header-align="center"
              align="center"
              label="总工时"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="commission"
              header-align="center"
              align="center"
              label="提成比例"
              width="100"
              >
               <template slot-scope="scope">
                    {{scope.row.commission}}%
                </template>
            </el-table-column>
             <el-table-column
              prop="commission_times"
              header-align="center"
              align="center"
              label="提成工时"
              width="100"
              >
            </el-table-column>
          </el-table>
           </div>
      </div>
          <div class="pay" v-if="!other" style="margin-top:20px">
           <el-button size="samll" v-if="item.pay_status!=3&&item.pay_status!=2" type="primary" @click="saveitem">保存</el-button>
          </div>
          <el-dialog 
          title="退款操作" 
          width="450px"
          center
          @open="open"
          @close="close"
          :visible.sync="dialogFormVisible.showkey">
          <el-form label-position="left" label-width="140px"  :model="dialogFormVisible">
             <el-form-item label="支付宝">
               <el-input v-model="tui_list.alipay"></el-input>
            </el-form-item>
            <el-form-item label="微信">
                <el-input v-model="tui_list.wxpay"></el-input>
            </el-form-item>
            <el-form-item label="现金">
                <el-input v-model="tui_list.cash"></el-input>
            </el-form-item>
            <el-form-item label="会员卡">
              <el-input v-model="tui_list.vipcard"></el-input>
            </el-form-item>
            <el-form-item label="挂账">
                <el-input v-model="tui_list.hold"></el-input>
            </el-form-item>
            <el-form-item label="银行卡">
                <el-input v-model="tui_list.card"></el-input>
            </el-form-item>
            <el-form-item label="活动券">
                <el-input v-model="tui_list.coupon"></el-input>
            </el-form-item>
            <el-form-item label="其他">
                <el-input v-model="tui_list.other"></el-input>
            </el-form-item>
          </el-form>
          <div slot="footer" class="dialog-footer">
            <el-button size="samll" @click="payment(false)">取 消</el-button>
            <el-popover
                          placement="top"
                          width="160"
                          v-model="visible">
                          <p>是否确认退款?</p>
                          <div style="text-align: right; margin: 0">
                            <el-button size="mini" type="text" @click="visible = false">取消</el-button>
                            <el-button type="primary" size="mini" @click="payment(true)">确定</el-button>
                          </div>
                           <el-button slot="reference" size="samll" type="primary" >退款</el-button>
                        </el-popover>
          </div>
        </el-dialog>
      </div>
     
    </div>
</template>

<script>
//0未支付 1挂账 2已支付 3已退款
let times,
     clicktime,
     LODOP,
    paylist=[0,1,3];
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";
import message from "@/components/Messagelist/Messagelist";
import { setTimeout, clearInterval, setInterval } from 'timers';
export default {
  methods: {
    handleSelectionChange() {
      console.log(23123);
    },
    totalpart(){
      let arr=0,
          that=this;
      if (that.item.part_list) {
          for (let i in that.item.part_list) {
            if (!that.item.part_list[i].total_price) {
              arr += 0;
            } else {
              arr += parseFloat(that.item.part_list[i].total_price);
            }
          }
        }
        return arr;
    },
    totaltimes(){
      let arr=0,
          that=this;
      if (that.item.item_list) {
          for (let i in that.item.item_list) {
            if (!that.item.item_list[i].total_price) {
              arr += 0;
            } else {
              arr += parseFloat(that.item.item_list[i].total_price);
            }
          }
        }
        return arr;
    },
    saveitem(){
      let paytype=["zonepaytype","alipay","wxpay","cash","vipcard","hold","card","coupon","other"],
          pays=[];
          for(let i  in this.pay_list){
              if(this.pay_list[i]>0){
                pays.push({
                  way:paytype.indexOf(i),
                  cost:this.pay_list[i]
                })
              }
          }
          Axios("settlement/savepay",{
            order_id:this.$route.query.id,
            pays:pays
          }).then(res=>{
        this.$notify({
          title: '成功',
          message: "保存成功",
          duration:4500,
          type: 'success'
           });
           this.getinfo();
          })
    
    },
     onecode(){
          LODOP=getLodop(document.getElementById('LODOP1'),document.getElementById('LODOP_EM1')); 
            LODOP.ADD_PRINT_BARCODE(40,200,400,47,"EAN128B",this.item.order_sn);
	        	LODOP.SET_PRINT_STYLEA(0,"AlignJustify",2);
            LODOP.PRINT_DESIGN();		
        },
    print(){
      this.printtime=this.checkdate(new Date().getTime());
      setTimeout(()=>{
        try{ 
		     var LODOP=getLodop(); 
			if (LODOP.VERSION) {
         LODOP.PRINT_INIT("");
        LODOP.SET_PRINT_STYLE("FontSize", 18);
        LODOP.SET_PRINT_STYLE("Bold", 1);
        LODOP.ADD_PRINT_HTM(40, 5, "100%", "100%", document.getElementById("form1").innerHTML);
        LODOP.PREVIEW();
			}else{
        this.$message({
              showClose: true,
              message: "打印控件未安装,请先联系管理员下载安装",
              type: "warning"
            });
      }
		 }catch(err){
       
 		 } 
      },500)
     
    },
    checkpay(value) {
      let title,
      that=this;
      switch(this.item.pay_status){
        case 1:
        title="该订单已挂账,不能再次操作了哟";
        break;
        case 2:
        title="该订单已支付,不能再次操作了哟";
        break;
        case 3:
        title="该订单已退款,不能再次操作了哟";
        break;
      }
      console.log(this.item.pay_status)
      if(this.item.pay_status!=0&&this.item.pay_status!=1){
        // 已经支付的情况下
        this.$message({
        message:title,
        type: "warning"
      });
      return;
      }
      // 额外一种情况 该订单处于挂账状态
      if(this.pay_state==1&&value==4){
         this.$message({
        message:"该订单已挂账,不能重复挂账了哟",
        type: "warning"
      });
      return;
      }
      if(value==4||value==2){
        //这里分为两种情况
          this.$confirm(`${value==2?'确认收到现金后点击确认按钮':'确认该项目进行挂账处理吗'}`, '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.dialogFormVisible.code=value;
          this.payment(value)
        }).catch(() => {
          this.$message({
            type: 'info',
            message:`已经取消${value==2?'现金支付':'挂账'}操作`
          });          
        });
      }else{
          this.dialogFormVisible.showkey = true;
      }
      this.dialogFormVisible.code = "";
         this.dialogFormVisible.cost =this.item.total_cost;
      this.dialogFormVisible.title=this.titles[value];
      this.dialogFormVisible.index = value;

    },
    checkdate(time) {
      return parseTime(time);
    },
    gotobeizhu() {
      let routeData = this.$router.resolve({
        name: "remarkindex",
        query: { id: this.$route.query.id }
      });
      window.open(routeData.href, "_blank");
    },
    refund() {
      this.dialogFormVisible.showkey=true;
    },
    open(){
      this.dialogFormVisible.code="";
    },
    close(){
      let that=this;
      setTimeout(()=>{
        this.dialogFormVisible.index=-1;
      },200)
    },
    returns() {
      let that=this;
      this.$confirm('确定要将该订单返回到开单状态吗', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          center:true,
          type: 'warning'
        }).then(() => {
           Axios("/common/resetorder",{
        order_id:this.$route.query.id
      }).then(res=>{
          this.$notify({
          title: '成功',
          message: '返回到开单状态成功',
          type: 'success',
          duration: 2000
        });
        setTimeout(()=>{
          that.$router.push({ name: "settlementindex"});
        },1500)
      })
        }).catch(() => {
                   
        });
    },
    payment(type) {
      let paytype=["zonepaytype","alipay","wxpay","cash","vipcard","hold","card","coupon","other"],
          refunds=[],
          nums=0;
          for(let i  in this.tui_list){
              nums+=parseFloat(this.tui_list[i]);
              if(this.tui_list[i]>0){
                refunds.push({
                  way:paytype.indexOf(i),
                  cost:this.tui_list[i]
                })
              }
          }
      if(nums>this.total_cost){
        this.$message({
            type: 'warning',
            message:"退款额应小于费用合计,请检查"
          });   
          return;
      }
      this.dialogFormVisible.showkey = false;
      this.visible=false;
      Axios("settlement/refund",{
        order_id:this.$route.query.id,
        refunds:refunds
      }).then(res=>{
        this.$notify({
          title: '成功',
          message: "退款成功",
          duration:4500,
          type: 'success'
           });
           this.getinfo();
      })
    },
    timeupdate(title){
      let that=this;
      clearInterval(times)
      let loading = this.$loading({
          lock: true,
          text: '等待确认支付中...',
          spinner: 'el-icon-loading',
          background: 'rgba(0, 0, 0, 0.7)'
        });
      times=setInterval(()=>{
        Axios("settlement/getpaystatus",{
          order_id:this.$route.query.id
        }).then(res=>{
          if(res.data.status==1){
            // 状态变为支付成功了。
             that.$notify({
          title: '成功',
          message: title,
          type: 'success'
           });
           loading.close();
           that.loadings=false;
           clearInterval(times)
            that.getinfo();
          }
        })
      },1500)
    },
    getinfo(){
      Axios("common/ordernotelist",{
        order_id:this.$route.query.id
      }).then(res=>{
        console.log(res.data.list.length)
         this.info=res.data.list.length>0?res.data.list[res.data.list.length-1]:{}
      })
      Axios("settlement/vieworder", {
      order_id: this.id
    }).then(res => {
      this.item = res.data;
      this.$message({
        message: "数据已更新",
        showClose:true,
        type: "success"
      });
    });
    },
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = "合计";
          return;
        }
        const values = data.map(item => Number(item[column.property]));
        if (index == columns.length - 1) {
          console.log("asdasdasda", index);
          sums[index] = values.reduce((prev, curr) => {
            const value = Number(curr);
            if (!isNaN(value)) {
              return prev + curr;
            } else {
              return prev;
            }
          }, 0);
          sums[index]=sums[index].toFixed(2);
          sums[index] += " 元";
        } else {
          sums[index] = " ";
        }
      });

      return sums;
    }
  },
  components: {
    message
  },
  mounted() {
    this.id = this.$route.query.id;
    this.other=this.$route.query.other
    this.getinfo();
  },
  data() {
    return {
      item: {},
      visible:false,
      id: "",
      printtime:"",
      pay_list:{
        alipay:"",
        wxpay:"",
        cash:"",
        vipcard:"",
        hold:"",
        card:"",
        coupon:"",
        other:""
      },
      tui_list:{
        alipay:"",
        wxpay:"",
        cash:"",
        vipcard:"",
        hold:"",
        card:"",
        coupon:"",
        other:""
      },
      other:false,
      info:{},
      titles:["","选择支付宝支付","选择微信支付","选择现金支付","选择会员卡结算","选择刷卡结算"],
      otherdata: [],
      form: {
        time: 2,
        cail: 2,
        sum: 2,
        sumcail: 2,
        sumother: 2,
        totalsum: 2
      },
      dialogFormVisible: {
        showkey: false,
        code: "",
        phone:"",
         cost:"",
        index: -1
      },
      pay: [
        {
          title: "支付宝",
          clickimg: require("./js_zfb_click.png"),
          norimg: require("./js_zfb_nor.png"),
          click: false
        },
        {
          title: "微信",
          clickimg: require("./js_wechat_click.png"),
          norimg: require("./js_wechat_nor.png"),
          click: false
        },
        {
          title: "现金",
          clickimg: require("./js_xj_click.png"),
          norimg: require("./js_xj_nor.png"),
          click: false
        },
        {
          title: "会员卡",
          clickimg: require("./js_vip_click.png"),
          norimg: require("./js_vip_nor.png"),
          click: false
        },
        {
          title: "挂账",
          clickimg: require("./js_gz_click.png"),
          norimg: require("./js_gz_nor.png"),
          click: false
        },
        {
          title: "刷卡",
          clickimg: require("./js_gz_click.png"),
          norimg: require("./js_gz_nor.png"),
          click: false
        }
      ],
      remuneration: [],
      draw: [],
      multipleTable: [],
      multipleTables: [],
      formInline: {
        ordernumber: "",
        owner: "",
        plate: "",
        model: "  ",
        phone: "",
        counselor: "",
        account: "",
       
        overtime: ""
      },
      checkList: [],
      data: []
    };
  }
};
</script>

<style  lang='scss'>
.print-ct {
  display: none;
}
.el-dialog__body {
  box-sizing: border-box;
}
.settlement-info{
  display:flex;
  >.table{
    margin-right:200px;
  }
}
@media print {
  // body {
  //   // height:100;
  //   // overflow: hidden;
  // }
  .printitem {
    div {
      display: flex;
      &:nth-of-type(1) {
        span {
          border-top: 2xp solid #ebeef5;
        }
      }
      &:last-of-type {
        span {
          border-bottom: 2xp solid #ebeef5;
        }
      }
      span {
        flex: 1;
        border: 1px solid #ebeef5;
        font-size: 10px;
        text-align: center;
        line-height: 30px;
        &:nth-of-type(1) {
          border-left: 2xp solid #ebeef5;
        }
        &:last-of-type {
          border-right: 2xp solid #ebeef5;
        }
      }
    }
  }
  .print-ct {
    position: absolute;
    top: 0;
    left: 0;
    // bottom: 0;
    width: 100%;
    min-height: 100%;
    z-index: 99999;
    display: block;
    &-header {
      display: flex;
      margin-bottom: 20px;
      div {
        margin-top: 20px;
        margin-right: 40px;
        display: inline-block;
      }
    }
    .ul {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      .list {
        flex: 0 0 30%;
        font-size: 13px;
        margin-bottom:5px;
      }
    }
  }
  .details {
    margin: 0;
  }

  .main-container {
    margin-left: 0 !important;
  }
  .el-scrollbar__bar {
    display: none !important;
  }
  .sidebar-container {
    display: none !important;
  }
  .el-scrollbar__view {
    display: none;
  }
  .el-menu {
    display: none;
  }
  .noprint {
    display: none;
  }
}
.pay {
  text-align: center;
  > div {
    padding-top: 20px;
    display: inline-block;
    width: 100px;
    &.active {
      background: #409eff;
      p {
        color: white;
      }
    }
  }
}
.el-input{
  width:210px !important;
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
</style>