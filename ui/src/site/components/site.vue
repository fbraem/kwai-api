<template>
    <div class="uk-offcanvas-content">
        <div uk-sticky="media: 960" class="uk-navbar-container uk-sticky">
            <div class="uk-container uk-container-expand">
                <nav class="uk-navbar">
                    <div class="uk-navbar-left">
                        <a href="#" class="uk-navbar-item uk-logo">
                            Judokwai Kemzeke
                        </a>
                    </div>
                    <div class="uk-navbar-right">
                        <login></login>
                    </div>
                </nav>
            </div>
        </div>
        <div class="uk-sticky-placeholder" style="height: 80px; margin: 0px;" hidden="hidden"></div>

        <slot name="content"></slot>

        <section class="uk-section uk-container uk-container-expand uk-section-secondary">
            <span>&copy; ClubMan</span>
        </section>
    </div>
</template>

<script>
  import Login from '../../apps/auth/components/login.vue';

  export default {
      components : {
          Login
      },
      data() {
          return {};
      }
  };
</script>
