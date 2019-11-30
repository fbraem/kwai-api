<template>
  <div class="double-color text-gray-500 pb-3">
    <div
      class="relative pt-8"
      style="z-index:2;"
    >
      <div class="container mx-auto">
        <img
          class="p-3 sm:p-5 shadow-lg bg-gray-300 mx-auto"
          :src="picture"
          :srcset="srcset"
          sizes="(max-width: 768px) 75vw, 66vw"
        />
      </div>
      <div class="container mx-auto mt-3 p-2">
        <h2 class="text-2xl md:text-6xl text-center">
          {{ title }}
        </h2>
        <div class="text-xl md:text-3xl text-center">
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.double-color {
  @apply bg-gray-800 w-full;
  position: relative;
}

.double-color:before {
  content: '';
  @apply bg-gray-300 w-full h-32;
  position: absolute;
  top: 0px;
  left: 0;
  right: 0;
  z-index: 1;
}
@screen sm {
  .double-color:before {
    @apply h-48;
  }
};

@screen lg {
  .double-color:before {
    @apply h-56;
  }
};

@screen xl {
  .double-color:before {
    @apply h-64;
  }
};
</style>

<script>
/**
 * Component for a header of a page
 */
export default {
  props: {
    /**
     * Title of the header
     */
    title: {
      type: String
    },
    /**
     * A badge to show
     */
    badge: {
      type: Object
    },
    /**
     * Picture to show in the header
     */
    picture: {
      type: String
    },
    pictures: {
      type: Object
    },
    /**
     * Sub title
     */
    subtitle: {
      type: String,
      required: false
    },
    /**
     * toolbar
     */
    toolbar: {
      type: Array,
      required: false
    },
    /**
     * Logo
     */
    logo: {
      type: String,
      required: false
    },
    /**
     * Link
     */
    route: {
      type: Object,
      required: false
    }
  },
  computed: {
    hasToolbar() {
      return this.toolbar && this.toolbar.length > 0;
    },
    srcset() {
      if (this.pictures) {
        let srcset = Object.keys(this.pictures).map(
          x => x + ' ' + this.pictures[x]
        );
        return srcset.join(',');
      }
      return null;
    }
  }
};
</script>
