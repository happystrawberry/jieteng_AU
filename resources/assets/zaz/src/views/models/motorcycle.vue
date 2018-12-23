<template>
   <div class="zone-content">
       <Modelsheader :formInline="formInline" @search="search"></Modelsheader>
        <div class="table">
            <div class="table-header" style="margin:30px 0;">
                <h3>车型列表</h3>
                <div>
                    <el-button type="primary" size="mini" @click="addmaterials">添加车型</el-button>
                </div>
            </div>
                <el-table
                    ref="multipleTable"
                    border
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width: 800px"
                    v-loading="loading"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="编号"
                    header-align="center"
                    align="center"
                    prop="id"
                    width="200">
                    </el-table-column>
                    <el-table-column
                    prop="materialsid"
                    header-align="center"
                    align="center"
                    label="品牌"
                    width="200">
                     <template slot-scope="scope">
                       {{conversion(scope.row.brand_id)}}
                     </template> 
                    </el-table-column>
                    <el-table-column
                    prop="vehicle_name"
                    header-align="center"
                    align="center"
                    label="车型"
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
                        <el-button size="mini"   type="primary" @click="compiled(scope)">编辑</el-button>
                       <el-popover
                            placement="top"
                            width="160"
                            v-model="scope.row.visible">
                            <p>确认删除{{scope.row.vehicle_name}}车型吗？</p>
                            <div style="text-align: right; margin: 0">
                                <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                                <el-button type="primary" size="mini" @click="suredetale(scope)">确定</el-button>
                            </div>
                            <el-button slot="reference" size="mini" type="danger">删除</el-button>
                            </el-popover>
                    </template>
                    </el-table-column>
                </el-table>
            </div>
            <el-dialog 
       :title="compile?'编辑车型':'添加车型'"
       width="50%"
       :visible.sync="dialogFormVisible.showkey">
        <el-form :model="dialogFormVisible.form"
            label-position="left">
            <el-row>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="品牌" label-width="140px">
                        <el-select filterable v-model="dialogFormVisible.form.brand_id" placeholder="请选择类别">
                            <el-option 
                            v-for="(value,key,index) in brandname"
                            :key="index"
                            :label="value.name" 
                            :value="value.id"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="车型" label-width="140px">
                        <el-input style="width:210px;" v-model="dialogFormVisible.form.vehicle_name" placeholder="请输入具体型号" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col style="text-align:center">
                    <el-button type="primary" @click="overadd()">{{compile?'确定':'添加'}}</el-button>
                    <el-button type="primary"  @click="dialogFormVisible.showkey=false">取消</el-button>
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
import Modelsheader from "./modelsheader";
export default {
  components: {
    Modelsheader,
    Pageing
  },
  mounted() {
    this.getlist();
    //获取品牌库
    Axios("models/getbrandlist").then(res=>{
      this.brandname=res.data.list;
    })
  },
  methods: {
    handleSelectionChange() {
      console.log(23123);
    },
    compiled(scope){
       this.compile=true;
       let ele=this.dialogFormVisible;
       ele.showkey=true;
       for(let i in ele.form){
          ele.form[i]=scope.row[i];
       }
    },
    suredetale(scope){
       scope.row.visible=false;
       Axios("models/deletetype",{
           id:scope.row.id
       }).then(res=>{
           let index=this.multipleTable.findIndex((el)=>{
               return  el.id==scope.row.id;
           })
           this.multipleTable.splice(index,1);
          this.$message({
          showClose: true,
          message: '删除车型成功',
          type: 'success'
        });
       })
    },
    conversion(el){
      console.log(el)
      for(let i in this.brandname){
        console.log(this.brandname[i].id,el)
        if(this.brandname[i].id==el){
          return this.brandname[i].name
        }
      }
    },
    pagechange(value) {
      this.getlist({
           query:this.formInline.user,
           page:value
       })
    },
    search(value) {
       this.formInline.user=value.user;
       this.getlist({
           query:value.user,
           page:1
       })
    },
    addmaterials() {
      this.dialogFormVisible.showkey = true;
       this.compile=false;
      for(let i in this.dialogFormVisible.form){
        this.dialogFormVisible.form[i]=""
      }
    },
    overadd() {
      this.dialogFormVisible.showkey = false;
       Axios("models/savevehicle",{
         id:this.dialogFormVisible.form.id,
          brand_id :this.dialogFormVisible.form.brand_id,
          vehicle_name:this.dialogFormVisible.form.vehicle_name,
      }).then((res)=>{
          this.$message({
          message: `恭喜,${this.compile?'保存':'新建'}品牌成功`,
          type: 'success'
        });

        if(!this.compile){
             this.getlist();
        }else{
          let index=this.multipleTable.findIndex((el)=>{
            return el.brand_id=this.dialogFormVisible.form.brand_id;
          })
          if(index>-1){
            this.multipleTable[index].vehicle_name=this.dialogFormVisible.form.vehicle_name
          }
        }
      })
    },
    getlist(params) {
      this.loading = true;
      Axios("models/vehiclelist", params).then(res => {
        this.loading = false;
        let list = res.data.list;
        for (let i in res.data.list) {
          list[i].visible = false;
        }
        this.multipleTable = list;
        let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        this.$message({
          showClose: true,
          message: "列表数据已更新",
          type: "success"
        });
      });
    }
  },
  data() {
    return {
      formInline: {
        user: "",
      },
      title:"",
      compile:false,
      brandname:{},
      loading: true,
      page: {},
      dialogFormVisible: {
        showkey: false,
        form: {
          id:"",
          brand_id:"",
          vehicle_name:""
        }
      },
      multipleTable: []
    };
  },
  name: "chexing"
};
</script>

<style scoped>
</style>
