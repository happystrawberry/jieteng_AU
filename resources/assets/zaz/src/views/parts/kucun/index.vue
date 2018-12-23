<template>
    <div class="partskucun  zone-content ">
        <div class="print-ct" id="form1">
          <h1 style="text-align:center">发货单</h1>
           <div class="print-ct-header" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;">
         <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">单号:{{item.order_sn}}</div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;"></div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">打印时间:{{printtime}}</div>
              <!-- <img style="margin-left:40px;max-width:400px;" :src="'data:image/jpeg;base64,'+item.barcode" alt=""> -->
          </div>
           <div style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;">
             <p style="flex:1">车牌号:{{item.vehicle_number}}</p>
              <p style="flex:1">车架号:{{item.vehicle_vin}}</p>
               <p style="flex:1">车型:{{item.brand}} {{item.model}} {{item.vehicle_model}}</p>
           </div>
           <div class="printitem"  >
            <h3 style="margin-bottom:20px">材料清单</h3>
            <table border="1" width="600" height="30" cellspacing="0"  style="margin:0 auto;border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="70" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">材料编码</td>
            <td width="200" height="30" align="center" style="border:solid 1px black">材料名称</td>
            <td width="120" height="30" align="center" style="border:solid 1px black">对应车型</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">规格</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">单位</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">数量</td>
          </tr>
          <tr v-for="(items,index) in checklist" :key="index" v-if="items.parts_status!=2">
            <td width="70" height="30" align="center" style="border:solid 1px black">{{items.id}}</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">{{items.parts_sn}}</td>
            <td width="200" height="30" align="center" style="border:solid 1px black">{{items.parts_name.split(" ")[0]}}</td>
            <td width="120" height="30" align="center" style="border:solid 1px black">{{items.target_model}}</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">{{items.spec}}</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">{{items.unit}}</td>
            <td width="70" height="30" align="center" style="border:solid 1px black">{{items.amount}}</td>
          </tr>
          <tr>
              <td width="60" height="30" align="center" style="border:solid 1px black">合计:</td>
              <td width="110" height="30" align="center" style="border:solid 1px black"></td>
              <td width="50" height="30" align="center" style="border:solid 1px black"></td>
              <td width="200" height="30" align="center" style="border:solid 1px black"></td>
              <td width="50" height="30" align="center" style="border:solid 1px black"></td>
              <td width="50" height="30" align="center" style="border:solid 1px black"></td>
              <td width="50" height="30" align="center" style="border:solid 1px black">{{totalnum()}}</td>
          </tr>
        </table>
            <div class="ul" style="margin-top:40px;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;margin-bottom:5px;">
                领用人:
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;margin-bottom:5px;">
                领用部门:
              </p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;margin-bottom:5px;">
                仓管员:
              </p>
            </div>
          </div>
        </div>
        <div class="noprint">

          <!-- 新增材料-->
          <el-dialog
        title="添加材料"
        :visible.sync="dialog.parts.showkey"
        width="50%"
        center
        style="box-sizing:border-box"
        >
        <el-form
        
        label-position="left"
        ref="parts"
        :model="dialog.parts"
        :rules="patts"
        label-width="100px">
       <el-row>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
          <el-form-item label="业务类别" prop="istype"  style="text-align:left;">
          <el-select  v-model="dialog.parts.istype" placeholder="请选择业务类别">
          <el-option
          :label="item.name"
          :value="item.id"
          :key="index"
          v-for="(item,index) in dialog.temporary.classes"
          ></el-option>
        </el-select>
        </el-form-item>
        </el-col>
         <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
          <el-form-item   style="text-align:left;tisth:300px" label="材料名称" prop="itemname">
           <el-select
            v-model="dialog.parts.itemname"
            filterable
            remote
            @change="partschange"
            placeholder="请输入关键词"
            >
            <el-option
              v-for="item in alloptionsd"
              :key="item.id"
              :label="item.parts_name+' '+item.spec+' '+item.target_model+' 剩余数量:'+item.amount"
              :value="item.id">
            </el-option>
          </el-select>

          </el-form-item>

         </el-col>

          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="折后单价" >
          <span>{{discountof(dialog.parts.item_cost,dialog.parts.discount)}}</span>
        </el-form-item>
        </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="规格" prop="spec">
          <span >{{dialog.parts.spec?dialog.parts.spec:'等待选择材料'}}</span>
        </el-form-item>
        </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="应收金额">
          <span>{{shouldvalue(dialog.parts.item_cost,dialog.parts.amount)}}</span>
        </el-form-item>
        </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="单位" prop="unit">
           <span >{{dialog.parts.unit?dialog.parts.unit:'等待选择材料'}}</span>
        </el-form-item>
        </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="折后金额">
         <span>{{discountvalue(dialog.parts.item_cost,dialog.parts.amount,dialog.parts.discount)}}</span>
        </el-form-item>
        </el-col>
         <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="数量" prop="amount">
          <el-input class="modelinput" v-model="dialog.parts.amount"></el-input>
        </el-form-item>
        </el-col>
         <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="单价" prop="item_cost">
          <span>{{dialog.parts.item_cost?dialog.parts.item_cost:'等待选择材料'}}</span>
        </el-form-item>
        </el-col>
          <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
        <el-form-item label="折扣" style="text-align:left" prop="discount">
          <el-input  v-model="dialog.parts.discount" style="width:50%" class="zhekou"></el-input>
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
          <el-button size="small" type="primary"  @click="dialog.parts.showkey = false;">取 消</el-button>
          <el-button size="small" type="primary" @click="parrtsubmit(true)">确 定</el-button>
        </span>
      </el-dialog>
        <el-row type="flex" jusitfy="space-between">
            <el-col class="left">
              <div>单号:{{item.order_sn}}</div>
              <img height="50px" @click="onecode" style="max-width:400px;" :src="'data:image/jpeg;base64,'+item.barcode" alt="">
            </el-col>
            <el-col class="right">
             <el-button  size="medium" type="primary" @click.native="gotobeizhu">备注({{item.notes}})</el-button>
              <el-button  size="medium" type="primary" @click="returns">返回到开单</el-button>
          </el-col>
        </el-row>
            <el-col >
             <span> 订单状态:{{item.order_status}}</span>
             <span> 排号:{{item.wait_number}}</span>
            </el-col>
        <el-col class="model">
          <span>业务类型</span>
           <el-checkbox-group v-model="item.op_type">
                            <el-checkbox label="维修"></el-checkbox>
                            <el-checkbox label="保养"></el-checkbox>
                            <el-checkbox label="钣喷"></el-checkbox>
                            <el-checkbox label="美容"></el-checkbox>
                            <el-checkbox label="改装"></el-checkbox>
                            <el-checkbox label="其他"></el-checkbox>
           </el-checkbox-group>
        </el-col>
        <h2>车辆信息</h2>
        <el-row :gutter="20" class="info">
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>车牌号:</span>
            <span>{{item.vehicle_number}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>VIN码:</span>
            <span>{{item.vehicle_vin}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>车型:</span>
            <span>{{item.brand}} {{item.model}} {{item.vehicle_model}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>颜色:</span>
            <span>{{item.vehicle_color}}</span>
          </el-col>
        </el-row>
        <div class="table" style="margin-top:80px;">
          <el-row type="flex" style="algin-items:center">
            <h2 style="margin:0;line-height:40px;width:140px;">材料清单</h2>
            <el-col>
                <el-button size="small" type="primary" @click="print" >打印发货单</el-button>
                <el-button size="small" type="primary" @click="deliver">发货</el-button>
                <el-button size="small" type="primary" @click="tuihuos">退货</el-button>
                <el-button size="small" type="primary" @click="addparint">增加材料</el-button>
            </el-col>
          </el-row>
          <el-table
            ref="multipleTable"
            border
            :data="item.list"
            tooltip-effect="dark"
            style="width: 100%"
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              type="selection"
              header-align="center"
              align="center"
              width="55">
            </el-table-column>
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="id"
              width="100">
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
              width="100"
              >
            </el-table-column>
            <el-table-column
              prop="unit"
              header-align="center"
              align="center"
              label="单位"
              width="120"
              >
            </el-table-column>
             <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="数量"
              width="80"
              >
               <template slot-scope="scope">
                 <span>{{scope.row.amount}}</span>
               </template>
            </el-table-column>
             <el-table-column
              prop="purchase_price_tax"
              label="进货单价(含税)"
              header-align="center"
              align="center"
              width="140"
              >
            </el-table-column>
             <el-table-column
              prop="purchase_price_notax"
              header-align="center"
              align="center"
              label="进货单价(不含税)"
              width="140"
              >
            </el-table-column>
            <el-table-column
              prop="final_price"
              header-align="center"
              align="center"
              label="出货单价"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="type_name"
              header-align="center"
              align="center"
              label="业务类型"
              width="100"
              >
            </el-table-column>
            <el-table-column
              prop="target_model"
              header-align="center"
              align="center"
              label="质检详情"
              >
              <template slot-scope="scope">
                <span>{{scope.row.quality_status==0?'待质检':scope.row.quality_status==1?'已质检':'返工'}}</span>
              </template>
            </el-table-column>
             <el-table-column
              prop="status"
              header-align="center"
              align="center"
              label="配件状态"
              width="100"
              >
            </el-table-column>
            <el-table-column
            
              prop="status"
              header-align="center"
              align="center"
              label="操作"
              width="100"
              >
                <template slot-scope="scope">
                 <el-button  v-if="scope.row.parts_status==0" type="primary" size="mini" @click="revamp(scope)">修改数量</el-button>
               </template>
            </el-table-column>
          </el-table>
        </div>

    <el-dialog 
    title="退货" 
    center
    :visible.sync="tuihuo.showkey">
  <el-form       
  label-position="left"
 :rules="rules" 
  ref="zonetuihuo"  
  :model="tuihuo.form" 
  class="tuihuo">
   <el-col el-col shadow="always" :xs={span:12} :sm={span:12} :md={span:12} :lg={span:12}>
    <el-form-item label="材料名称"  prop="parts_name"  label-width="140px" >
      <el-input style="width:210px;" v-model="tuihuo.form.parts_name" auto-complete="off"></el-input>
    </el-form-item>
   </el-col>
    <el-col el-col shadow="always" :xs={span:12} :sm={span:12} :md={span:12} :lg={span:12}>
    <el-form-item label="数量" prop="amount" label-width="140px">
      <el-input style="width:210px;" v-model="tuihuo.form.amount" auto-complete="off"></el-input>
    </el-form-item>
   </el-col>
  </el-form>
  <div slot="footer" class="dialog-footer">
    <el-button size="mini" type="primary" @click="tuihuo.showkey = false">取 消</el-button>
    <el-button size="mini" type="primary" @click="suretuihuo()">确 定</el-button>
  </div>
</el-dialog>
<el-dialog 
    title="修改数量" 
    center
    :visible.sync="number.showkey">
  <el-form       
  label-position="left"
 :rules="rules" 
  ref="zonetuihuo"  
  :model="number.form" 
  class="tuihuo">
   <el-col el-col shadow="always" :xs={span:12} :sm={span:12} :md={span:12} :lg={span:12}>
    <el-form-item label="材料名称"  prop="parts_name"  label-width="140px" >
      <span>{{number.form.parts_name}}</span>
    </el-form-item>
   </el-col>
    <el-col el-col shadow="always" :xs={span:12} :sm={span:12} :md={span:12} :lg={span:12}>
    <el-form-item label="数量" prop="amount" label-width="140px">
      <el-input style="width:210px;" v-model="number.form.amount" auto-complete="off"></el-input>
    </el-form-item>
   </el-col>
  </el-form>
  <div slot="footer" class="dialog-footer">
    <el-button size="mini" type="primary" @click="number.showkey = false">取 消</el-button>
    <el-button size="mini" type="primary" @click="surenumber()">确 定</el-button>
  </div>
</el-dialog>
    </div>
    
     </div>
</template>

<script>
let LODOP;
// 0待发货1已发货2已退货
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
import { setTimeout } from "timers";
export default {
  name: "index",
  methods: {
    gotodetails() {
      this.$router.push({ name: "orderdetails", query: { id: index } });
    },
     onecode(){
          LODOP=getLodop(document.getElementById('LODOP1'),document.getElementById('LODOP_EM1')); 
            LODOP.ADD_PRINT_BARCODE(40,200,400,47,"EAN128B",this.item.order_sn);
	        	LODOP.SET_PRINT_STYLEA(0,"AlignJustify",2);
            LODOP.PRINT_DESIGN();		
        },
    totalnum(){
      let arr=0,
          that=this;
      if (that.checklist) {
          for (let i in that.checklist) {
            if (!that.checklist[i].amount||that.checklist[i].parts_status==2) {
              arr += 0;
            } else {
              arr += parseInt(that.checklist[i].amount);
            }
          }
        }
        return arr;
    },
    totalpart(){
      let arr=0,
          that=this;
      if (that.item.list) {
          for (let i in that.item.list) {
            if (!that.item.list[i].final_price||that.item.list[i].parts_status==2) {
              arr += 0;
            } else {
              arr += parseFloat(that.item.list[i].final_price);
            }
          }
        }
        return arr+"元";
    },
    surenumber(){
      let ele=this.number.form;
      Axios("parts/saveorderparts", {
          order_id:ele.order_id,
          id:ele.id,
            op_type:ele.op_type,
            parts_sn:ele.parts_sn,
            parts_name:ele.parts_name,
            spec: ele.spec,
            unit: ele.unit,
            amount: ele.amount,
            sell_price: ele.sell_price,
            discount: ele.discount,
            final_price:ele.final_price
          }).then(res => {
            this.number.showkey = false;
            this.$message({
              message: "修改数量成功",
              type: "success"
            });
            this.getinfo();
          });
    },
    gotobeizhu() {
      let routeData = this.$router.resolve({
        name: "remarkindex",
        query: { id: this.$route.query.id }
      });
      window.open(routeData.href, "_blank");
    },
    checkdate(time) {
      return parseTime(time);
    },
     shouldvalue(cost, time) {
      let value = (parseFloat(cost || 0) * parseFloat(time || 0)).toFixed(2);
      return isNaN(value) ? "" : value + "元";
    },
    discountof(cost, discount) {
      let value = (parseFloat(cost || 0) * parseFloat(discount || 0) / 10).toFixed(2);
      return isNaN(value) ? "" : value + "元";
    },
    handleSelectionChange(value) {
      this.checklist = value;
    },
    parrtsubmit(){
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
           if (this.dialog.parts.istype == 0) {
            this.$message({
              message: "材料类型异常,请重试",
              type: "warning"
            });
            return;
          }
          Axios("parts/saveorderparts", {
            order_id:this.$route.query.id,
            is_temporary:0,
            is_add:1,
            op_type:this.dialog.parts.istype,
            parts_sn: this.dialog.parts.parts_sn,
            parts_name: this.dialog.parts.itemname,
            spec: parts.spec,
            unit: parts.unit,
            amount: parts.amount,
            sell_price:parseFloat(parts.item_cost),
            discount: parseFloat(parts.discount/10),
          }).then(res => {
            this.dialog.parts.showkey = false;
            this.$message({
              message: "创建材料成功",
              type: "success"
            });
            this.getinfo();
          });
        }
      });
    },
    addparint() {
      let that=this;
      //  这里的材料跳转到新页面
      // this.$router.push({ name: "orderdetails", query: { id: this.$route.query.id ,isadd:1} });
      this.dialog.parts.showkey=true;
      this.$nextTick(()=>{
        that.$refs.parts.resetFields();
      })
      //  let routeData = this.$router.resolve({ name: 'partskucunlist', query: {  id: this.$route.query.id } });
      //   window.open(routeData.href, '_blank');
    },
    revamp(scope){
      let ele=this.number.form;
      for(let i in ele){
        ele[i]=scope.row[i]
      }
      this.number.showkey=true;
    },
     discountvalue(cost, time, discount) {
      let value =(
        parseFloat(cost || 0) *
        parseFloat(time || 0) *
        parseFloat(discount || 0) /
        10).toFixed(2);
      return isNaN(value) ? "" : value + "元";
    },
    print() {
      if (this.item.list.length == 0) {
        this.$message({
          showClose: true,
          message: "表单数据为空",
          type: "warning"
        });
        return;
      }
       if (this.checklist.length == 0) {
        this.$message({
          showClose: true,
          message: "请先勾选打印材料",
          type: "warning"
        });
        return;
      }

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
    deliver() {
      let that = this;
      if (this.checklist.length == 0) {
        this.$message({
          message: "请先勾选表单",
          type: "warning"
        });
        return;
      }
      for (let i in this.checklist) {
        //这里已经退货的不能发货
        if (this.checklist[i].parts_status == 2) {
          setTimeout(() => {
            that.$notify({
              title: "警告",
              message: `${that.checklist[i].parts_name}已退货不能再发货了哟`,
              type: "warning",
              duration: 3000
            });
          }, 500);
        } else {
          Axios("parts/sendoutparts", {
            id: this.checklist[i].id
          })
            .then(res => {
              this.$notify({
                title: "成功",
                message: `${this.checklist[i].parts_name}发货成功`,
                type: "success",
                offset: 100,
                duration: 1500
              });
              this.checklist[i].parts_status = 1;
              this.checklist[i].status = "已发货";
            })
            .catch(res => {
              this.$notify.error({
                title: "失败",
                duration: 1500,
                message: `${this.checklist[i].parts_name}发货失败`
              });
            });
        }
      }
    },
    returns() {
      // 这里改为直接请求
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
          that.$router.push({ name: "partsindex"});
        },1500)
      })
        }).catch(() => {
                   
        });

    },
    suretuihuo() {
      this.$refs.zonetuihuo.validate(valid => {
        if (valid) {
          this.tuihuo.showkey = false;
          Axios("parts/delete", {
            id: this.tuihuo.form.id,
            num: this.tuihuo.form.amount
          }).then(res => {
            this.$message({
              message: "退货成功",
              type: "success"
            });
            this.getinfo();
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    tuihuos() {
      let that = this;
      if (this.checklist.length == 0) {
        this.$message({
          message: "请先勾选表单",
          type: "warning"
        });
        return;
      }
      //这里循环退货
      for (let i in this.checklist) {
        if (this.checklist[i].parts_status == 0) {
          setTimeout(() => {
            that.$notify({
              title: "警告",
              message: `${that.checklist[i].parts_name}未发货不能退货哟`,
              type: "warning",
              duration: 3000
            });
          });
        } else {
          Axios("parts/returnparts", {
            id: this.checklist[i].id,
            num: this.checklist[i].amount
          })
            .then(res => {
              this.$notify({
                title: "成功",
                message: `${this.checklist[i].parts_name}退货${
                  this.checklist[i].amount
                }${this.checklist[i].unit}成功`,
                type: "success",
                offset: 100,
                duration: 1500
              });
              this.checklist[i].parts_status = 2;
              this.checklist[i].status = "已退货";
            })
            .catch(res => {
              this.$notify.error({
                title: "成功",

                duration: 1500,
                message: `${this.checklist[i].parts_name}退货${
                  this.checklist[i].amount
                }${this.checklist[i].unit}失败`
              });
            });
        }
      }
    },
    checksale(type) {
      this.checktype = type;
      if (this.checkasale == true) {
        if (this.checklist.length == 0) {
          this.$message({
            message: "请先勾选表单",
            type: "warning"
          });
          return;
        } else if (this.checklist.length > 1) {
          this.$message({
            message: "一次只能操作一个材料",
            type: "warning"
          });
          return;
        }
        for (let i in this.dialogFormVisible.form) {
          this.dialogFormVisible.form[i] = this.checklist[0][i];
        }
      } else {
        for (let i in this.dialogFormVisible.form) {
          this.dialogFormVisible.form[i] = "";
        }
      }

      this.dialogFormVisible.showkey = true;
      this.$nextTick(() => {
        this.$refs.zoneform.resetFields();
      });
    },
     partschange(value) {
      let index = this.alloptionsd.findIndex(el => {
        return el.id == value;
      });
      if (index > -1) {
        this.dialog.parts.unit = this.alloptionsd[index].unit;
        this.dialog.parts.spec = this.alloptionsd[index].spec;
        this.dialog.parts.itemname = this.alloptionsd[index].parts_name+' '+this.alloptionsd[index].spec+''+this.alloptionsd[index].target_model;
        this.dialog.parts.parts_sn = this.alloptionsd[index].sn;
        this.dialog.parts.item_cost = this.alloptionsd[index].sell_price;
      }
    },
    checkitem(){
      let that=this;
      that.dialog.parts.showkey = true;
      this.$nextTick(() => {
          that.$refs.parts.resetFields();
      });
    },
    getinfo() {
      let id = this.$route.query.id;
      Axios("parts/vieworder", {
        order_id: id
      }).then(res => {
        let item = res.data;
        switch (item.order_status) {
          case 0:
            item.status_num = "作废";
            break;
          case 1:
            item.status_num = "已接车";
            break;
          case 2:
            item.status_num = "已开单";
            break;
          case 3:
            item.status_num = "已调度";
            break;
          case 4:
            item.status_num = "已开工";
            break;
          case 5:
            item.status_num = "已完工";
            break;
          case 6:
            item.status_num = "已结算";
            break;
          case 7:
            item.status_num = "已回访";
            break;
          case 99:
            item.status_num = "已暂停";
            break;
        }
        this.item = item;
      });
    }
  },
  mounted() {
     this.dialog.parts.itemname = "";
          Axios("common/getallparts", {
            op_type: 0
          }).then(res => {
            this.alloptionsd = res.data.list;
          });
    Axios("common/getoptypelist").then(res => {
      this.getoptypelist = res.data.list;
    });
     //获取业务类型
      Axios("common/getoptypelist").then(res => {
        this.dialog.temporary.classes = res.data.list;
      });
    this.getinfo();
  },
  data() {
    var validiscount=(rule,value,callback)=>{
      if(isNaN(value)){
      callback(new Error("请输入数字"));
      }
      if(parseFloat(value)<0||parseFloat(value)>10){
         callback(new Error("折扣应在0-10之间"));
      }else{
        callback();
      }
    }
    return {
      item: {},
         patts: {
        spec: [{ required: true, message: "请输入规格", trigger: "blur" }],
        itemname: [
          { required: true, message: "请输入项目名称", trigger: "blur" }
        ],
        unit: [{ required: true, message: "请输入单位", trigger: "blur" }],
        amount: [{ required: true, message: "请填写数量", trigger: "blur" }],
        item_cost: [{ required: true, message: "请填写单价", trigger: "blur" }],
        istype: [
          { required: true, message: "请选择业务类型", trigger: "change" }
        ],
        itemname: [
          { required: true, message: "请填写材料名称", trigger: "blur" }
        ],
         discount: [
          { required: true, validator:validiscount,trigger: "blur" }
        ]
      },
      alloptionsd:[],//材料
      rules: {
        sn: [{ required: true, message: "请输入材料编码", trigger: "blur" }],
        parts_name: [
          { required: true, message: "请输入材料名称", trigger: "blur" }
        ],
        target_model: [
          { required: true, message: "请输入对应车型", trigger: "blur" }
        ],
        spec: [{ required: true, message: "请输入材料规格", trigger: "blur" }],
        unit: [{ required: true, message: "请输入材料单位", trigger: "blur" }],
        amount: [
          { required: true, message: "请输入材料数量", trigger: "blur" }
        ],
        purchase_price_tax: [
          { required: true, message: "请输入材料进货单价", trigger: "blur" }
        ],
        purchase_price_notax: [
          { required: true, message: "请输入材料进货单价", trigger: "blur" }
        ],
        sell_price: [
          { required: true, message: "请输入材料出货价格", trigger: "blur" }
        ],
        parts_location: [
          { required: true, message: "请输入材料库存位置", trigger: "blur" }
        ],
         discount: [
          { required: true, validator:validiscount,trigger: "blur" }
        ]
      },
      rule: {
        parts_name: [
          { required: true, message: "请输入材料名称", trigger: "blur" }
        ],
        amount: [
          { required: true, message: "请输入材料数量", trigger: "blur" }
        ],
        spec: [{ required: true, message: "请输入材料规则", trigger: "blur" }],
        unit: [{ required: true, message: "请输入材料单位", trigger: "blur" }],
        purchase_price_tax: [
          { required: true, message: "请输入材料进货单价", trigger: "blur" }
        ],
        purchase_price_notax: [
          { required: true, message: "请输入材料进货单价", trigger: "blur" }
        ],
        final_price: [
          { required: true, message: "请输入材料出货单价", trigger: "blur" }
        ],
         discount: [
          { required: true, validator:validiscount,trigger: "blur" }
        ]
      },
      getoptypelist: [],
      checklist: [],
      totalnumber:"",
      totalmoney:"",
      printtime:"",
      dialogFormVisible: {
        showkey: false,
        form: {
          id: "",
          op_type: "",
          sn: "",
          parts_name: "",
          target_model: "",
          spec: "",
          unit: "",
          amount: "",
          purchase_price_tax: "",
          purchase_price_notax: "",
          sell_price: "",
          parts_location: ""
        },
      },
      number:{
        showkey:false,
        form:{
         order_id: "",
         id:"",
          itemname: "",
          istype: "",
          is_temporary: "",
          parts_sn: "",
          parts_name: "",
          spec: "",
          unit: "",
          amount: "",
          sell_price: "",
          discount:10,
          final_price: ""
        }
      },
      dialog: {
        showkey:false,
        parts: {
          showkey: false,
          order_id: "",
          itemname: "",
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
        temporary:{
          classes:[]
        }
      },
      tuihuo: {
        showkey: false,
        form: {
          parts_name: "",
          amount: "",
          spec: "",
          unit: "",
          purchase_price_tax: "",
          purchase_price_notax: "",
          final_price: "",
          id: ""
        }
      },
      checktype: "",
      tableData3: [{}],
      checkList: [],
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
.print-ct{
  display:none;
}
 
@media print {
 .el-message{
    display:none !important;
  }
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
        flex:  0 0 11%;
        width:11%;
        border: 1px solid #ebeef5;
        font-size: 10px;
        text-align: center;
        line-height: 30px;
         word-wrap: break-word;
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
    width: 100%;
    min-height: 100%;
    z-index: 99999;
    background:white;
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
    display: none !important;
  }
  .noprint {
    display: none !important;
  }
}
.el-tag {
  padding: 0 15px;
}
.el-form-item__label {
  font-size: 13px;
}
.tuihuo {
  input {
    width: 210px;
  }
}
.model {
  display: flex;
  position: relative;
  &:after {
    content: "";
    width: 100%;
    position: absolute;
    height: 100%;
    z-index: 1;
    top: 0;
    left: 0;
  }
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
