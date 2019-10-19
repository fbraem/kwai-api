<template>
  <!-- eslint-disable max-len -->
  <Header
    :picture="require('./images/judokwaikemzeke.jpg')"
  >
    <div style="grid-area: hero-text; display: flex; flex-direction: column; justify-content: center;align-items: center">
      <div>
        <img
          :src="require('./images/logo2.png')"
          style="width:121px;height:121px;" />
      </div>
      <div style="text-align:center">
        <h2 class="text-white">Judokwai Kemzeke</h2>
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
  </Header>
</template>

<script>
import messages from './lang';

import Header from '@/components/Header';

export default {
  components: {
    Header
  },
  i18n: messages
};
</script>
