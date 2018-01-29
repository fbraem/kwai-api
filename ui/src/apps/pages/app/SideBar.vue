<template>
    <v-card>
        <v-card-media :src="backgroundImage" height="200">
            <v-container class="button-container" fluid>
                <v-flex xs8 sm8 md4 px-0>
                    <v-btn :to="{ name : 'pages' }" style="width:100%" active-class="">
                        <v-icon large color="red">fa-info</v-icon>
                        {{ $t('information') }}
                    </v-btn>
                </v-flex>
            </v-container>
        </v-card-media>
        <v-list two-line>
            <template v-for="(category, index) in categories">
                <v-list-tile @click="selectCategory(category.id)">
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ category.name }}
                        </v-list-tile-title>
                        <v-list-tile-sub-title>
                            <span class="category-description">{{ category.description }}</span>
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                    <v-list-tile-action>
                      <v-btn icon ripple>
                        <v-icon>fa-chevron-right</v-icon>
                      </v-btn>
                    </v-list-tile-action>
                </v-list-tile>
                <v-divider v-if="index != categories.length - 1"></v-divider>
            </template>
        </v-list>
        <v-card-actions v-if="$category.isAllowed('create')">
            <v-spacer>
            </v-spacer>
            <v-btn icon :to="{ name : 'categories.create' }" fab small>
                <v-icon>fa-plus</v-icon>
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import messages from '../lang/App';

export default {
    i18n : {
        messages
    },
    props : [
        'categories'
    ],
    computed : {
        backgroundImage() {
            return require('../images/page.jpg');
        }
    },
    methods : {
        selectCategory(id) {
            this.$router.push({ name : 'pages.category', params : { category_id : id }});
        }
    }
};
</script>
