<template>
  <!-- eslint-disable max-len -->
  <div class="hero-container">
    <div style="grid-area: hero-image;align-self:center;justify-self:center">
      <img :src="require('./images/judokwaikemzeke.jpg')" />
    </div>
    <div style="grid-area: hero-text; display: flex; flex-direction: column; justify-content: center;align-items: center">
      <div>
        <img
          :src="require('./images/logo2.png')"
          style="width:121px;height:121px;" />
      </div>
      <div style="text-align:center">
        <h2>Judokwai Kemzeke</h2>
        {{ $t("message.hello") }}
        <br />
        {{ $t("message.info") }}
      </div>
      <div>
        <img
          :src="require('./images/40jaar.png')"
          style="width:200px;height:200px;" />
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages
};
</script>
