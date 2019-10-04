<template>
  <div class="page-container">
    <div style="grid-area: page-content;">
      <slot></slot>
    </div>
    <div style="grid-area: page-sidebar">
      <slot name="sidebar"></slot>
    </div>
  </div>
</template>

<style scoped>
.page-container {
    display: grid;
    grid-gap: 20px;
    @apply p-2 mt-3;
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr;
    grid-template-areas:
        "page-content"
        "page-sidebar"
    ;
}
@screen lg {
    .page-container {
        grid-template-columns: 3fr 1fr;
        grid-template-rows: auto;
        grid-template-areas:
            "page-content page-sidebar"
        ;
    }
}
</style>

<script>
export default {
};
</script>
