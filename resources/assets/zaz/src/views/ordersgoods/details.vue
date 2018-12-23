<template>
      <div class="zone-content details" >
        <div  class="print-ct" id="form1">
            <h1 style="text-align:center">结算单</h1>
            <div class="print-ct-header" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;" >
            <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">单号:{{info.info.order_sn}}</div>
            <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;"></div>
            <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">打印时间:{{printtime}}</div>
          </div>
            <div style="margin:20px 0;">
            <span>业务类型:{{info.service_items?info.service_items.join(" "):''}}</span>
            </div>
            <div class="print-ct-header" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;" >
            <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">联系人:{{info.info.contact_name}}</div>
            <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">联系人电话:{{info.info.phone}}</div>
            </div>
              <div class="print-ct-header" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;" >
            <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">车主姓名:{{info.info.vehicle_owner}}</div>
            </div>
            <div style="margin:20px 0;">
              <div><p>施工人:</p></div>
            <div>
              <p :key="index" v-for="(item,index) in info.operators">
                类别: <el-tag  style="margib-right:20px;">{{item.type}}</el-tag>
                人员:{{item.operators}}
              </p>
            </div>
            </div>
            <div style="display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;"
        class="ul">
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          车牌号 :{{info.info.vehicle_number?info.info.vehicle_number:"未填写"}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          VIN号码 : {{info.info.vehicle_vin?info.info.vehicle_vin:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px" class="list">
          品牌 : {{getbrandlist(info.info.brand_id)}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          车型 : {{info.info.brand_model?info.info.brand_model:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          颜色 : {{info.info.vehicle_color?info.info.vehicle_color:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          本次里程 :{{info.info.current_km?info.info.current_km+'公里':'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          到店时间 :{{info.info.arrive_times?checkdate(info.info.arrive_times):'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          预计完工时间 : {{info.info.plan_endtimes?checkdate(info.info.plan_endtimes):'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          服务顾问 : {{info.info.service_consultant_name}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          顾问手机号 : {{info.info.consultant_phone?info.info.consultant_phone:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          是否需要洗车 : {{info.info.need_wash==1?'需要':'不需要'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          是否需要保养 : {{info.info.need_maintenance==1?'属于':'不属于'}}
        </p>
      </div>
       <div>
        <p>故障描述:</p>
        <p>{{info.fault_intro}}</p>
      </div>
      <div class="printitem"  v-if="info.progress" >
        <h3 style="margin-bottom:20px">项目进度</h3>
        <table border="1" width="300" height="106" cellspacing="0"  style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="104" height="30" align="center" style="border:solid 1px black">项目类别</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">进度</td>
          </tr>
          <tr v-for="(items,index) in info.progress" :key="index">
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.item}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.status}}</td>
          </tr>
        </table>
          </div>
               <div class="printitem"  v-if="info.item_list" >
        <h3 style="margin:20px 0">项目详情</h3>
        <table border="1" width="600" height="106" cellspacing="0"  style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="80" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">项目编码</td>
             <td width="80" height="30" align="center" style="border:solid 1px black">业务类型</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">项目名称</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">工时/小时</td>
            <td width="120" height="30" align="center" style="border:solid 1px black">维修小组</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">工时费</td>
            <td width="120" height="30" align="center" style="border:solid 1px black">应收金额</td>
          </tr>
          <tr v-for="(items,index) in info.item_list" :key="index">
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.op_number}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.id}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.type}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.item_name}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.item_times}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.operator}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.item_cost}}</td>
            <td width="120" height="30" align="center" style="border:solid 1px black">{{items.item_cost*items.item_times}}</td>
          </tr>
        </table>
          </div>
           <div class="printitem"  v-if="info.part_list" >
        <h3 style="margin:20px 0">材料详情</h3>
        <table border="1" width="700" height="106" cellspacing="0"  style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="80" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">材料编码</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">业务类型</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">材料名称</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">规格</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">单位</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">数量</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">应收金额</td>
          </tr>
          <tr v-for="(items,index) in info.part_list" :key="index">
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.id}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.parts_sn}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{checkyewu(items.op_type)}}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.itemname}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.spec}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.unit}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.amount}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{items.sell_price*items.amount}}</td>
          </tr>
        </table>
          </div>
  
          <div class="details-ct-cost noprint" style="margin-top:30px;">
        <h3 style="margin-bottom:20px;">费用信息</h3>
        <div class="ul" style="width:100%; display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
          <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            工时数合计 : {{info.info.total_times}}
          </p>
          <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            材料数量合计 : {{info.info.total_parts}}
          </p>
           <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            工时费用 : {{info.info.times_cost}}
          </p>
          <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            材料费用 : {{info.info.parts_cost}}
          </p>
           <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            其他费用 : {{info.info.other_cost}}
          </p>
          <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            费用合计 : {{info.info.total_cost}}
          </p>
           <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            结算人员 : {{info.info.bill_operator}}
          </p>
          <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
            支付方式 : {{info.info.pay_ways.join(",")}}
          </p>
        </div>
      </div>
           <div style="margin-bottom:60px;">
              <h3 style="margin:30px 0;">下一次维修建议</h3>
              <span>{{info.info.next_advice?info.info.next_advice:'暂无维修建议'}}</span>
            </div>
        </div>
        <div class="details-ct">
        <div class="details-ct-header noprint">
          <el-row class="row-bg" style="display:flex;margin-bottom:10px;justify-content: space-around;align-items:center">
            <el-col  :xs={span:24} :sm={span:24} :md={span:20} :lg={span:20}>
              <div style="display:flex;align-items:center;">
                <div style="flex:0 0 600p'x%;display: flex; align-items: center;"><span>单号:{{info.info.order_sn}}</span>
              <img @click="onecode" style="max-width:400px;margin-left:40px;" :src="'data:image/jpeg;base64,'+info.info.barcode" alt=""></div>
            
              </div>
             
              
            </el-col>
            <el-col class="noprint" style="float:right;text-align:left" :xs={span:24} :sm={span:24} :md={span:4} :lg={span:4}
              :gutter="20">
                <el-button type="primary" size="medium" v-show="info.info.id" @click="cheshi(1)">打印</el-button>
              <!-- <el-button type="primary" size="medium" v-show="info.info.id" @click="gotoaccount">结算</el-button> -->
            </el-col>
          </el-row>
          <div style="flex:0 0 50%;">
            <p style="display:inline-block;margin-right:10px;">业务类型:</p>
                <el-tag style="margin-right:5px" v-show="item" v-for="(item,index) in info.service_items" :key="index">{{item}}</el-tag>
             

            </div>
             <div style="display:flex;">
                 <p style="margin-right:200px;width:200px;">联系人:{{info.info.contact_name}}</p>
                   <p>联系人电话:{{info.info.phone}}</p>
              </div>
          <div style="display:flex;">
            <p style="margin-right:200px;width:200px;">订单状态:{{info.info.status}}</p>
              <p>车主姓名:{{info.info.vehicle_owner}}</p>
          </div>
          <div class="shigong">
          <div><p>施工人</p></div>
            <div>
              <p :key="index" v-for="(item,index) in info.operators">
                类别: <el-tag  style="margib-right:20px;">{{item.type}}</el-tag>
                人员:{{item.operators}}
              </p>
            </div>
            
          </div>
        </div>
        <div class="details-ct-info noprint">
          <el-form label-position="left" :model="info.info"  ref="info.info" label-width="100px" class="demo-info.info">
            <el-row class="elzone">
              <el-col class="zoneel" style="display:inline-block;" :xs={span:12} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车牌号：" prop="vehicle_number">
              
                   <span>{{info.info.vehicle_number}}</span>
                 
                </el-form-item>
              </el-col>
              <el-col class="zoneel" style="display:inline-block;" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="VIN码：" prop="vehicle_vin">

                  <span>{{info.info.vehicle_vin}}</span>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="品牌：" prop="brand_id">
                  <span>{{getbrandlist(info.info.brand_id)}}</span>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车型：" prop="model">
                 <span>{{info.info.brand_model}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="具体型号：" prop="vehicle_model">
                  <span>{{info.info.vehicle_model}}</span>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="颜色：" prop="vehicle_color">
                  <span>{{info.info.vehicle_color}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车辆等级：" prop="model_class">
                  <span>{{info.info.model_class}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} label="购车日期：">
                 <span>{{info.info.buy_times>0?checktime(info.info.buy_times):'未知'}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="保险日期：">
                <span> {{info.info.insurance_times>0?checktime(info.info.insurance_times):'未知'}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="保险公司：">
                <span> {{info.info.insurance_company}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车主名称：">
                  <span>{{info.info.vehicle_owner}}</span>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="联系人：" >
                  <span>{{info.info.contact_name}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="电话：" prop="phone">
                 <span> {{info.info.phone}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="客户来源：" prop="customer_source">
                  <span> {{info.info.customer_source}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} label="介绍人姓名：">
                  <span>{{info.info.introducer}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>

                <el-form-item label="介绍人电话：">
                 <span> {{info.info.introducer_phone}}</span>
                </el-form-item>
              </el-col>
              <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="本次里程：" prop="current_km">
                  <span> {{info.info.current_km?info.info.current_km+"公里":''}}</span>
                </el-form-item>
              </el-col>
              <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="到店时间：" prop="arrive_time">
                  <span>{{info.info.arrive_time>0?checktime(info.info.arrive_time*1000):""}}</span>
                </el-form-item>
              </el-col>
              <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label-width="120px" label="预计完工时间：" prop="plan_endtimes">
                 <span> {{info.info.plan_endtime>0?checktime(info.info.plan_endtime):''}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="完工时间：">
                    {{info.info.done_time>0?checktime(info.info.done_time):"未完工"}}
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="服务顾问：" prop="service_consultant">
                  <span>{{info.info.service_consultant_name}}</span>
                  <!-- <el-autocomplete
                    v-model="info.info.service_consultants"
                    :fetch-suggestions="querySearchAsync"
                    placeholder="请输入顾问"
                    :change="guwenchange"
                    @select="handleSelect"
                  ></el-autocomplete> -->
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="顾问手机号：" prop="consultant_phone">
                 <span> {{info.info.consultant_phone}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label-width="140px" label="是否需要洗车：" prop="need_wash">
                  <span>{{info.info.need_wash==1?'需要':'不需要'}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label-width="140px" label="本次是否属于保养：">
                  <span>{{info.info.need_maintenance==1?'属于':'不属于'}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8}
                :lg={span:6} v-if="info.info.need_maintenance==1">
                <el-form-item label-width="120px" label="保养里程周期：">
                  <span>{{info.info.maintenance_km?info.info.maintenance_km+"公里":""}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel"  :xs={span:24} :sm={span:12} :md={span:8}
                :lg={span:6} v-if="info.info.need_maintenance==1">
                <el-form-item label-width="120px" label="保养时间周期：">
                  <span>{{info.info.maintenance_cycle?info.info.maintenance_cycle+"天":''}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} v-if="info.info.need_maintenance==1">
                <el-form-item label-width="120px" label="下次保养里程：">
                    <span>{{info.info.maintenance_km?info.info.next_maintenance_km+"公里":""}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} v-if="info.info.need_maintenance==1">
                <el-form-item label-width="120px" label="下次保养日期：">
                  <span>{{info.info.next_maintenance_time>0?checktime(info.info.next_maintenance_time):''}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:24} :md={span:24} :lg={span:24}>
                <h3 style="margin-bottom:20px;">故障描述</h3>
                <el-input readonly="ture" type="textarea" :rows="10" placeholder="请输入内容" prop="fault_intro" v-model="info.info.fault_intro">
                </el-input>
              </el-col>
            </el-row>
            <el-dialog :visible.sync="dialogVisible">
                  <img width="100%" :src="dialogImageUrl" alt="">
             </el-dialog>
            <div class="table noprint " style="margin-top:40px;" v-if="info.info.check_images.length>0">
              <div class="upload">
                <h3 style="display:inline-block;width:120px;margin-bottom:20px;">车辆查验图</h3>
                <div>
                  <img @click="preimg(item)" style="cursor: pointer;width:200px;height:200px;display:inline-block;margin-right:40px;border-radius: 10px;" v-for="(item,index) in info.info.check_images"   :key="index" :src="item" alt="">
                </div>
              </div>
              <div>
              </div>
            
            </div>
          </el-form>
        </div>
        <div>
          <h3 style="margin-bottom:20px;margin-top:20px;">项目进度</h3>
          <el-table
            :data="info.progress"
            border
            style="width:300px">
            <el-table-column
              prop="item"
              align="center"
              header-align="center"
              label="项目类别"
              width="180">
            </el-table-column>
            <el-table-column
            align="center"
              header-align="center"
              prop="status"
              label="进度">
            </el-table-column>
          </el-table>
        </div>
        <div>
           <h3 style="margin:20px 0;">项目详情</h3>
           <el-table  :data="info.item_list" show-summary :summary-method="getSummaries" border tooltip-effect="dark" style="width:1300px"
             >
              <el-table-column label="编号" prop="op_number" align="center" header-align="center" width="80">
              </el-table-column>
              <el-table-column prop="id" align="center" header-align="center" label="项目编码" width="120">
              </el-table-column>
               <el-table-column prop="type" align="center" header-align="center" label="业务类型" show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="item_name" label="项目名称" width="120" align="center" show-overflow-tooltip>
              </el-table-column>
               <el-table-column prop="operator" label="维修小组" width="240" align="center" show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="item_times" align="center" header-align="center" label="工时(小时)" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="item_cost" label="工时费(元)" align="center" header-align="center" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="item_cost" label="应收金额(元)" align="center" header-align="center" width="120"
                show-overflow-tooltip>
                 <template slot-scope="scope">
                  <span>{{scope.row.item_cost*scope.row.item_times}}元</span>
                </template>
              </el-table-column>
              <el-table-column prop="discount" align="center" header-align="center" label="折扣" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="final_price" align="center" header-align="center" label="折后金额(元)"
                show-overflow-tooltip>
              </el-table-column>
            </el-table>
        </div>
        <div>
          <h3 style="margin:20px 0;">材料详情</h3>
          <el-table  :data="info.part_list" show-summary  :summary-method="getSummaries" border tooltip-effect="dark" style="width:1400px"
             >
              <el-table-column label="编号" prop="id" align="center" header-align="center">
              </el-table-column>
              <el-table-column prop="parts_sn" label="材料编码" align="center" header-align="center" width="100">
              </el-table-column>
              <el-table-column prop="parts_name" align="center" header-align="center" label="材料名称" width="150"
                show-overflow-tooltip>
                 <template slot-scope="scope">
                      {{scope.row.parts_name.split(" ")[0]}}
                  </template>
              </el-table-column>
              
              <el-table-column prop="spec" align="center" header-align="center" label="规格" width="90"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="unit" align="center" header-align="center" label="单位" width="70"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="amount" label="数量" align="center" header-align="center" width="70"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="sell_price" align="center" header-align="center" label="应收金额(元)" width="100"
                show-overflow-tooltip>
                 <template slot-scope="scope">
                      {{scope.row.sell_price*scope.row.amount}}
                  </template>
              </el-table-column>
              <el-table-column prop="type_name" align="center" header-align="center" label="业务类型" show-overflow-tooltip>
                <template slot-scope="scope">
                  <span>{{checkyewu(scope.row.op_type)}}</span>
                </template>
              </el-table-column>
              <el-table-column prop="final_price" align="center" header-align="center" label="折后金额(元)" width="100"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="purchase_price_tax" align="center" header-align="center" label="进货单价(含税)" width="100"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="purchase_price_notax" align="center" header-align="center" label="进货单价(不含税)" width="100"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="sell_price" align="center" header-align="center" label="出货单价(元)" width="100"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="location" align="center" header-align="center" label="发货位置" width="100"
                show-overflow-tooltip>
              </el-table-column>
            </el-table>
        </div>
        <div>
          <!-- <el-table
            :data="info.progress"
            border
            style="width:800px">
            <el-table-column
              prop="username"
              align="center"
              header-align="center"
              label="姓名"
              width="120">
            </el-table-column>
             <el-table-column
              prop="times"
              align="center"
              header-align="center"
              label="工时(小时)"
              width="120">
            </el-table-column>
             <el-table-column
              prop="op_type"
              align="center"
              header-align="center"
              label="项目"
              width="120">
            </el-table-column>
             <el-table-column
              prop="ratio"
              align="center"
              header-align="center"
              label="提成比例"
              width="120">
            </el-table-column>
            <el-table-column
            align="center"
              header-align="center"
              prop="total_price"
              label="总工时费">
            </el-table-column>
            <el-table-column
            align="center"
              header-align="center"
              prop="fee"
              label="提成金额">
            </el-table-column>
          </el-table> -->
        </div>
        <div>
           <h3 style="margin:20px 0;">薪酬统计</h3>
            <el-table
            :data="info.salary"
            border
            style="width:800px">
            <el-table-column
              prop="username"
              align="center"
              header-align="center"
              label="姓名"
              width="120">
            </el-table-column>
             <el-table-column
              prop="times"
              align="center"
              header-align="center"
              label="工时(小时)"
              width="120">
            </el-table-column>
             <el-table-column
              prop="op_type"
              align="center"
              header-align="center"
              label="项目"
              width="120">
            </el-table-column>
             <el-table-column
              prop="ratio"
              align="center"
              header-align="center"
              label="提成比例"
              width="120">
            </el-table-column>
            <el-table-column
            align="center"
              header-align="center"
              prop="total_price"
              label="总工时费">
            </el-table-column>
            <el-table-column
            align="center"
              header-align="center"
              prop="fee"
              label="提成金额">
            </el-table-column>
          </el-table>
        </div>

        <!-- 费用信息 -->
        <div class="details-ct-cost noprint" style="margin-top:30px;">
          <h3 style="margin-bottom:20px;">费用信息</h3>
          <el-form label-position="left" ref="form"  label-width="140px">
            <el-row>
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="工时数合计">
                        <span>{{info.info.total_times}}</span>
                      </el-form-item>
                  </el-col>
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="材料数量合计">
                        <span>{{info.info.total_parts}}</span>
                      </el-form-item>
                  </el-col>
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="工时费用">
                        <span>{{info.info.times_cost}}</span>
                      </el-form-item>
                  </el-col>
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="材料费用">
                        <span>{{info.info.parts_cost}}</span>
                      </el-form-item>
                  </el-col>
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="其他费用">
                        <span>{{info.info.other_cost}}</span>
                      </el-form-item>
                  </el-col>
        
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="费用合计">
                        <span>{{info.info.total_cost}}</span>
                      </el-form-item>
                  </el-col>
      
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="结算人员">
                        <span>{{info.info.bill_operator}}</span>
                      </el-form-item>
                  </el-col>
         
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item label="支付方式">
                        <span>{{info.info.pay_ways.join(",")}}</span>
                      </el-form-item>
                  </el-col>
            </el-row>
           
          </el-form>
        </div>
        <div v-if="info.refund_list.length>0">
          <h3>退款详情</h3>
          <div style="display:flex;" >
            <P style="margin-right:200px;">操作人: {{info.refund_operator}}</P>
            <p>退款时间: {{info.refund_time}}</p>
          </div>
          <el-form label-position="left" ref="form"  label-width="140px">
            <el-row>
                <el-col  :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} >
                    <el-form-item  v-for="(item,index) in info.refund_list" :key="index"  :label="item.way">
                        <span>{{item.amount}}元</span>
                      </el-form-item>
                  </el-col>
            </el-row>
          </el-form>
        </div>
         <el-col style="margin-bottom:60px;">
              <h3 style="margin:30px 0;">下一次维修建议</h3>
              <span>{{info.info.next_advice?info.info.next_advice:'暂无维修建议'}}</span>
            </el-col>
        <div v-if="info.questions.length>0">
          <h3 style="margin:20px 0;">客户回访列表</h3>
          <el-form class="zonebg" label-position="top" label-width="80px">
              <el-form-item :label="(index+1)+'、'+item.question" v-for="(item,index) in info.questions" :key="index">
                  <el-radio-group v-model="item.choice">
                      <el-radio v-for="(items,indexs) in item.options" :key="indexs" :label="indexs">{{items}}</el-radio>
                    </el-radio-group>
                 
              </el-form-item>
            </el-form>
        </div>
      </div>
      </div>
</template>

<script>
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";
export default {
      data(){
        return{
          info:{},
          classes:{},
          printtime:"",
          dialogImageUrl:"",
          modellist:"",
          brandlist:"",
          dialogVisible:false,
          consultantlist:"",
        }
      },
      methods:{
        checktime(time){
          return parseTime(time);
        },
           onecode(){
          LODOP=getLodop(document.getElementById('LODOP1'),document.getElementById('LODOP_EM1')); 
            LODOP.ADD_PRINT_BARCODE(40,200,400,47,"EAN128B",this.info.info.order_sn);
	        	LODOP.SET_PRINT_STYLEA(0,"AlignJustify",2);
            LODOP.PRINT_DESIGN();		
        },
         getSummaries(param) {
        const {
          columns,
          data
        } = param;
        console.log(data)
        if (data.length == 0) {
          return ["合计", "", "", "", "", "", ""];
        }
        if (data.length > 0) {
          const numarray =
            data[0].check == 1 ? [0, 1, 2, 3,4,6,8] : [0, 1, 2, 3, 4, 5, 6,7,9,10,11,12,13];
          const sums = [];
          columns.forEach((column, index) => {
            if (index === 0) {
              sums[index] = "合计";
              return;
            }
            const values = data.map(item => Number(item[column.property]));
            if (
              !values.every(value => isNaN(value)) &&
              numarray.indexOf(index) == -1
            ) {
              // console.log("sums[index]",values)
              sums[index] = values.reduce((prev, curr) => {
                const value = Number(curr);
                if (!isNaN(value)) {
                  return prev + curr;
                } else {
                  return prev;
                }
              }, 0);
              if (data[0].check != 1 && (index ==8)) {
                for (let i in data) {
                  if (data[i].parts_status == "已退货") {
                    if (index == 7) {
                      console.log(data[i])
                      sums[index] = sums[index] - parseFloat(data[i].sell_price)* parseFloat(data[i].amount)
                    } else {
                      
                      sums[index] = sums[index] - parseFloat(data[i].final_price) 
                    }
                  }
                }
              }
              if(data[0].check!=1 && index==8){
                sums[index]=0;
                 for (let i in data) {
                  sums[index]+=parseFloat(data[i].sell_price)*parseFloat(data[i].amount);
                }
              }
              if(data[0].check==1 && index==7){
                sums[index]=0;
                 for (let i in data) {
                  sums[index]+=parseFloat(data[i].item_cost)*parseFloat(data[i].item_times);
                }
              }
              sums[index] = parseFloat(sums[index].toFixed(2));
              sums[index] += index != 5 ? " 元" : " 小时";
            } else {
              sums[index] = "";
            }
          });

          return sums;
        }
      },
        preimg(img){
          this.dialogImageUrl = img;
        this.dialogVisible = true;
        },
        cheshi(){
           //声明为全局变量 
        this.printtime=this.checktime(new Date().getTime());
        setTimeout(()=>{
            try {
          var LODOP = getLodop();
          if (LODOP.VERSION) {
            LODOP.PRINT_INIT("");
            LODOP.SET_PRINT_STYLE("FontSize", 18);
            LODOP.SET_PRINT_STYLE("Bold", 1);
            LODOP.ADD_PRINT_HTM(20, 20, "100%", "100%", document.getElementById("form1").innerHTML);
            LODOP.PREVIEW();
          } else {
            this.$message({
              showClose: true,
              message: "打印控件未安装,请先联系管理员下载安装",
              type: "warning"
            });
          }
        } catch (err) {

        }
        },500)
        },
        checkyewu(res){
          for(let i in this.classes){
            if(res ==this.classes[i].id){
              return this.classes[i].name
            }
          }
        },
        getbrandlist(index){
          for(let i in this.brandlist){
            if(this.brandlist[i].id==index){
              return this.brandlist[i].name
            }
          }
        }
      },
     async mounted() {
          await Axios("common/getoptypelist").then(res => {
            this.classes = res.data.list;
          });
        Axios("workorder/vieworder",{
          order_id:this.$route.query.id
        }).then(res=>{
          let data=res.data;
          for(let i in data.item_list){
              data.item_list[i].check=1;
          }
          for(let i in data.part_list){
              data.part_list[i].check=2;
          }
          for(let i in data.questions){
            data.questions[i].choice=parseInt(data.questions[i].choice)
          }
           this.info=data;
            // 请求品牌
            Axios("models/getbrandlist").then(res => {
          this.brandlist = res.data.list;
        });
            Axios("models/getvehiclelist", {
          brand_id: this.info.info.brand_id
        }).then(res => {
          this.modellist = res.data.list;
        });
        })
      },

}
</script>

<style scoped lang='scss'>
.zoneel {
    height: 62px;
  }
 .shigong{
    display:flex;
    p{
      margin:0;
      line-height:30px;
      margin-bottom:10px;
      margin-right:40px;
    }
  }
.zonebg{
  position:relative;
  &:after{
    content:"";
    display:block;
    width:100%;
    height:100%;
    position:absolute;
    top:0;
    left:0;
    z-index:1;
  }
}
  .print-ct {
    display: none;
  }

  .el-dialog__body {
    box-sizing: border-box;
  }
  .el-form-item__content{
    font-size:16px;
  }
  .el-col-md-8{
    height:65px;
  }
  @media print {
    .el-message {
      display: none !important;
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

  .details {
    .el-slider__runway {
      width: 210px;
    }

    .el-upload--picture-card {
      width: 200px;
      height: 200px;
      margin-top: 40px;
    }

    .el-dialog__footer {
      display: block;
      text-align: center;
    }

    .zhekou input {
      width: 140px !important;
    }

    input {
      width: 210px !important;
    }
    .el-form--label-top .el-form-item__label{
      padding:0 0 0px;
    }
    .flex {
      width: 140px !important;
      display: inline-block;
      margin-right: 5px;

      input {
        width: 140px !important;
      }
    }

    .el-upload-list__item {
      display: inline-block;
      width: 200px !important;
      height: 200px !important;
      margin-top: 40px;
      margin-right: 100px;

      a {
        display: none;
      }

      position: relative;

      img {
        width: 200px;
        height: 200px;
        position: absolute;
        top: 0;
        left: 0px;
      }
    }
  }

  .details {
    .el-input {
      text-align: left;
      width:220px;
    }

    

    table {}

    .model-type {
     >div{
        p,
      div {
        display: inline-block;
        margin-right: 20px;

        .el-tag {
          margin-right: 10px;
        }
      }
     }
    }

    margin: 20px;

    &-ct {
      margin-top: 42px;

      &-header {
        margin-bottom: 20px;
      }

      &-info {
        .el-form-item {
          display: inline-block;
          margin-right: 20px;
        }
      }

      &-feedback {
        .el-col {
          div {
            display: inline-block;
            vertical-align: bottom;
          }

          margin-bottom: 20px;

          .el-rate {
            margin-left: 30px;
          }
        }
      }
    }
  }
</style>