<template>
    <div class="carousel" :class="showinfokey?'active':''">
         <div class="zone-content">
             <el-table
            
              type="index"
              :stripe=true
              ref="multipleTable"
              :data="showinfo"
              border
              class="printprimary"
              tooltip-effect="dark"
              style="width:100%"
              >
              <el-table-column
                label="排号"
                prop="vehicle_number"
                align="center"
              header-align="center"
                >
                 <template slot-scope="scope">
                 {{checkdate(scope.row.arrive_time,'{y}-{m}-{d}')}}第 {{scope.row.wait_number}}位
                </template>
              </el-table-column>
              <el-table-column
                prop="vehicle_number"
                label="车牌号"
                align="center"
              header-align="center"
                >
              </el-table-column>
              <el-table-column
                prop="contact_name"
                align="center"
              header-align="center"
                label="联系人"
               
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                prop="service_consultant"
                align="center"
              header-align="center"
                label="服务顾问"
               
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                prop="consultant_phone"
                align="center"
              header-align="center"
                label="服务顾问电话"
               
                show-overflow-tooltip>

              </el-table-column>
              <!-- <el-table-column
                prop="unit"
                align="center"
              header-align="center"
                label="排号"
                width="180"
                show-overflow-tooltip>
                <template slot-scope="scope">
                  {{checkdate(scope.row.arrive_time,'{y}-{m}-{d}')}}第 {{scope.row.wait_number}}位
                </template>
              </el-table-column> -->
              <el-table-column
                prop="arrive_date"
                align="center"
              header-align="center"
                label="到店时间"
               
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                prop="plan_date"
                align="center"
              header-align="center"
                label="预计完工时间"
                show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                prop="order_status"
                align="center"
              header-align="center"
                label="车辆状态"
                show-overflow-tooltip>
              </el-table-column>
            </el-table>
            <el-card  @click.native="checkbtn" shadow="always" class="carousel-tip">
            切换
          </el-card>
         </div>
        
    </div>
</template>

<script>
import { parseTime } from "@/utils/index";

export default {
  props: {
    showinfo: {
      type: Array
    },
    page:{
      type:Object,
      default:{}
    },
    page:{
      type:Object,
      default:{}
    },
    showinfokey:{
      type:Boolean,
      default:false
    }
  },
  data(){
    return{
      type:"",
    }
  },
  methods: {
    checkdate(time,type){
      return parseTime(time).split(" ")[0]
    },
    checkbtn(){
       this.$emit("checkbtn",false)
    },
    onSubmit() {
     this.$emit("search",this.formInline)
    },
    checkprev(type){
      this.type=type;
      this.$emit("getmore",type)
    },
    neworder(){
      this.$emit("neworder",this.neworder)
    }
  },
  data() {
    return {};
  }
};
</script>

<style  lang='scss'>
    .carousel{
      // display:none;
      position:fixed;
      overflow: hidden;
      height:100%;
      top:0;
      box-sizing:border-box;
      left:0;
      bottom:0;
      width:100%;
      background:white;
      z-index:-1;
      transform: scale(0.01);
      &.active{
        transform: scale(1);
        z-index:99999;
      }
      .grid-content{
        margin-bottom:80px;
      }
      .el-card__body{
        padding:0;
      }
          .el-row{
      padding:40px 20px;
    }
      &:hover{
        .carousel-tip{
          opacity:1;
           transform: scale(1);
        }
      }
      &-tip{
        position:fixed;
        width:50px;
        height:50px;
        border-radius:50px;
        background:white;
        right:50px;
        bottom:50px;
        cursor:pointer;
       line-height:50px;
       text-align:center;
       z-index:-1;
       font-size:12px;
       &.prev{
         right:120px;
       }
       &.next{
         right:180px;
       }
      &.active{
        z-index:9999999999;
      }
       opacity:0;
       transform: scale(0.01);
       transition: all .4s ease;
      }
    }
    .el-carousel{
     padding:10px;
    }
    .el-carousel__container{
      height:100%;
      overflow: hidden;

    }
    .el-carousel{
      height:100%;
      overflow: hidden;
    }

    .el-carousel__item h3 {
    font-size: 18px;
    opacity: 0.75;
    line-height: 300px;
    margin: 0;
  }
  
  // .el-carousel__item:nth-child(2n) {
  //   background-color: #99a9bf;
  // }
  
  // .el-carousel__item:nth-child(2n+1) {
  //   background-color: #d3dce6;
  // }
</style>