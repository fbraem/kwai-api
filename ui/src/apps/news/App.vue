<template>
    <v-container fluid grid-list-xl ma-0 pa-0>
        <v-layout>
            <v-flex xs12>
                <v-card flat>
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs12 sm5 md3>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-card flat>
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
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-card>
                                            <v-card-title>
                                                <div class="headline">{{ $t('categories') }}</div>
                                            </v-card-title>
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
                                                <v-btn icon :to="{ name : 'category.create' }" fab small>
                                                    <v-icon>fa-plus</v-icon>
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-card>
                                            <v-card-title>
                                                <div class="headline">{{ $t('archive') }}</div>
                                            </v-card-title>
                                            <v-list dense>
                                                <template v-for="(months, year) in archive">
                                                    <v-subheader>{{ year }}</v-subheader>
                                                    <v-list-tile @click="selectArchive(year, month.month)" v-for="(month) in months" key="month.month">
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
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12 sm7 md9>
                                <router-view name="NewsContent"></router-view>
                            </v-flex>
                        </v-layout>
                        <v-layout>
                            <v-flex xs12>
                                <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'news.create' }" fab small>
                                    <v-icon>fa-plus</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<style>
.category-description {
    font-size: 12px;
    color: #999;
}

.button-container {
    border-radius: 2px;
    overflow: hidden;
    margin: 0;
}

.button-container .btn {
    background-color:hsla(0,0%,94%,.9);
    height:9rem;
    margin:0;
    border-radius:0
}

.button-container .btn .icon {
    font-size:2.5rem;
    margin-bottom:.25rem;
    color:#0279d7
}

.button-container .btn .btn__content {
    -webkit-box-orient:vertical;
    -webkit-box-direction:normal;
    -ms-flex-direction:column;
    flex-direction:column
}

@media screen and (max-width:959px) {
    .button-container .btn {
        height:6rem
    }
}
</style>

<script>
    import messages from './lang/App'

    export default {
        i18n : {
            messages
        },
        computed : {
            categories() {
                return this.$store.getters['categoryModule/categories'];
            },
            backgroundImage() {
                return require('./images/news.jpg');
            },
            archive() {
                return this.$store.getters['newsModule/archive'];
            }
          },
        created() {
            this.$store.dispatch('setSubTitle', this.$t('news'));
            this.$store.dispatch('categoryModule/browse');
            this.$store.dispatch('newsModule/loadArchive');
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
