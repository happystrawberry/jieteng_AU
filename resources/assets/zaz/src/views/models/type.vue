<template>
   <div class="zone-content">
       <Modelsheader :formInline="formInline" @search="search" ></Modelsheader>
        <div class="table">
            <div class="table-header" style="margin:30px 0;">
                <h3>型号列表</h3>
                <div>
                    <el-button type="primary" size="mini" @click="addmaterials">添加型号</el-button>
                </div>
            </div>
                <el-table
                    ref="multipleTable"
                    v-loading="loading"
                    border
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width:1200px"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="编号"
                    header-align="center"
                    align="center"
                    width="100"
                    prop="brand_id"> 
                    </el-table-column>
                    <el-table-column
                    prop="brand_name"
                    header-align="center"
                    align="center"
                    label="品牌"
                    width="200">
                    <template slot-scope="scope">
                        <el-tag  type="success">{{scope.row.brand_name}}</el-tag>
                    </template>
                    </el-table-column>

                    <el-table-column
                    prop="materialsnum"
                    header-align="center"
                    align="center"
                    label="车型"
                    width="200"
                    >
                    <template slot-scope="scope">
                        <el-tag  type="success">{{scope.row.vehicle_name}}</el-tag>
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="year"
                    header-align="center"
                    align="center"
                    label="年份"
                    width="100"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="model_class"
                    header-align="center"
                    align="center"
                    label="具体型号"
                    width="200"
                    >
                     <template slot-scope="scope">
                        {{scope.row.model_name?scope.row.model_name:'暂无'}}
                    </template>
                    </el-table-column>
                    <el-table-column
                    prop="specification"
                    header-align="center"
                    align="center"
                    label="操作"
                    >
                    <template slot-scope="scope">
                        <el-button size="mini" type="primary" @click="compiled(scope)">编辑</el-button>
                        <el-popover
                            placement="top"
                            width="160"
                            v-model="scope.row.visible">
                            <p>确认删除{{scope.row.vehicle_name}}车型吗？</p>
                            <div style="text-align: right; margin: 0">
                                <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                                <el-button type="primary" size="mini" @click="suredetale(scope)">确定</el-button>
                            </div>
                            <el-button slot="reference" type="danger" size="mini">删除</el-button>
                            </el-popover>
                    </template>
                    </el-table-column>
                </el-table>
            </div>
            <el-dialog 
       title="添加型号"
       width="50%"
       :visible.sync="dialogFormVisible.showkey">
        <el-form :model="dialogFormVisible.form"
            ref="diaform"
            label-position="left">
            <el-row>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="品牌" label-width="140px">
                        <el-select  @change="changlabel" v-model="dialogFormVisible.form.brand_id" placeholder="请选择类别">
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
                        <el-select no-match-text=""  v-model="dialogFormVisible.form.vehicle_model_id" placeholder="请选择类别">
                            <el-option 
                            v-for="(value,key,index) in brandlist"
                            :key="index"
                            :label="value.name" 
                            :value="value.id"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="年份" label-width="140px">
                        <el-input style="width:195px;" v-model="dialogFormVisible.form.year" placeholder="请输入年份" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                 <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="具体型号" label-width="140px">
                        <el-input style="width:195px;" v-model="dialogFormVisible.form.model_name" placeholder="请输入具体型号" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="车辆等级" label-width="140px">
                        <el-select v-model="dialogFormVisible.form.model_class" placeholder="车辆等级">
                        <el-option
                        v-for="item in modelclass"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">
                        </el-option>
                    </el-select>
                    </el-form-item>
                </el-col>
                <el-col style="text-align:center">
                     <el-button type="primary" size="mini" @click="overadd()">{{this.compile?'确认':'添加'}}</el-button>
                     <el-button type="info" size="mini" @click="dialogFormVisible.showkey=false">取消</el-button>
                </el-col>
            </el-row>
        </el-form>
        </el-dialog>
              <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>

        </div>
</template>

<script>
import Pageing from "@/components/Paging/Paging";
import Modelsheader from './modelsheader';

import { Axios } from "@/api/login";

export default {
    components: {
    Pageing,
    Modelsheader
  },
  watch:{
      poker(newvalue,oldvalue){
          if(newvalue.length==0)return;
        Axios("models/getvehiclelist",{
           brand_id:this.dialogFormVisible.form.brand_id,
       }).then(res=>{
           this.brandlist=res.data.list
       })
      }
  },
  computed:{
      poker(){
          return this.dialogFormVisible.form.brand_id
      }
  },
  mounted(){
      this.getlist();
      Axios("models/getbrandlist").then(res=>{
      this.brandname=res.data.list;
    })
  },

  methods: {
    handleSelectionChange() {
    },
    changlabel(){
        this.dialogFormVisible.form.vehicle_model_id="";
    },
    compiled(scope){
       this.compile=true;
       let ele=this.dialogFormVisible;
       ele.showkey=true;
       for(let i in ele.form){
          ele.form[i]=scope.row[i];
       }
       ele.form.vehicle_model_id=scope.row.vehicle_model_id;
       ele.form.model_class=scope.row.model_class;
       if(ele.brand_id.length==0)return;
       Axios("models/getvehiclelist",{
           brand_id:scope.row.brand_id,
       }).then(res=>{
           this.brandlist=res.data.list
       })
    },
    search(value){
        console.log(this.formInline.user)
        this.getlist({
           query:this.formInline.user,
           page:1
       })
    },
    pagechange(value){
       this.getlist({
           query:this.formInline.user,
           page:value
       })
    },
    conversion(el){
      for(let i in this.brandname){
        if(this.brandname[i].id==el){
          return this.brandname[i].name
        }
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
    addmaterials(){
        let that=this;
        this.compile=false;
          this.dialogFormVisible.showkey=true;
          this.dialogFormVisible.form={
                "id":0,
                "brand_id":"",
                "vehicle_id":"",
                "model_year":"",
                "model_name":"",
                vehicle_model_id:"",
                "model_class":""
          }
      },
      overadd(){
          let ele=this.dialogFormVisible;
           ele.showkey=false;
           Axios("models/savetype",{
               "id":ele.form.id,
                "brand_id":ele.form.brand_id,
                "vehicle_id":ele.form.vehicle_model_id,
                "model_year":ele.form.year,
                "model_name":ele.form.model_name,
                "model_class":ele.form.model_class
           }).then((res)=>{
          this.$message({
          message: `恭喜,${this.compile?'保存':'新建'}品牌成功`,
          type: 'success'
        });
             this.getlist();
      
      })
      },
      getlist(params){
          this.loading=true;
          Axios("models/typelist",params).then(res=>{
               let list=res.data.list;
             for(let i in res.data.list){
                list[i].visible=false;
             }
              this.multipleTable=res.data.list;
              let pages=res.data.page;
              pages.totalnum=parseInt(pages.totalnum);
               let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
               this.loading=false
                 this.$message({
          showClose: true,
          message: '列表数据已更新',
          type: 'success'
        });
          }).catch(()=>{
               this.loading=false
          })
      }
  },
  data() {
    return {
      loading:true,
      page:{},
      compile:false,
      brandlist:[],
      brandname:{},
      formInline:{
          user:"",
      },
      modelclass:[
           {
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
      dialogFormVisible:{
          showkey:false,
          form:{
          brand_id:"",
          id:"",
          model_name:"",
          vehicle_model_id:"",
          year:"",
          model_class:""
          }
      },
      multipleTable: [
        
      ]
    };
  },
  name: "chexing"
};
</script>

<style scoped>
</style>
