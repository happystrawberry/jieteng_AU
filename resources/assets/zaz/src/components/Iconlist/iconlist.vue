<template>
  <div class="filterlist">
    <el-button
      class="filter-list el-button--medium"
      @click="check(item.order_status)"
      size="mini"
      v-for='(item,index) in data'
      :key="index"
      :class="numlist.indexOf(item.order_status)>-1?'active':''"
    >
      <span>{{item.name}}({{item.num}})</span>
    </el-button>
  </div>
</template>

<script>
export default {
  name: "iconlist",
  props: {
    data: {
      type: Array,
      default: []
    },
    checktype:{
      type:Boolean,
      default:false
    },
    numlist: {
      type: Array,
      default: []
    },
    
  },
  mounted () {
    
  },
  data(){
    return{
      numlists:[]
    }
  },
  methods: {
    check(index) {
       if(this.checktype){
         let numindex = this.numlist.indexOf(index);
      if (index == -1) {
        if(this.numlists.length>0){
           
            if(numindex>-1){
             this.numlists = [];
          }else{
             this.numlists = [];
             this.numlists.push(index);
          }
        }else{
           this.numlists = [];
             this.numlists.push(index);
        }
      } else {
        if (numindex > -1) {
          // 说明存在
          this.numlists.splice(numindex, 1);
        } else {
           let allindex = this.numlist.indexOf("-1");
           if(allindex>-1){
             this.numlists.splice(allindex, 1);
           }
           
          this.numlists.push(index);
          
        }
        // 这里还要再判断下
        // if (this.numlist.length == 0) {
        //   this.numlists = [];
        //   for (let i in this.data) {
        //     this.numlists.push(this.data[i].order_status);
        //   }
        // }
      }
       this.$emit("numlists", this.numlists);
      return;
      let allindex = this.numlists.indexOf(-1);
      if (allindex > -1) {
        this.numlists.splice(allindex, 1);
      }
      if (this.numlists.length == this.data.length - 1) {
        this.numlists=[-1]
        this.$emit("numlists", this.numlists);
      } else {
        this.$emit("numlists", this.numlists);
      }
       }else{
          this.numlists=[];
        this.numlists.push(index)
       this.$emit("numlists", this.numlists);
       }
      // 我们这里做一个判断
      

      // 这里改成单选
      // this.$emit("numlists", this.numlists);
    }
  }
};
</script>

<style scoped lang="scss">
.el-button:focus {
  color: #606266;
  border-color: #dcdfe6;
  background-color: white;
}
.filterlist {
  margin-top: 15px;
  margin-bottom: 20px;
  .el-button {
    cursor: pointer;
    margin-right: 10px;
    margin-bottom: 10px;
    box-sizing: border-box;
    &:focus {
      outline: none;
    }
    &.active {
      background: #409EFF;
      color: white;
      border-color: transparent;
      box-sizing: border-box;
    }
  }
}
</style>
