<template>
    <site>
        <div slot="content">
            Home page
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
