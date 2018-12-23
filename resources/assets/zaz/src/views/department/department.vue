<template>
    <div class="zone-content">
        <div class="table">
          <div class="table-header">
            <h3>部门组列表</h3>
            <div>
              <el-button size="mini" type="primary" @click="addmaterials(detailform)">添加部门</el-button>
            </div>
          </div>
                <el-table
                    ref="multipleTable"
                    border
                    v-loading="loadings"
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width:100%"

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
                    prop="group_name"
                    header-align="center"
                    align="center"
                    label="部门"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="phone"
                    header-align="center"
                    align="center"
                    label="可查看列表组"
                    >
                    <template slot-scope="scope">
                        <el-tag style="margin:5px" type="success" v-for="(item,index) in scope.row.taglist" :key="index">{{item.text}}</el-tag>
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="types"
                    header-align="center"
                    align="center"
                    label="业务类型分组"
                    show-overflow-tooltip
                    >
                     <template slot-scope="scope">
                        <el-tag style="margin:0 10px" type="success" v-for="(item,index) in scope.row.types" :key="index">{{item}}</el-tag>
                    </template>
                    </el-table-column>

                    <el-table-column
                    prop="specification"
                    header-align="center"
                    align="center"
                    label="操作"
                    width="200"
                    >
                    <template slot-scope="scope">
                        <el-button size="mini" type="primary" @click="addmaterials(scope.row,1)">编辑</el-button>
                        <el-popover
                          placement="top"
                          width="160"
                          v-model="scope.row.visible">
                          <p>真的要{{scope.row.enable==0?'恢复':'禁用'}}{{scope.row.username}}吗?</p>
                          <div style="text-align: right; margin: 0">
                            <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                            <el-button type="primary" size="mini" @click="surecheck(scope)">确定</el-button>
                          </div>
                          <el-button slot="reference" :type="scope.row.enable==0?'success':'danger'" size="mini">{{scope.row.enable==0?'恢复':'禁用'}}</el-button>
                        </el-popover>
                    </template>
                    </el-table-column>
                </el-table>
        </div>
         <el-dialog 
       :title="dialogFormVisible.form.id==0?'新建部门':'修改部门'"
       width="50%"
       :visible.sync="dialogFormVisible.showkey">
        <el-form 
            :model="dialogFormVisible.form"
            :rules="rules2" 
            ref="ruleForm2" 
            label-position="left">
            <el-row>
                 <el-col>
                    <el-form-item label="部门名称" label-width="140px" prop="group_name">
                      <el-input style="width:210px;" v-model="dialogFormVisible.form.group_name"></el-input>
                    </el-form-item>
                </el-col>
                 <el-col>
                    <el-form-item label="可查看列表组" label-width="140px">
                         <el-checkbox-group v-model="dialogFormVisible.form.newlist">
                          <el-checkbox 
                          v-for="(val,key,index) in getpermslist"
                          :key="index"
                          :label="val" 
                          :name="val"
                          ></el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>
                </el-col>
                <el-col>
                    <el-form-item label="业务类型分组" label-width="140px">
                         <el-checkbox-group v-model="dialogFormVisible.form.type">
                          <el-checkbox 
                          v-for="(item,index) in typelist"
                          :key="index"
                          :label="item.name" 
                          :name="item.value"
                          ></el-checkbox>
                        </el-checkbox-group>
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
import Pageing from "@/components/Paging/Paging";
import { Axios } from "@/api/login";

export default {
  components: {
    Pageing
  },
  data() {
    var validatePass = (rule, value, callback) => {
      if (!value) {
        return callback(new Error("部门名称不能为空"));
      } else {
        callback();
      }
    };
    return {
      rules2: {
        group_name: [{ validator: validatePass, trigger: "blur" }]
      },
      loadings:true,
      detailform: {
        id: 0,
        group_name: "",
        taglist: [],
        region: [],
        newlist: [],
        params: [],
        perms: "",
        type: []
      },
      multipleTable: [],
      getpermslist: {},
      page: {},
      typelist: [],
      title: ["修改部门", "新建部门"],
      dialogFormVisible: {
        showkey: false,
        form: {
          id: "",
          group_name: "",
          taglist: [],
          region: [],
          newlist: [],
          params: [],
          perms: "",
          type: []
        }
      }
    };
  },
  methods: {
    addmaterials(value,index) {
      // 这里为了防止数据联动变化,重新赋值
    
        for (let i in this.dialogFormVisible.form) {
          this.dialogFormVisible.form[i] = value[i];
        }
      this.dialogFormVisible.showkey = true;
    },
    surecheck(scope){
      console.log(scope.row.id)
       Axios("account/switchGroupStatus",{
           id:scope.row.id
       }).then(res=>{
         scope.row.visible=false;
          scope.row.enable=res.data.enable;
       })
    },
    saveaccount() {
      let ele = this.dialogFormVisible.form,
        that = this;
      this.$refs.ruleForm2.validate(valid => {
        if (valid) {
          // 这里判断下
          if (this.dialogFormVisible.form.newlist.length == 0) {
            this.$message({
              showClose: true,
              message: "请先勾选可查看列表组",
              type: "warning"
            });
            return;
          }
          if (ele.type.length == 0) {
            this.$message({
              showClose: true,
              message: "请先勾选业务类型分组",
              type: "warning"
            });
            return;
          }
          //这里再最后转换下
          let newperms = [],
            newitems = [],
            types = [];
          for (let i in this.getpermslist) {
            if (ele.newlist.indexOf(this.getpermslist[i]) > -1) {
              newperms.push(i);
            }
          }
          console.log("type", ele.type);
          for (let i in this.typelist) {
            if (ele.type.indexOf(this.typelist[i].name) > -1) {
              newitems.push(this.typelist[i].id);
              types.push(this.typelist[i].name);
            }
          }
          ele.types = types;
          ele.perms = newperms.join(",");
          Axios("/account/savegroup", {
            id: ele.id,
            group_name: ele.group_name,
            items: newitems.join(","),
            perms: newperms.join(",")
          }).then(res => {
            this.$message({
              showClose: true,
              message: ele.id ? "修改用户组成功" : "新建用户组成功",
              type: "success"
            });
            //修改用户组状态
            if (ele.id!=0) {
              let index = this.multipleTable.findIndex(el => {
                return (el.id == ele.id);
              });
              for (let i in this.multipleTable[index]) {
                if (ele[i]) {
                  this.multipleTable[index][i] = ele[i];
                }
              }
              console.log(this.multipleTable[0].type, ele.type);
              that.checkstatsu(this.multipleTable);
            }else{
              //这里默认新建的适合重新请求数据
              this.getlist();
            }

            this.dialogFormVisible.showkey = false;
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    handleSelectionChange() {
      console.log("啦啦啦");
    },
    pagechange(value) {
      this.getlist({
        page: value
      });
    },
    checkstatsu(data) {
      for (let a in this.multipleTable) {
        let list = data[a].perms.split(","),
          newlist = [],
          newlists = [];
        this.multipleTable[a].params = list;
        for (let i in list) {
          newlist.push({
            text: this.getpermslist[list[i]],
            value: list[i]
          });
          newlists.push(this.getpermslist[list[i]]);
        }
        let lists = [];
        for (let i in this.multipleTable[a].types) {
          lists.push(this.multipleTable[a].types[i]);
        }
        this.multipleTable[a].taglist = newlist;
        this.multipleTable[a].type = lists;
        this.multipleTable[a].newlist = newlists;
      }
      console.log(this.multipleTable[0]);
    },
    getlist(params) {
      Axios("/account/grouplist", params).then(res => {
        this.multipleTable = res.data.list;
         let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        this.checkstatsu(res.data.list);
        this.loadings=false;
      });
    }
  },
  mounted() {
    //获取权限功能列表
    Axios("/account/getpermslist").then(res => {
      this.getpermslist = res.data.list;
      this.getlist();
    });
    //获取typs列表
    Axios("/common/getoptypelist").then(res => {
      this.typelist = res.data.list;
    });
  }
};
</script>

<style scoped lang='scss'>
.table-header {
  margin-bottom: 30px;
}
.el-checkbox + .el-checkbox {
  margin-left: 0px;
}
.el-checkbox {
  margin-right: 30px;
}
</style>