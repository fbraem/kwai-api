<template>
    <section class="uk-section uk-section-default uk-section-small">
        <div class="uk-container">
            <router-view name="UserContent"></router-view>
        </div>
    </section>
</template>

<script>
    import userStore from '@/apps/users/store';
    import messages from './lang';

    export default {
        i18n : messages,
        created() {
            if (!this.$store.state.userModule) {
                this.$store.registerModule('userModule', userStore);
            }
        }
  };
</script>
