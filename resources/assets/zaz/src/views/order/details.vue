<template>
  <div class="details" v-loading="loadings">
    <div style="padding:20px;" class="print-ct"  id="form1">
      <h1 style="text-align:center;width:100%;margin-bottom:20px;">施工单</h1>
      <div class="print-ct-header" style="margin-bottom:30px;width:100%;display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;">
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">单号:{{ruleForm.order_sn}}</div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;"></div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">打印时间:{{printtime}}</div>

        <!-- <img style="margin-left:40px;max-width:400px;" :src="'data:image/jpeg;base64,'+ruleForm.barcode"
          alt=""> -->
      </div>
      <div style="display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;"
        class="ul">
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          车牌号 :{{ruleForm.vehicle_number?ruleForm.vehicle_number:"未填写"}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          VIN号码 : {{ruleForm.vehicle_vin?ruleForm.vehicle_vin:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px" class="list">
          品牌 : {{brandlists()}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          车型 : {{ruleForm.brand_model?ruleForm.brand_model:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          颜色 : {{ruleForm.vehicle_color?ruleForm.vehicle_color:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          本次里程 :{{ruleForm.current_km?ruleForm.current_km+'公里':'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          到店时间 :{{ruleForm.arrive_times?checkdate(ruleForm.arrive_times):'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          预计完工时间 : {{ruleForm.plan_endtimes?checkdate(ruleForm.plan_endtimes):'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          服务顾问 : {{guwen()}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          顾问手机号 : {{ruleForm.consultant_phone?ruleForm.consultant_phone:'未填写'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          是否需要洗车 : {{ruleForm.need_wash==1?'需要':'不需要'}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;" class="list">
          是否需要保养 : {{ruleForm.need_maintenance==1?'属于':'不属于'}}
        </p>
      </div>
      <div>
        <p>故障描述:</p>
        <p>{{ruleForm.fault_intro}}</p>
      </div>
      <div class="printitem" id="dataitems" v-if="dataitems.length>0">
        <h3 style="margin:20px auto">项目与工时</h3>
        <table border="1" width="600" height="30" cellspacing="0"  style="margin:0 auto;border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="125" height="20" align="center" style="border:solid 1px black">编号</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">项目编码</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">项目名称</td>
            <!-- <td width="99" height="20" align="center" style="border:solid 1px black">工时(小时)</td>
            <td width="99" height="20" align="center" style="border:solid 1px black">单价(元)</td>
            <td width="99" height="20" align="center" style="border:solid 1px black">应收金额(元)</td> -->
            <td width="125" height="20" align="center" style="border:solid 1px black">业务类型</td>
          </tr>
          <tr v-for="(item,index) in dataitems" :key="index">
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.op_number}}</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.id}}</td>
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.item_name}}</td>
            <!-- <td width="99" height="20" align="center" style="border:solid 1px black">{{item.item_times}}</td>
            <td width="99" height="20" align="center" style="border:solid 1px black">{{item.item_cost}}</td>
            <td width="99" height="20" align="center" style="border:solid 1px black">{{item.total_price}}</td> -->
            <td width="125" height="20" align="center" style="border:solid 1px black">{{item.type_name}}</td>
          </tr>
           <tr>
              <td width="125" height="20" align="center" style="border:solid 1px black">合计:</td>
              <td width="125" height="20" align="center" style="border:solid 1px black"></td>
              <td width="125" height="20" align="center" style="border:solid 1px black"></td>
              <!-- <td width="99" height="20" align="center" style="border:solid 1px black">{{totalhour()}}小时</td>
              <td width="99" height="20" align="center" style="border:solid 1px black"></td>
              <td width="99" height="20" align="center" style="border:solid 1px black">{{totaltimes()}}元</td> -->
              <td width="125" height="20" align="center" style="border:solid 1px black"></td>
          </tr>
        </table>
      </div>
      <div class="printitem" id="datamaterials" v-if="datamaterials.length>0">
        <h3 style="margin:10px auto;">材料项目</h3>
         <table border="1"  height="30" cellspacing="0"  style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="80" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="200" height="30" align="center" style="border:solid 1px black">商品名称</td>
            <td width="100" height="30" align="center" style="border:solid 1px black">规格</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">单位</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">数量</td>
            <td width="100" height="30" align="center" style="border:solid 1px black">应收金额(元)</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">业务类型</td>
          </tr>
          <tr v-for="(item,index) in datamaterials" :key="index">
            <td width="80" height="30" align="center" style="border:solid 1px black">{{item.order_id}}</td>
            <td width="200" height="30" align="center" style="border:solid 1px black">{{item.parts_name.split(" ")[0]}}</td>
            <td width="100" height="30" align="center" style="border:solid 1px black">{{item.spec}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{item.unit}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{item.amount}}</td>
            <td width="100" height="30" align="center" style="border:solid 1px black">{{item.sell_price*item.amount}}</td>
            <td width="80" height="30" align="center" style="border:solid 1px black">{{item.type_name}}</td>
          </tr>
          <tr>
              <td width="80" height="30" align="center" style="border:solid 1px black">合计:</td>
              <td width="200" height="30" align="center" style="border:solid 1px black"></td>
              <td width="100" height="30" align="center" style="border:solid 1px black"></td>
              <td width="80" height="30" align="center" style="border:solid 1px black"></td>
              <td width="80" height="30" align="center" style="border:solid 1px black"></td>
              <td width="100" height="30" align="center" style="border:solid 1px black">{{totalpartts()}}元</td>
              <td width="80" height="30" align="center" style="border:solid 1px black"></td>
          </tr>
        </table>
      </div>
      <h3 style="margin:10px auto">费用信息</h3>
      <div class="ul" style="width:100%; display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;margin-bottom:5px;font-size:16px;" class="list">
          工时数合计 : {{totalhour()}}
        </p>
        <p style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;margin-bottom:5px;font-size:16px" class="list">
          材料数量合计 : {{totalparts()}}
        </p>
      </div>
    </div>
    <div class="noprint" >
      <messagelist v-show="info.account_name" :info="info"></messagelist>
      <div class="details-ct">
        <div class="details-ct-header noprint">
          <el-row class="row-bg" style="display:flex;margin-bottom:10px;justify-content: space-around;align-items:center">
            <el-col style="display:flex;align-items:center;" :xs={span:24} :sm={span:24} :md={span:12} :lg={span:12}>
              <span>单号:{{ruleForm.order_sn}}</span>
              <img @click="onecode" style="margin-left:40px;max-width:400px;" :src="'data:image/jpeg;base64,'+ruleForm.barcode" alt="">
            </el-col>
            <el-col class="noprint" style="float:right;text-align:right" :xs={span:24} :sm={span:24} :md={span:12} :lg={span:12}
              :gutter="20">
              <el-button type="primary" size="medium" v-show="ruleForm.id" @click="gotobeizhu">备注</el-button>
              <el-button v-show="ruleForm.id&&ruleForm.order_status!=0" type="primary" size="medium" @click="abolish">作废</el-button>
              <el-button type="primary" size="medium" v-show="ruleForm.id" @click="ceshi(1)">打印</el-button>
              <!-- <el-button type="primary" size="medium" v-show="ruleForm.id" @click="gotoaccount">结算</el-button> -->
            </el-col>
          </el-row>
          <div>
            订单状态：{{ruleForm.status}}
          </div>
          <div class="model-type">
            <p>业务类型:</p>
            <div>
              <el-tag v-show="item" v-for="(item,index) in service_items" :key="index">{{item}}</el-tag>
            </div>
          </div>
        </div>
        <div class="details-ct-info noprint">
          <el-form label-position="left" :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
            <el-row class="elzone">
              <el-col class="zoneel" style="display:inline-block;" :xs={span:12} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车牌号" prop="vehicle_number">
                  <el-input placeholder="请输入车牌号" @change="changinput(ruleForm.vehicle_number,1)" v-model="ruleForm.vehicle_number" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" style="display:inline-block;" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="VIN码" prop="vehicle_vin">
                  <el-input placeholder="请输入VIN码" @change="changinput(ruleForm.vehicle_vin,2)" v-model="ruleForm.vehicle_vin" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="品牌" prop="brand_id">
                  <el-select v-model="ruleForm.brand_id" clearable placeholder="请选择">
                    <el-option v-for="(value,key,index) in brandlist" :key="index" :label="value.name" :value="value.id">
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车型" prop="model">
                  <el-input placeholder="请输入车型" @change="changinput" v-model="ruleForm.model" clearable>
                  </el-input>
                  <!--<el-select v-model="ruleForm.model_id" value="compymodelid()" clearable placeholder="请选择">
                 <el-option
                  v-for="(value,key,index) in modellist"
                  :key="index"
                  :label="value.name"
                  :value="value.id">
                </el-option>
              </el-select> -->
                </el-form-item>
              </el-col>
              <!-- <el-col class="noprint" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
            <el-form-item  label="年份" prop="model_year">
               <el-input
               disabled
                placeholder="请选择型号"
                v-model="ruleForm.model_year"
                clearable>
              </el-input>
            </el-form-item>
                </el-col> -->
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="具体型号" prop="vehicle_model">
                  <el-input placeholder="请输入具体型号" v-model="ruleForm.vehicle_model" clearable>
                  </el-input>
                  <!-- <el-select v-model="ruleForm.vehicle_model_id" clearable placeholder="请选择">
                 <el-option
                  v-for="(value,key,index) in vehicle"
                  :key="index"
                  :label="value.name"
                  :value="value.ids">
                </el-option>
              </el-select> -->
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="颜色" prop="vehicle_color">
                  <el-input placeholder="请输入颜色" v-model="ruleForm.vehicle_color" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车辆等级" prop="model_class">
                  <el-select v-model="ruleForm.model_class" clearable placeholder="请选择">
                    <el-option v-for="item in carlevel" :key="item.value" :label="item.label" :value="item.value">
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} label="购车日期">
                  <el-date-picker v-model="ruleForm.buy_times" type="date" placeholder="选择日期" format="yyyy 年 MM 月 dd 日"
                    :editable=false value-format="timestamp">
                  </el-date-picker>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="保险">
                  <el-date-picker v-model="ruleForm.insurance_times" type="date" placeholder="选择日期" format="yyyy 年 MM 月 dd 日"
                    :editable=false value-format="timestamp">
                  </el-date-picker>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="保险公司">
                  <el-input placeholder="请输入保险公司" v-model="ruleForm.insurance_company" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="车主名称">
                  <el-input placeholder="请输入车主姓名" v-model="ruleForm.vehicle_owner" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="联系人" prop="contact_name">
                  <el-input placeholder="请输入联系人名称" @change="changinput(ruleForm.contact_name,3)" v-model="ruleForm.contact_name" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="电话" prop="phone">
                  <el-input placeholder="请输入电话号码" type="tel" v-model="ruleForm.phone" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="客户来源" prop="customer_source">
                  <el-input placeholder="请输入客户来源" v-model="ruleForm.customer_source" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} label="介绍人姓名">
                  <el-input placeholder="请输入介绍人姓名" v-model="ruleForm.introducer" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>

                <el-form-item label="介绍人电话">
                  <el-input placeholder="请输入介绍人电话" type="tel" v-model="ruleForm.introducer_phone" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="本次里程" prop="current_km">
                  <el-input class="flex" placeholder="请输入本次里程" v-model="ruleForm.current_km" clearable>
                  </el-input>
                  <span>公里</span>
                </el-form-item>
              </el-col>
              <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="到店时间" prop="arrive_times">
                  <el-date-picker v-model="ruleForm.arrive_times" type="datetime" placeholder="请选择到店时间" value-format="timestamp">
                  </el-date-picker>
                </el-form-item>
              </el-col>
              <el-col :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label-width="120px" label="预计完工时间" prop="plan_endtimes">
                  <el-date-picker v-model="ruleForm.plan_endtimes" type="datetime" placeholder="请选择预计完工时间" value-format="timestamp">
                  </el-date-picker>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="完工时间">
                  <el-input v-model="ruleForm.done_time" disabled ></el-input>
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="服务顾问" prop="service_consultant">
                  <el-select v-model="ruleForm.service_consultant" filterable remote reserve-keyword placeholder="请输入关键词"
                    :remote-method="remoteMethod" @change="remotechange" :loading="loading">
                    <el-option v-for="item in consultantlist" :key="item.index" :label="item.username" :value="item.id">
                    </el-option>
                  </el-select>

                  <!-- <el-autocomplete
                    v-model="ruleForm.service_consultants"
                    :fetch-suggestions="querySearchAsync"
                    placeholder="请输入顾问"
                    :change="guwenchange"
                    @select="handleSelect"
                  ></el-autocomplete> -->
                </el-form-item>
              </el-col>
              <el-col class="zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label="顾问手机号" prop="consultant_phone">
                  <el-input placeholder="请输入顾问手机号" type="tel" disabled v-model="ruleForm.consultant_phone" clearable>
                  </el-input>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label-width="140px" label="是否需要洗车" prop="need_wash">
                  <el-switch :content="ruleForm.need_wash" v-model="ruleForm.need_wash" active-color="#13ce66"
                    inactive-color="#dcdfe6" :active-value="1" :inactive-value="0">
                  </el-switch>
                  <span>{{ruleForm.need_wash==1?'需要':'不需要'}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6}>
                <el-form-item label-width="140px" label="本次是否属于保养">
                  <el-switch :content="ruleForm.need_maintenance" v-model="ruleForm.need_maintenance" active-color="#13ce66"
                    inactive-color="#dcdfe6" :active-value="1" :inactive-value="0">
                  </el-switch>
                  <span>{{ruleForm.need_maintenance==1?'属于':'不属于'}}</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" v-if="ruleForm.need_maintenance==1" :xs={span:24} :sm={span:12} :md={span:8}
                :lg={span:6}>
                <el-form-item label-width="120px" label="保养里程周期">
                  <el-input class="flex" placeholder="请输入保养里程" type="tel" v-model="ruleForm.maintenance_km" clearable>
                  </el-input>
                  <span>公里</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" v-if="ruleForm.need_maintenance==1" :xs={span:24} :sm={span:12} :md={span:8}
                :lg={span:6}>
                <el-form-item label-width="120px" label="保养时间周期">
                  <el-input class="flex" placeholder="请选择时间周期" type="tel" v-model="ruleForm.maintenance_cycle"
                    clearable>
                  </el-input>
                  <span>天</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} v-if="ruleForm.need_maintenance==1">
                <el-form-item label-width="120px" label="下次保养里程">
                  <el-input class="flex" placeholder="等待计算" disabled="" type="tel" v-model="ruleForm.next_maintenance_km"
                    clearable>
                  </el-input>
                  <span>公里</span>
                </el-form-item>
              </el-col>
              <el-col class="noprint zoneel" :xs={span:24} :sm={span:12} :md={span:8} :lg={span:6} v-if="ruleForm.need_maintenance==1">
                <el-form-item label-width="120px" label="下次保养日期">
                  <el-date-picker v-model="ruleForm.next_maintenance_times" type="date" placeholder="等待计算" :editable=false
                    disabled format="yyyy 年 MM 月 dd 日" value-format="timestamp">
                  </el-date-picker>
                </el-form-item>
              </el-col>
              <el-col class="noprint" :xs={span:24} :sm={span:24} :md={span:24} :lg={span:24}>
                <h3 style="margin-bottom:20px;">故障描述</h3>
                <el-input type="textarea" :rows="10" placeholder="请输入内容" prop="fault_intro" v-model="ruleForm.fault_intro">
                </el-input>
              </el-col>
            </el-row>
            <div class="table noprint " style="margin-top:40px;">
              <div class="upload">
                <h3 style="display:inline-block;width:120px;">车辆查验图</h3>
              </div>
              <div>
                <el-upload action="/api/common/upload" list-type="picture-card" :limit="10" ref="uploadimg"
                  :on-exceed="handleExceed" :file-list="checkimages" :on-success="getimg" :before-upload="beforeupload"
                  :multiple="true" :on-preview="handlePictureCardPreview" :on-remove="handleRemove">
                  <i style="margin-top:83px;" class="el-icon-plus"></i>
                </el-upload>
                <el-dialog :visible.sync="dialogVisible">
                  <img width="100%" :src="dialogImageUrl" alt="">
                </el-dialog>

              </div>
              <el-form-item style="text-align:center;margin-top:40px;width:100%;">
                <el-button type="danger" size="small" @click="onSubmit">{{ruleForm.key?'保存':'创建'}}</el-button>
              </el-form-item>
            </div>
          </el-form>
        </div>
        <!-- 工时与项目 -->
        <div class="details-ct-project noprint" style="margin-top:30px;">
          <el-form label-position="left" label-width="100px" :model="items" ref="items" class="demo-ruleForm">
            <el-form-item label="项目与工时" class="noprint">
              <el-button type="primary" size="medium" @click="checktemporary(1,'添加临时项目')">添加临时项目</el-button>
              <el-button type="primary" size="medium" @click="checkitem(1,'添加项目')">添加项目</el-button>
              <el-button type="primary" size="medium" @click="checkdiscount(1)">设置折扣</el-button>
            </el-form-item>

            <el-table :stripe=true ref="multipleTable" :data="dataitems" border tooltip-effect="dark" style="width:1300px"
              :summary-method="getSummaries" show-summary @selection-change="handleSelectionChange">
              <el-table-column type="selection" width="80">
              </el-table-column>
              <el-table-column label="编号" prop="op_number" align="center" header-align="center" width="80">
              </el-table-column>
              <el-table-column prop="id" align="center" header-align="center" label="项目编码" width="120">
              </el-table-column>
              <el-table-column prop="item_name" label="项目名称" width="120" align="center" show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="item_times" align="center" header-align="center" label="工时(小时)" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="item_cost" label="工时费(元)" align="center" header-align="center" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="total_price" label="应收金额(元)" align="center" header-align="center" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="discount" align="center" header-align="center" label="折扣" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="final_price" align="center" header-align="center" label="折后金额(元)"
                show-overflow-tooltip>
              </el-table-column>

              <el-table-column prop="type_name" align="center" header-align="center" label="业务类型" show-overflow-tooltip>
              </el-table-column>
              <el-table-column label="操作" align="center" header-align="center" show-overflow-tooltip>
                <template slot-scope="scope">
                  <el-popover placement="top" width="100" v-model="scope.row.delete">
                    <p>确定删除吗？</p>
                    <div style="text-align: right; margin: 0">
                      <el-button size="mini" type="text" @click="scope.row.delete = false">取消</el-button>
                      <el-button type="primary" size="mini" @click="deleteitem(scope.$index, scope,true)">确定</el-button>
                    </div>
                    <el-button size="small" slot="reference" type="danger">删除</el-button>
                  </el-popover>
                </template>
              </el-table-column>
            </el-table>
          </el-form>

        </div>
        <!-- 材料 -->
        <div class="details-ct-project " style="margin-top:30px;">
          <el-form label-position="left" label-width="100px" :model="materials" ref="materials" class="demo-ruleForm">
            <el-form-item label="材料" class="noprint">
              <el-button type="primary" size="medium" @click="checktemporary(2,'添加临时材料')">添加临时材料</el-button>
              <el-button type="primary" size="medium" @click="checkitem(2,'添加材料')">添加材料</el-button>
              <el-button type="primary" size="medium" @click="checkdiscount(2)">设置折扣</el-button>
            </el-form-item>
            <el-table type="index" :stripe=true show-summary :summary-method="getSummaries" ref="multipleTable" :data="datamaterials"
              border class="printprimary" :class="printnum==3?'active':''" tooltip-effect="dark" style="width:100%"
              @selection-change="handleSelectionChanged">
              <el-table-column type="selection" width="55">
              </el-table-column>
              <el-table-column label="编号" prop="parts_sn" align="center" header-align="center">
              </el-table-column>
              <el-table-column prop="parts_name" label="商品名称" align="center" header-align="center" width="120">
              </el-table-column>
              <el-table-column prop="spec" align="center" header-align="center" label="规格" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="unit" align="center" header-align="center" label="单位" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="sell_price" align="center" header-align="center" label="单价(元)" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="amount" align="center" header-align="center" label="数量" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="discount" label="折扣" align="center" header-align="center" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column  prop="sell_price"  label="应收金额(元)" width="120" align="center" header-align="center"
                show-overflow-tooltip>
                <template slot-scope="scope">
                  <span>{{scope.row.sell_price*scope.row.amount}}</span>
                </template>
              </el-table-column>
              <el-table-column prop="final_price" align="center" header-align="center" label="折后金额(元)" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="type_name" align="center" header-align="center" label="质检状态" show-overflow-tooltip>
                <template slot-scope="scope">
                  <span>{{scope.row.quality_status==0?'待质检':scope.row.quality_status==1?'已质检':'返工'}}</span>
                </template>
              </el-table-column>
              <el-table-column prop="parts_status" align="center" header-align="center" label="发货状态" width="120"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column prop="type_name" label="业务类别" align="center" header-align="center" show-overflow-tooltip>
              </el-table-column>
              <el-table-column label="操作" align="center" show-overflow-tooltip>
                <template slot-scope="scope">
                  <el-popover vi-if="scope.row.parts_status=='待发货'" placement="top" width="100" v-model="scope.row.delete">
                    <p>确定删除吗？</p>
                    <div style="text-align: right; margin: 0">
                      <el-button size="mini" type="text" @click="scope.row.delete = false">取消</el-button>
                      <el-button type="primary" size="mini" @click="deletepart(scope.$index, scope,true)">确定</el-button>
                    </div>
                    <el-button v-if="scope.row.parts_status=='待发货'" size="small" slot="reference" type="danger">删除</el-button>
                  </el-popover>
                  <el-popover v-if="scope.row.parts_status=='已发货'&&scope.row.quality_status==2" placement="top" width="100"
                    v-model="scope.row.tuihuo">
                    <p>确认退货吗</p>
                    <div style="text-align: right; margin: 0;">
                      <el-button size="mini" type="text" @click="scope.row.tuihuo = false">取消</el-button>
                      <el-button type="primary" size="mini" @click="tuihuopart(scope.$index, scope,true)">确定</el-button>
                    </div>
                    <el-button v-if="scope.row.parts_status=='已发货'&&scope.row.quality_status==2" style="display:block;margin:10px auto;"
                      size="small" slot="reference" type="danger">退货</el-button>
                  </el-popover>
                </template>
              </el-table-column>
            </el-table>
          </el-form>
        </div>
        <!-- 费用信息 -->
        <div class="details-ct-cost noprint" style="margin-top:30px;">
          <h3 style="margin-bottom:20px;">费用信息</h3>
          <el-form label-position="left" ref="form" :model="cost" label-width="140px">
            <el-form-item label="工时数合计(小时)">
              <span>{{totalhour()}}</span>
            </el-form-item>
            <el-form-item label="材料数量合计">
              <span>{{totalparts()}}</span>
            </el-form-item>
            <el-form-item label="工时费用(元)">
              <span>{{totaltime()}}</span>
            </el-form-item>
            <el-form-item label="材料费用(元)">
              <span>{{totalpartt()}}</span>
            </el-form-item>
            <el-form-item label="其他费用(元)">
              <el-input type="text" v-model="cost.othercost"></el-input>
            </el-form-item>
            <el-form-item label="费用合计(元)">
              <span>{{totlacost()}}</span>
            </el-form-item>
            <el-col style="margin-bottom:60px;">
              <h5>下一次维修建议</h5>
              <el-input type="textarea" :rows="10" placeholder="请输入内容" v-model="cost.next_advice"></el-input>
            </el-col>
            <div style="text-align:center;" class="noprint">
              <el-button type="primary" @click="saveotherinfo">完成</el-button>
              <el-button type="primary" @click="ceshi(1)">打印</el-button>
            </div>
          </el-form>
        </div>
        <!--客户反馈 -->
        <div class="details-ct-feedback" style="margin-top:30px;display:none">
          <h3 style="margin-bottom:20px;">客户反馈</h3>
          <el-row>
            <el-col>
              <div>维修情况</div>
              <el-rate v-model="feedback.maintain" disabled show-text>
              </el-rate>
            </el-col>
            <el-col>
              <div>服务环境</div>
              <el-rate v-model="feedback.environment" disabled show-text>
              </el-rate>
            </el-col>
            <el-col>
              <div>服务状态</div>
              <el-rate v-model="feedback.manner" disabled show-text>
              </el-rate>
            </el-col>
            <el-col>
              <div>休息区域</div>
              <el-rate v-model="feedback.rest" disabled show-text>
              </el-rate>
            </el-col>
            <el-col>
              <el-input type="textarea" :rows="10" placeholder="请输入内容" v-model="feedback.suggest"></el-input>
            </el-col>
          </el-row>
        </div>
      </div>
      <!-- 添加项目 -->
      <el-dialog :title="this.is_temporary==1?'添加临时项目':'添加项目'" :visible.sync="dialog.temporary.showkey" width="50%"
        style="box-sizing:border-box">
        <el-form style="text-align:center;" label-position="left" ref="temporarys" :model="dialog.temporary" :rules="temprule"
          label-width="100px">
          <el-row>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-form-item label="业务类别" prop="istype" style="text-align:left">
                <el-select  @change="typechange" v-model="dialog.temporary.istype" placeholder="请选择业务类别">
                  <el-option :label="item.name" :value="item.id" :key="index" v-for="(item,index) in dialog.temporary.classes"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                <el-form-item style="text-align:left" :label="checkindex==1?'项目名称':'材料名称'" prop="itemname">
                  <el-select v-show="this.is_temporary!=1" v-model="dialog.temporary.itemname" filterable remote
                    @change="itemchange" placeholder="请输入名称" :loading="loadingd">
                    <el-option v-for="item in alloptions" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                  </el-select>
                  <el-input v-show="this.is_temporary==1" v-model="dialog.temporary.itemname"></el-input>
                </el-form-item>
              </el-col>

            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-form-item label="折后单价">
                <span>{{discountof}}</span>
              </el-form-item>
            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-form-item label="项目工时" prop="parts_time">
                <el-input v-if="this.is_temporary==1" v-model="dialog.temporary.parts_time"></el-input>
                <span v-else>{{dialog.temporary.parts_time?dialog.temporary.parts_time:'等待选择项目'}}</span>
              </el-form-item>
            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-form-item label="应收金额">
                <span>{{shouldvalue}}</span>
              </el-form-item>
            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-form-item label="工时费" prop="item_cost">
                <el-input v-if="this.is_temporary==1" v-model="dialog.temporary.item_cost"></el-input>
                <span v-else>{{dialog.temporary.item_cost?dialog.temporary.item_cost:"等待选择项目"}}</span>
              </el-form-item>
            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-form-item label="折后金额">
                <span>{{discountvalue}}</span>
              </el-form-item>
            </el-col>
            <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
              <el-form-item label="折扣" style="text-align:left" prop="discount">
                <el-input v-model="dialog.temporary.discount" style="width:50%" class="zhekou"></el-input>
                <span>(0-10之间)</span>
              </el-form-item>
            </el-col>
            <el-col>
              <!-- <el-form-item label="业务类别">
          <el-radio-group v-model="dialog.temporary.op_type">
            <el-radio :label="item.id"
            v-for="(item,index) in dialog.temporary.classes"
            :key="index">{{item.name}}</el-radio>
          </el-radio-group>
        </el-form-item> -->
            </el-col>
          </el-row>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button size="small" type="primary" @click="dialog.temporary.showkey = false;">取 消</el-button>
          <el-button size="small" type="primary" @click="dialogtemporary(true)">确 定</el-button>
        </span>
      </el-dialog>

    </div>

    <!-- 添加材料 -->
    <el-dialog :title="this.is_temporary==1?'添加临时材料':'添加材料'" :visible.sync="dialog.parts.showkey" width="50%" style="box-sizing:border-box">
      <el-form label-position="left" ref="parts" :model="dialog.parts" :rules="patts" label-width="100px">
        <el-row>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="业务类别" prop="istype" style="text-align:left;">
              <el-select  v-model="dialog.parts.istype" placeholder="请选择业务类别">
                <el-option :label="item.name" :value="item.id" :key="index" v-for="(item,index) in dialog.temporary.classes"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item style="text-align:left" :label="checkindex==1?'项目名称':'材料名称'" prop="itemname">
              <el-select v-show="this.is_temporary!=1" v-model="dialog.parts.itemname" filterable remote @change="partschange"
                placeholder="请输入关键词" :loading="loadingd">
                <el-option v-for="item in alloptionsd" :key="item.id" :label="item.parts_name+' '+item.spec+' '+item.target_model+' 剩余数量:'+item.amount"
                  :value="item.id">
                </el-option>
              </el-select>
              <el-input v-show="this.is_temporary==1" v-model="dialog.parts.itemname"></el-input>

            </el-form-item>

          </el-col>

          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="折后单价">
              <span>{{discountofs}}</span>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="规格" prop="spec">
              <el-input v-if="this.is_temporary==1" v-model="dialog.parts.spec"></el-input>
              <span v-else>{{dialog.parts.spec?dialog.parts.spec:'等待选择材料'}}</span>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="应收金额">
              <span>{{shouldvalues}}</span>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="单位" prop="unit">
              <el-input v-if="this.is_temporary==1" v-model="dialog.parts.unit"></el-input>
              <span v-else>{{dialog.parts.unit?dialog.parts.unit:'等待选择材料'}}</span>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="折后金额">
              <span>{{discountvalues}}</span>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="数量" prop="amount">
              <el-input v-model="dialog.parts.amount"></el-input>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="单价" prop="item_cost">

              <el-input v-if="is_temporary==1" v-model="dialog.parts.item_cost"></el-input>
              <span v-else>{{dialog.parts.item_cost?dialog.parts.item_cost:'等待选择材料'}}</span>
            </el-form-item>
          </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
            <el-form-item label="折扣" style="text-align:left" prop="discount">
              <el-input v-model="dialog.parts.discount" style="width:50%" class="zhekou"></el-input>
              <span>(0-10之间)</span>
            </el-form-item>
          </el-col>
          <el-col>
            <!-- <el-form-item label="业务类别">
          <el-radio-group v-model="dialog.temporary.op_type">
            <el-radio :label="item.id"
            v-for="(item,index) in dialog.temporary.classes"
            :key="index">{{item.name}}</el-radio>
          </el-radio-group>
        </el-form-item> -->
          </el-col>
        </el-row>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button size="small" type="primary" @click="dialog.parts.showkey = false;">取 消</el-button>
        <el-button size="small" type="primary" @click="parrtsubmit(true)">确 定</el-button>
      </span>
    </el-dialog>
    <!-- 设置折扣 -->
    <el-dialog :title="dialog.discount.title" :visible.sync="dialog.discount.showkey" ref="dialog" :model="dialog.discount"
      :rules="dialogs" width="400px">
      <div class="block" style="text-align:center;margin-bottom:10px;">
        <el-input style="width:210px;display:inline-block;margin-right:40px;" v-model="dialog.discount.discount"></el-input>
        <span>(0-10之间)</span>
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialog.discount.showkey=false">取 消</el-button>
        <el-button type="primary" @click="dialogdiscount(true)">保存</el-button>
      </div>

    </el-dialog>
  </div>
</template>

<script>
  let LODOP;
  let timeclick,
    chepaitime;
  // 这里本地新建一个映射表
  let otherlist = [0, 6]; // 0=>作废 6=>已结算
  import Messagelist from "@/components/Messagelist/Messagelist";
  import {
    Axios
  } from "@/api/login";
  import {
    setTimeout,
    clearTimeout
  } from "timers";
  import {
    parseTime
  } from "@/utils/index";
  import {
    getCookie,
    setToken,
    removeToken,
    setCookie,
    removeCookie
  } from "@/utils/auth";

  // import { settings } from 'cluster';
  // import { setTimeout } from 'timers';

  export default {
    name: "detailsd",
    components: {
      Messagelist
    },
    methods: {
      ceshi() {
        //声明为全局变量 
        let that=this;
         this.printtime=this.checkdate(new Date());
         setTimeout(()=>{
            try{ 
         var LODOP=getLodop(); 
          if (LODOP.VERSION) {
            LODOP.PRINT_INIT("");
            LODOP.SET_PRINT_STYLE("FontSize", 18);
            LODOP.SET_PRINT_STYLE("Bold", 1);
            LODOP.ADD_PRINT_HTM(20, 5, "100%", "100%", document.getElementById("form1").innerHTML);
            LODOP.PREVIEW();
          }else{
            that.$message({
                  showClose: true,
                  message: "打印控件未安装,请先联系管理员下载安装",
                  type: "warning"
                });
          }
        }catch(err){

        } 
         },500)
       

       
      },
      checkinput(res){
        let that=this;
        this.$confirm('检测到远程数据与本地填写资料不同,是否覆盖本地填写资料', '提示', {
                          confirmButtonText: '覆盖',
                          cancelButtonText: '不覆盖',
                          center:true,
                          type: 'warning'
                        }).then(() => {
                          that.ruleForm.vehicle_number=res.data.info.vehicle_number;
                          that.ruleForm.vehicle_vin = res.data.info.vehicle_vin;
                          that.ruleForm.brand_id = res.data.info.brand_id;
                          that.ruleForm.model = res.data.info.brand_model;
                          that.ruleForm.vehicle_model = res.data.info.vehicle_model;
                          that.ruleForm.vehicle_color = res.data.info.vehicle_color;
                          that.ruleForm.model_class = res.data.info.model_class;
                          that.ruleForm.vehicle_owner = res.data.info.vehicle_owner;
                          that.ruleForm.model_year = res.data.info.model_year;
                          that.ruleForm.contact_name = res.data.info.contact_name;
                          that.ruleForm.phone = res.data.info.phone;
                          that.ruleForm.insurance_time = res.data.info.insurance_time;
                          that.ruleForm.insurance_company = res.data.info.insurance_company;
                          that.ruleForm.buy_time = res.data.info.buy_time;
                           that.ruleForm.insurance_times = res.data.info.insurance_time*1000;
                          that.ruleForm.buy_times = res.data.info.buy_time*1000;;
                        }).catch(() => {
                          that.ruleForm.vehicle_number=that.ruleForm.vehicle_number==""?res.data.info.vehicle_number:that.ruleForm.vehicle_number;
                          that.ruleForm.vehicle_vin =that.ruleForm.vehicle_vin==""?res.data.info.vehicle_vin:that.ruleForm.vehicle_vin;
                          that.ruleForm.brand_id =  that.ruleForm.brand_id==""?res.data.info.brand_id:that.ruleForm.brand_id;
                          that.ruleForm.model = ruleForm.model==""?res.data.info.model:that.ruleForm.model;
                          that.ruleForm.vehicle_model = that.ruleForm.vehicle_model ==""?res.data.info.vehicle_model:that.ruleForm.vehicle_model;
                          that.ruleForm.vehicle_color = that.ruleForm.vehicle_color==""?res.data.info.vehicle_color:that.ruleForm.vehicle_color;
                          that.ruleForm.model_class = that.ruleForm.model_class==""?res.data.info.model_class:that.ruleForm.model_class;
                          that.ruleForm.vehicle_owner = that.ruleForm.vehicle_owner==""?res.data.info.vehicle_owner:that.ruleForm.vehicle_owner;
                          that.ruleForm.model_year = that.ruleForm.model_yea==""?res.data.info.model_year:that.ruleForm.model_year;
                          that.ruleForm.contact_name = that.ruleForm.contact_name==""?res.data.info.contact_name:that.ruleForm.contact_name;
                          that.ruleForm.insurance_time = that.ruleForm.insurance_time==""?res.data.info.phone:that.ruleForm.insurance_time;
                          that.ruleForm.buy_time = that.ruleForm.buy_time==""?res.data.info.buy_time:that.ruleForm.buy_time;
                          that.ruleForm.insurance_times= that.ruleForm.insurance_time==""?res.data.info.phone:that.ruleForm.insurance_time*1000;
                          that.ruleForm.buy_times = that.ruleForm.buy_time==""?res.data.info.buy_time:that.ruleForm.buy_time*1000;         
                          that.ruleForm.insurance_company = that.ruleForm.insurance_company==""?res.data.info.insurance_company:that.ruleForm.insurance_company; 
                        });

      },
      checkinputs(res){
        let that=this;
        that.ruleForm.vehicle_number=res.data.info.vehicle_number;
                          that.ruleForm.vehicle_vin = res.data.info.vehicle_vin;
                          that.ruleForm.brand_id = res.data.info.brand_id;
                          that.ruleForm.model = res.data.info.brand_model;
                          that.ruleForm.vehicle_model = res.data.info.vehicle_model;
                          that.ruleForm.vehicle_color = res.data.info.vehicle_color;
                          that.ruleForm.model_class = res.data.info.model_class;
                          that.ruleForm.vehicle_owner = res.data.info.vehicle_owner;
                          that.ruleForm.model_year = res.data.info.model_year;
                          that.ruleForm.contact_name = res.data.info.contact_name;
                          that.ruleForm.phone = res.data.info.phone;
                          that.ruleForm.insurance_time = res.data.info.insurance_time;
                          that.ruleForm.buy_time = res.data.info.buy_time;
                           that.ruleForm.insurance_times = res.data.info.insurance_time*1000;
                          that.ruleForm.buy_times = res.data.info.buy_time*1000;;
                          that.ruleForm.insurance_company = res.data.info.insurance_company;
                         
      },
      changinput(value,type) {
        let that=this;
        if(!value)return;
        clearTimeout(chepaitime);
        chepaitime = setTimeout(() => {
          Axios("order/searchbyvehiclenumber", {
            vn: value
          }).then(res => {
            if (res.data.info != null&&res.data.info!="") {
              this.timecheck = false;
              setTimeout(() => {
                that.timecheck = true;
              },100)
              // 这里做个判断
              
              if(type==1){
                   console.log(that.ruleForm.vehicle_vin!=""&&that.ruleForm.vehicle_vin!=res.data.info.vehicle_vin,that.ruleForm.brand_id!=""&&that.ruleForm.brand_id!=res.data.info.brand_id,
                  that.ruleForm.vehicle_model!=""&&that.ruleForm.vehicle_model!=res.data.info.vehicle_model,
                  that.ruleForm.vehicle_color!=""&&that.ruleForm.vehicle_color!=res.data.info.vehicle_color,
                  that.ruleForm.vehicle_owner!=""&&that.ruleForm.vehicle_owner!=res.data.vehicle_owner,that.ruleForm.model_year!=""&&that.ruleForm.model_year!=res.data.model_year,
                  that.ruleForm.contact_name!=""&&that.ruleForm.contact_name!=res.data.contact_name,that.ruleForm.phone!=""&&that.ruleForm.phone!=res.data.phone)
                  if(that.ruleForm.vehicle_vin!=""&&that.ruleForm.vehicle_vin!=res.data.info.vehicle_vin||that.ruleForm.brand_id!=""&&that.ruleForm.brand_id!=res.data.info.brand_id||
                  that.ruleForm.vehicle_model!=""&&that.ruleForm.vehicle_model!=res.data.info.vehicle_model||
                  that.ruleForm.vehicle_color!=""&&that.ruleForm.vehicle_color!=res.data.info.vehicle_color||
                  that.ruleForm.vehicle_owner!=""&&that.ruleForm.vehicle_owner!=res.data.info.vehicle_owner||that.ruleForm.model_year!=""&&that.ruleForm.model_year!=res.data.info.model_year||
                  that.ruleForm.contact_name!=""&&that.ruleForm.contact_name!=res.data.info.contact_name||that.ruleForm.phone!=""&&that.ruleForm.phone!=res.data.info.phone||
                  that.ruleForm.buy_time!=""&&that.ruleForm.buy_time!=res.data.info.buy_time|| that.ruleForm.insurance_company!=""&&that.ruleForm.insurance_company!=res.data.info.insurance_company||
                   that.ruleForm.insurance_time!=""&&that.ruleForm.insurance_time!=res.data.info.insurance_time
                  ){
                    that.checkinput(res);
                }else{
                    that.checkinputs(res)
                }
              }else if(type==2){
                if(that.ruleForm.vehicle_number!=""&&that.ruleForm.vehicle_number!=res.data.info.vehicle_number||that.ruleForm.brand_id!=""&&that.ruleForm.brand_id!=res.data.info.brand_id||
                !!that.ruleForm.vehicle_model&&that.ruleForm.vehicle_model!=res.data.info.vehicle_model||
                  that.ruleForm.vehicle_color!=""&&that.ruleForm.vehicle_color!=res.data.info.vehicle_color||
                  that.ruleForm.vehicle_owner!=""&&that.ruleForm.vehicle_owner!=res.data.info.vehicle_owner||that.ruleForm.model_year!=""&&that.ruleForm.model_year!=res.data.model_year||
                  that.ruleForm.contact_name!=""&&that.ruleForm.contact_name!=res.data.info.contact_name||that.ruleForm.phone!=""&&that.ruleForm.phone!=res.data.info.phone||
                  that.ruleForm.buy_time!=""&&that.ruleForm.buy_time!=res.data.info.buy_time|| that.ruleForm.insurance_company!=""&&that.ruleForm.insurance_company!=res.data.info.insurance_company||
                   that.ruleForm.insurance_time!=""&&that.ruleForm.insurance_time!=res.data.info.insurance_time){
                   that.checkinput(res);
                }else{
                    that.checkinputs(res)
                }
              }else if(type==3){
                if(that.ruleForm.vehicle_number!=""&&that.ruleForm.vehicle_number!=res.data.info.vehicle_number||that.ruleForm.vehicle_vin!=""&&that.ruleForm.vehicle_vin!=res.data.info.vehicle_vin||that.ruleForm.brand_id!=""&&that.ruleForm.brand_id!=res.data.info.brand_id||
                  that.ruleForm.vehicle_model!=""&&that.ruleForm.vehicle_model!=res.data.info.vehicle_model||
                  that.ruleForm.vehicle_color!=""&&that.ruleForm.vehicle_color!=res.data.info.vehicle_color||
                  that.ruleForm.vehicle_owner!=""&&that.ruleForm.vehicle_owner!=res.data.info.vehicle_owner||that.ruleForm.model_year!=""&&that.ruleForm.model_year!=res.data.info.model_year||
                  that.ruleForm.phone!=""&&that.ruleForm.phone!=res.data.info.phone||
                  that.ruleForm.buy_time!=""&&that.ruleForm.buy_time!=res.data.info.buy_time|| that.ruleForm.insurance_company!=""&&that.ruleForm.insurance_company!=res.data.info.insurance_company||
                   that.ruleForm.insurance_time!=""&&that.ruleForm.insurance_time!=res.data.info.insurance_time){
                       that.checkinput(res);
                }else{
                    that.checkinputs(res)
                }
              }
              
              

            }
          });
        }, 500)

      },
      itemcost(value) {
        this.dialog.parts.item_cost = value;
      },
      checkdate(time) {
        return parseTime(time);
      },
      gotobeizhu() {
        let routeData = this.$router.resolve({
          name: "remarkindex",
          query: {
            id: this.$route.query.id
          }
        });
        window.open(routeData.href, "_blank");
        // this.$router.push({name:"remarkindex",query:{id:this.$route.query.id}})
      },
      gotoaccount() {
        let routeData = this.$router.resolve({
          name: "settlementindex",
          query: {
            id: this.$route.query.id
          }
        });
      },
      updatestatus() {
        Axios("order/getopitemstype", {
          order_id: this.ruleForm.id
        }).then(res => {
          this.service_items = res.data.items;
        });
      },
      guwen() {
        let name = "";
        let index = this.consultantlist.findIndex(el => {
          return el.id == this.ruleForm.service_consultant;
        });
        if (index > -1) {
          return (name = this.consultantlist[index].value);
        } else {
          return (name = "未选择");
        }
      },
      abolish() {
        if (this.ruleForm.id == "") {
          this.$message({
            message: "请先完成开单操作",
            type: "warning"
          });
        } else {
          Axios("order/cancelorder", {
            order_id: this.ruleForm.id
          }).then(res => {
            this.$message({
              message: "订单已作废",
              type: "success"
            });
            this.ruleForm.order_status = 0;
            this.ruleForm.status = "作废";
          });
        }
      },
      brandlists() {
        let name = "";
        let index = this.brandlist.findIndex(el => {
          return el.id == this.ruleForm.brand_id;
        });
        if (index > -1) {
          return (name = this.brandlist[index].name);
        } else {
          return (name = "未选择");
        }
      },
      modellists() {
        let name = "";
        let index = this.modellist.findIndex(el => {
          return el.id == this.ruleForm.model_id;
        });
        if (index > -1) {
          return (name = this.modellist[index].name);
        } else {
          return (name = "未选择");
        }
      },
      saveotherinfo() {
        // 先判断是否开单了。
        if (!this.ruleForm.id) {
          this.$message({
            message: "请先完成开单操作",
            type: "warning"
          });
          return;
        } else {
          if (otherlist.indexOf(this.ruleForm.order_status) > -1) {
            this.$message({
              message: `${this.ruleForm.status}订单不能进行操作`,
              type: "warning"
            });
            return;
          }

          let times = 0,
            el = this.cost;
          // for(let i in )
          Axios("order/saveotherinfo", {
            order_id: this.ruleForm.id,
            total_times: el.hour,
            total_parts: el.materials,
            times_cost: el.hourcost,
            parts_cost: el.totalpartt,
            other_cost: el.othercost,
            total_cost: el.totlacost,
            next_advice: el.next_advice
          }).then(res => {
            this.$message({
              message: "订单进入调度环境",
              type: "success"
            });
          });
        }
      },
      totalhour() {
        let arr = 0;
        // datamaterials
        for (let i in this.dataitems) {
          arr += parseFloat(this.dataitems[i].item_times);
        }
        this.cost.hour = arr;
        return arr;
      },
      totalparts() {
        let arr = 0;
        for (let i in this.datamaterials) {
          arr += parseInt(this.datamaterials[i].amount);
        }
        this.cost.materials = arr;
        return arr;
      },
      totalpartts() {
        let arr = 0,
          that = this;
        for (let i in that.datamaterials) {
          if (!that.datamaterials[i].sell_price) {
            arr += 0;
          } else {
            arr += parseFloat(that.datamaterials[i].sell_price)*(that.datamaterials[i].amount);
          }
        }
        that.cost.totalpartts = arr;
        return arr;
      },
      totalpartt() {
        let arr = 0,
          that = this;
        for (let i in that.datamaterials) {
          if (!that.datamaterials[i].final_price) {
            arr += 0;
          } else {
            arr += parseFloat(that.datamaterials[i].final_price);
          }
        }
        that.cost.totalpartt = arr.toFixed(2);
        return arr.toFixed(2);
      },
      totaltime() {
        let arr = 0,
          that = this;
        if (that.dataitems.length > 0) {
          for (let i in that.dataitems) {
            if (!that.dataitems[i].final_price) {
              arr += 0;
            } else {
              arr += parseFloat(that.dataitems[i].final_price);
            }
          }
        }
        that.cost.hourcost = arr.toFixed(2);
        return arr.toFixed(2);
      },
      totaltimes() {
        let arr = 0,
          that = this;
        if (that.dataitems.length > 0) {
          for (let i in that.dataitems) {
            if (!that.dataitems[i].total_price) {
              arr += 0;
            } else {
              arr += parseFloat(that.dataitems[i].total_price);
            }
          }
        }
        that.cost.hourcosts = arr;
        return arr;
      },
      totlacost() {
        let arr = 0;
        let nums = isNaN(parseFloat(this.cost.othercost)) ? 0 : parseFloat(this.cost.othercost)
        arr = parseFloat(this.cost.hourcost) + nums + parseFloat(this.cost.totalpartt);
        this.cost.totlacost = parseFloat(arr.toFixed(2));
        return arr.toFixed(2);
      },
      deleteitem(index, scope) {
        scope.row.delete = false;
        Axios("order/deleteitem", {
          id: scope.row.id
        }).then(res => {
          this.$message({
            message: "删除维修信息成功",
            type: "success"
          });
          this.getitemlist();
        });
      },
      parrtsubmit() {
        let el = this.ruleForm,
          parts = this.dialog.parts;
        this.$refs.parts.validate(valid => {
          if (valid) {
            // 这里折扣不能为0
            if (this.dialog.parts.discount == 0) {
              this.$message({
                message: "折扣不能为0",
                type: "warning"
              });
              return;
            }
            if(this.dialog.parts.istype==0){
                this.$message({
                message: "材料类型异常,请重试",
                type: "warning"
              });
              return;
            }
            // 这里材料类型不能为0
            Axios("order/saveparts", {
              order_id: el.id,
              op_type: this.dialog.parts.istype,
              is_temporary: this.is_temporary,
              is_add: this.$route.query.isadd,
              parts_sn: this.dialog.parts.parts_sn,
              parts_name: this.is_temporary == 1 ?
                this.dialog.parts.itemname : this.dialog.parts.itemname,
              spec: parts.spec,
              unit: parts.unit,
              amount: parts.amount,
              sell_price: parseFloat(parts.item_cost),
              discount: parseFloat(parts.discount / 10),
            }).then(res => {
              this.dialog.parts.showkey = false;
              this.$message({
                message: `创建${
                this.is_temporary == 1 ? "临时" : ""
              }材料成功`,
                type: "success"
              });
              this.getpartlist();
              this.updatestatus();
            });
          }
        });
      },
      tuihuopart(index, scope) {
        scope.row.tuihuo = false;
        Axios("order/deletepart", {
          id: scope.row.id
        }).then(res => {
          this.$message({
            message: "零件退货成功",
            type: "success"
          });
          this.getpartlist();
        });
      },
      deletepart(index, scope) {
        scope.row.delete = false;
        Axios("order/deletepart", {
          id: scope.row.id
        }).then(res => {
          this.$message({
            message: "删除零件信息成功",
            type: "success"
          });
          this.getpartlist();
        });
      },
      remoteMethod(query) {
        return new Promise((resolve, reject) => {
          if (query && query != "") {
            this.loading = true;
            setTimeout(() => {
              Axios("common/searchaccountname", {
                name: query
              }).then(res => {
                let nelist = res.data.list;
                for (let i in res.data.list) {
                  nelist[i].value =
                    res.data.list[i].ding_department +
                    "  " +
                    res.data.list[i].username;
                  nelist[i].ids = res.data.list[i].id;
                }
                this.loading = false;
                this.consultantlist = nelist;
                resolve();
              });
            }, 200);
          } else {
            resolve();
            this.options4 = [];
          }
        });
      },
      remotechange(value) {},
      itemchange(value) {
        let index = this.alloptions.findIndex(el => {
          return el.id == value;
        });
        if (index > -1) {
          this.dialog.temporary.parts_time = this.alloptions[index].times;
          this.dialog.temporary.item_cost = this.alloptions[index].cost;
          this.dialog.temporary.itemname = this.alloptions[index].name;
          this.dialog.temporary.op_number = this.alloptions[index].op_number;
          this.dialog.temporary.id = this.alloptions[index].id;
        }
      },
      partschange(value) {
        let index = this.alloptionsd.findIndex(el => {
          return el.id == value;
        });
        if (index > -1) {
          this.dialog.parts.unit = this.alloptionsd[index].unit;
          this.dialog.parts.spec = this.alloptionsd[index].spec;
          this.dialog.parts.itemname = this.alloptionsd[index].parts_name + ' ' + this.alloptionsd[index].spec + ' ' +
            this.alloptionsd[index].target_model
          this.dialog.parts.parts_sn = this.alloptionsd[index].sn;
          this.dialog.parts.item_cost = this.alloptionsd[index].sell_price;
        }
      },
      typechange(value) {
       
        if (this.checkindex == 1) {
          this.dialog.temporary.itemname = "";
          if (this.is_temporary != 1) {
            //非临时项目 请求远端数据
            Axios("common/getAllOpItems", {
              class: this.ruleForm.model_class,
              op_type: 0
            }).then(res => {
              this.alloptions = res.data.list;
            });
          }
        } else {
           return;
          this.dialog.parts.itemname = "";
          if (this.is_temporary != 1) {
            Axios("common/getallparts", {
              op_type: 0
            }).then(res => {
              this.alloptionsd = res.data.list;
            });
          }
        }
      },
      querySearchAsync(queryString, cb) {
        if (queryString == undefined || queryString == "") return;
        Axios("common/searchaccountname", {
          name: queryString
        }).then(res => {
          let nelist = res.data.list;
          for (let i in res.data.list) {
            nelist[i].value =
              res.data.list[i].ding_department + "  " + res.data.list[i].username;
          }
          var restaurants = nelist;
          var results = queryString ?
            restaurants.filter(this.createStateFilter(queryString)) :
            restaurants;
          clearTimeout(this.timeout);
          this.consultantinfo = results;
          this.timeout = setTimeout(() => {
            cb(results);
          }, 100);
        });
      },
      handleitemname(value) {
        let index = this.querylist.findIndex(el => {
          if (this.checkindex == 1) {
            return el.id == value;
          } else {
            return el.sn == value;
          }
        });
        this.chooselist = this.querylist[index];
        if (this.checkindex == 1) {
          this.dialog.temporary.item_cost = this.chooselist.cost;
          this.dialog.temporary.parts_time = this.chooselist.tiems;
        } else {
          this.dialog.parts.unit = this.chooselist.unit;
          this.dialog.parts.spec = this.chooselist.spec;
          this.dialog.parts.item_cost = this.chooselist.sell_price;
        }
      },
      querypartname(query) {
        let type =
          this.checkindex == 1 ?
          this.dialog.temporary.istype :
          this.dialog.parts.istype;
        if (type == "") {
          this.$message({
            showClose: true,
            message: `请先填写${this.checkindex == 1 ? "项目类别" : "材料类别"}`,
            type: "warning"
          }).then(res => {});
        } else {
          this.loadingd = true;
          Axios("parts/searchpartslist", {
            //  class: this.ruleForm.model_class,
            query: query,
            op_type: type
          }).then(res => {
            this.loadingd = false;
            this.querylist = res.data.list;
          });
        }
      },
      queryitemname(query) {
        let type =
          this.checkindex == 1 ?
          this.dialog.temporary.istype :
          this.dialog.parts.istype;
        if (type == "") {
          this.$message({
            showClose: true,
            message: `请先填写${this.checkindex == 1 ? "项目类别" : "材料类别"}`,
            type: "warning"
          }).then(res => {});
        } else {
          this.loadingd = true;
          Axios("items/getitemlist", {
            class: this.ruleForm.model_class,
            query: query,
            op_type: type
          }).then(res => {
            this.loadingd = false;
            this.querylist = res.data.list;
          });
        }
        // 这里暂存 防止后面需求改变
        // if (queryString == undefined || queryString == ""){

        // }

        // if (query !== '') {
        //     this.loadingd = true;
        //       this.loadingd = false;
        //       this.options4 = this.list.filter(item => {
        //         return item.label.toLowerCase()
        //           .indexOf(query.toLowerCase()) > -1;
        //       });
        //   } else {
        //     this.options4 = [];
        //   }
        // return;
        // if (this.dialog.temporary.istype == "") {
        //   cb([{ value: "请先填写业务类别" }]);
        //   return;
        // }
        // if (queryString == undefined || queryString == "") return;
        // Axios("items/getitemlist", {
        //   class: this.ruleForm.model_class,
        //   query: queryString,
        //   op_type: this.ruleDom.type
        // }).then(res => {
        //   if (res.data.list.length == 0) {
        //     this.itemlist = res.data.list;
        //     cb();
        //     return;
        //   }
        //   let nelist = res.data.list;
        //   for (let i in res.data.list) {
        //     nelist[i].value = res.data.list[i].item_name;
        //   }
        //   var restaurants = nelist;
        //   var results = queryString
        //     ? restaurants.filter(this.createStateFilter(queryString))
        //     : restaurants;
        //   clearTimeout(this.timeout);
        //   this.consultantinfo = results;
        //   cb(results);
        // });
      },
      createStateFilter(queryString) {
        return state => {
          return (
            state.value.toLowerCase().indexOf(queryString.toLowerCase()) > -1
          );
        };
      },
      handleSelect() {},
      print(type) {
        this.printnum = type;
        if (type == 2) {
          if (this.dataitems.length == 0) {
            this.$message({
              showClose: true,
              message: "表单数据为空,请先填写表单数据",
              type: "warning"
            });
            return;
          }
        }
        if (type == 3) {
          if (this.datamaterials.length == 0) {
            this.$message({
              showClose: true,
              message: "表单数据为空,请先填写表单数据",
              type: "warning"
            });
            return;
          }
        }
        setTimeout(() => {
          window.print();
        }, 500);
      },
      dialogcheck(type) {
        let that = this;
        if (type) {
          // 这里先进行校验处理
          this.$refs.ruleDom.validate(valid => {
            if (valid) {
              // 判断品牌车型请
              that.dialog.add.showkey = false;
              setTimeout(() => {
                // 这里判断下
                if (that.checkindex == 1) {
                  // 项目
                  that.dialog.temporary.item_name = that.ruleDom.itemname;
                  that.dialog.temporary.showkey = true;
                  that.checkindex = type;
                  that.dialog.temporary.title =
                    type == 1 ? "添加临时项目" : "添加临时材料";
                } else {
                  that.dialog.parts.parts_name = that.ruleDom.itemname;
                  that.dialog.parts.showkey = true;
                }
              }, 500);
            } else {
              return false;
            }
          });
        } else {
          this.dialog.add.showkey = false;
        }
      },
      querySearch(queryString, cb) {
        var restaurants = this.vehicle;
        var results = queryString ?
          restaurants.filter(this.createFilter(queryString)) :
          restaurants;
        cb(results);
      },
      createFilter(queryString) {
        return vehicle => {
          return (
            vehicle.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0
          );
        };
      },
      onSubmit(value) {
        if (otherlist.indexOf(this.ruleForm.order_status) > -1) {
          this.$message({
            message: `${this.ruleForm.status}订单不能进行操作`,
            type: "warning"
          });
          return;
        }
        this.$refs.ruleForm.validate(valid => {
          if (valid) {
            // 判断品牌车型
            let ele = this.ruleForm,
              imglist = "";
            for (let i in this.checkimages) {
              imglist += this.checkimages[i].url + ",";
            }
            ele.check_images = imglist.replace(/,$/, "");
            ele.buy_time = ele.buy_times / 1000;
            ele.insurance_time = ele.insurance_times / 1000;
            ele.plan_endtime = ele.plan_endtimes / 1000;
            ele.arrive_time = ele.arrive_times / 1000;
            ele.next_maintenance_time = ele.next_maintenance_times / 1000;
            Axios("order/saveorder", ele).then(res => {
              for (let i in res.data.list) {
                this.ruleForm[i] = res.data.list[i];
              }
              this.ruleForm.key = true;
              this.$message({
                message: `${
                !this.ruleForm.key ? "开单" : "保存订单"
              }成功,请执行余下操作`,
                type: "success"
              });
              this.ruleForm.id = res.data.order_id;
              this.ruleForm.order_sn = res.data.order_sn;
              this.ruleForm.key = true;
              this.$router.push({
                name: "orderdetails",
                query: {
                  id: res.data.order_id
                }
              });
              this.getmounted();
            });
          } else {
            document.documentElement.scrollTop = 0;
            this.$message.error("表单验证错误,请检查");
            return false;
          }
        });
      },
      formatTooltip(val) {
        return val / 100;
      },
       onecode(){
          LODOP=getLodop(document.getElementById('LODOP1'),document.getElementById('LODOP_EM1')); 
            LODOP.ADD_PRINT_BARCODE(40,200,400,47,"EAN128B",this.ruleForm.order_sn);
	        	LODOP.SET_PRINT_STYLEA(0,"AlignJustify",2);
            LODOP.PRINT_DESIGN();		
        },
      hadnleSuccess(res) {
        this.checkimages.push(res.data.url);
      },
      handleRemove(file, fileList) {
        let index = this.checkimages.findIndex((el) => {
          return el.url == file.url
        })
        this.checkimages.splice(index, 1)
      },
      handlePreview(file) {},
      checkdiscount(type) {
        // type 1 设置项目折扣 type 2设置材料折扣
        this.dialog.discount.value = ""
        if (type == 1) {
          if (this.dataitemslist.length == 0) {
            this.$message({
              message: "请先勾选项目",
              type: "warning"
            });
            return;
          }
        } else {
          if (this.datamaterialslist.length == 0) {
            this.$message({
              message: "请先勾选材料",
              type: "warning"
            });
            return;
          }
        }
        this.dialog.discount.showkey = true;
        this.checkindex = type;
        this.dialog.discount.title = type == 1 ? "设置项目折扣" : "设置材料折扣";
      },
      dialogdiscount() {
        //这里折扣不能为0
        if (this.dialog.discount.discount <= 0 || this.dialog.discount.discount > 10) {
          this.$message({
            message: "折扣应在0-10之间",
            type: "warning"
          });
          return;
        }
        if (this.checkindex == 1) {
          //设置项目折扣
          let list = [];
          for (let i in this.dataitemslist) {
            list.push({
              id: this.dataitemslist[i].id,
              discount: this.dataitemslist[i].discount,
              item_cost: this.dataitemslist[i].item_cost,
              item_times: this.dataitemslist[i].item_times
            });
          }
          //统一设置折扣
          for (let i in list) {
            console.log(list[i])
            list[i].final_price =
              parseFloat(list[i].item_cost) *
              list[i].item_times *
              parseFloat(this.dialog.discount.discount) /
              10;
            list[i].discount = parseFloat(this.dialog.discount.discount) / 10;
          }
          Axios("order/batchdiscountitem", {
            batch: list
          }).then(res => {
            this.dialog.discount.showkey = false;
            this.$message({
              message: "项目设置折扣成功",
              type: "success"
            });
            this.getitemlist();
          });
        } else {
          //设置材料折扣
          let list = [];
          for (let i in this.datamaterialslist) {
            list.push({
              id: this.datamaterialslist[i].id,
              discount: this.datamaterialslist[i].discount,
              amount: this.datamaterialslist[i].amount,
              sell_price: this.datamaterialslist[i].sell_price
            });
          }
          //统一设置折扣
          for (let i in list) {
            console.log(list[i])
            list[i].final_price =
              parseFloat(list[i].amount) *
              list[i].sell_price *
              parseFloat(this.dialog.discount.discount) /
              10;
            list[i].discount = parseFloat(this.dialog.discount.discount) / 10;
          }
          Axios("order/batchdiscountpart", {
            batch: list
          }).then(res => {
            this.dialog.discount.showkey = false;
            this.$message({
              message: "材料设置折扣成功",
              type: "success"
            });
            this.getpartlist();
          });
        }
      },
      dialogtemporary(type) {
        let el = this.ruleForm;
        this.$refs.temporarys.validate(valid => {
          if (valid) {
            // 这里再判断下 折扣折扣不能为0
            if (this.dialog.temporary.discount == 0) {
              this.$message({
                message: "折扣不能为0",
                type: "warning"
              });
              return;
            }
            //这单独区分下项目和材料
            if (this.checkindex == 1) {
              Axios("order/saveitem", {
                order_id: el.id,
                is_temporary: this.is_temporary,
                op_number: this.dialog.temporary.op_number,
                item_name: this.is_temporary != 1 ?
                  this.dialog.temporary.itemname : this.dialog.temporary.itemname,
                op_type: this.dialog.temporary.istype,
                item_times: this.dialog.temporary.parts_time,
                item_cost: this.dialog.temporary.item_cost,
                discount: this.dialog.temporary.discount / 10,

              }).then(res => {
                this.dialog.temporary.showkey = false;
                this.$message({
                  message: `添加${this.is_temporary == 1 ? "临时" : ""}项目成功`,
                  type: "success"
                });
                this.getitemlist();
                this.updatestatus();
              });
            }
          } else {
            return false;
          }
        });
      },
      getitemlist() {
        if (this.ruleForm.id) {
          Axios("order/getitemlist", {
            order_id: this.ruleForm.id
          }).then(res => {
            let list = res.data.list;
            for (let i in list) {
              list[i].delete = false;
              list[i].tuihuo = false;
              list[i].check = 1;
            }
            this.dataitems = list;
          });
        }
      },
      getpartlist() {
        if (this.ruleForm.id) {
          Axios("order/getpartlist", {
            order_id: this.ruleForm.id
          }).then(res => {
            let list = res.data.list;
            for (let i in list) {
              list[i].delete = false;
              list[i].tuihuo = false;
              list[i].check = 2;
            }
            this.datamaterials = list;
          });
        }
      },
      handleSelectionChange(val) {
        this.dataitemslist = val;
      },
      handleSelectionChanged(val) {
        this.datamaterialslist = val;
      },
      checktemporary(type, title) {
        let that = this;
        // 先判断接车已经创建了。
        if (!this.ruleForm.key) {
          this.$message({
            message: "请先完成开单操作",
            type: "warning"
          });
          document.documentElement.scrollTop = 0;
          return;
        }
        if (otherlist.indexOf(this.ruleForm.order_status) > -1) {
          this.$message({
            message: `${this.ruleForm.status}订单不能进行操作`,
            type: "warning"
          });
          return;
        }
        if (type == 1) {
          this.dialog.temporary.showkey = true;
        } else {
          that.dialog.parts.showkey = true;
        }
        // this.dialog.add.showkey = true;
        this.$nextTick(() => {
          if (type == 1) {
            that.$refs.temporarys.resetFields();
          } else {
            that.$refs.parts.resetFields();
          }
          // this.$refs.ruleDom.resetFields();
        });
        this.checkindex = type;
        this.dialog.add.title = title;
        this.is_temporary = 1;
        // this.dialog.temporary.showkey = true;
        // this.checkindex = type;
        // this.dialog.temporary.title = type == 1 ? "添加临时项目" : "添加临时材料";
      },
      guwenchange(value) {},
      handleExceed(files, fileList) {
        this.$message.warning(`当前限制选择10 个文件,请删除后再上传`);
      },
      handlePictureCardPreview(file) {
        this.dialogImageUrl = file.url;
        this.dialogVisible = true;
      },
      checkitem(type) {
        // 先判断接车已经创建了。
        let that = this;
        if (!this.ruleForm.key) {
          this.$message({
            message: "请先完成开单操作",
            type: "warning"
          });
          document.documentElement.scrollTop = 0;
          return;
        }
        if (otherlist.indexOf(this.ruleForm.order_status) > -1) {
          this.$message({
            message: `${this.ruleForm.status}订单不能进行操作`,
            type: "warning"
          });
          return;
        }
        if (type == 1) {
          this.dialog.temporary.showkey = true;
        } else {
          that.dialog.parts.showkey = true;
        }
        this.$nextTick(() => {
          if (type == 1) {
            that.$refs.temporarys.resetFields();
          } else {
            that.$refs.parts.resetFields();
          }
          // this.$refs.ruleDom.resetFields();
        });
        this.is_temporary = 0;
        this.checkindex = type;
        this.dialog.add.title = type == 1 ? "添加项目" : "添加材料";
      },
      handleDelete(index, ele, type) {
        if (ele.check == 0) {
          this.dataitems[index].delete = false;
        } else {
          this.datamaterials[index].delete = false;
        }
        if (!type) return;
        if (ele.check == 0) {
          this.dataitems.splice(index, 1);
          this.dataitems[index].delete = false;
        } else {
          this.datamaterials.splice(index, 1);
          this.datamaterials[index].delete = false;
        }
      },
      costchange(e) {},
      getimg(value, file, fileList) {
         let index = this.checkimages.findIndex((el) => {
            return el.url == value.data.url;
          })
          if (index == -1) {
            this.checkimages.push({
              name: "zone",
              url: value.data.url
            });
          } else {
            this.$message({
              message: "请勿重复上传图片",
              type: "warning"
            });
          }
    
      },
      beforeupload() {},

      getmounted() {
        let that = this;
        Axios("models/getbrandlist").then(res => {
          this.brandlist = res.data.list;
        });
        //获取业务类型
        Axios("common/getoptypelist").then(res => {
          this.dialog.temporary.classes = res.data.list;
        });
        // 这里先全部抓取材料和
        // Axios("common/getAllOpItems",{
        //   op_type:1
        // }).then(res=>{
        //    this.alloptions=res.data.list;
        // })
        this.ruleForm.id = this.$route.query.id;
        this.getitemlist();
        this.getpartlist();
        if (this.$route.query.id > -1) {
          Axios("common/ordernotelist", {
            order_id: this.$route.query.id
          }).then(res => {
            this.info = res.data.list.length > 0 ? res.data.list[0] : {};
          });
          this.loadings = true;
          Axios("order/vieworder", {
            order_id: this.$route.query.id
          }).then(res => {
            // 这里对于拿到的数据进行一下加工
            this.cost.next_advice = res.data.info.next_advice;
            let checkimages = [];
            for (let i in res.data.info.check_images) {
              checkimages.push({
                name: "zone",
                url: res.data.info.check_images[i]
              })
            }
            this.checkimages = checkimages;

            Axios("common/searchaccountname", {
              name: res.data.info.service_consultant_name
            }).then(res => {
              let nelist = res.data.list;
              for (let i in res.data.list) {
                nelist[i].value =
                  res.data.list[i].ding_department +
                  "  " +
                  res.data.list[i].username;
                nelist[i].ids = res.data.list[i].id;
              }
              this.loading = false;
              this.consultantlist = nelist;
            });
            let list = res.data.info;
            list.insurance_times = list.insurance_time>0?list.insurance_time * 1000:"";
            list.buy_times = list.buy_time>0?list.buy_time * 1000:"";
            list.service_consultant = list.service_consultant;
            list.arrive_times = list.arrive_time>0?list.arrive_time * 1000:"";
            list.plan_endtimes = list.plan_endtime>0?list.plan_endtime * 1000:"";
            list.model = list.brand_model;
            this.cost.othercost = isNaN(parseFloat(res.data.info.other_cost)) ? 0 : parseFloat(res.data.info.other_cost);
            this.ruleForm = list;
            this.ruleForm.key = true;
            this.service_items = res.data.service_items;
            this.loadings = false;
            setTimeout(() => {
              this.timecheck = true;
            }, 700);
          });
        } else {
          this.remoteMethod(getCookie("username")).then(res => {
            that.ruleForm.service_consultant = (getCookie("uid"));
          });

          setTimeout(() => {
            this.timecheck = true;
          }, 700);
        }
      },
      // getSummaries(param){
      //   console.log(param)
      // },
      getSummaries(param) {
        const {
          columns,
          data
        } = param;
        if (data.length == 0) {
          return ["合计", "", "", "", "", "", ""];
        }
        if (data.length > 0) {
          const numarray =
            data[0].check == 1 ? [0, 1, 2, 3, 5, 7] : [0, 1, 2, 3, 4, 5, 6, 7, 10];
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
              sums[index] = parseFloat(sums[index].toFixed(2));
              sums[index] += index != 4 ? " 元" : " 小时";
            } else {
              sums[index] = "";
            }
          });

          return sums;
        }
      }
    },
    watch: {
      poker(newvalue, oldvalue) {
        if (newvalue.length == 0) return;
        Axios("models/getvehiclelist", {
          brand_id: this.ruleForm.brand_id
        }).then(res => {
          if (this.timecheck) {
            this.ruleForm.model_id = "";
            this.ruleForm.model_year = "";
          }
          this.modellist = res.data.list;
        });
      },
      current_km() {
        this.ruleForm.next_maintenance_km =
          parseInt(this.ruleForm.current_km) +
          parseInt(this.ruleForm.maintenance_km);
      },
      maintenance_km() {
        this.ruleForm.next_maintenance_km =
          parseInt(this.ruleForm.current_km) +
          parseInt(this.ruleForm.maintenance_km);
      },
      maintenance_cycle() {
        if (new Date(this.ruleForm.arrive_times).getTime() > 0) {
          this.ruleForm.next_maintenance_times =
            new Date(this.ruleForm.arrive_times).getTime() +
            parseInt(this.ruleForm.maintenance_cycle) * 24 * 3600 * 1000;
        }
      },
      plan_endtimes(newvalue) {
    
        // this.ruleForm.plan_endtimes =
        //   new Date(this.ruleForm.arrive_times).getTime() +
        //   parseInt(this.ruleForm.maintenance_cycle) * 24 * 3600 * 1000;
        // this.ruleForm.plan_endtimes = newvalue;
      },

      counselor(newvalue, oldvalue) {
        if (!this.consultantlist || this.consultantlist.length == 0) return;
        let index = this.consultantlist.findIndex(res => {
          return res.id == newvalue;
        });
        this.ruleForm.consultant_phone = this.consultantlist[index].phone;
        // this.ruleForm.service_consultant = this.consultantinfo[index].id;
      },
      modelid(newvalue, oldvalue) {
        let index = this.vehicle.findIndex(el => {
          return (el.id = newvalue);
        });
        if (index < 0) return;
        this.ruleForm.model_year = this.vehicle[index].year;
      },
      vehicles(newvalue, oldvalue) {
        // 改为input框输入 暂时不需要了
        return;
        if (this.timecheck) {
          this.ruleForm.vehicle_model_id = "";
          this.ruleForm.model_year = "";
        }

        if (this.ruleForm.model_id == "") return;
        Axios("models/getvehiclemodellist", {
          brand_id: this.ruleForm.brand_id,
          model_id: this.ruleForm.model_id
        }).then(res => {
          let newlist = res.data.list;
          for (let i in newlist) {
            newlist[i].value = res.data.list[i].name;
            newlist[i].ids = res.data.list[i].id;
          }
          this.vehicle = newlist;
        });
      },
      totlacostd(newvalue, oldvalue) {
        let that = this,
          el = this.cost;
        if (!this.ruleForm) {
          return;
        } else {
          if (otherlist.indexOf(this.ruleForm.order_status) > -1) {
            return;
          }
        }
      },
      endtimed(newvalue, oldvalue) {
        this.ruleForm.endtimes = this.ruleForm.plan_endtimes;

      },
    },
    computed: {
      discountof() {
        let constd = this.dialog.temporary.item_cost,
          discount = this.dialog.temporary.discount;
        let value = (parseFloat(constd || 0) * parseFloat(discount || 0) / 10).toFixed(2);
        return isNaN(value) ? "" : value + "元";
      },
      shouldvalue() {
        let constd = this.dialog.temporary.item_cost,
          discount = this.dialog.temporary.parts_time;
        let value = parseFloat(constd || 0) * parseFloat(discount || 0).toFixed(2);
        return isNaN(value) ? "" : value + "元";
      },
      discountvalue() {
        let cost = this.dialog.temporary.item_cost,
          time = this.dialog.temporary.parts_time,
          discount = this.dialog.temporary.discount;
        let value =
          (parseFloat(cost || 0) *
            parseFloat(time || 0) *
            parseFloat(discount || 0) /
            10).toFixed(2);
        return isNaN(value) ? "" : value + "元";
      },
      discountofs() {
        let constd = this.dialog.parts.item_cost,
          discount = this.dialog.parts.discount;
        let value = parseFloat(constd || 0) * parseFloat(discount || 0) / 10;
        return isNaN(value) ? "" : value + "元";
      },
      shouldvalues() {
        let constd = this.dialog.parts.item_cost,
          discount = parseFloat(this.dialog.parts.discount / 10);
        let value = parseFloat(constd || 0) * parseFloat(discount || 0).toFixed(2);
        return isNaN(value) ? "" : value + "元";
      },
      discountvalues() {
        let cost = this.dialog.parts.item_cost,
          time = this.dialog.parts.amount,
          discount = this.dialog.parts.discount;
        let value = (
          parseFloat(cost || 0) *
          parseFloat(time || 0) *
          parseFloat(discount || 0) /
          10).toFixed(2);
        return isNaN(value) ? "" : value + "元";
      },
      poker() {
        return this.ruleForm.brand_id;
      },
      totlacostd() {
        return this.cost.totlacost;
      },
      current_km() {
        return this.ruleForm.current_km;
      },
      maintenance_cycle() {
        return this.ruleForm.maintenance_cycle;
      },
      plan_endtimes() {
        return this.ruleForm.arrive_times;
      },
      maintenance_km() {
        return this.ruleForm.maintenance_km;
      },
      vehicles() {
        return this.ruleForm.model_id;
      },
      counselor() {
        return this.ruleForm.service_consultant;
      },
      modelid() {
        return this.ruleForm.vehicle_model_id;
      },
      compymodelid() {
        let index;
        for (let i in this.modellist) {
          if (this.modellist[i].id == this.ruleForm.model_id) {
            index = i;
          }
        }
        return this.modellist[i].name;
      }
    },
    mounted() {
      this.getmounted();
      Axios("common/getallparts", {
              op_type: 0
            }).then(res => {
              this.alloptionsd = res.data.list;
            });
    },
    data() {
      var validatePass2 = (rule, value, callback) => {
        if (this.ruleForm.service_consultant == "") {
          callback(new Error("请输入顾问姓名"));
        } else {
          callback();
        }
      };
      var validiscount = (rule, value, callback) => {
        if (isNaN(value)) {
          callback(new Error("请输入数字"));
        }
        if (parseFloat(value) <= 0 || parseFloat(value) > 10) {
          callback(new Error("折扣应在0-10之间"));
        } else {
          callback();
        }
      }
      var itemnames = (rule, value, callback) => {
        // 判断是否临时项目
        if (this.checkindex == 1) {
          if (this.dialog.temporary.itemname == "") {
            callback(new Error("请输入项目名称"));
          } else {
            callback();
          }
        } else {
          if (this.dialog.parts.itemname == "") {
            callback(new Error("请输入项目名称"));
          } else {
            callback();
          }
        }
      };
      return {
        printtime:"",
        info: {},
        printnum: -1,
        timecheck: false,
        loadingd: false,
        alloptions: [], // 项目
        alloptionsd: [], //材料
        querylist: [],
        dialogVisible: false,
        dialogImageUrl: "",
        loadings: false,
        loading: false,
        is_temporary: "",
        consultantlist: [],
        dataitemslist: [],
        service_items: [],
        datamaterialslist: [],
        infomodel: [],
        carlevel: [{
            id: "A0",
            value: "A0"
          },
          {
            id: "A",
            value: "A"
          },
          {
            id: "B",
            value: "B"
          },
          {
            id: "C",
            value: "C"
          },
          {
            id: "D",
            value: "D"
          }
        ],
        temprule: {
          parts_time: [{
            required: true,
            message: "请输入项目工时",
            trigger: "blur"
          }],
          item_cost: [{
            required: true,
            message: "请输入单价",
            trigger: "blur"
          }],
          itemname: [{
            required: true,
            validator: itemnames,
            trigger: "blur"
          }],
          istype: [{
            required: true,
            message: "请选择业务类型",
            trigger: "change"
          }],
          discount: [{
            required: true,
            validator: validiscount,
            trigger: "blur"
          }]

        },
        itemrules: {},
        carlist: [{
            id: "A0",
            value: "A0"
          },
          {
            id: "A",
            value: "A"
          },
          {
            id: "B",
            value: "B"
          },
          {
            id: "C",
            value: "C"
          },
          {
            id: "D",
            value: "D"
          }
        ],
        ruleDom: {
          type: "",
          carclass: "AO",
          itemname: ""
        },
        checkimages: [],
        value3: 1,
        checkindex: 0,
        brandlist: [],
        vehicle: [],
        modellist: [],
        dialog: {
          add: {
            showkey: false,
            type: "",
            names: "",
            category: [{
                name: "预期1",
                value: "1"
              },
              {
                name: "预期2",
                value: "2"
              }
            ],
            name: [{
              name: "预期1",
              value: "1"
            }, {
              name: "预期2",
              value: "2"
            }]
          },
          temporary: {
            showkey: false,
            itemname: "",
            parts_time: "",
            istype: "",
            type: "",
            names: "",
            carclass: "A0",
            id: "",
            specification: "",
            unit: "",
            num: "",
            item_cost: "",
            unitprice: "",
            discount: 10,
            amount: "",
            receivable: "",
            breakprice: "",
            classes: [],
            classesid: ""
          },
          parts: {
            showkey: false,
            order_id: "",
            itemname: "",
            item_cost: "",
            amount: "",
            istype: "",
            is_temporary: "",
            parts_sn: "",
            parts_name: "",
            spec: "",
            unit: "",
            amount: "",
            sell_price: "",
            discount: 10,
            final_price: ""
          },
          discount: {
            title: "",
            showkey: false,
            value: 0
          }
        },
        options: [],
        itemlist: [],
        dialogs: {
          discount: [{
            required: true,
            validator: validiscount,
            trigger: "blur"
          }]
        },
        patts: {
          spec: [{
            required: true,
            message: "请输入规格",
            trigger: "blur"
          }],
          itemname: [{
            required: true,
            message: "请输入项目名称",
            trigger: "blur"
          }],
          unit: [{
            required: true,
            message: "请输入单位",
            trigger: "blur"
          }],
          amount: [{
            required: true,
            message: "请填写数量",
            trigger: "blur"
          }],
          item_cost: [{
            required: true,
            message: "请填写单价",
            trigger: "blur"
          }],
          istype: [{
            required: true,
            message: "请选择业务类型",
            trigger: "change"
          }],
          itemname: [{
            required: true,
            message: "请填写材料名称",
            trigger: "blur"
          }],
          discount: [{
            required: true,
            validator: validiscount,
            trigger: "blur"
          }]
        },
        rules: {
          vehicle_number: [{
            required: true,
            message: "请选择车牌号",
            trigger: "blur"
          }],
          vehicle_vin: [{
            required: true,
            message: "请输入VIN码",
            trigger: "blur"
          }],
          brand_id: [{
            required: true,
            message: "请输入VIN码",
            trigger: "blur"
          }],
          model: [{
            required: true,
            message: "请输入车型",
            trigger: "blur"
          }],
          vehicle_model: [{
            required: true,
            message: "请输入具体型号",
            trigger: "blur"
          }],
          model_year: [{
            required: true,
            message: "请选择年份",
            trigger: "blur"
          }],
          vehicle_color: [{
            required: true,
            message: "请输入颜色",
            trigger: "blur"
          }],
          model_class: [{
            required: true,
            message: "请输入具体型号",
            trigger: "blur"
          }],
          buy_times: [{
            required: true,
            message: "请选择购车时间",
            trigger: "blur"
          }],
          insurance_times: [{
            required: true,
            message: "请选择保险日期",
            trigger: "blur"
          }],
          insurance_company: [{
            required: true,
            message: "请输入保险公司",
            trigger: "blur"
          }],
          vehicle_owner: [{
            required: true,
            message: "请输入车主名称",
            trigger: "blur"
          }],
          contact_name: [{
            required: true,
            message: "请输入联系人姓名",
            trigger: "blur"
          }],
          current_km: [{
            required: true,
            message: "请输入本次里程",
            trigger: "blur"
          }],
          phone: [{
            required: true,
            message: "请输入电话号码",
            trigger: "blur"
          }],
          customer_source: [{
            required: true,
            message: "请输入客户来源",
            trigger: "blur"
          }],
          introducer: [{
            required: true,
            message: "请输入胡介绍人姓名",
            trigger: "blur"
          }],
          introducer_phone: [{
            required: true,
            message: "请输入介绍人电话",
            trigger: "blur"
          }],
          current_km: [{
            required: true,
            message: "里程数为空",
            trigger: "blur"
          }],
          arrive_times: [{
            required: true,
            message: "请选择到店时间",
            trigger: "blur"
          }],
          plan_endtimes: [{
            required: true,
            message: "请选择预计完工时间",
            trigger: "blur"
          }],
          service_consultant: [{
            required: true,
            validator: validatePass2,
            trigger: "blur"
          }],
          consultant_phone: [{
            required: true,
            message: "请输入顾问手机号",
            trigger: "blur"
          }],
          maintenance_km: [{
            required: true,
            message: "保养里程数为空",
            trigger: "blur"
          }],
          maintenance_cycle: [{
            required: true,
            message: "保养里程周期数为空",
            trigger: "blur"
          }],
          next_maintenance_km: [{
            required: true,
            message: "请输入保养里程数",
            trigger: "blur"
          }],
          next_maintenance_times: [{
            required: true,
            message: "请输入保养时间",
            trigger: "blur"
          }],
          fault_intro: [{
            required: true,
            message: "请输入故障描述",
            trigger: "blur"
          }],
          need_wash: [{
            required: true
          }],
          need_maintenance: [{
            required: true
          }]
        },
        dataitems: [],
        datamaterials: [],
        chooselist: {},
        multipleSelection: [],
        value4: "",
        input1: "",
        input2: "",
        value12: "",
        radio2: 3,
        value5: "不需要",
        items: {
          id: ""
        },
        feedback: {
          maintain: 1,
          environment: 2,
          manner: 3,
          rest: 5
        },
        cost: {
          hour: 0,
          materials: 0,
          hourcost: 0,
          othercost: 0,
          totlacost: 0,
          totalpartt: 0,
          next_advice: ""
        },
        checkimages: [],
        materials: {},
        ruleForm: {
          key: false,
          order_id: "",
          order_sn: "",
          pay_order_sn: "未创建",
          wait_number: 2,
          consultantinfo: "",
          id: "",
          is_temporary: "",
          vehicle_number: "",
          vehicle_vin: "",
          brand_id: "",
          model_id: "",
          vehicle_model:"",
          vehicle_model_id: "",
          model_year: "",
          vehicle_color: "",
          model_class: "A0",
          buy_time: "",
          buy_times: "",
          insurance_time: "",
          insurance_times: "",
          insurance_company: "",
          vehicle_owner: "",
          contact_name: "",
          phone: "",
          customer_source: "",
          introducer: "",
          introducer_phone: "",
          current_km: "",
          arrive_time: "",
          arrive_times: "",
          plan_endtime: "",
          plan_endtimes: "",
          service_consultant: "",
          consultant_phone: "",
          need_wash: "",
          need_maintenance: "",
          maintenance_km: "5000",
          maintenance_cycle: "30",
          next_maintenance_km: "",
          next_maintenance_time: "",
          next_maintenance_times: "",
          fault_intro: "",
          check_images: []
        }
      };
    }
  };

</script>

<style lang="scss">
  .zoneel {
    height: 62px;
  }

  .print-ct {
    display: none;
  }

  .el-dialog__body {
    box-sizing: border-box;
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
    }

    .el-date-picker__editor-wrap {
      .el-input {
        width: 100px !important;
      }
    }

    .el-table--striped .el-table__body tr.el-table__row--striped td {
      background: transparent !important;
    }

    table {}

    .model-type {
      p,
      div {
        display: inline-block;
        margin-right: 20px;

        .el-tag {
          margin-right: 10px;
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
