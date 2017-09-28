<template>
    <site>
        <div slot="content">
            <div v-for="user in users">
                {{ user.email }}
            </div>
        </div>
    </site>
</template>

<script>
  import Site from '@/site/components/site.vue';

  export default {
      components : {
          Site
      },
      computed : {
        users() {
          return this.$store.state.userModule.users;
        }
      },
      mounted() {
        this.$store.dispatch('userModule/read')
          .catch((error) => {
            console.log(error);
            if (error.response && error.response.status == 401) { // Not authorized
              //TODO: show an alert?
            }
        });
      },
      methods : {
      }
  };
</script>
