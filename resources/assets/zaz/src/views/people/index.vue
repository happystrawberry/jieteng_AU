<template>
   <div class="zone-content index">
        <comheader 
        @search="search"
        :formInline="formInline"></comheader>
         <div class="table">
          <div class="table-header">
            <h2>员工列表</h2>
            <div>
                <el-button type="primary" size="mini" @click="upload" >同步钉钉数据</el-button>
            </div>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="multipleTable"
            tooltip-effect="dark"
            style="width:100%"
            v-loading="loading"
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              label="工号"
              header-align="center"
              align="center"
              prop="ding_jobnumber"
              width="100">
            </el-table-column>
            <el-table-column
              prop="username"
              header-align="center"
              align="center"
              label="姓名"
              width="100">
            </el-table-column>
             <el-table-column
              header-align="center"
              align="center"
              label="头像"
              width="100"
              >
              <template slot-scope="scope">
               <div style="display:flex;align-items:center;justify-content:center;">
                   <img class="avatar" :src="scope.row.project?scope.row.project:avatar" alt="">
               </div>
            </template>
            </el-table-column>
            <el-table-column
              prop="ding_department"
              header-align="center"
              align="center"
              label="钉钉部门"
              width="150"
              >
            </el-table-column>
            <el-table-column
              prop="groupname"
              header-align="center"
              align="center"
              label="权限组"
              width="200"
              >
              <template slot-scope="scope">
                <el-tag>{{scope.row.groupname?scope.row.groupname:'未知'}}</el-tag>
              </template>
            </el-table-column>
             <el-table-column
              prop="phone"
              header-align="center"
              align="center"
              label="手机"
              width="150"
              >
            </el-table-column>
              <el-table-column
              prop="ding_position"
              header-align="center"
              align="center"
              label="职位"
              width="250"
              >
            </el-table-column>
              <el-table-column
              prop="notes"
              header-align="center"
              align="center"
              label="备注"
              width="150"
              >
            </el-table-column>
            <el-table-column
              prop="check"
              header-align="center"
              align="center"
              label="操作"
              >
               <template slot-scope="scope">
                 <el-button size="mini" type="primary" @click="details(scope.row.id)">查看详情</el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
         <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
   </div>
</template>

<script>
import Iconlist from "@/components/Iconlist/Iconlist";
import Comheader from "@/components/comHeader/comheader";
import avatar from './avatar.jpg'
import { Axios } from "@/api/login";
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";

export default {
  name: "zhijian",
  components: {
    Iconlist,
    Comheader,
    Pageing
  },
  methods: {
    details(index) {
      this.$router.push({ name: "peopledetails", query: { id: index } });
    },
    upload(){
      this.loading=true;
      Axios("people/sync").then(res=>{
          this.getlist();
        this.loading=false;
      }).catch(()=>{
        this.loading=false;
      })
    },
    checkdate(time){
      return parseTime(time);
    },
    handleSelectionChange(){
        console.log(123)
    },
    pagechange(value){
      this.getlist({
        page:value,
        query:this.formInline.query
      })
    },
    getlist(params){
      this.loading=true
        Axios("people/list",params).then(res=>{
            let list=res.data.list;
            list=list.filter((el)=>{
              return el.display!=0;
            })
              this.multipleTable=list;
              let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
              this.loading=false
              this.$message({
              message: "数据已更新",
              type: "success",
              showClose: true,
            });
        })
    },
    search(value){
       this.getlist({
        page:1,
        query:this.formInline.query
      })
    },
    addmaterials(){
          this.dialogFormVisible.showkey=true;
      },
      overadd(){
           this.dialogFormVisible.showkey=false;
      },
  },
  mounted() {
    this.getlist();
  },
  data() {
    return {
      page:{},
      avatar:avatar,
      formInline: {
        query: "",
        hidetime:true,
      },
      loading:true,
      dialogFormVisible:{
            showkey:false,
            form:{
                serial:"",
              materialsid:"",
              project:"",
              materialsnum:"",
              typeao:"",
              typea:"",
              typeb:"",
              typec:"",
              typed:"",
              moneyao:"",
              moneya:"",
              moneyb:"",
              moneyc:"",
              moneyd:"",
            }
        },
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
      ],
      multipleTable:[
          
      ],
    };
  }
};
</script>

<style scoped lang="scss">
        .avatar{
           width:50px;
           height:50px;
           border-radius:50px;

        }
</style>
