<template>
    <div class="zone-content">
        <div class="print-list">
          <div class="remark-list">
                <div class="remark-li left"
                v-for="(item,index) in list"
                :key="index"
                >
                <img src="./avatar.png" alt="" class="remark-list-left">
                <div class="remark-list-right">
                    <p class="top">{{item.account_name}}  {{checkdate(item.dateline)}}</p>
                    <p class="content left">{{item.message}}</p>
                </div>
                 </div>
            </div>
        </div>
        <div class="noprint">
        <messagelist :info="info"></messagelist>
        <el-button type="primary" style="margin-bottom:20px;"  @click="print" size="small">打印备注记录</el-button>
        <div class="remark">
            <div class="remark-list">
                <div class="remark-li left"
                v-for="(item,index) in list"
                :key="index"
                >
                <img src="./avatar.png" alt="" class="remark-list-left">
                <div class="remark-list-right">
                    <p class="top">{{item.account_name}}  {{checkdate(item.dateline)}}</p>
                    <p class="content left">{{item.message}}</p>
                </div>
                 </div>
            </div>
            <div class="remark-list-bottom">
                <div class="left">
                    <img src="./avatar.png" alt="">
                    <p>{{userinfo.groupname}}--{{userinfo.username}}</p>
                </div>
                <div class="right">
                    <el-input type="textarea" v-model="message" :rows="6" placeholder="请输入备注"></el-input>
                    <el-button type="primary" size="small" @click="submit" class="submit">发送</el-button>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
import Messagelist from "@/components/Messagelist/Messagelist";
import { getCookie, setToken, removeToken,setCookie,removeCookie} from '@/utils/auth'
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";
import { mapGetters } from "vuex";
export default {
  components: {
    Messagelist
  },
  data() {
    return {
      message: "",
      list: "",
      info: {},
      userinfo:{},
    };
  },
  computed: {
    ...mapGetters(["uid", "sidebar"])
  },
  methods: {
    checkdate(time) {
      return parseTime(time);
    },
    print() {
      if(this.list.length==0){
        this.$message({
          message: "留言备注为空",
          type: "warning"
        });
        return;
      }
      window.print();
    },
    getlist() {
      Axios("common/ordernotelist", {
        order_id:this.$route.query.id
      }).then(res => {
        this.list = res.data.list;
        this.info = res.data.list[0];
      });
    },
    submit() {
      if (this.message.length == 0) {
        this.$message({
          message: "留言备注不能为空哟",
          type: "warning"
        });
      } else {
        Axios("common/saveordernote", {
          order_id: this.$route.query.id,
          message: this.message
        }).then(res => {
          this.$message({
            message: "恭喜你，留言成功",
            type: "success"
          });
         document.getElementsByClassName("remark-list")[1].scrollTop=0;
          this.message = "";
          this.getlist();
        });
      }
    }
  },
  mounted() {
    this.getlist();
    this.userinfo.groupname=getCookie("groupname");
     this.userinfo.username=getCookie("username");
  },
  name: "remark"
};
</script>

<style scoped lang="scss">
.print-list{
  display:none;
}

@media print {
  .el-message{
    display:none !important;
  }
  .el-message{
    display:none !important;
  }
  .main-container {
    margin-left: 0 !important;
  }
  .el-scrollbar__bar {
    display: none !important;
  }
  .sidebar-container {
    display: none !important;
  }
  .el-scrollbar__view {
    display: none;
  }
  .el-menu {
    display: none;
  }
  .noprint {
    display: none;
  }
  .print-list{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    min-height: 100%;
    z-index: 99999;
    display: block;
    background:white;
    .remark-list{
      height:auto;
    }
  }
}
.remark {
  width: 100%;
  box-sizing: border-box;
  border: 1px solid #bbb;
  border-radius: 5px;
  padding: 30px;
  &-li {
    display:-webkit-box;
    flex-direction: row;
    align-items: center;
    &.right {
      flex-direction: row-reverse;
      img {
        margin-left: 10px;
      }
    }
  }
  &-list {
    height: 500px;
    overflow: auto;
    box-sizing: border-box;
    padding: 10px;
    display:flex;
    flex-direction: column;
    &-bottom {
      display: flex;
      margin-top: 40px;
      .left {
        text-align: center;
        align-items: center;
        flex: 0 0 140px;
        margin-right: 20px;
        img {
          width: 45px;
          height: 45px;
        }
      }
      .right {
        flex: 1;
        position: relative;
        .submit {
          position: absolute;
          bottom: 20px;
          right: 20px;
        }
      }
    }
    &-left {
      width: 45px;
      height: 45px;
      margin-right: 10px;
    }

    .top {
      font-size: 14px;
      color: #999;
      margin-bottom: 10px;
    }
    .content {
      padding: 10px;
      color: #000;
      line-height: 20px;
      min-height: 48px;
      border-radius: 10px;
      font-size: 12px;
      position: relative;
      &:after {
        position: absolute;
        content: "";
        width: 23px;
        height: 42px;
        top: 0;
      }
      &.left {
        background: #f3f3f3;
        padding: 10px 10px 10px 20px;
      }
      &.right {
        background: #d0e9ff;
        padding: 10px 10px 20px 10px;
      }
      &.left:after {
        left: -6px;
        background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAqCAYAAABYzsDTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QzI0QTVCMjIxNjBBMTFFODlGQzlDRDUzQTg4RDcxQzMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QzI0QTVCMjMxNjBBMTFFODlGQzlDRDUzQTg4RDcxQzMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDMjRBNUIyMDE2MEExMUU4OUZDOUNENTNBODhENzFDMyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDMjRBNUIyMTE2MEExMUU4OUZDOUNENTNBODhENzFDMyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PsDqUnkAAADjSURBVHjaYvz8+TMDHiADxF1A7ADEkgyEwXMgPgDEZTw8PE8Y8RgOMvgiEAsxkA7eAbE+Ex4FXWQazADV14XP5c+IDAqcQYTP5ZQYDNbPxEBDMGr44DFci1aGJwDxaWoYzoLE5gLiaUAcT61gYUEKhtXUCg7kYImGBoMWtSMUVLY8o0JWx+nyQiD+RivDVwKxKRBfo1U6vwa1YAGtMhEoaBKh6fwbtSIUVw69Sqvsf42WZctokTtq+Kjho4aPGj58DH9Oodl4uy0HKDT8wIB1FZ+AFADxchKC6DlUvT6okwsQYAC/AjSzkv2m0gAAAABJRU5ErkJggg==);
      }
      &.right:after {
        right: -6px;
        background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAqCAYAAABYzsDTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QzI0QTVCMUUxNjBBMTFFODlGQzlDRDUzQTg4RDcxQzMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QzI0QTVCMUYxNjBBMTFFODlGQzlDRDUzQTg4RDcxQzMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDMjRBNUIxQzE2MEExMUU4OUZDOUNENTNBODhENzFDMyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDMjRBNUIxRDE2MEExMUU4OUZDOUNENTNBODhENzFDMyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqBcZigAAAD0SURBVHjaYrzw8j8DEMgAcRcQOwCxJANh8ByIDwBxmb4YwxNcihiBhoMMvgjEQgykg3dArI/LAiaoi8kxmAGqrwuXJBM0KCgBDvgMl6TQcEl8htMMjBo+RA2/+IpBi5YuPw20IIFWhnMB8XygBQuAmItWYR4P9YUWrOD6T4O4/AbEabRKLaCg6aaV4SCXF7LQwOBrQBwKLOOvUdvlC4DYFGQwiMNCxWDIAhq6EFmQWobDXUv1TITN4NFScdTwUcNHDR81fHgZ/pxCM57jM/wAhYYfwGd4GbTLRw54B9WP03BQH1IfiJeTEETPoer18XVyAQIMACbMN3JUxFKPAAAAAElFTkSuQmCC);
      }
    }
  }
}
</style>
