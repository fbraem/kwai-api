<template>
  <div uk-grid>
    <div class="uk-width-1-1 uk-width-1-2@m uk-width-2-3@l uk-width-3-5@xl uk-flex uk-flex-middle">
      <div>
        <img :src="require('./images/judokwaikemzeke.jpg')" />
      </div>
    </div>
    <div class="uk-width-1-1 uk-width-1-2@m uk-width-1-3@l uk-width-2-5@xl">
      <div uk-grid class="uk-flex uk-flex-center">
        <div class="uk-width-1-1">
          <img class="uk-align-center"
            :src="require('./images/logo2.png')"
            style="width:121px;height:121px;" />
        </div>
        <div class="uk-width-1-1">
          <h4 class="uk-margin-remove-top uk-text-center">Judokwai Kemzeke</h4>
          {{ $t("message.hello") }}
          <br />
          {{ $t("message.info") }}
        </div>
        <div class="uk-width-1-1">
          <img class="uk-align-center"
            :src="require('./images/40jaar.png')"
            style="width:200px;height:200px;" />
        </div>
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
