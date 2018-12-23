<template>
   <div class="zone-content modelindex">
        <Modelsheader :formInline="formInline" @search="search"></Modelsheader>
        <div class="table">
            <div class="table-header" style="margin:30px 0;">
                <h3>品牌列表</h3>
                <div>
                    <el-button type="primary"  size="mini" @click="addmaterials">添加品牌</el-button>
                    <!--<el-button type="primary"  size="mini" @click="addmaterials">Excel数据导入</el-button> -->
                     <el-upload
                      class="upload-demo"
                      action="/api/models/importbrand"
                      :on-success="success"
                      :show-file-list=false
                      accept="excel">
                      <el-button size="mini" type="primary">导入品牌列表</el-button>
                    </el-upload>
                </div>
            </div>
                <el-table
                    ref="multipleTable"
                    border
                    v-loading="loading"
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width:1000px"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="编号"
                    header-align="center"
                    align="center"
                    prop="id"
                    width="100">
                    </el-table-column>
                    <el-table-column
                    prop="brand_name"
                    header-align="center"
                    align="center"
                    label="品牌"
                    width="300">
                     <template slot-scope="scope">
                        <el-tag type="success">{{scope.row.brand_name}}</el-tag>
                     </template>    
                    </el-table-column>
                     <el-table-column
                    prop="brand_name"
                    header-align="center"
                    align="center"
                    label="品牌LOGO"
                    width="300">
                     <template slot-scope="scope">
                       <img  style="vertical-align: middle;" width="50px" height="50px"  :src="scope.row.img" alt="">
                     </template>    
                    </el-table-column>
                    <el-table-column
                    prop="specification"
                    header-align="center"
                    align="center"
                    label="操作"
                    >
                    <template slot-scope="scope">
                        <el-button size="mini" type="primary" @click="compiled(scope.row)">编辑</el-button>
                        <el-popover
                            placement="top"
                            width="160"
                            v-model="scope.row.visible">
                            <p>确认删除{{scope.row.brand_name}}品牌吗？</p>
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
       title="添加品牌"
       width="500px"
       :visible.sync="dialogFormVisible.showkey">
        <el-form :model="dialogFormVisible.form"
            label-position="left">
            <el-row>
                <el-col >
                    <el-form-item label="品牌LOGO" label-width="140px">
                       <div class="img">
                            <el-upload
                         
                            class="avatar-uploader"
                            :before-upload="beforeimgs"
                            action="/api/common/upload"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                           >
                            <img v-if="imageUrl" :src="imageUrl" class="avatar">
                            <div class="el-upload__text" style="margin-top:10px;width:100%;text-align:center;display:block;">图片尺寸100*100</div>
                            </el-upload>
                       </div>
                    </el-form-item>
                </el-col>
                <el-col>
                     <el-form-item label="品牌" label-width="140px">
                        <el-autocomplete
                            class="inline-input"
                            v-model="dialogFormVisible.form.project"
                            :fetch-suggestions="querySearch"
                            placeholder="请输入内容"
                            :trigger-on-focus="false"
                            @select="handleSelect"
                        ></el-autocomplete>
                    </el-form-item>
                </el-col>
                
                <el-col style="text-align:center;margin-top:30px;">
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
import { Axios } from "@/api/login";
import Modelsheader from "./modelsheader";
import Pageing from "@/components/Paging/Paging";
import loading from "./loading.gif";

export default {
  components: {
    Modelsheader,
    Pageing
  },
  mounted() {
    this.getlist();
    //获取下拉品牌框
    Axios("models/getbrandlist").then(res => {
      let list = [];
      for (let i in res.data.list) {
        list.push({
          value: res.data.list[i].name,
          id: res.data.list[i].id
        });
      }
      this.restaurants = list;
    });
  },
  methods: {

    compiled(el) {
      this.compile = true;
      this.dialogFormVisible.showkey = true;
      this.dialogFormVisible.form.project = el.brand_name;
      this.dialogFormVisible.form.id = el.id;
      this.imageUrl = el.img;
    },
    success(){
      this.$notify({
          title: '成功',
          message: '导入品牌列表成功',
          type: 'success',
          duration:4500,
        });
        this.getlist();
    },
    handleAvatarSuccess(res) {
      this.imageUrl = res.data.url;
    },
    beforeimgs() {
      this.imageUrl = loading;
    },
    createFilter(queryString) {
      console.log(queryString);
      return restaurant => {
        return (
          restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) ===
          0
        );
      };
    },
    querySearch(queryString, cb) {
      var restaurants = this.restaurants;
      var results = queryString
        ? restaurants.filter(this.createFilter(queryString))
        : restaurants;
      // 调用 callback 返回建议列表的数据
      cb(results);
    },
    search(value) {
      this.getlist({
        name: this.formInline.user,
        page: 1
      });
    },
    handleSelect(ele) {
      console.log(ele);
    },
    handleSelectionChange() {
      console.log(23123);
    },
    pagechange(value) {
      this.getlist({
        name: this.formInline.project,
        page: value
      });
    },
    suredetale(scope) {
      scope.row.visible = false;
      Axios("models/brandlist", {
        id: scope.row.id
      }).then(res => {
        let index = this.multipleTable.findIndex(el => {
          return el.id == scope.row.id;
        });
        this.multipleTable.splice(index, 1);
        this.$message({
          showClose: true,
          message: "删除品牌成功",
          type: "success"
        });
      });
    },
    getlist(params) {
      this.loading = true;
      Axios("models/brandlist", params).then(res => {
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
    },
    addmaterials() {
      this.compile = false;
      this.dialogFormVisible.showkey = true;
      this.dialogFormVisible.form.project = "";
      this.dialogFormVisible.form.id = "";
      this.imageUrl = "";
    },
    overadd() {
      this.dialogFormVisible.showkey = false;
      Axios("models/savebrand", {
        id: this.dialogFormVisible.form.id,
        brand_name: this.dialogFormVisible.form.project,
        img: this.imageUrl
      }).then(res => {
        this.$message({
          message: `恭喜,${this.compile ? "保存" : "新建"}品牌成功`,
          type: "success"
        });
        if (!this.compile) {
          this.getlist();
        }else{
          let index=this.multipleTable.findIndex((el)=>{
                return el.id==this.dialogFormVisible.form.id
          })
          this.multipleTable[index].brand_name=this.dialogFormVisible.form.project
           this.multipleTable[index].img=this.imageUrl
            this.multipleTable[index].id=this.dialogFormVisible.form.id
        }
      });
    }
  },
  data() {
    return {
      formInline: {
        user: "",
        time: ""
      },
      isloading: false,
      imageUrl: "",
      compile: false,
      restaurants: [],
      loading: loading,
      dialogFormVisible: {
        showkey: false,
        form: {
          car: "",
          typeao: "",
          id: "",
          project: "",
          img: "",
          materialsid: ""
        }
      },
      page: {},
      multipleTable: []
    };
  },
  name: "chexing"
};
</script>

<style  lang="scss">
.modelindex {
  .el-upload {
      div{
           border: none !important;
      }
  }
  .upload-demo{
        display:inline-block;
        margin:0 10px;
        div{
          margin-left:0;
        }
     }
  .img {
    width: 100px;
    height: 100px;
    border: 1px solid #ebebeb;
    overflow: hidden;
    margin-left: 50px;
  }
  .avatar-uploader .el-upload {
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 100%;
  }
  //   .el-upload--text{
  //       margin-top:200px;
  //   }
  .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }
  .avatar {
    width: 100px;
    height: 100px;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar-uploader {
    width: 100px;
    height: 100px;
    position: relative;
    .imgloading {
      position: absolute;
      width: 100px;
      height: 100px;
      .el-progress-circle {
        width: 100px;
        height: 100px;
      }
    }
  }
}
</style>

