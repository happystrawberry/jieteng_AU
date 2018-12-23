<template>
   <div class="zone-content">
       <el-form :inline="true" :model="formInline" class="demo-form-inline"> 
            <el-form-item >
                <el-input style="width:250px" size="mini" v-model="formInline.user" placeholder="请输入品牌型号、手机号、车主姓名"></el-input>
            </el-form-item>
            <el-form-item @clcik.native="search">
                <el-button size="mini" type="primary" @click="search" >搜索</el-button>
            </el-form-item>
        </el-form>
        <div class="table">
            <div class="table-header" style="margin:30px 0;">
                <h3>会员列表</h3>
                <div>
                    <el-button type="primary" size="mini" @click="checkexport" >导出列表</el-button>
                </div>
            </div>
                <el-table
                    ref="multipleTable"
                    border
                    v-loading="loading"
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width: 1300px"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="会员编号"
                    header-align="center"
                    align="center"
                    prop="id"
                    width="100">
                    </el-table-column>
                    <el-table-column
                    prop="username"
                    header-align="center"
                    align="center"
                    label="会员名称"
                    width="100">
                    </el-table-column>
                    <el-table-column
                    prop="phone"
                    header-align="center"
                    align="center"
                    label="会员手机号"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="vehicle_number"
                    header-align="center"
                    align="center"
                    label="车牌"
                    width="100"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="vehicle_model"
                    header-align="center"
                    align="center"
                    label="车型"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="wallet_birthday"
                    header-align="center"
                    align="center"
                    label="账户余额"
                    width="100"
                    >
                    </el-table-column>
                     <el-table-column
                    prop="integral"
                    header-align="center"
                    align="center"
                    label="积分"
                    width="100"
                    >
                    </el-table-column>
                     <el-table-column
                    prop="viplevel"
                    header-align="center"
                    align="center"
                    label="会员等级"
                    width="100"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="specification"
                    header-align="center"
                    align="center"
                    label="操作"
                    >
                    <template slot-scope="scope">
                        <el-button size="mini" type="primary" @click="gotodetails(scope.row.id)">用户详情</el-button>
                        <el-button size="mini" type="primary"  @click="gotoremain(scope.row.id)">积分余额管理</el-button>
                    </template>
                    </el-table-column>
                </el-table>
            </div>
                  <pageing  @pagechange="pagechange"  :pageing="page"></pageing>

        </div>
</template>

<script>
import Pageing from "@/components/Paging/Paging";
import { Axios } from "@/api/login";
export default {
  components: {
    Pageing
  },
  methods: {
    gotodetails(index) {
      this.$router.push({ name: "memberdetails", query: { uid: index } });
    },
    gotoremain(index) {
      this.$router.push({ name: "memberremain", query: { uid: index } });
    },
    pagechange(value) {
      this.getlist({
        page: value,
        query: this.formInline.user
      });
    },
    checkexport() {
      let el = this.formInline;
      window.open(
        `/api/member/list?export=1&page=${
          this.page.curpage
        }&query=${this.formInline.user}`,
        "_blank"
      );
    },
    handleSelectionChange() {
      console.log(23123);
    },
    getlist(params) {
      this.loading = true;
      Axios("member/list", params)
        .then(res => {
          this.multipleTable = res.data.list;
           let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
          this.$message({
            message: "数据请求成功",
            type: "success"
          });
          this.loading = false;
        })
        .catch(res => {
          this.loading = false;
        });
    },
    search(value) {
      console.log(123123);
      this.getlist({
        page: 1,
        query: this.formInline.user
      });
    },
    addmaterials() {
      this.dialogFormVisible.showkey = true;
    },
    overadd() {
      this.dialogFormVisible.showkey = false;
    }
  },
  mounted() {
    this.getlist();
  },
  data() {
    return {
      formInline: {
        user: "",
        time: ""
      },
      page: {},
      loading: true,
      dialogFormVisible: {
        showkey: false,
        form: {
          car: "",
          typeao: "",
          project: "",
          pingpai: "",
          materialsid: ""
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
