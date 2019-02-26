<template>
    <div>
        <header>
            <div class="uk-navbar-container" uk-navbar>
                <div class="uk-navbar-left">
                    <span class="uk-navbar-item uk-logo"><slot name="title"></slot></span>
                </div>
                <div class="uk-navbar-right uk-padding">
                    <router-link v-if="canCreate" class="uk-icon-button" :to="{ 'name' : 'users.create' }">
                        <i class="fas fa-plus"></i>
                    </router-link>
                    <slot name="toolbar"></slot>
                </div>
            </div>
        </header>
        <section v-if="$slots.header" class="uk-section uk-section-secondary">
            <slot name="header"></slot>
        </section>
        <section class="uk-section uk-section-default uk-padding-small">
            <slot name="content"></slot>
        </section>
    </div>
</template>

<script>
import User from '@/models/User';
import userStore from '@/stores/users';

export default {
  computed: {
    canCreate() {
      return this.$can('create', User.type());
    }
  }
};
</script>
