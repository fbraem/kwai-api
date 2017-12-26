<template>
    <v-layout>
        <v-flex xs12 md6 offset-md3>
            <v-card v-if="story">
                <v-card-title>
                    <div>
                        <h3 class="headline mb-0">{{ title }}</h3>
                        <div v-html="summary">
                        </div>
                    </div>
                </v-card-title>
                <v-card-text>
                    {{ $t('sure_to_delete') }}
                </v-card-text>
                <v-card-actions>
                    <v-btn color="error" @click="deleteStory">
                        <v-icon left>fa-trash</v-icon>
                        {{ $t('delete') }}
                    </v-btn>
                    <v-btn @click="$router.go(-1)">
                        {{ $t('cancel') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
    data() {
        return {};
    },
    mounted() {
        this.fetchData(this.$route.params.id);
    },
    computed : {
        story() {
            return this.$store.getters['newsModule/story'](this.$route.params.id);
        },
        title() {
            var content = _.find(this.story.contents, function(o) {
                return o.locale == 'nl';
            });
            if (content) {
                return content.title;
            }
            return "";
        },
        summary() {
            var content = _.find(this.story.contents, function(o) {
                return o.locale == 'nl';
            });
            if (content) {
                return content.html_summary;
            }
            return "";
        }
    },
    beforeRouteUpdate(to, from, next) {
        this.fetchData(to.params.id);
        next();
    },
    methods : {
        fetchData(id) {
            this.$store.dispatch('newsModule/read', {
                id : id
            });
        },
        deleteStory() {
            this.$store.dispatch('newsModule/delete', {
                id : this.story.id
            }).then(() => {
                this.$router.push('/');
            }).catch((err) => {
                console.log(err);
            });
        }
    }
};
</script>
