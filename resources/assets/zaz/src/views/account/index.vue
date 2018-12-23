<template>
   <div class="zone-content">
       <el-form :inline="true" @submit.native.prevent :model="formInline" class="demo-form-inline"> 
            <el-form-item >
                <el-input size="mini" v-model="formInline.user" @keyup.enter.native="search" placeholder="请输入电话或姓名"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button size="mini" type="primary"  @click="search">搜索</el-button>
            </el-form-item>
        </el-form>
        <div class="table">
            <div class="table-header" style="margin:30px 0;">
                <h3>系统登录人员列表</h3>
            </div>
                <el-table
                    ref="multipleTable"
                    border
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width:1300px"
                    row-class-name=""
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="序号"
                    header-align="center"
                    align="center"
                    prop="id"
                    width="120">
                    </el-table-column>
                    <el-table-column
                    prop="username"
                    header-align="center"
                    align="center"
                    label="姓名"
                    width="120">
                    </el-table-column>
                    <el-table-column
                    prop="group_name"
                    header-align="center"
                    align="center"
                    label="部门"
                    width="200"
                    >
                     <template slot-scope="scope">
                        <el-tag type="success" v-if="scope.row.group_name.length==0">未分配</el-tag>
                        <el-tag type="success" style="margin-right:10px;margin-bottom:10px;" v-if="scope.row.group_name.length>1" v-for="(item,index) in scope.row.group_name.split(',')" :key="index">{{item}}</el-tag>
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="phone"
                    header-align="center"
                    align="center"
                    label="手机"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="notes"
                    header-align="center"
                    align="center"
                    label="备注"
                    show-overflow-tooltip
                    >
                     <template slot-scope="scope">
                        <el-tag type="success">{{scope.row.notes==""?'暂无':scope.row.notes}}</el-tag>
                    </template>
                    </el-table-column>
                     <el-table-column
                    header-align="center"
                    align="center"
                    label="状态"
                    width="120"
                    >
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.enable==0?'danger':'success'" >{{scope.row.enable==0?'禁用':'开启'}}</el-tag>
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="specification"
                    header-align="center"
                    align="center"
                    label="操作"
                    >
                    <template slot-scope="scope">
                        <el-button size="mini" type="primary" @click="addmaterials(scope.row,1)">编辑</el-button>
                        <el-button size="mini" type="primary" @click="addmaterials(scope.row,2)" >修改密码</el-button>
                        <el-popover
                          placement="top"
                          width="160"
                          v-model="scope.row.visible">
                          <p>真的要{{scope.row.enable==0?'恢复':'禁用'}}{{scope.row.username}}吗?</p>
                          <div style="text-align: right; margin: 0">
                            <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                            <el-button type="primary" size="mini" @click="surecheck(scope)">确定</el-button>
                          </div>
                          <el-button slot="reference" :type="scope.row.enable==0?'success':'danger'" size="mini">{{scope.row.enable==0?'开启':'禁用'}}</el-button>
                        </el-popover>
                    </template>
                    </el-table-column>
                </el-table>
            </div>
            <el-dialog 
       :title="title[dialogFormVisible.type]"
       width="50%"
       :visible.sync="dialogFormVisible.showkey">
        <el-form :model="dialogFormVisible.form"
            label-position="left">
            <el-row>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="姓名" label-width="70px">
                      <span >{{dialogFormVisible.form.username}}</span>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="用户组" label-width="70px">
                     <span v-if="dialogFormVisible.type==2" >{{dialogFormVisible.form.group_name.length>0?dialogFormVisible.form.group_name:"未分配"}}</span>
                      <el-select v-else multiple v-model="dialogFormVisible.form.group_id" @change="change"  placeholder="请选择部门" >
                        <el-option
                          v-for="(item,index) in group"
                          :key="index"
                          :label="item"
                          :value="index">
                        </el-option>
                      </el-select>
                        <!-- <el-input v-model="dialogFormVisible.form.department" placeholder="请输入部门" auto-complete="off"></el-input> -->
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="手机" label-width="70px">
                        <span >{{dialogFormVisible.form.phone}}</span>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="备注" label-width="70px">
                        <span v-if="dialogFormVisible.type==2" >{{dialogFormVisible.form.notes==''?'暂无':dialogFormVisible.form.notes}}</span>
                        <el-input style="width:210px;" v-else  v-model="dialogFormVisible.form.notes" placeholder="请输入备注" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col v-show="dialogFormVisible.type!=1" :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12} v-if="dialogFormVisible.type!=0">
                    <el-form-item label="修改密码" label-width="70px">
                        <el-input style="width:210px" v-model="dialogFormVisible.form.password"  :type="pwdType" placeholder="请输入密码" auto-complete="off"></el-input>
                         <span class="show-pwd" @click="showPwd"><svg-icon icon-class="eye" /></span>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="管理组" label-width="70px">
                       <el-switch
                        :disabled="dialogFormVisible.type==2?true:false"
                        v-model="dialogFormVisible.form.is_admin"
                        active-color="#13ce66"
                        inactive-color="#ff4949"
                        active-value="1"
                        inactive-value="0">
                      </el-switch>
                      <span style="margin-left:20px;">{{dialogFormVisible.form.is_admin==1?'超级管理员':'普通权限'}}</span>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="状态" label-width="70px">
                       <el-switch
                       :disabled="dialogFormVisible.type==2?true:false"
                        v-model="dialogFormVisible.form.enable"
                        active-color="#13ce66"
                        inactive-color="#ff4949"
                        active-value="1"
                        inactive-value="0">
                      </el-switch>
                      <span style="margin-left:20px;">{{dialogFormVisible.form.enable==1?'开启':'禁用'}}</span>
                    </el-form-item>
                </el-col>
                <el-col style="text-align:center">
                   <el-button type="primary" @click="saveaccount()">保存</el-button>
                </el-col>
            </el-row>
        </el-form>
        </el-dialog>
          <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
        </div>
</template>

<script>
import { Axios } from "@/api/login";
import Pageing from "@/components/Paging/Paging";

export default {
  components: {
    Pageing
  },
  methods: {
    showPwd() {
      if (this.pwdType === "password") {
        this.pwdType = "";
      } else {
        this.pwdType = "password";
      }
    },
    change(value) {
      console.log(value);
      this.dialogFormVisible.form.group_id = value;
      let list = [];
      for (let i in value) {
        list.push(this.group[value[i]]);
      }
      this.dialogFormVisible.form.group_name = list;
    },
    handleSelectionChange() {
      console.log(23123);
    },
    surecheck(scope) {
      scope.row.visible = false;

      Axios("/account/switchaccountstatus", { id: scope.row.id }).then(res => {
        scope.row.enable = res.data.enable;
        this.$message({
          showClose: true,
          message:
            res.data.enable == 0
              ? `禁用${scope.row.username}操作成功`
              : `恢复${scope.row.username}操作成功`,
          type: "success"
        });
      });
    },
    addmaterials(value, index) {
      console.log(value.group_id,value.group_name)
      let newgroupid=value.group_id;
      if(newgroupid.length==0){
        newgroupid=[]
      }else{
        for(let i in newgroupid){
          newgroupid[i]=newgroupid[i]+"";
        }
      }
      console.log(213)
      this.dialogFormVisible.form.phone = value.phone;
      this.dialogFormVisible.form.notes = value.notes;
      this.dialogFormVisible.form.password = value.password;
      this.dialogFormVisible.form.isadmin = value.isadmin;
      this.dialogFormVisible.form.enable = value.enable;
      this.dialogFormVisible.form.group_id = newgroupid;
      this.dialogFormVisible.form.id = value.id;
      this.dialogFormVisible.form.username = value.username;
      this.dialogFormVisible.form.visible = value.visible;
      this.dialogFormVisible.form.is_admin = value.is_admin;
      this.dialogFormVisible.form.group_name = value.group_name.length==0?[]:value.group_name;
      this.dialogFormVisible.showkey = true;
      this.dialogFormVisible.type = index;
      console.log(1111)
    },
    saveaccount(type) {
      let that = this;
      this.dialogFormVisible.showkey = false;
      let el=this.dialogFormVisible.form;
      Axios("/account/saveaccount", {
        serialnum: el.serialnum,
        name:el.name,
        group_name:el.group_name,
        group_id:el.group_id.join(","),
        department: el.department,
        phone: el.phone,
        is_admin: el.is_admin,
        enable: el.enable,
        position: el.position,
        remark: el.remark,
        verification: el.verification,
        status: el.status,
        password:el.password,
        admin: el.admin,
        notes: el.notes,
        id: el.id,
        username: el.username,
        visible: el.visible
      }).then(res => {
        let index = this.multipleTable.findIndex(age => {
          return age.id == that.dialogFormVisible.form.id;
        });
        this.multipleTable[index] = this.dialogFormVisible.form;
        this.$message({
          showClose: true,
          message:
            this.dialogFormVisible.type == 1
              ? `保存${this.dialogFormVisible.form.username}信息成功`
              : `修改${this.dialogFormVisible.form.username}密码成功`,
          type: "success"
        });
        if( this.dialogFormVisible.type == 1){
          this.getaccountlist({
        page: this.page.curpage,
        phone: /^[0-9]{11}$/.test(this.formInline.user)
          ? this.formInline.user
          : "",
        username: /^[0-9]{11}$/.test(this.formInline.user)
          ? ""
          : this.formInline.user
      });
        }
        
      });
    },
    search() {
      // if(this.formInline.user.length==0){
      //   this.$message({
      //     message: '手机号或用户名为空,请先填写',
      //     type: 'warning'
      //   });
      //   return;
      // }
      this.getaccountlist({
        page: 1,
        phone: /^[0-9]{11}$/.test(this.formInline.user)
          ? this.formInline.user
          : "",
        username: /^[0-9]{11}$/.test(this.formInline.user)
          ? ""
          : this.formInline.user
      });
    },
    pagechange(value) {
      this.getaccountlist({
        page: value,
        phone: /^[0-9]{11}$/.test(this.formInline.user)
          ? this.formInline.user
          : "",
        username: /^[0-9]{11}$/.test(this.formInline.user)
          ? ""
          : this.formInline.user
      });
    },
    getaccountlist(param) {
      Axios("/account/accountlist", param).then(res => {
        console.log(res.data.list);
        let newlist = res.data.list;
        for (let i in newlist) {
          newlist[i].visible = false;
        }
        this.multipleTable = newlist;
        let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
       
      });
    }
  },
  data() {
    return {
      formInline: {
        user: ""
      },
      pwdType: "password",
      title: ["添加人员", "编辑人员", "修改密码"],
      page: {},
      group: [],
      dialogFormVisible: {
        showkey: false,
        type: 0,
        form: {
          serialnum: "",
          name: "",
          group_name: "",
          group_id: "",
          department: "",
          phone: "",
          is_admin: "",
          enable: "",
          position: "",
          remark: "",
          password: "",
          verification: "",
          status: "",
          admin: ""
        }
      },
      multipleTable: []
    };
  },
  mounted() {
    //读取用户组信息
    Axios("/account/getgrouplist").then(res => {
      console.log(res.data.list);
      this.group = res.data.list;
    });
    //读取用户信息
    this.getaccountlist({
      page: 1
    });
  },
  name: "chexing"
};
</script>

<style  lang="scss">
.show-pwd {
  position: absolute;
  top: 0;
  left: 180px;
  z-index: 10;
  cursor: pointer;
}
.el-dialog {
  user-select: none;
}
.otheritem {
  display: flex;
  .el-form-item__content {
    display: flex;
    margin-left: 0;
    .el-button {
      margin-left: 20px;
    }
  }
  .el-form-item__label {
    flex: 0 0 70px;
  }
  .el-input__inner {
    width: 140px !important;
  }
}
</style>
