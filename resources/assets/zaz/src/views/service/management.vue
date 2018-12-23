<template>
    <div class="servicement zone-content">
       <comheader 
         @search="search"
         @create="create"
     :formInline="formInline"
        ></comheader>
        <el-table
              v-if="!nodata"
              type="index"
              :stripe=true
              ref="multipleTable"
              :data="list"
              border
              class="printprimary"
              tooltip-effect="dark"
              style="width: 1272px"
              >
              <el-table-column
                label="编号"
                prop="id"
                align="center"
              header-align="center"
                width="130">
              </el-table-column>
               <el-table-column
                label="问题"
                prop="question"
                align="center"
              header-align="center"
                width="500">
              </el-table-column>
                <el-table-column
                label="新建时间"
                prop="plan_date"
                align="center"
              header-align="center"
                width="200">
              </el-table-column>
                <el-table-column
                label="基础样本/份"
                prop="nums"
                align="center"
              header-align="center"
                width="200">
              </el-table-column>
                <el-table-column
                label="操作"
                prop="order_sn"
                align="center"
              header-align="center"
                width="240">
                 <template slot-scope="scope">
                 <el-popover
                    style="margin-left:20px"
                    placement="top"
                    width="220"
                    v-model="scope.row.visible">
                    <p>您是否确定{{scope.row.enable==1?'关闭':'开启'}}这个问题</p>
                    <div style="text-align: right; margin: 0">
                        <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                        <el-button type="primary" size="mini" @click="checksure(scope.row)">确定</el-button>
                    </div>
                    <el-button  slot="reference" size="mini" type="primary">{{scope.row.enable==1?'关闭问题':'开启问题'}}</el-button>
                    </el-popover>
                  <el-button style="margin-left:20px;" size="mini" slot="reference" type="danger" @click="details(scope.row.id)">查看详情</el-button>
                </template>
              </el-table-column>
            </el-table>
        <div class="nodata" v-else>
          <img src="../../assets/nodata.png"  style="display:block;margin:240px auto 20px;" alt="">
          <p>暂时还没有数据哟</p>
        </div>
         <el-dialog
         center
         :close-on-click-modal="true"
         :close-on-press-escape="true"
         @close="beforeclose"
         width="600px"
         title="添加新的答案"
         :visible.sync="dialog.showkey">
        <el-form  @submit.native.prevent label-position="left" ref="temporarys" :rules="temprule" :model="dialog">
           <el-form-item  label="新增问题" label-width="120px">
             <el-input
            type="textarea"
            :rows="2"
            placeholder="请输入内容"
            v-model="dialog.text">
          </el-input>
          </el-form-item>
         <el-form-item  label="答案(勾选即为需要输入原因)" label-width="120px">
           <el-checkbox-group v-model="dialog.checkList">
              <el-checkbox class="otherel"  :label="index"   :key="index" v-for="(tag,index) in dynamicTags">
            <el-tag
          closable
          :disable-transitions="false"
          @close="handleClose(tag)">
          {{tag}}
        </el-tag>
        </el-checkbox>
                <el-input
          class="input-new-tag"
          v-if="inputVisible"
          v-model="inputValue"
          ref="saveTagInput"
          size="small"
          @keyup.enter.native="handleInputConfirm"
          @blur="handleInputConfirm"
        >
        </el-input>
<el-button v-else class="button-new-tag" size="small" type="primary"  @click="showInput">新增答案</el-button>
        </el-checkbox-group>

          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button size="small" @click="dialog.showkey = false">取 消</el-button>
          <el-button size="small" type="primary" @click="sureckeck(dialog)">确 定</el-button>
        </div>
      </el-dialog>
     <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
    </div>
</template>

<script>
import Comheader from "@/components/comHeader/comheader";
import { parseTime } from "@/utils/index";
import Pageing from "@/components/Paging/Paging";
import { Axios } from "@/api/login";
export default {
    name:"mengt",
    components:{
      Pageing,
      Comheader
    },
    methods:{
      checktime(time){
        return parseTime(time);
      },
      onadd(){
        console.log(123123)
      },
      checksure(item){
        item.visible=false;
        Axios("service/switchquestion",{
          id:item.id
        }).then(res=>{
         let index=this.list.findIndex(el=>{
           return el.id==item.id;
         })
         this.list[index].enable=res.data.enable;
          this.$notify({
          title: '成功',
          message: `${res.data.enable==1?'开启':'关闭'}编号为${res.data.id}的问题成功`,
          type: 'success'
          });
        })
      },
      create(){
           this.dialog.showkey=true;
      },
      handleClose(tag) {
        this.dynamicTags.splice(this.dynamicTags.indexOf(tag), 1);
      },
      closed(){
        this.dynamicTags=[];
        this.dialog.checkList=[];
      },
      showInput() {
        this.inputVisible = true;
        this.$nextTick(_ => {
          this.$refs.saveTagInput.$refs.input.focus();
        });
      },
      handleInputConfirm() {
        let inputValue = this.inputValue;
        if (inputValue) {
          this.dynamicTags.push(inputValue);
        }
        this.inputVisible = false;
        this.inputValue = '';
      },
      search(value){
        this.getlist({
          query:value.servicequery,
          page:1
        })
      },
      sureckeck(){
       // 这里判断下
     
        if(this.dialog.text.length==0){
           this.$message({
          message: "请填写问题题目",
          type: "warning"
        });
        return;
        }
        if(this.dynamicTags.length==0){
          this.$message({
          message: "是不是忘记写答案了",
          type: "warning"
        });
        return;
        }
        let cause=[];
        for(let i in this.dynamicTags){
          if(this.dialog.checkList.indexOf(this.dynamicTags[i]>-1)){
            cause.push(1)
          }else{
            cause.push(0)
          }
        }
        this.dialog.showkey=false;
        Axios("service/savequestion",{
            "id":0,
            "question":this.dialog.text,
            "options":this.dynamicTags,
            cause:cause
          }).then(res=>{
              this.dialog.text="";
              this.dynamicTags=[];
            this.getlist()
          })
      },
      beforeclose(){
        this.dialog.sure="";
         this.dialog.checkList=[];
         this.dynamicTags=[];
          this.dialog.result=[];
          this.dialog.text="";
      },
      details(index){
        this.$router.push({ name: "servicemanagedetails", query: { id: index } });
      },
      pagechange(value){
          this.getlist({
          query:value.servicequery,
          page:value
        })
      },
      getlist(params){
       Axios("service/questionlist",params).then(res=>{
         let list=res.data.list;
        for(let i in res.data.list){
          list[i].plan_date=parseTime(list[i].dateline);
          list.visible=false;
        }
         this.list = res.data.list;
        this.nodata=res.data.list.length==0;
        let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
       })
         this.$message({
        message: "数据已更新",
        type: "success"
      });
      },
    },
    mounted() {
      this.getlist();
    },
    data(){
      return{
        formInline:{
          servicequery:"",
          hidetime:true,
        },
        dynamicTags: [],
        inputVisible: false,
        inputValue: '',
        temprule: {
          text: [{
            required: true,
            message: "请输入问题题目",
            trigger: "blur"
          }],
        },
        dialog:{
          showkey:false,
          sure:"",
          checkList:[],
          result:[],
          text:"",
        },
        nodata:false,
        list:[],
        page:{}
      }
    }

}
</script>

<style scoped lang='scss'>
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .otherel{
   
  }
  .button-new-tag {
    margin-left: 10px;
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    margin-left: 10px;
    vertical-align: bottom;
  }

</style>