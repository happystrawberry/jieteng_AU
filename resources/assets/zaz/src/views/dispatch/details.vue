<template>
  <div class="zone-content details">
    <div id="form1" class="print-ct" >
      <h1 style="text-align:center;width:100%;margin-bottom:20px;">施工单</h1>
      <div class="print-ct-header" style="width:100%;display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;">
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">单号:{{info.info.order_sn}}</div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;"></div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">打印时间:{{printtime}}</div>
        <!-- <img style="margin-left:40px;max-width:400px;" :src="'data:image/jpeg;base64,'+ruleForm.barcode"
          alt=""> -->
      </div>
      <div class="model-type" style="display:flex;align-items:center;margin-bottom:0;">
        <div style="flex:0 0 50%;">
          <p style="display:inline-block;margin-bottom:10px;">业务类型:</p>
          <p style="display:inline-block;margin-right:20px;margin-bottom:0;" v-for="(item,index) in info.service_items"
            :key="index">{{item}}</p>
        </div>
      </div>
      <div class="shigong">
        <div>
          <p>施工人:</p>
        </div>
        <div>
          <p :key="index" v-for="(item,index) in info.operators">
            类别: <span style="margib-right:20px;">{{item.type}}</span>
            人员:{{item.operators}}
          </p>
        </div>
      </div>
      <div class="ul" style="width:100%; display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          车牌号: {{info.info.vehicle_number}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          VIN号码: {{info.info.vehicle_vin}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          品牌: {{getbrandlist(info.info.brand_id)}}
        </p>

        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          车型: {{info.info.brand_model}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          颜色: {{info.info.vehicle_color}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          本次里程：{{info.info.current_km}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          到店时间:{{info.info.arrive_times>0?checktime(info.info.arrive_times):"未知"}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          预计完工时间 :{{info.info.plan_endtime>0?checktime(info.info.plan_endtime):''}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          完工时间: {{info.info.done_time>0?checktime(info.info.done_time):'未完工'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          服务顾问: {{info.info.service_consultant}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          顾问手机号: {{info.info.consultant_phone}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list">
          是否需要洗车: {{info.info.need_wash==1?'需要':'不需要'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list" v-if="info.info.need_maintenance==1">
          保养里程周期: {{info.info.maintenance_km?info.info.maintenance_km+"公里":""}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list" v-if="info.info.need_maintenance==1">
          保养时间周期：: {{info.info.maintenance_cycle?info.info.maintenance_cycle+"天":''}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list" v-if="info.info.need_maintenance==1">
          下次保养里程: {{info.info.maintenance_km?info.info.next_maintenance_km+"公里":""}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 30%;flex:0 0 30%;font-size:16px;margin-bottom:5px;" class="list" v-if="info.info.need_maintenance==1">
          下次保养日期: {{info.info.next_maintenance_time>0?checktime(info.info.next_maintenance_time):''}}
        </p>
      </div>
      <div class="printitem" id="dataitems" v-if="info.item_list.length>0">
        <h3 style="margin:20px auto">项目与工时</h3>
        <table border="1" width="600" height="106" cellspacing="0" style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="125" height="20" align="center" style="border:solid 1px black">编号</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">项目编码</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">项目名称</td>
            <!--  <td width="99" height="20" align="center" style="border:solid 1px black">工时(小时)</td> -->
            <td width="99" height="20" align="center" style="border:solid 1px black">单价(元)</td>
            <!-- <td width="99" height="20" align="center" style="border:solid 1px black">应收金额(元)</td>  -->
            <td width="125" height="20" align="center" style="border:solid 1px black">业务类型</td>
          </tr>
          <tr v-for="(item,index) in info.item_list" :key="index">
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.op_number}}</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.id}}</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.item_name}}</td>
            <!--  <td width="99" height="20" align="center" style="border:solid 1px black">{{item.item_times}}</td> -->
            <td width="99" height="20" align="center" style="border:solid 1px black">{{item.item_cost}}</td>
            <!--<td width="99" height="20" align="center" style="border:solid 1px black">{{item.total_price}}</td>  -->
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.type}}</td>
          </tr>

        </table>
      </div>
      <div class="printitem" id="datamaterials" v-if="info.part_list.length>0">
        <h3 style="margin:20px auto;">材料项目</h3>
        <table border="1" width="600" height="150" cellspacing="0" style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="83" height="20" align="center" style="border:solid 1px black">编号</td>
            <td width="200" height="20" align="center" style="border:solid 1px black">商品名称</td>
            <td width="83" height="40" align="center" style="border:solid 1px black">规格</td>
            <td width="83" height="20" align="center" style="border:solid 1px black">单位</td>
            <!-- <td width="56" height="20" align="center" style="border:solid 1px black">单价(元)</td> -->
            <td width="83" height="20" align="center" style="border:solid 1px black">数量</td>
            <!-- <td width="56" height="20" align="center" style="border:solid 1px black">应收金额(元)</td> -->
            <td width="83" height="20" align="center" style="border:solid 1px black">业务类型</td>
          </tr>
          <tr v-for="(item,index) in info.part_list" :key="index">
            <td width="83" height="20" align="center" style="border:solid 1px black">{{item.order_id}}</td>
            <td width="200" height="20" align="center" style="border:solid 1px black">{{item.parts_name}}</td>
            <td width="83" height="40" align="center" style="padding:12px 0;border:solid 1px black">{{item.spec}}</td>
            <td width="83" height="20" align="center" style="border:solid 1px black">{{item.unit}}</td>
            <!-- <td width="56" height="20" align="center" style="border:solid 1px black">{{item.sell_price}}</td> -->
            <td width="83" height="20" align="center" style="border:solid 1px black">{{item.amount}}</td>
            <!-- <td width="56" height="20" align="center" style="border:solid 1px black">{{item.sell_price*item.amount}}</td> -->
            <td width="83" height="20" align="center" style="border:solid 1px black">{{item.type}}</td>
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
        </div>
      </div>
    </div>
    <div class="details-ct">
      <div class="details-ct-header noprint">
        <el-row class="row-bg" style="display:flex;margin-bottom:10px;justify-content: space-around;align-items:center">
          <el-col :xs={span:24} :sm={span:24} :md={span:20} :lg={span:20}>
            <div style="display:flex;align-items:center;">
              <div style="flex:0 0 600p'x%;display: flex; align-items: center;"><span>单号:{{info.info.order_sn}}</span>
                <img @click="onecode" style="max-width:400px;margin-left:40px;" :src="'data:image/jpeg;base64,'+info.info.barcode"
                  alt=""></div>

            </div>
            <div style="margin-top:20px;">
              <span>订单状态:{{info.info.status}}</span>
              <span style="margin-left:100px;">排号:{{info.info.wait_number}}</span>
            </div>
            <div class="model-type" style="display:flex;align-items:center;">
              <div style="flex:0 0 50%;">
                <p>业务类型:</p>
                <div>
                  <el-tag v-show="item" v-for="(item,index) in info.service_items" :key="index">{{item}}</el-tag>
                </div>
              </div>
            </div>
          </el-col>
          <el-col class="noprint" style="float:right;text-align:left" :xs={span:24} :sm={span:24} :md={span:4} :lg={span:4}
            :gutter="20">
            <el-button @click.native="gotobeizhu" size="medium" type="primary">备注 ({{info.notes}})</el-button>
            <el-button type="primary" size="medium" v-show="info.info.id" @click="ceshi(1)">打印</el-button>
          </el-col>
        </el-row>
        <div class="shigong">
          <div>
            <p>施工人</p>
          </div>
          <div>
            <p :key="index" v-for="(item,index) in info.operators">
              类别: <el-tag style="margib-right:20px;">{{item.type}}</el-tag>
              人员:{{item.operators}}
            </p>
          </div>
        </div>
      </div>
      <div class="details-ct-info noprint">
        <el-form label-position="left" :model="info.info" ref="info.info" label-width="100px" class="demo-info.info">
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
              <el-form-item label="联系人：" prop="contact_name">
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
              <el-form-item label="到店时间：" prop="arrive_times">
                <span>{{info.info.arrive_times>0?checktime(info.info.arrive_times):""}}</span>
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
                <span>{{info.info.service_consultant}}</span>

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
            <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} v-if="info.info.need_maintenance==1">
              <el-form-item label-width="120px" label="保养里程周期：">
                <span>{{info.info.maintenance_km?info.info.maintenance_km+"公里":""}}</span>
              </el-form-item>
            </el-col>
            <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} v-if="info.info.need_maintenance==1">
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
        </el-form>
      </div>

      <div>
        <h3 style="margin:20px 0;">项目与工时</h3>
        <el-table :data="info.item_list" show-summary :summary-method="getSummaries" border tooltip-effect="dark" style="width:600px">
          <el-table-column label="编号" prop="op_number" align="center" header-align="center" width="80">
          </el-table-column>
          <el-table-column prop="id" align="center" header-align="center" label="项目编码" width="120">
          </el-table-column>
          <el-table-column prop="item_name" label="项目名称" width="120" align="center" show-overflow-tooltip>
          </el-table-column>
          <el-table-column prop="item_times" align="center" header-align="center" label="工时(小时)" 
            show-overflow-tooltip>
          </el-table-column>
          <el-table-column prop="type" label="业务类别" align="center" header-align="center" width="120"
            show-overflow-tooltip>
          </el-table-column>

        </el-table>
      </div>
      <div>
        <h3 style="margin:20px 0;">材料</h3>
        <el-table :data="info.part_list" show-summary :summary-method="getSummaries" border tooltip-effect="dark" style="width:900px">
          <el-table-column label="编号" prop="id" align="center" header-align="center" >
          </el-table-column>
          <el-table-column prop="parts_sn" label="材料编码" align="center" header-align="center" width="100">
          </el-table-column>
          <el-table-column prop="parts_name" align="center" header-align="center" label="材料名称" width="150"
            show-overflow-tooltip>
            <template slot-scope="scope">
              {{scope.row.parts_name.split(" ")[0]}}
            </template>
          </el-table-column>

          <el-table-column prop="spec" align="center" header-align="center" label="规格" width="90" show-overflow-tooltip>
          </el-table-column>
          <el-table-column prop="unit" align="center" header-align="center" label="单位" width="70" show-overflow-tooltip>
          </el-table-column>
          <el-table-column prop="amount" label="数量" align="center" header-align="center" width="70"
            show-overflow-tooltip>
          </el-table-column>
          <el-table-column prop="type" label="业务类别" align="center" header-align="center" 
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
        <el-table :data="info.salary" border style="width:800px">
          <el-table-column prop="username" align="center" header-align="center" label="姓名" width="120">
          </el-table-column>
          <el-table-column prop="times" align="center" header-align="center" label="工时(小时)" width="120">
          </el-table-column>
          <el-table-column prop="op_type" align="center" header-align="center" label="项目" width="120">
          </el-table-column>
          <el-table-column prop="ratio" align="center" header-align="center" label="提成比例" width="120">
          </el-table-column>
          <el-table-column align="center" header-align="center" prop="total_price" label="总工时费">
          </el-table-column>
          <el-table-column align="center" header-align="center" prop="fee" label="提成金额">
          </el-table-column>
        </el-table>
      </div>
      <!-- 费用信息 -->
      <div class="details-ct-cost noprint" style="margin-top:30px;">
        <h3 style="margin-bottom:20px;">费用信息</h3>
        <el-form label-position="left" ref="form" :model="cost" label-width="140px">
          <el-row>
            <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
              <el-form-item label="工时数合计">
                <span>{{info.info.total_times}}</span>
              </el-form-item>
            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
              <el-form-item label="材料数量合计">
                <span>{{info.info.total_parts}}</span>
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script>
  let LODOP;
  import {
    Axios
  } from "@/api/login";
  import {
    parseTime
  } from "@/utils/index";
  export default {
    data() {
      return {
        info: {},
        printtime:"",
        dialogImageUrl: "",
        modellist: "",
        brandlist: "",
        dialogVisible: false,
        consultantlist: "",
      }
    },
    methods: {
      checktime(time) {
        return parseTime(time);
      },
      onecode() {
        LODOP = getLodop(document.getElementById('LODOP1'), document.getElementById('LODOP_EM1'));
        LODOP.ADD_PRINT_BARCODE(40, 200, 400, 47, "EAN128B", this.info.info.order_sn);
        LODOP.SET_PRINT_STYLEA(0, "AlignJustify", 2);
        LODOP.PRINT_DESIGN();
      },
      ceshi() {
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
            data[0].check == 1 ? [0, 1, 2, 3, 4, 6, 8] : [0, 1, 2, 3, 4, 5, 6, 8, 10, 11, 12, 13];
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
              console.log("sums[index]", values)
              sums[index] = values.reduce((prev, curr) => {
                const value = Number(curr);
                if (!isNaN(value)) {
                  return prev + curr;
                } else {
                  return prev;
                }
              }, 0);
              if (data[0].check != 1 && (index == 8 || index == 9)) {
                for (let i in data) {
                  if (data[i].parts_status == "已退货") {
                    if (index == 8) {
                      sums[index] = sums[index] - parseFloat(data[i].sell_price) * parseFloat(data[i].amount)
                    } else {
                      sums[index] = sums[index] - parseFloat(data[i].final_price)
                    }
                  }
                }
              }
              if (data[0].check != 1 && index == 8) {
                sums[index] = 0;
                for (let i in data) {
                  sums[index] += parseFloat(data[i].sell_price) * parseFloat(data[i].amount);
                }
              }
              if (data[0].check == 1 && index == 7) {
                sums[index] = 0;
                for (let i in data) {
                  sums[index] += parseFloat(data[i].item_cost) * parseFloat(data[i].item_times);
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
      preimg(img) {
        this.dialogImageUrl = img;
        this.dialogVisible = true;
      },
      gotobeizhu() {
        let routeData = this.$router.resolve({
          name: "remarkindex",
          query: {
            id: this.$route.query.id
          }
        });
        window.open(routeData.href, "_blank");
      },
      getbrandlist(index) {
        for (let i in this.brandlist) {
          if (this.brandlist[i].id == index) {
            return this.brandlist[i].name
          }
        }
      }
    },
    mounted() {
      Axios("workorder/vieworder", {
        order_id: this.$route.query.id
      }).then(res => {
        let data = res.data;
        for (let i in data.item_list) {
          data.item_list[i].check = 1;
        }
        for (let i in data.part_list) {
          data.part_list[i].check = 2;
        }
        for (let i in data.questions) {
          data.questions[i].choice = parseInt(data.questions[i].choice)
        }
        this.info = data;

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

  .shigong {
    display: flex;

    p {
      margin: 0;
      line-height: 30px;
      margin-bottom: 10px;
      margin-right: 40px;
    }
  }

  .zonebg {
    position: relative;

    &:after {
      content: "";
      display: block;
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 1;
    }
  }

  .print-ct {
    display: none;
  }

  .el-dialog__body {
    box-sizing: border-box;
  }

  .el-form-item__content {
    font-size: 16px;
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
    .el-col-md-8{
      height:65px;
    }
    .zhekou input {
      width: 140px !important;
    }

    input {
      width: 210px !important;
    }

    .el-form--label-top .el-form-item__label {
      padding: 0 0 0px;
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
      width: 220px;
    }



    table {}

    .model-type {
      >div {

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
