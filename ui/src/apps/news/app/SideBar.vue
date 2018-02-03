<template>
    <div>
        <v-card>
            <v-card-media :src="backgroundImage" height="200">
                <v-container class="button-container" fluid>
                    <v-flex xs8 sm8 md4 px-0>
                        <v-btn :to="{ name : 'news.browse' }" style="width:100%" active-class="">
                            <v-icon large color="red">fa-newspaper</v-icon>
                            {{ $t('news') }}
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
        <v-card>
            <v-card-title>
                <div class="headline">{{ $t('archive') }}</div>
            </v-card-title>
            <v-list dense>
                <template v-for="(months, year) in archive">
                    <v-subheader>{{ year }}</v-subheader>
                    <v-list-tile @click="selectArchive(year, month.month)" v-for="(month) in months" :key="month.month">
                        <v-list-tile-content>
                            {{ month.monthName }} {{ year }}
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-chip small color="primary" text-color="white">
                                {{ month.count }}
                            </v-chip>
                        </v-list-tile-action>
                    </v-list-tile>
                </template>
            </v-list>
        </v-card>
    </div>
</template>

<script>
import messages from '../lang/App';

export default {
    i18n : {
        messages
    },
    props : [
        'categories',
        'archive'
    ],
    computed : {
        backgroundImage() {
            return require('../images/news.jpg');
        }
    },
    methods : {
        selectCategory(id) {
            this.$router.push({ name : 'news.category', params : { category_id : id }});
        },
        selectArchive(year, month) {
            this.$router.push({ name : 'news.archive', params : { year : year, month : month }});
        }
    }
};
</script>
