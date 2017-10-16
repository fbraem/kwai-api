<template>
    <v-layout row wrap>
        <v-flex xs12>
            <div ref="croppie">
            </div>
        </v-flex>
    </v-layout>
</template>

<script>
    import Croppie from 'croppie';
    import 'croppie/croppie.css';

    export default {
        props : {
            value : {
                required : true
            },
            url : {
                type : String
            },
            boundary : {
                type : Object,
                default : function() {
                    return {
                        width : 400,
                        height : 400
                    };
                }
            },
            customClass : {
                type : String,
                default : ''
            },
            enableExif : {
                type : Boolean,
                default : false
            },
            enableOrientation : {
                type : Boolean,
                default : false
            },
            enableZoom : {
                type : Boolean,
                default : true
            },
            enforceBoundary : {
                type : Boolean,
                default : true
            },
            mouseWheelZoom : {
                type : Boolean,
                default : true
            },
            showZoomer : {
                type : Boolean,
                default : true
            },
            viewport : {
                type : Object,
                default : function() {
                    return {
                        width : 100,
                        height : 100,
                        type : 'square'
                    };
                }
            }
        },
        data() {
            return {
                croppie : null,
                result : null,
                filename : null
            }
        },
        mounted() {
            var el = this.$refs.croppie;

            el.addEventListener('update', (ev) => {
                this.$emit('input', {
                    'crop' : ev.detail
                });
                this.croppie.result({ format : 'blob' }).then((output) => {
                    this.$emit('result', output);
                    this.result = output;
                });
            });

            this.croppie = new Croppie(el, {
                boundary : this.boundary,
                customClass : this.customClass,
                enableExif : this.enableExif,
                enableOrientation : this.enableOrientation,
                enableZoom : this.enableZoom,
                enforceBoundary : this.enforceBoundary,
                mouseWheelZoom : this.mouseWheelZoom,
                showZoomer : this.showZoomer,
                viewport : this.viewport
            });
            if (this.url) {
                this.croppie.bind({
                    url : this.url
                });
            }
        },
        destroy() {
            this.croppie.destroy();
        },
        methods : {
        },
        watch : {
            url(nv) {
                if (nv) {
                    this.croppie.bind({
                        url : nv
                    });
                }
            }
        }
    };
</script>
