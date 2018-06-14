<template>
    <section class="uk-section uk-section-default uk-section-small">
        <div class="uk-container uk-container-expand">
            <div uk-grid>
                <div class="uk-width-1-1">
                    <h4 class="uk-heading-line">
                        <span>{{ $t('news') }} &ndash; {{ $t('create') }}</span>
                    </h4>
                </div>
                <NewsForm />
            </div>
        </div>
    </section>
</template>

<script>
    import messages from '../lang';

    import newsStore from '../store';
    import categoryStore from '@/apps/categories/store';

    import NewsForm from './NewsForm.vue';
    export default {
        i18n : messages,
        components : {
            NewsForm
        },
        created() {
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', newsStore);
            }
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
            }
        }
    };
</script>
