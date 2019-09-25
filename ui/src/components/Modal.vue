<template>
  <transition name="modal-fade">
    <div
      class="modal"
      @click="click"
    >
      <div class="modal-container" role="dialog">
        <header>
          <slot name="header"></slot>
        </header>
        <span
          class="modal-close"
          @click.prevent.stop="close"
        >
          &times;
        </span>
        <div class="modal-content">
          <slot></slot>
        </div>
        <footer>
          <slot name="footer"></slot>
        </footer>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.modal {
    //display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-container {
    margin: 10% auto; /* 15% from the top and centered */
    border: 1px solid #888;
    width: 70%; /* Fallback for fit-content */
    width: fit-content;
    display: grid;
    grid-template-columns: auto 30px;
    grid-template-rows: 30px auto 1fr;
    grid-gap: 10px;
    grid-template-areas:
        "modal-header modal-close"
        "modal-content modal-content"
        "modal-footer modal-footer"
    ;
    background-color: var(--kwai-color-default-bg);
    color: var(--kwai-color-default-fg);
    filter: drop-shadow(0 2px 0 rgba(0,0,0,.15) );
    border-radius: 15px;
    padding: 10px;
}

.modal-content {
    grid-area: modal-content;
    background-color: var(--kwai-color-default-bg);
    margin: 1em;
}

.modal-container header {
    font-size: 28px;
    padding-left: 20px;
    grid-area: modal-header;
}

.modal-container footer {
    grid-area: modal-footer;
    margin-bottom: 20px;
}

.modal-close {
  color: var(--kwai-color-default-fg);
  font-size: 28px;
  font-weight: bold;
  grid-area: modal-close;
  text-align: center;
}

.modal-close:hover,
.modal.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.modal-fade-enter,
.modal-fade-leave-active {
    opacity: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity .5s ease
}
</style>

<script>
export default {
  methods: {
    close() {
      this.$emit('close');
    },
    click(e) {
      if (e.target.className === 'modal') this.$emit('close');
    }
  }
};
</script>
