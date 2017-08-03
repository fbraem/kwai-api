<template>
    <site>
        <div slot="content">
            <section class="uk-section uk-container uk-container-expand">
                Home page
            </section>
        </div>
    </site>
</template>

<script>
    import Site from './components/site.vue';

    import { createPerimeter } from 'vue-kindergarten';
    var sitePerimeter = createPerimeter({
        purpose : 'site',
        can : {
            read : () => true
        }
    });

    export default {
        components : {
            Site
        },
        data() {
            return {};
        },
        perimeters : [
            sitePerimeter
        ]
    };
</script>
