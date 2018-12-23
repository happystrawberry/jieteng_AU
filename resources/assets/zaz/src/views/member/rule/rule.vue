<template>
    <div class="zone-content table">
      <div class="table-header">
          <h3>积分规则</h3>
          <div>
            <el-button size="mini" @click="addmaterials()" type="primary">添加规则</el-button>
          </div>
      </div>
      <el-table
                    ref="multipleTable"
                    border
                    :data="multipleTable"
                    tooltip-effect="dark"
                    v-loading="loading"
                    style="width:1200px"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="创建时间"
                    header-align="center"
                    align="center"
                    prop="rule_name"
                    width="180">
                    <template slot-scope="scope">
                      {{checkdate(scope.row.dateline)}}
                    </template>
                    </el-table-column>
                    
                    <el-table-column
                    label="规则名称"
                    header-align="center"
                    align="center"
                    prop="rule_name"
                    width="100">
                    </el-table-column>
                    <el-table-column
                    header-align="center"
                    align="center"
                    label="积分范围"
                    width="200">
                    <template slot-scope="scope">
                     {{scope.row.min_credit}}---{{scope.row.max_credit}}
                    </template>
                    </el-table-column>                  
                    <el-table-column
                    prop="discount"
                    header-align="center"
                    align="center"
                    label="折扣"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="specification"
                    header-align="center"
                    align="center"
                    label="操作"
                    >
                    <template slot-scope="scope">
                        <el-button size="mini" type="primary" @click="addmaterials(scope)">编辑</el-button>
                        <el-popover
                            placement="top"
                            width="160"
                            v-model="scope.row.visible">
                            <p>确定删除该计分规则吗?</p>
                            <div style="text-align: right; margin: 0">
                              <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                              <el-button type="primary" size="mini" @click="danger(scope)">确定</el-button>
                            </div>
                            <el-button slot="reference" type="danger" size="mini" >删除</el-button>
                          </el-popover>
                    </template>
                    </el-table-column>
                </el-table
                >
                <el-dialog title="编辑规则" 
                width="500px"
                
                :visible.sync="dialogFormVisible.showkey">
                  <el-form 
                  ref="rule"
                :rules="rules"
                  :model="dialogFormVisible.form">
                    <el-form-item label="规则名称" label-width="120px" prop="rule">
                      <el-input style="width:250px;" v-model="dialogFormVisible.form.rule" auto-complete="off" placeholder="单行输入"></el-input>
                    </el-form-item>
                     <el-form-item label="计分范围" label-width="120px" prop="scope">
                      <el-input style="width:100px !important" v-model="dialogFormVisible.form.min_credit" auto-complete="off" placeholder="单行输入"></el-input>
                      <span style="margin:0 15px;">到</span>
                       <el-input style="width:100px !important" v-model="dialogFormVisible.form.max_credit" auto-complete="off" placeholder="单行输入"></el-input>
                    </el-form-item>
                    <el-form-item label="折扣" label-width="120px" prop="discount" class="other">
                       <el-input style="width:250px;" max="10" min="0" v-model="dialogFormVisible.form.discount" auto-complete="off" placeholder="请输入折扣"></el-input>
                    </el-form-item>
                  </el-form>
                  <div slot="footer" class="dialog-footer" style="text-align:center;">
                    <el-button type="primary" @click="overadd()">保存</el-button>
                  </div>
                </el-dialog>
      <pageing  @pagechange="pagechange"  :pageing="page"></pageing>         
    </div>
</template>

<script>
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";
import Pageing from "@/components/Paging/Paging";
export default {
   components: {
    Pageing
  },
  mounted() {
    this.getlist();
  },
  methods: {
    handleSelectionChange() {
      console.log(23123);
    },
    pagechange(value){
      this.getlist({
        page:value
      })
    },
    addmaterials(scope) {
      console.log(scope)
      this.dialogFormVisible.showkey = true;
            let el=this.dialogFormVisible.form,
                that=this;
      // 这里先进进行下赋值
      console.log(scope)
      el.id=scope==undefined?'0':scope.row.id;
      el.rule=scope==undefined?'':scope.row.rule_name
      el.min_credit=scope==undefined?'':scope.row.min_credit
      el.max_credit=scope==undefined?'':scope.row.max_credit
      el.discount=scope==undefined?10:scope.row.discount;
      // this.$nextTick(()=>{
      //     that.$refs.rule.resetFields();
      // })
    },
    checkdate(time){
      return parseTime(time)
    },
    danger(scope) {
        let index=this.multipleTable.findIndex((el)=>{
           return el.id=scope.row.id;
        })
        Axios("member/deletecreditrule",{
          id:scope.row.id
        }).then(res=>{
          this.$message({
          showClose: true,
          message: '删除成功',
          type: 'success'
        });
          this.multipleTable.splice(index,1)
        })
    },
    formatTooltip(val) {
      return val / 100;
    },
    getlist(params){
      this.loading=true;
      Axios("member/getcreditrulelist",params).then(res=>{
          let list=res.data.list;
          for(let i in list){
            list[i].visible=false;
        }
         let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        this.multipleTable = list;
        this.$message({
          showClose: true,
          message: '数据已更新',
          type: 'success'
        });
        this.loading=false;
      })
    },
    overadd() {
      let el=this.dialogFormVisible;
      
       this.$refs.rule.validate((valid) => {
          if (valid) {
             this.dialogFormVisible.showkey = false;
             Axios("member/savecreditrule", {
        id: el.form.id,
        rule_name:el.form.rule,
        min_credit: el.form.min_credit,
        max_credit: el.form.max_credit,
        discount:el.form.discount
      }).then(res=>{
           this.$message({
          showClose: true,
          message: `${el.form.id==0?'新增':'保存'}规则成功`,
          type: 'success'
        });
        this.getlist();
        el.showkey=false
      });
          } else {
           
            return false;
          }
        });
     
    }
  },
  data() {
    var validatePass = (rule, value, callback) => {
      console.log(value);
      if (this.dialogFormVisible.form.max_credit == "" || this.dialogFormVisible.form.min_credit == "") {
        callback(new Error("请输入合法的计分范围"));
      } else {
        callback();
      }
    };
    var validiscount=(rule,value,callback)=>{
      if(parseFloat(value)<0||parseFloat(value)>10){
         callback(new Error("折扣应在0-10之间"));
      }else{
        callback();
      }
    }
    return {
      rules: {
        rule: [{ required: true, message: "请输入活动名称", trigger: "blur" }],
        scope: [{ required: true, validator: validatePass, trigger: "blur" }],
        discount: [{ required: true, validator: validiscount, trigger: "blur" }],
      },
      page:{},
      loading:true,
      dialogFormVisible: {
        showkey: false,
        form: {
          id:"",
          rule: "",
          min_credit:"",
          max_credit:"",
          starscope: "",
          endscope: "",
          discount: 0
        }
      },
      multipleTable: [
        
      ]
    };
  }
};
</script>

<style scoped lang='scss'>
.table-header {
  margin: 30px 0;
}

.other {
  span {
    line-height: 40px;
    padding:0 20px;
    width:50px;
    box-sizing:border-box;
    display: inline-block;
    transform: translateY(-15px);
  }
  .slider {
    display: inline-block;
    width: 230px;
  }
}
</style>