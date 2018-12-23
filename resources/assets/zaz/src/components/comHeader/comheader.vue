<template>
    <div class="comheader">
      <el-form @keyup.enter.native="onSubmit" :inline="true" :model="formInline" class="demo-form-inline">
         <el-form-item v-if="formInline.accessories||formInline.accessories==''">
          <el-input  
          size="mini" class="other" v-model="formInline.accessories" placeholder="输入配件名称"></el-input>
        </el-form-item>
         <el-form-item v-if="formInline.servicequery||formInline.servicequery==''">
          <el-input  
          size="mini" class="other" v-model="formInline.servicequery" placeholder="搜索问题"></el-input>
        </el-form-item>
         <el-form-item v-if="formInline.workquery||formInline.workquery==''">
          <el-input  
          size="mini" style="width:300px;" class="other" v-model="formInline.workquery" placeholder="请输入订单号、车牌号、联系人、手机号"></el-input>
        </el-form-item>
        
         <el-form-item v-if="formInline.materials||formInline.materials==''">
          <el-input  
          size="mini" class="other" v-model="formInline.materials" placeholder="请输入材料编码"></el-input>
        </el-form-item>
        <el-form-item v-if="formInline.plate||formInline.plate==''">
          <el-input  
          size="mini" v-model="formInline.plate" placeholder="请输入车牌号"></el-input>
        </el-form-item>
        <el-form-item
          v-if="formInline.consultant"
          class="otherinput"
        >
          <el-select 
          size="mini"
           clearable
           filterable
          v-model="formInline.checkconsultant" placeholder="请选择服务顾问">
            <el-option
            style="width:200px"
            v-for="(item,index) in formInline.consultant"
            :key="index" 
            :label="item.label" 
            :value="item.id"></el-option>
          </el-select>
        </el-form-item>
  
        <el-form-item v-if="formInline.query||formInline.query==''">
          <el-input  
          size="mini" class="query" v-model="formInline.query" placeholder="工号查询、名字查询、手机号"></el-input>
        </el-form-item>
         <el-form-item v-if="formInline.otherplate||formInline.otherplate==''">
          <el-input  
          size="mini" class="other" v-model="formInline.otherplate" placeholder="请输入车牌号"></el-input>
        </el-form-item>
        <el-form-item v-if="formInline.phone||formInline.phone==''" >
          <el-input size="mini" v-model="formInline.phone" placeholder="手机号"></el-input>
        </el-form-item>
        <el-form-item v-if="formInline.names||formInline.names==''">
          <el-input  size="mini" v-model="formInline.names" placeholder="车主名称"></el-input>
        </el-form-item>
        <el-form-item v-if="formInline.user||formInline.user==''" >
          <el-input size="mini" v-model="formInline.user" placeholder="联系人"></el-input>
        </el-form-item>
        <el-form-item v-if="formInline.ordernum||formInline.ordernum==''">
          <el-input  size="mini"  v-model="formInline.ordernum" placeholder="订单编号"></el-input>
        </el-form-item>
        <el-form-item v-if="formInline.order_sn||formInline.order_sn==''">
          <el-input  size="mini" class="otherinput" v-model="formInline.order_sn" placeholder="订单编号"></el-input>
        </el-form-item>
        <el-form-item v-show="!formInline.hidetime"  v-if="(formInline.time||formInline.time=='')||formInline.time==null">
          <el-date-picker
            size="mini"
            v-model="formInline.time"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
             :default-time="['00:00:00', '23:59:59']"
            value-format="timestamp"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
        <el-form-item
          v-if="formInline.type"
          class="other"
        >
          <el-select 
          size="mini"
          v-model="formInline.types" placeholder="请选择方式">
            <el-option
            style="width:200px"
            v-for="(item,index) in formInline.type"
            :key="index" 
            :label="item.label" 
            :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item style="margin-top:-1px;">
          <el-button  size="mini" type="primary" @keyup.enter.native="onSubmit"  @click="onSubmit">搜索</el-button>
          <el-button  size="mini" type="primary"  v-if="formInline.download" @click="ondownload">数据下载</el-button>
           <el-button  size="mini" type="primary"  v-if="formInline.servicequery||formInline.servicequery==''" @click="onCreate">新建问题</el-button>
          <el-button size="mini" style="color:white;background:#17d5db;border-color:#17d5db" v-if="formInline.neworder||formInline.neworder==''" @click="neworder">新建接单</el-button>
           <el-button  v-if="formInline.showinfo" size="mini" type="primary"  @click="checkbtns(true)">切换预览</el-button>
        </el-form-item>
      </el-form>
    </div>
</template>

<script>
export default {
  props: {
    formInline: {
      type: Object
    }
  },
  methods: {
    checkbtns(){
      console.log(1231)
       this.$emit("checkbtn",true)
    },
    onSubmit() {
     this.$emit("search",this.formInline)
    },
    onCreate(){
      this.$emit("create",this.formInline)
    },
    neworder(){
      this.$emit("neworder",this.neworder)
    },
    ondownload(){
      this.$emit("download",this.formInline)
    },
  },
  data() {
    return {};
  }
};
</script>

<style  lang='scss'>
  .query{
    width:255px;
     input{
        width:255px !important;
     }
  }
  .others{
    width:180px;
     input{
        width:180px !important;
     }
  }
  .otherinput{
    input{
      width:210px !important;
    }
  }
</style>