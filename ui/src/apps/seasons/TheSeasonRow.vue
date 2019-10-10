<template>
  <tr>
    <td class="py-4 px-4">
      <i class="fas fa-check" v-if="season.active"></i>
    </td>
    <td class="py-4 px-4">
      <router-link
        :to="{ name: 'seasons.read', params: { id: season.id} }"
      >
        {{ season.name }}
      </router-link>
    </td>
    <td class="py-4 px-4">
      {{ season.formatted_start_date }}
      <i class="fas fa-arrows-alt-h"></i>
      {{ season.formatted_end_date }}
    </td>
    <td class="py-4 px-4">
      <router-link
        v-if="$can('update', season)"
        class="kwai-icon-button"
        style="margin-top:-10px"
        :to="{ name: 'seasons.update', params: { id: season.id } }"
      >
        <i class="fas fa-edit"></i>
      </router-link>
    </td>
  </tr>
</template>

<script>
import Season from '@/models/Season';

export default {
  props: {
    season: {
      type: Season,
      required: true
    }
  }
};
</script>
