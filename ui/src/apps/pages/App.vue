<template>
    <v-container fluid :class="{ 'pa-0' : smallDevice }">
        <v-layout row wrap>
            <v-flex xs12 class="hidden-md-and-up no-print">
                <v-menu offset-y>
                    <v-btn color="red" flat @click="drawer = !drawer" slot="activator">
                        <v-icon left>fa-bars</v-icon> {{ $t('information') }}
                    </v-btn>
                    <PageSideBar :categories="categories"></PageSideBar>
                </v-menu>
            </v-flex>
            <v-flex md3 class="hidden-sm-and-down pl-3 no-print">
                <PageSideBar :categories="categories"></PageSideBar>
            </v-flex>
            <v-flex xs12 md9>
                <v-layout row wrap>
                    <v-flex xs12 class="pt-0">
                        <router-view name="PageContent"></router-view>
                    </v-flex>
                    <v-flex xs12>
                        <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'pages.create' }" fab small>
                            <v-icon>fa-plus</v-icon>
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<style>
.button-container {
    border-radius: 2px;
    overflow: hidden;
    margin: 0;
}

.button-container .btn {
    background-color:hsla(0,0%,94%,.9);
    height:7rem;
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
@media print
{
    .no-print, .no-print *
    {
        display: none !important;
    }
}
</style>

<script>
    import PageSideBar from './app/SideBar.vue';

    import messages from './lang/App'

    export default {
        components : {
            PageSideBar
        },
        i18n : {
            messages
        },
        data() {
            return {
                drawer : false
            }
        },
        computed : {
            smallDevice() {
                return this.$vuetify.breakpoint.name == 'xs' || this.$vuetify.breakpoint.name == 'sm';
            },
            categories() {
                return this.$store.getters['categoryModule/categories'];
            },
            backgroundImage() {
                return require('./images/page.jpg');
            }
          },
        created() {
            this.$store.dispatch('setSubTitle', this.$t('information'));
            this.$store.dispatch('categoryModule/browse');
        },
        methods : {
            selectCategory(id) {
                this.$router.push({ name : 'pages.category', params : { category_id : id }});
            }
        }
    };
</script>
