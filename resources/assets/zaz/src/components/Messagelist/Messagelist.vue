<template>
    <div class="message" v-if="info.message">
      <div v-if="info.message">{{info?info.account_name:''}}:{{info?info.message:''}}</div>
      <div v-else>暂无留言</div>
    </div>
</template>

<script>
    export default {
        props:{
            info:{
                type:Object,
                default:{},
            }
        },
        name: "Messagelist"
    }
</script>

<style scoped lang="scss">
    .message{
      height:100px;
      background:#eee;
      text-align:center;
      overflow-y: auto;
      line-height:100px;
      box-sizing: border-box;
      margin-bottom:50px;
    }

</style>
