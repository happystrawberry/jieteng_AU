<template>
  <div class="menu-wrapper">
    <template v-for="item in routes" v-if="!item.hidden&&item.children">

      <router-link    v-if="hasOneShowingChildren(item.children) && !item.children[0].children&&!item.alwaysShow" :to="item.path+'/'+item.children[0].path"
        :key="item.children[0].name">
        <el-menu-item :index="item.path+'/'+item.children[0].path"  :class="[{'submenu-title-noDropdown':!isNest},item.path=='/'+path[1]?'active':'']">
          <svg-icon v-if="item.children[0].meta&&item.children[0].meta.icon" :icon-class="item.children[0].meta.icon"></svg-icon>
          <span v-if="item.children[0].meta&&item.children[0].meta.title" slot="title">{{item.children[0].meta.title}}</span>
          <el-badge :value="badgevalue(item.path)"  :hidden="badgehidden(item.path)" class="item"></el-badge>
        </el-menu-item>
      </router-link>

      <el-submenu v-else :index="item.name||item.path" :key="item.name">
        <template slot="title" :class="1>0?'active':''">
          <div :class="item.path=='/'+path[1]?'active':''">
             <svg-icon v-if="item.meta&&item.meta.icon" :icon-class="item.meta.icon"></svg-icon>
            <span v-if="item.meta&&item.meta.title" slot="title">{{item.meta.title}}</span>
            <el-badge :value="badgevalue(item.path)"  :hidden="badgehidden(item.path)" class="item"></el-badge>
          </div>
        </template>
        <template v-for="child in item.children" v-if="!child.hidden" >
          <sidebar-item :is-nest="true" class="nest-menu"  v-if="child.children&&child.children.length>0" :routes="[child]" :key="child.path"></sidebar-item>
          <router-link  v-else :to="item.path+'/'+child.path" :key="child.name">
            <el-menu-item :index="item.path+'/'+child.path" :class="item.path=='/'+path[1]&&child.path==path[2]?'active':''">
              <svg-icon v-if="child.meta&&child.meta.icon" :icon-class="child.meta.icon"></svg-icon>
              <span v-if="child.meta&&child.meta.title" slot="title">{{child.meta.title}}</span>
            </el-menu-item>
          </router-link>
        </template>
      </el-submenu>

    </template>
  </div>
</template>

<script>
import { Axios } from "@/api/login";
export default {
  name: 'SidebarItem',
  props: {
    routes: {
      type: Array
    },
    isNest: {
      type: Boolean,
      default: false
    }
  },
  data(){
    return{
      path:"",
      tipsinfo:{},
    }
  },
  watch:{
    $route(){
      this.path=this.$route.path.split("/");
    }
  },
  created(){
    this.path=this.$route.path.split("/");
  },
  mounted() {
     Axios("common/getnewtips").then(res=>{
        this.tipsinfo=res.data.counts;
      })
    setInterval(()=>{
      Axios("common/getnewtips").then(res=>{
        this.tipsinfo=res.data.counts;
      })
    },30000)
  },
  methods: {
    badgevalue(value){
      for(let i in this.tipsinfo){
        if(value.indexOf(i)>-1){
          return this.tipsinfo[i]
        }
      }
    },
    badgehidden(value){
      for(let i in this.tipsinfo){
        if(value.indexOf(i)>-1&&this.tipsinfo[i]>0){
          return false
        }
      }
      return true;
    },
    hasOneShowingChildren(children) {
      const showingChildren = children.filter(item => {
        return !item.hidden
      })
      if (showingChildren.length === 1) {
        return true
      }
      return false
    }
  }
}
</script>
<style  rel="stylesheet/scss" lang="scss" scoped>
  .active {
    color:#409EFF !important;
  }
  .el-badge{
      position:absolute;
      top:5px;
      right:36px;
  }
</style>
