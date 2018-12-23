<template>
    <div class="zone-content table">
         <el-form :model="form" 
            ref="form"
            :rules="rule"
            label-position="left">
             <div class="table-header">
                <h3>用户信息</h3>
                <div>
                  <el-button  size="mini" type="primary" @click="submit">保存</el-button>
                  <el-button  size="mini" type="primary" @click="balance">积分余额管理</el-button>
                  <el-button  size="mini"   type="primary" @click="amend">修改余额积分</el-button>  
                </div>
              </div>
            <el-row>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="会员名称" label-width="140px">
                       <el-input v-model="form.username" placeholder="请输入会员名称" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                <el-form-item label="手机号" label-width="140px" prop="phone">
                       <el-input @change="changephone" v-model="form.phone" placeholder="请输入手机号" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col
                v-show="form.phone!=form.oldphone"
                :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="验证码" label-width="100px">
                       <el-input  v-model="verify.code" placeholder="请输入验证码" auto-complete="off"></el-input>
                       <el-button style="margin-left:10px;" :disabled="verify.disabled" @click="verifycode" size="mini" :loading="verify.loading" type="primary">{{verify.text}}</el-button> 
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="所属车辆" label-width="140px">
                        <el-select 
                        style="width:210px;"
                        @change="itemchange"
                        v-model="form.materialsid" 
                        placeholder="请选择车辆">
                        <el-option 
                            v-for="(item,index) in options"
                            :key="index"
                            :label="item.text" 
                            :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="账户余额" label-width="140px">
                        <span>{{form.wallet_birthday==0?'0':form.wallet_birthday}}元</span>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="积分" label-width="140px">
                     <span>{{form.integral==0?'0':form.integral}}元</span>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="会员等级" label-width="140px">
                        <span>VIP{{form.vip}}</span>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="会员状态" label-width="140px">
                        <el-switch
                          v-model="form.status"
                          active-color="#13ce66"
                          inactive-color="#ff4949"
                          active-value="1"
                          inactive-value="0">
                        </el-switch>
                        <span style="margin-left:20px;">{{form.status==1?'开启':'禁用'}}</span>
                    </el-form-item>
                </el-col>
            </el-row>
            </el-form>

             <el-form 
              ref="listform"
              :rules="rules"
              label-position="left"
             :model="list[itemcheck].vehicle" v-show="itemcheck">
            <div class="table-header" >
                    <h3>车辆管理 </h3>
                    <div>
                      <el-button @click="record(list[itemcheck].vehicle.license_plate)" size="mini" type="primary">维修保养记录</el-button>
                    </div>
           </div>
           
             <el-row> 
              <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="车牌号" label-width="140px" prop="license_plate">
                       <el-input v-model="list[itemcheck].vehicle.license_plate" placeholder="请输入VIN码" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="昵称" label-width="140px" porp="nickname">
                       <el-input v-model="list[itemcheck].vehicle.nickname" placeholder="请输入会员名称" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="VIN码" label-width="140px" prop="frame">
                       <el-input v-model="list[itemcheck].vehicle.frame" placeholder="请输入VIN码" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="品牌" label-width="140px">
                 <el-select 
                 @change="brandchange"
                 v-model="list[itemcheck].vehicle.brand" clearable placeholder="请选择">
                    <el-option
                    v-for="(value,key,index) in brandlist"
                    :key="value.id"
                    :label="value.name"
                    :value="value.id">
                    </el-option>
                     </el-select>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="车型" label-width="140px">
                        <el-select 
                     @change="modelchange"   
                 v-model="list[itemcheck].vehicle.model" clearable placeholder="请选择">
                    <el-option
                    v-for="(value,key,index) in modellist"
                    :key="value.id"
                    :label="value.name"
                    :value="value.id">
                    </el-option>
                     </el-select>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="具体型号" label-width="140px">
                        <el-select v-model="list[itemcheck].vehicle.vehicle_model" clearable placeholder="请选择">
                            <el-option
                            v-for="(value,key,index) in vehicle"
                            :key="index"
                            :label="value.name"
                            :value="value.ids">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="当前里程(万/公里)" label-width="140px">
                       <el-input v-model="list[itemcheck].vehicle.current_mileage" placeholder="请输入里程" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="年审时间" label-width="140px">
                      <el-date-picker
                        v-model="list[itemcheck].vehicle.checkup_date"
                        type="datetime"
                        value-format="timestamp"
                        placeholder="选择日期">
                      </el-date-picker>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="保险到期时间" label-width="140px">
                      <el-date-picker
                        v-model="list[itemcheck].vehicle.insurance_expire"
                        type="datetime"
                        value-format="timestamp"
                        placeholder="请选择保险到期时间">
                      </el-date-picker>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="保险公司" label-width="140px">
                       <el-input v-model="list[itemcheck].vehicle.insurance_company" placeholder="请输入保险公司" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
             <div class="table-header">
                    <h3>保养信息</h3>
              </div>
              <el-row>
                <el-col :xs={span:24} :sm={span:12} :md={span:9} :lg={span:9}>
                    <el-form-item label="保养工程工期(公里)" label-width="140px">
                       <el-input v-model="list[itemcheck].vehicle.maintenance_km" placeholder="请输入保养工程工期" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:9} :lg={span:9}>
                    <el-form-item label="保养时间周期(天)" label-width="140px">
                       <el-input v-model="list[itemcheck].vehicle.maintenance_cycle" placeholder="请输入保养时间周期" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:9} :lg={span:9}>
                    <el-form-item label="下次保养公里数" label-width="140px">
                       <el-input v-model="list[itemcheck].vehicle.maintenance_next_km" placeholder="请输入公里数" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:9} :lg={span:9}>
                    <el-form-item label="下次保养时间" label-width="140px">
                        <el-date-picker
                        v-model="list[itemcheck].vehicle.maintenance_next_date"
                        type="datetime"
                        value-format="timestamp"
                        placeholder="请选择下次保养时间">
                      </el-date-picker>
                    </el-form-item>
                </el-col>
              </el-row>
            <el-row v-show="flase">
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <h3 class="upload-title">行驶证信息</h3>
                    <div class="upload">
                    <div class="content" :style="{backgroundImage:'url('+defaultimg+')'}">
                            <img @click="previewimg(list[itemcheck].driving_license.img_1)" v-if="list[itemcheck].driving_license.img_1" :src="list[itemcheck].driving_license.img_1" class="avatar">
                    </div>
                     <el-upload
                    class="upload-demo"
                    :on-success="getimg"
                    :show-file-list="false"
                    :before-upload="before"
                   :action="BASE_API"
                    >
                    
                    <el-button size="small" type="primary">点击上传</el-button>
                    </el-upload>
                    </div>
                   
                    <div class="upload">
                     <div class="content" :style="{backgroundImage:'url('+defaultimg+')'}">
                         <img @click="previewimg(list[itemcheck].driving_license.img_2)" v-if="list[itemcheck].driving_license.img_2" :src="list[itemcheck].driving_license.img_2" class="avatar">
                    </div>
                         <el-upload
                    class="upload-demo"
                    :before-upload="beforeimgs"
                    :on-success="getimgs"
                    :show-file-list="false"
                     :action="BASE_API"
                    >
                   
                    <el-button size="small" type="primary">点击上传</el-button>
                    </el-upload>
                    </div>
                 </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <h3 class="upload-title">车主信息</h3>
                    <div class="upload">
                     <div class="content" :style="{backgroundImage:'url('+defaultimg+')'}" >
                        <img @click="previewimg(list[itemcheck].vehicle_owner.img_1)" v-if="list[itemcheck].vehicle_owner.img_1" :src="list[itemcheck].vehicle_owner.img_1" class="avatar">
                    </div>
                     <el-upload
                    class="upload-demo"
                    :show-file-list="false"
                     :before-upload="beforeown"
                    :on-success="getown"
                     :action="BASE_API"
                    >
                   
                    <el-button size="small" type="primary">点击上传</el-button>

                    </el-upload>
                    </div>

                    <div class="upload">
                     <div class="content" :style="{backgroundImage:'url('+defaultimg+')'}">
                         <img @click="previewimg(list[itemcheck].vehicle_owner.img_2)" v-if="list[itemcheck].vehicle_owner.img_2" :src="list[itemcheck].vehicle_owner.img_2" class="avatar">
                    </div>
                         <el-upload
                    class="upload-demo"
                    :on-success="getowns"
                    :before-upload="beforeowns"
                    :show-file-list="false"
                    :action="BASE_API"
                    >
                    <el-button size="small" type="primary">点击上传</el-button>

                    </el-upload>
                    </div>
                 </el-col>
            </el-row>
           </el-form>
        <el-dialog title="图片预览"
        center
        :visible.sync="preview.key">
            <img :src="preview.img" alt="" style="margin:0 auto;display:block;">
            <div slot="footer" class="dialog-footer">
            <el-button @click="preview.key = false" size="small" type="primary">关 闭</el-button>
            </div>
        </el-dialog>
        <el-dialog
        title="手动修改用户信息"
        :visible.sync="centerDialogVisible.showkey"
         label-position="left"
        center
        width="600px">
          <el-form 
          ref="centerform"
          style="margin-left:130px;"
          :rules="centerforms"
          label-position="left"
          :model="centerDialogVisible.form">
              <el-form-item label="账号" label-width="100px" >
                <span>{{centerDialogVisible.form.phone}}</span>
              </el-form-item>
              <el-form-item label="用户积分" label-width="100px" prop="integral">
                <el-input v-model="centerDialogVisible.form.integral" auto-complete="off"></el-input>
              </el-form-item>
              <el-form-item label="用户余额" label-width="100px" prop="balance">
                <el-input v-model="centerDialogVisible.form.balance" auto-complete="off"></el-input>
              </el-form-item>
            </el-form>
            <el-popover
                placement="top"
                width="160"
                v-model="centerDialogVisible.visible">
                <p>真的确认修改该账号的用户信息</p>
                <div style="text-align: right; margin: 0">
                    <el-button size="mini" type="text" @click="()=>{this.centerDialogVisible.visible = false}">取消</el-button>
                    <el-button type="primary" size="mini" @click="surechange">确定</el-button>
                </div>
                <el-button style="display:block;margin:0 auto;" size="small" type="primary" slot="reference">确定</el-button>
                </el-popover>
          </el-dialog>  
    </div>
</template>

<script>
let counttime,
    time=30;
import loading from "./loading.gif";
import defaultimg from "./defalut.png";
import { Axios } from "@/api/login";
import Upload from "@/components/imgupload/imgupload";
export default {
  components: {
    Upload
  },
  methods: {
    record(index) {
      this.$router.push({
        name: "memberrecord",
        query: { vehicle_number:index }
      });
    },
    surechange(){
        let el=this.centerDialogVisible.form;
        this.centerDialogVisible.visible=false;
        this.centerDialogVisible.showkey=false;
         this.$refs.centerform.validate((valid) => {
          if (valid) {
            if(el.integral!=this.form.integral){
                Axios("member/updatecredit",{
                    id:this.$route.query.uid,
                    credit:el.integral
                }).then(res=>{
                    this.form.integral=el.integral
                    this.$message({
                    showClose: true,
                    message:"手动修改用户积分成功",
                    type: 'success'
                    });
                })
            }
            if(el.balance!=this.form.wallet_birthday){
                Axios("member/updatewallet",{
                    id:this.$route.query.uid,
                    cash:el.balance
                }).then(res=>{
                      this.form.wallet_birthday=el.balance
                    this.$message({
                    showClose: true,
                    message:"手动修改用户余额成功",
                    type: 'success'
                    });
                })
            }
          } else {
            console.log('error submit!!');
            return false;
          }
        });
    },
    amend(){
        let el=this.centerDialogVisible;
        el.form.phone=this.form.oldphone,
        el.form.integral=this.form.integral,
        el.form.balance=this.form.wallet_birthday;
        el.showkey=true;
        let that=this;
        this.$nextTick(()=>{
              that.$refs.centerform.resetFields();
        })
    },
    changephone(value){
       if(value.length==0){
           this.form.pohone=this.form.oldphone;
       }
    },
    timecount(){
        let that=this;
        clearInterval(counttime);
        counttime=setInterval(()=>{
            time--;
            if(time==0){
                 clearInterval(counttime);
                  that.verify.text="发送验证码"
                  this.verify.loading=false,
                  this.verify.disabled=true
                  return
            }
            that.verify.text=`${time}秒后重试`
        },1000)
    },
    verifycode(){
        if( this.verify.disabled)return;
         this.$refs.form.validate((valid) => {
          if (valid) {
              this.verify.loading=true;
                this.verify.text="验证码发送中";
                this.verify.disabled=true;
                Axios("member/sendsms",{
                    phone:this.form.phone
                }).then(res=>{
                    this.$message({
                    showClose: true,
                    message: `验证码已发送到该手机号(${this.form.phone})请查收`,
                    type: 'success'
                    });
                     this.timecount();
                })
          } else {
              this.$message({
                    showClose: true,
                    message:"手机号长度不合法,请先检查",
                    type: 'warning'
                    });
            return false;
          }
        });
        
    },
    submit() {
      //先判断是否勾选了所属车辆
      let that=this,
          el=that.list[that.itemcheck].vehicle;
      if(!this.form.materialsid){
        this.$message({
          showClose: true,
          message: '请先选择所属车辆',
          type: 'warning'
        });
        return;
      }else{
        this.$refs.listform.validate((valid) => {
          if (valid) {
                Axios("member/saveinfo",{
                    "uid":this.uid,
                    "member_name":that.form.username,
                    "weibo_name":that.form.wb_name,
                    "verify_code":that.verify.code,
                    "phone":that.form.phone,
                    "weixin_name":that.form.wx_name,
                    "enable":that.form.status
                }).then(res=>{
                    Axios("member/savevehicleinfo",{
                        uid:that.uid,
                        vehicle_number:el.license_plate,
                        vehicle_nickname:el.nickname,
                        vehicle_frame:el.frame,
                        id:el.vehicle_id,
                        brand_id:el.brand,
                        vehicle_id:el.model,
                        vehicle_model_id:el.vehicle_model,
                        current_km:el.current_mileage,
                        checkup_date:el.checkup_date/1000,
                        insurance_expire:el.insurance_expire/1000,
                        insurance_company:el.insurance_company,
                        maintenance_km:el.maintenance_km,
                        maintenance_cycle:el.maintenance_cycle,
                        maintenance_next_km:el.maintenance_next_km,
                         maintenance_next_date:el.maintenance_next_date/1000,
                         lic_front_img:that.list[that.itemcheck].driving_license.img_1,
                         lic_reverse_img:that.list[that.itemcheck].driving_license.img_2,
                         id_front_img:that.list[that.itemcheck].vehicle_owner.img_1,
                         id_reverse_img:that.list[that.itemcheck].vehicle_owner.img_2
                    }).then(res=>{
                            this.$message({
                        showClose: true,
                        message: '保存用户信息成功',
                        type: 'success'
                        });
                        // setTimeout(()=>{
                        //      that.$router.push({ name: "memberindex"});
                        // },2000)
                    })
                    
                })
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      }
        
    },
    previewimg(img) {
      this.preview.key = true;
      this.preview.img = img;
    },
    brandchange() {
      this.brandkey = true;
    },
    modelchange() {
      this.model = true;
    },
    balance(value) {
      this.$router.push({
        name: "memberremain",
        query: { uid: this.$route.query.uid }
      });
    },
    itemchange(value) {
      this.itemcheck = value;
    },
    before() {
      this.list[this.itemcheck].driving_license.img_1 = loading;
    },
    beforeimgs() {
      this.list[this.itemcheck].driving_license.img_2 = loading;
    },
    beforeown() {
      this.list[this.itemcheck].vehicle_owner.img_1 = loading;
    },
    beforeowns() {
      this.list[this.itemcheck].vehicle_owner.img_2 = loading;
    },
    getimg(res, file, filelist) {
      this.list[this.itemcheck].driving_license.img_1 = res.data.path;
       this.list[this.itemcheck].driving_license.imgd_1 = res.data.path;

    },
    getimgs(res, file, filelist) {
      this.list[this.itemcheck].driving_license.img_2 = res.data.path;
      this.list[this.itemcheck].driving_license.imgd_2 = res.data.path;
    },
    getown(res, file, filelist) {
      this.list[this.itemcheck].vehicle_owner.img_1 = res.data.path;
       this.list[this.itemcheck].vehicle_owner.imgd_1 = res.data.path;
    },
    getowns(res, file, filelist) {
      this.list[this.itemcheck].vehicle_owner.img_2 = res.data.path;
       this.list[this.itemcheck].vehicle_owner.imgd_2 = res.data.path;
    }
  },
  watch: {
    poker(newvalue, oldvalue) {
      if(newvalue.length==0)return;
      Axios("models/getvehiclelist", {
        brand_id: this.list[this.itemcheck].vehicle.brand
      }).then(res => {
        this.modellist = res.data.list;
        if (!this.brandkey) return;
        this.list[this.itemcheck].vehicle.vehicle_model = "";
        this.list[this.itemcheck].vehicle.model = "";
      });
    },
    vehicles(newvalue, oldvalue) {
      if (this.modelkey) {
        this.list[this.itemcheck].vehicle.vehicle_model = "";
      }
      if (this.list[this.itemcheck].vehicle.model== "") return;
      Axios("models/getvehiclemodellist", {
        brand_id: this.list[this.itemcheck].vehicle.brand,
        model_id: this.list[this.itemcheck].vehicle.model
      }).then(res => {
        let newlist = res.data.list;
        for (let i in newlist) {
          newlist[i].value = res.data.list[i].name;
          newlist[i].ids = res.data.list[i].id;
        }
        this.vehicle = newlist;
      });
    }
  },
  computed: {
    poker() {
      return this.list[this.itemcheck].vehicle.brand;
    },
    vehicles() {
      return this.list[this.itemcheck].vehicle.model;
    }
  },
  mounted() {
    this.uid=this.$route.query.uid;
    Axios("member/viewinfo", {
      uid: this.$route.query.uid
    }).then(res => {
      // 这里稍微加工下
      let options = [],
        newlist = res.data.list;
      for (let i in newlist) {
        newlist[i].vehicle.brand = (newlist[i].vehicle.brand);
        newlist[i].vehicle.model = (newlist[i].vehicle.model);

        newlist[i].vehicle.vehicle_model = (
          newlist[i].vehicle.vehicle_model
        );
        newlist[i].vehicle.insurance_expire =
          newlist[i].vehicle.insurance_expire_time>0?newlist[i].vehicle.insurance_expire_time * 1000:"";
        newlist[i].vehicle.next_limited_time =
          newlist[i].vehicle.next_limited_time?newlist[i].vehicle.next_limited_time * 1000:"";
        newlist[i].vehicle.next_maintain_time =
          newlist[i].vehicle.next_maintain_time>0?newlist[i].vehicle.next_maintain_time * 1000:"";
           newlist[i].vehicle.maintenance_km =
          newlist[i].vehicle.maintain_mileage_cycle;

          newlist[i].vehicle.maintenance_cycle =
          newlist[i].vehicle.maintain_time_cycle;

          newlist[i].vehicle.maintenance_next_km =
          newlist[i].vehicle.next_maintain_mileage;

          newlist[i].vehicle.maintenance_next_date =
          newlist[i].vehicle.next_maintain_time;

           newlist[i].vehicle.checkup_date =
          newlist[i].vehicle.next_limited_time>0?newlist[i].vehicle.next_limited_time:"";
          
          
       
      }
      for (let i in res.data.list) {
        options.push({
          value: i,
          text: res.data.list[i].vehicle_number,
          info: res.data.list[i]
        });
      }
      this.options = options;
      this.form = res.data.user;
      this.form.oldphone=res.data.user.phone;
      this.form.materialsid="0";
      this.itemcheck="0"
      this.list = newlist;
      let that = this;
      setTimeout(() => {
        that.modelkey = true;
        that.brandkey = true;
      }, 500);
    });
    Axios("models/getbrandlist").then(res => {
      this.brandlist = res.data.list;
    });
  },
  data() {
    return {
        uid:"",
        verify:{
            loading:false,
            text:"发送验证码",
            code:"",
        },
        BASE_API:process.env.BASE_API+'/common/upload',
      centerDialogVisible:{
          showkey:false,
          visible:false,
          form:{
              balance:"",
              integral:"",
              phone:"",
          }
      },  
      defaultimg: defaultimg,
      brandlist: [],
      modellist: [],
      preview: {
        key: false,
        img: ""
      },
     rules: {
        frame: [{ required: true, message: "请输入车牌号",trigger: "blur"}],
        license_plate: [{ required: true, message: "请输入车牌号",trigger: "blur"}],
      },
      centerforms: {
        integral: [{ required: true, message: "请输入积分",trigger: "blur"}],
        balance: [{ required: true, message: "请输入用户余额",trigger: "blur"},],
      },
      rule:{
          phone:[
            { required: true, message: '请输入手机号', trigger: 'blur' },
            { min: 11, max: 11, message: '手机号长度不合法', trigger: 'blur' }
          ], 
      },
      vehicle: [],
      brandkey: false,
      modelkey: false,
      list: [
        {
          vehicle_number: "鄂A93205",
          vehicle: {
            vehicle_id: 126,
            uid: 51,
            nickname: "",
            license_plate: "",
            frame: "",
            brand: "",
            model: "43",
            vehicle_model: "66",
            next_limited_time: "2019-07-01 00:00:00",
            insurance_expire_time: "2019-07-01 00:00:00",
            insurance_company: "",
            vehicle_type: null,
            maintain_mileage_cycle: 5000,
            maintain_time_cycle: 90,
            status:1,
            created_at: null,
            next_maintain_mileage: "26000",
            next_maintain_time: "2018-08-27 00:00:00",
            next_maintain_project: "38,30",
            current_mileage: 21000,
            weight: null
          },
          driving_license: {
            img_1: "",
            img_2: ""
          },
          vehicle_owner: {
            img_1: "",
            img_2: ""
          }
        }
      ],
      itemcheck: 0,
      options: [],
      form: {}
    };
  }
};
</script>

<style scoped lang='scss'>
.table-header {
  margin: 30px 0;
}
.el-input {
  width: 210px;
}
.upload {
  display: inline-block;
  margin-right: 40px;
  &:nth-of-type(2) {
    margin-right: 0;
  }
  .content {
    position: relative;
    width: 260px;
    height: 170px;
    border: 1px solid #ebebeb;
    margin-bottom: 20px;
    img {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
    }
  }
  &-demo {
    position: relative;
    width: 260px;
    height: 170px;
    text-align: center;
  }
  &-title {
    margin-bottom: 30px;
    margin-top: 20px;
  }
}
</style>