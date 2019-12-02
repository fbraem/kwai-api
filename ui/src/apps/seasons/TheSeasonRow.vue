<template>
  <tr>
    <td>
      <i class="fas fa-check" v-if="season.active"></i>
    </td>
    <td>
      <router-link
        :to="{ name: 'seasons.read', params: { id: season.id} }"
      >
        {{ season.name }}
      </router-link>
    </td>
    <td>
      {{ season.formatted_start_date }}
      <i class="fas fa-arrows-alt-h"></i>
      {{ season.formatted_end_date }}
    </td>
    <td>
      <router-link
        v-if="$can('update', season)"
        class="icon-button text-gray-700 hover:bg-gray-300"
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
