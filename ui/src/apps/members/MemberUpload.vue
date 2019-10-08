<template>
  <div class="container mx-auto">
    <upload url="/api/sport/judo/members/upload" field="csv" />
  </div>
</template>

<script>
import messages from './lang';

import Upload from '@/components/Upload';

/**
 * Page for uploading members file
 */
export default {
  i18n: messages,
  components: {
    Upload
  }
};
</script>
