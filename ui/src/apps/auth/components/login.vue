<template>
    <div>
        <v-btn v-if="$isAllowed('login')" icon href="auth.html">
            <v-icon>lock</v-icon>
        </v-btn>
        <v-btn v-else icon href="auth.html">
            <v-icon>person</v-icon>
        </v-btn>
    </div>
</template>

<script>
  export default {
    components : {
    },
    data() {
      return {};
    }
  };
</script>
