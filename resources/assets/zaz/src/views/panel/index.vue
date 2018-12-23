<template>
      <div class="zone-content">
          <h3>维修阶梯提成设置</h3>
          <el-button  style="margin:20px auto" size="mini" type="primary"  @click="complite(false)">添加提成规则</el-button>
          <div class="table">
                 <el-table
                :data="list"
                border
                align="center"
                style="width:800px">
                <el-table-column
                  prop="id"
                  label="编号"
                  align="center"
                  header-align="center"
                  >
                </el-table-column>
                <el-table-column
                  prop="bill_time"
                  label="总营业额范围"
                  align="center"
                  header-align="center"
                  >
                  <template slot-scope="scope">
                      {{scope.row.min_turnover+'一'+scope.row.max_turnover}}
                </template>
                </el-table-column>
                 <el-table-column
                  label="提成比例"
                  prop="total_cost"
                  align="center"
                  header-align="center"
                  >
                   <template slot-scope="scope">
                      {{parseFloat(scope.row.commission_rate).toFixed(2)*100}}%
                </template>
                </el-table-column>
                  <el-table-column
                  prop="num"
                  label="操作"
                  align="center"
                  header-align="center"
                  >
                    <template slot-scope="scope">
                      <el-button   size="mini" type="primary"  @click="complite(scope)">编辑</el-button>
                      <el-popover
                            placement="top"
                            width="200"
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
              </el-table>
              <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
          </div>
          <el-dialog :title="titlekey?'新建提成规则':'编辑提成规则'" center width="500px"  :visible.sync="dialogFormVisible">
            <el-form :model="form" label-position="left">
                <el-form-item label="起始金额" label-width="120px">
                <el-input v-model="form.min" ></el-input>
                </el-form-item>
                <el-form-item label="截止金额" label-width="120px">
                <el-input v-model="form.max" ></el-input>
                </el-form-item>
                <el-form-item label="提成比例(0-100)" label-width="120px">
                <el-input v-model="form.rate" ></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="surecheck">确 定</el-button>
            </div>
            </el-dialog>
      </div>
</template>


<script>
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
export default {
    components: {
        Pageing
    },
    methods: {
        getlist(params){
            Axios("setting/commissionlist",params).then(res=>{
                this.$message({
                    message: "数据已更新",
                    type: "success"
                });
                this.list=res.data.list;
                let page=res.data.page;
                for(let i in page){
                    page[i]=parseInt(page[i])
                    }
                    this.page=page;
            })
        },
        pagechange(value){
            this.getlist({page:value});
        },
        danger(scope){
            scope.row.visible=false;
            Axios("setting/deletecommission",{
                id:scope.row.id
            }).then(res=>{
                 this.$notify({
                title: '成功',
                message: '删除提成规则成功',
                type: 'success'
                });
                this.getlist({page:1})
            })
        },
        create(){
            console.log()
        },
        surecheck(){
            //这里判断下
            if(parseInt(this.form.min)>=parseInt(this.form.max)){
                 this.$message({
                    message: "截止金额应大于起始金额",
                    type: "warning"
                });
                return;
            }
             if(this.form.rate>100||this.form.rate<0){
                 this.$message({
                    message: "提成比例应在0-100之内",
                    type: "warning"
                });
                return;
            }

            Axios("setting/savecommission",{
                id:this.form.id,
                min:this.form.min,
                max:this.form.max,
                rate:this.form.rate/100,
            }).then(res=>{
                this.dialogFormVisible=false;
                 this.$notify({
                title: '成功',
                message: `${this.titlekey?'新建':'编辑'}提成规则成功`,
                type: 'success'
                });
                if(this.titlekey){
                    this.getlist({page:1})
                }else{
                    let index=this.list.findIndex((el)=>{
                        return el.id==this.form.id;
                    })
                this.list[index].min_turnover=this.form.min;
                this.list[index].max_turnover=this.form.max;
                this.list[index].commission_rate=this.form.rate/100;
                }
            })
        },
        complite(res){
            let form={};
            if(!!res){
                form={
                    id:res.row.id,
                    min:res.row.min_turnover,
                    max:res.row.max_turnover,
                    rate:res.row.commission_rate*100,
                }
                this.titlekey=false;
            }else{
                form={
                    id:0,
                    min:"",
                    max:"",
                    rate:"",
                }
                this.titlekey=true;
            }
            this.form=form;
            this.dialogFormVisible=true;
        },
    },
    mounted(){
       this.getlist();
    },
    data() {
        return {
            list:[],
            page:{},
            titlekey:false,
            form:{
                id:"",
                min:"",
                max:"",
                rate:"",
            },
            dialogFormVisible:false

        }
    },
}
</script>


<style  lang='scss'>


</style>
