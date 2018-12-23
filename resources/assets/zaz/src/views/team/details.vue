<template>
    <div class="zone-content" v-loading="loading">
       <div class="print-ct" id="form1" >
          <div class="print-ct-header" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;">
               <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">单号:{{item.order_sn}}</div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;"></div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">打印时间:{{printtime}}</div>
              <!-- <img style="margin-left:40px;max-width:400px;" :src="'data:image/jpeg;base64,'+item.barcode" alt=""> -->
          </div>
      <div class="printitem"  v-if="item.items_list" v-show="printnum==1">
        <h3 style="margin-bottom:20px">维修项目</h3>
        <table border="1" width="600" height="106" cellspacing="0"  style="border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="104" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">项目编码</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">项目名称</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">工时/小时</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">业务类型</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">维修小组</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">维修人员</td>
          </tr>
          <tr v-for="(items,index) in item.items_list" :key="index">
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.order_id}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.op_number}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.item_name}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.item_times}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.type_name}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.dispatch_type!='team'?'暂无':items.dispatch_team}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black"> <span v-show="items.dispatch_type!='team'" v-for="(key,val,index) in items.dispatch_user" :key="index">{{key}}</span>
               <span v-show="items.dispatch_type=='team'">{{allot(items)}}</span>
               <span v-show="items.dispatch_user.length==0">未分配</span></td>
          </tr>
        </table>
          </div>
           <div class="printitems printitem"   v-if="item.parts_list" v-show="printnum==2">
              <h3 style="margin-bottom:20px">领料单</h3>
             <table border="1" width="600" height="30" cellspacing="0"  style="margin:0 auto;border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="104" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">材料编码</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">材料名称</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">单位</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">数量</td>
            <!-- <td width="104" height="30" align="center" style="border:solid 1px black">领取状态</td> -->
          </tr>
          <tr v-for="(items,index) in item.parts_list" :key="index">
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.order_id}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.parts_sn}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.parts_name}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.unit}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.amount}}</td>
            <!-- <td width="104" height="30" align="center" style="border:solid 1px black">{{items.status}}</td> -->
          </tr>
        </table>
          </div>
        </div>
      <div class=" noprint partskucun  zone-content ">
       
        <div class="">
           <el-row type="flex" jusitfy="space-between">
            <el-col class="left">
              <div>单号:{{item.order_sn}}</div>
              <img height="50px" @click="onecode" :src="'data:image/jpeg;base64,'+item.barcode" alt="">
            </el-col>
            <el-col class="right">
             <el-button   @click.native="gotobeizhu" size="medium" type="primary">备注({{item.notes}})</el-button>
             <el-button  @click.native="startwork" size="medium" type="primary">{{item.order_status==99?'开工':'暂停'}}</el-button>
              <el-button size="medium" type="primary" @click="goto(1)">返回到开单</el-button>
          </el-col>
        </el-row>
            <el-col >
             <span> 订单状态:{{item.status}}</span>
             <span style="margin-left:100px;"> 排号:{{item.wait_number}}</span>
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
            <span>单号:</span>
            <span>{{item.order_sn}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>联系人:</span>
            <span>{{item.contact_name}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>车牌:</span>
            <span>{{item.vehicle_number}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>车型:</span>
           <span>{{item.brand}} {{item.model}} {{item.vehicle_model}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>预计完工时间:</span>
            <span>{{checkdate(item.plan_endtime)}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>完工时间:</span>
            <span>{{item.done_time>0?checkdate(item.done_time):'暂未完工'}}</span>
          </el-col>
        </el-row>
        <div class="tab">
          <div class="table-header">
            <h2>项目进度</h2>
          </div>
          <el-table
            ref="multipleTable"
            border
            :data="arrlist"
            tooltip-effect="dark"
            style="width: 451px"
            stripe
          >
            <el-table-column
              prop="item_name"
              header-align="center"
              align="center"
              label="项目名称"
              width="100">
            </el-table-column>
              <el-table-column
              prop="cost"
              label="进度"
              header-align="center"
              align="center"
              >
               <template slot-scope="scope">
                 <el-tag>{{item.order_status==99?'暂停':scope.row.is_redo==1?'返工':scope.row.is_start==0?'未开始':scope.row.is_done==1?'已完成':'进行中'}}</el-tag>
               </template> 
            </el-table-column> 

             <el-table-column
              prop="cost"
              label="操作"
              width="260"
              header-align="center"
              align="center"
              >
              <template slot-scope="scope">
              <el-popover
                placement="top"
                width="160"
                v-model="scope.row.visible">
                <p>{{scope.row.is_start==0?'确认开启项目吗？':'确认完工该项目吗？'}}</p>
                <div style="text-align: right; margin: 0">
                  <el-button size="mini" type="text" @click="surecheck(scope,false)">取消</el-button>
                  <el-button type="primary" size="mini" @click="surecheck(scope,true)">确定</el-button>
                </div>
                <el-button v-if="item.order_status!=99" v-show="scope.row.is_redo==1?true:scope.row.is_done!=1" slot="reference" size="mini" type="primary">{{scope.row.is_start==0?'开始':'完工'}}</el-button>
              </el-popover>
            </template>
            </el-table-column> 
          </el-table>
        </div>
        <div class="table">
          <div class="table-header">
            <h2>维修项目</h2>
            <div>
              <el-button size="mini" type="primary" @click="parint(1)">打印维修项目</el-button>
              <el-button size="mini" type="primary" @click="goto(2)"  >返回到调度</el-button>
            </div>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="item.items_list"
            tooltip-effect="dark"
            style="width: 1181px"
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
              prop="specification"
              header-align="center"
              align="center"
              label="业务类别"
      
              >
               <template slot-scope="scope">
                  <el-tag>{{scope.row.type_name}}</el-tag>
               </template>
            </el-table-column>
            <el-table-column
              prop="unit"
              header-align="center"
              align="center"
              label="维修小组"
              width="120"
              >
                <template slot-scope="scope">
                  
                  <el-tag>{{scope.row.dispatch_type!='team'?'暂无':scope.row.dispatch_team}}</el-tag>
               </template>
            </el-table-column>
             <el-table-column
              prop="nums"
              header-align="center"
              align="center"
              label="维修人员"
              width="220"
              >
              <template slot-scope="scope">
               <span v-show="scope.row.dispatch_type!='team'" v-for="(key,val,index) in scope.row.dispatch_user" :key="index">{{key}}</span>
               <span v-show="scope.row.dispatch_type=='team'">{{allot(scope.row)}}</span>
               <el-tag v-show="scope.row.dispatch_user.length==0">未分配</el-tag>
            </template> 
            </el-table-column>
             <el-table-column
              prop="cost"
              label="操作"
              width="260"
              header-align="center"
              align="center"
              >
              <template slot-scope="scope">
              <el-button  v-if="scope.row.dispatch_type=='team'" @click.native="checkitem(scope.row)" size="mini" type="primary">修改人员</el-button>
            </template>
            </el-table-column> 
       
          </el-table>
        </div>
        <div class="table">
          <div class="table-header">
            <h2>领取详情</h2>
            <div>
              <el-button size="mini" type="primary" @click="parint(2)">打印领料单</el-button>
             
            </div>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="item.parts_list"
            tooltip-effect="dark"
            style="width:800px"
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
              label="材料编码"
              width="100">
            </el-table-column>
            <el-table-column
              prop="parts_name"
              header-align="center"
              align="center"
              label="材料名称"
              
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
              width="120"
              >
            </el-table-column>
            <el-table-column
              prop="status"
              header-align="center"
              align="center"
              label="领取状态"
              width="120"
              >
               <template slot-scope="scope">
                 <el-tag> {{scope.row.status}}</el-tag>
               </template>  
            </el-table-column>
          </el-table>
        </div>
     
        </div>
       
        <el-dialog
         center
         width="600px"
         :visible.sync="dialog.showkey">
        <el-form :model="dialog.form">
          <el-form-item :label="dialog.title" label-width="120px">
          <el-checkbox-group v-model="dialog.check" >
             <el-checkbox class="margin-left" :key="item.id" v-for="item in dialog.list" :label="parseInt(item.id)" >{{item.username}}
               <el-input style="width:80px;!important" type="number" max="100" min="0"  v-model="item.commission" size="mini" placeholder="提成比例"></el-input>
             </el-checkbox>
            </el-checkbox-group> 
            
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button size="small" @click="dialog.showkey = false">取 消</el-button>
          <el-button size="small" type="primary" @click="sureckeck(dialog)">确 定</el-button>
        </div>
      </el-dialog>
        </div>
      </div>
    </div>
</template>

<script>
let LODOP;
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";

export default {
  methods: {
    handleSelectionChange() {
      console.log(23123);
    },
     onecode(){
          LODOP=getLodop(document.getElementById('LODOP1'),document.getElementById('LODOP_EM1')); 
            LODOP.ADD_PRINT_BARCODE(40,200,400,47,"EAN128B",this.item.order_sn);
	        	LODOP.SET_PRINT_STYLEA(0,"AlignJustify",2);
            LODOP.PRINT_DESIGN();		
        },
    allot(item){
      console.log(item)
      let value="";
      if(typeof item.dispatch_commission =="string"){
          return value=item.dispatch_user+"("+item.dispatch_commission+")";
      }else{
        if(item.dispatch_user.length==0){
          return "";
        }else{
            let arr=item.dispatch_user.split(",")
        for(let i in arr){
          value+=arr[i]+"("+item.dispatch_commission[i]+")"+"  "
        }
        return value;
        }
        
      }
    },
    parint(type){
      let that=this;
       this.printnum = type;
       this.printtime=this.checkdate(new Date().getTime());
       if(type==1){
         if (this.item.items_list.length == 0) {
          this.$message({
            showClose: true,
            message: "维修项目单数据为空,请先填写表单数据",
            type: "warning"
          });
          return;
        }
       }
       if (type == 2) {
        if (this.item.parts_list.length == 0) {
          this.$message({
            showClose: true,
            message: "领料单数据为空,请先填写表单数据",
            type: "warning"
          });
          return;
        }
      }
       setTimeout(function(){
        try{ 
		     var LODOP=getLodop(); 
			if (LODOP.VERSION) {
         LODOP.PRINT_INIT("");
        LODOP.SET_PRINT_STYLE("FontSize", 18);
        LODOP.SET_PRINT_STYLE("Bold", 1);
        LODOP.ADD_PRINT_HTM(40, 5, "100%", "100%", document.getElementById("form1").innerHTML);
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
    checkdate(value){
      return aprseTime(value)
    },
    gotobeizhu(){
      let routeData = this.$router.resolve({ name: 'remarkindex', query: {  id: this.$route.query.id } });
        window.open(routeData.href, '_blank');
    },
    surecheck(scope,type){
       scope.row.visible=false;
       if(scope.row.is_done==1&&scope.row.is_redo!=1){
           this.$message({
            message: "该项目已完成,请勿重复操作",
            type: "warning"
          });
       }
      if(type){
          if(scope.row.is_start==0&&scope.row.is_done==0){
            Axios("team/setstartstatus",{
              id:scope.row.id
            }).then(res=>{
                this.$message({
            message: "设置项目开工成功",
            type: "success"
          });
          this.getlist();
          scope.row.is_start=1;
            })
          }else{
            Axios("team/setdonestatus",{
              id:scope.row.id
            }).then(res=>{
              this.$message({
            message: "设置项目完工成功",
            type: "success"
          });
          this.getlist();
          scope.row.is_done=1;
          if(scope.row.is_redo==1){
            scope.row.is_redo=0;
          }
            })
          }
      }else{
      }
    },
    overtwork(){
      Axios("team/setdonestatus",{
        id:this.id
      }).then(res=>{
        this.item.order_status=5;
        this.item.status="已质检(已完工)"
        this.$message({
            message: "操作成功",
            type: "success"
          });
      })
    },
    handleCheckAllChange(val) {
        this.checkedCities = val ? cityOptions : [];
        this.isIndeterminate = false;
      },
      handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.cities.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
      },
    goto(type) {
      let that = this;
      if (type == 1) {
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
          that.$router.push({ name: "orderindex"});
        },1500)
      })
        }).catch(() => {
              
        });

      } else {
        // 返回到调度
        this.$confirm('确定要将该订单返回到调度状态吗', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          center:true,
          type: 'warning'
        }).then(() => {
           Axios("/team/resettodispatch",{
        order_id:this.$route.query.id
      }).then(res=>{
          this.$notify({
          title: '成功',
          message: '返回到调度状态成功',
          type: 'success',
          duration: 2000
        });
        setTimeout(()=>{
          that.$router.push({ name: "dispatchindex"});
        },1500)
      })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          });          
        });
        // that.$router.push({ name: "dispatchindex" });
      }
    },
    sureckeck(id){
      if(this.dialog.check.length==0){
        this.$message({
            message: "请分配人员",
            type: "warning"
          });
          return;
      }
      let url=this.dialog.type==1?'team/setoperator':'team/setoperator';
      // 新增一个参数 是每个人的分配比例
      let commission=[],
          nums=0;
      for(let i in this.dialog.list){
         if(this.dialog.check.indexOf(parseInt(this.dialog.list[i].id))>-1){
          nums+=parseFloat(this.dialog.list[i].commission);
        commission.push(this.dialog.list[i].commission);
         }
      }
      if(nums!=100){
        this.$message({
            message: "分配人员提成比例总和应为100,请检查提成比例"+nums,
            type: "warning"
          });
          return;
      }
      Axios(url,{
           operators:this.dialog.check.join(","),
           id:this.dialog.id,
           commission:commission,
           item_id:this.dialog.teamid
      }).then(res=>{
         this.dialog.showkey=false;
         this.getlist();
      })
    },
    checkitem(item) {
      console.log(item,item.dispatch_type)
      if(item.dispatch_type=='team'){
          this.dialog.type=1;
          Axios("team/getoperators",{
          "team_id":item.team_id,
          "order_id":item.order_id,
          "item_id":item.id
        }).then(res=>{
          console.log("item",item)
           this.dialog.showkey=true;
           this.dialog.id=item.worktask_id;
           this.dialog.teamid=item.id;
           this.dialog.title=`请分配${item.type_name}人员`;
           this.dialog.list=res.data.list;
           let list=[];
            for(let i in item.dispatch_uids){
              console.log(item.dispatch_uids[i])
              list.push(parseInt(item.dispatch_uids[i]))
           }
           console.log("list",list)
          this.dialog.check=list;
        })
      }else{
        Axios("team/getOperatorsByOpType",{
          "op_type":item.op_type
        }).then(res=>{
          this.dialog.type=2;
           this.dialog.showkey=true;
           this.dialog.id=item.worktask_id;
           this.dialog.title=`请分配${item.type_name}人员`;
           this.dialog.list=res.data.list;
           let list=[];
           for(let i in item.dispatch_user){
              list.push(parseInt(i))
           }
          this.dialog.check=list;
        })
      }
        
    },
    updatelist(){
      this.getlist();
    Axios("team/getdispatchlist",{
      order_id:this.$route.query.id
    }).then(res=>{
        let list=res.data.list;
        for(let i in list){
          list[i].visible=false;
        }
        this.arrlist=list;
    })
    },
    startwork() {
      // 切换开工状态
      let item=this.item;
      if (this.item.order_status == 3) {
        Axios("team/setstartstatus", {
          id: this.$route.query.id
        }).then(res => {
          this.$message({
            message: "操作成功",
            type: "success"
          });
          this.updatelist();
        });
      }
      // 
      if (this.item.order_status == 4||this.item.order_status==95) {
        Axios("/team/pausetask", {
          order_id: this.$route.query.id
        }).then(res => {
          this.$message({
            message: "操作成功",
            type: "success"
          });
          this.updatelist();
        });
      }
      // 暂停状态
      if (this.item.order_status == 99) {
        Axios("team/resumetask", {
          order_id: this.$route.query.id
        }).then(res => {
          this.$message({
            message: "操作成功",
            type: "success"
          });
          this.updatelist();
        });
      }
      
    },
    checkdate(time) {
      return parseTime(time);
    },
      getlist(){
       Axios("team/vieworder", {
      order_id: this.id
    }).then(res => {
      let list=res.data;
      for(let i in list.items_list){
        
        list.items_list[i].visible=false
      }
      this.item = list;
      // this.item.done_time=this.item.done_time*1000;
      this.loading = false;
      this.$message({
        message: "数据已更新",
        type: "success"
      });
    });
  },
  },



  mounted() {
    this.id=this.$route.query.id;
    this.getlist();
    Axios("team/getdispatchlist",{
      order_id:this.$route.query.id
    }).then(res=>{
        let list=res.data.list;
        for(let i in list){
          list[i].visible=false;
        }
        this.arrlist=list;
    })
   
  },
  data() {
    return {
      item: {},
      printtime:"",
      printnum:-1,
      loading: true,
      arrlist:[],
      dialog:{
        showkey:false,
        title:"",
        check:[],
        id:"",
        checkall:false,
        isIndeterminate:false,
        list:[],
      },
      id:"",
      formInline: {},
      otherdata: [],
      remuneration: [],
      draw: [],
      multipleTable: [],
      tableData3: [{}],
      checkList: [],
      data: []
    };
  }
};
</script>

<style scoped lang='scss'>
.margin-left{
  &:nth-child(2n){
    margin-left: 30px;
  }
  &:nth-child(2n-1){
    margin-left: 0px;
  }
}
#form1 {
  display: none;
}

@media print {
  .el-message{
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
        flex:0 0 14%;
        width:14%;
        &:nth-of-type(3){
          flex:0 0 28%;
        width:28%;
        }
        word-wrap: break-word;
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
    width: 100%;
    min-height: 100%;
    z-index: 99999;
    display: block;
    background: red;
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
  .navbar {
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
.model {
  display: flex;
  position: relative;
  &:after {
    content: "";
    display: block;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 11;
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
.el-tag {
  cursor: pointer;
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