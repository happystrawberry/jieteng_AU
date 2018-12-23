<template>
   <div class="zone-content ">
        <div class="details">
          <div class="item" >
            <div class="left">
              <h3>问题</h3>
            </div>
            <div class="right">
              <el-input
              type="textarea"
              :rows="6"
              placeholder="请输入问题内容"
              v-model="info.question">
            </el-input>
            <el-button  style="margin-left:20px;transform:translateY(-50px)" size="mini" type="danger" @click="add" >修改问题</el-button>

            </div>
          </div>
          <div class="item ">
            <div class="left">
                <h3>答案</h3>
            </div>
            <div class="right">
                <el-tag
                v-for="(item,index) in info.options"
                :key="index"
                >
                {{item}}
              </el-tag>
            </div>
          </div>
          <div class="item other">
            <div class="left"><h3>统计</h3></div>
            <div class="right">
             <p  style="text-align:center;width:400px;" v-if="info.stat.length==0">暂无统计数据</p>
             <div  v-if="info.stat.length>0" class="ct">
               <ve-pie :data="chartData"></ve-pie>
             </div>
             <div class="tips">
                <el-table
                v-if="info.stat.length>0"
                :data="info.stat"
                border
                align="center"
                style="width:300px">
                <el-table-column
                  prop="option"
                  label="答案"
                  align="center"
                  header-align="center"
                  width="100">
                </el-table-column>
                <el-table-column
                  prop="num"
                  label="占比份数"
                  align="center"
                  header-align="center"
                  width="100">
                </el-table-column>
                <el-table-column
                  prop="percentage"
                  align="center"
                  header-align="center"
                  label="占比">
                </el-table-column>
              </el-table>
             </div>
            </div>
          </div>
        </div>
        <el-dialog
        center
        width="600px"
        title="添加新的答案"
        :visible.sync="dialog.showkey">
       <el-form  @submit.native.prevent label-position="left" ref="temporarys"  :model="dialog">
          <el-form-item  label="新增问题" label-width="120px">
            <el-input
           type="textarea"
           :rows="2"
           placeholder="请输入内容"
           v-model="dialog.result">
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
        </el-checkbox-group >
         </el-form-item>
       </el-form>
       <div slot="footer" class="dialog-footer">
         <el-button size="small" @click="dialog.showkey = false">取 消</el-button>
         <el-button size="small" type="primary" @click="sureckeck(dialog)">确 定</el-button>
       </div>
     </el-dialog>
   </div>
</template>

<script>
import { Axios } from "@/api/login";

export default {
    data(){
      return{
        info:{},
        dialog:{
           showkey:false,
           result:"",
           checkList:[],

        },
        dynamicTags: [],
        inputVisible: false,
        inputValue: '',
        chartData:{
          columns: ['答案', '占比份数'],
          rows:[]
        }
      }
    },
    mounted(query) {
      console.log("query",this.$route.query.id)
      this.getlist(this.$route.query.id)
    },
    methods:{
      add(){
        this.dialog.result=this.info.question;
        let list=[],
            cause=JSON.parse(this.info.need_cause);
        for(let i in cause){
          console.log(cause[i])
          if(cause[i]==1){
            this.dialog.checkList.push(parseInt(i))
          }
        }   
        for(let i in this.info.options){
          list.push(this.info.options[i]);
        }
        this.dynamicTags=list;
        this.dialog.showkey=true;
      },
      handleClose(tag) {
        this.dynamicTags.splice(this.dynamicTags.indexOf(tag), 1);
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
      getlist(id){
        Axios("service/viewquestion",{
        id:id
      }).then(res=>{
          this.info=res.data.info;
          // 这里重新弄一份数据
          let list=[];
          for(let i in res.data.info.stat){
            list.push({
              '答案':res.data.info.stat[i].option,
              '占比份数':parseInt(res.data.info.stat[i].num),
            })
          }
          this.chartData.rows=list;
          this.$message({
        message: "数据已更新",
        type: "success"
      });
      })
      },
      sureckeck(value){
        if(this.dialog.result.length==0){
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
            "id":this.info.id,
            "question":this.dialog.result,
            "options":this.dynamicTags,
            cause:cause
          }).then(res=>{
            this.getlist(this.$route.query.id)
          })
      },
    }

}
</script>

<style scoped lang='scss'>
  .el-tag + .el-tag {
    margin-left: 10px;
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
.details{
   .item{
     width: 100%;
     overflow:hidden;
     display:flex;
     flex-wrap:wrap;
     margin-top:20px;
     margin-bottom:30px;
     .left{
       display:inline-block;
       margin-right:20px;
       display:flex;
       align-items:center;
       margin-bottom:20px;
       
     }
     .right{
       display:inline-block;
       .el-tag{
         margin-right:20px;
       }
       .el-textarea{
         width:400px;
       }
     }
     &.other{
       .right{
         flex: 0 0 100%;
         padding-left:60px;
         display:flex;
       }
       .ct{
         flex:0 0 400px;
       }
     }
   }
}
</style>