<template>
    <site>
        <div slot="content">
            <h3><v-icon large>add</v-icon>&nbsp;Create News</h3>
            <news-form></news-form>
        </div>
    </site>
</template>

<script>
    import NewsForm from './form.vue';
    import Site from '@/site/components/site.vue';
    export default {
        components : {
            NewsForm,
            Site
        }
    };
</script>
