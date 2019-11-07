<template>
  <div class="relative w-full h-1 bg-red-700">
    <div class="bar inline absolute h-full w-0 text-center bg-black"></div>
    <div class="bar inline absolute h-full w-0 text-center bg-red-700"></div>
  </div>
</template>

<style scoped>
.bar {
  content: "";
  left: 50%;
}
.bar:nth-child(1) {
  -webkit-animation: loading 3s linear infinite;
          animation: loading 3s linear infinite;
}
.bar:nth-child(2) {
  -webkit-animation: loading 3s linear 1s infinite;
          animation: loading 3s linear 1s infinite;
}
@-webkit-keyframes loading {
    from {left: 50%; width: 0;z-index:100;}
    33.3333% {left: 0; width: 100%;z-index: 10;}
    to {left: 0; width: 100%;}
}
@keyframes loading {
    from {left: 50%; width: 0;z-index:100;}
    33.3333% {left: 0; width: 100%;z-index: 10;}
    to {left: 0; width: 100%;}
}
</style>

<script>
export default {
};
</script>
