<template>
  <router-view></router-view>
</template>

<script>
import SideBar from './components/SideBar.vue'


export default {
  components: { SideBar },
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}

body {
  margin: 0px;
  padding: 0px;
  box-sizing: border-box;
}
</style>
