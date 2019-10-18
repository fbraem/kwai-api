<template>
  <tr class="odd:bg-gray-200 border-b border-gray-400">
    <td class="py-2 px-3 text-gray-700">
      <i class="fas fa-check" v-if="season.active"></i>
    </td>
    <td class="py-2 px-3 text-gray-700">
      <router-link
        :to="{ name: 'seasons.read', params: { id: season.id} }"
      >
        {{ season.name }}
      </router-link>
    </td>
    <td class="py-2 px-3 text-gray-700">
      {{ season.formatted_start_date }}
      <i class="fas fa-arrows-alt-h"></i>
      {{ season.formatted_end_date }}
    </td>
    <td class="py-2 px-3 text-gray-700">
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
