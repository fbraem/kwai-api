<template>
    <div class="text-xs-center">
        <v-container>
            <v-layout justify-center>
                <v-flex xs8>
                    <v-pagination :length.number="pageCount" :total-visible="maxPagesToShow" v-model="currentPage"></v-pagination>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
    export default {
        props : {
            maxPagesToShow : {
                type : Number,
                default : 10
            },
            limit : Number,
            offset : Number,
            count : Number
        },
        data() {
            var currentPage = 1;
            for(var offset = 0; offset < this.offset; currentPage++) {
                 offset += this.limit;
            }
            return {
                currentPage : currentPage,
                pageCount : Math.ceil(this.count / this.limit)
            };
        },
        watch : {
            currentPage(nv) {
                if (nv) {
                    var offset = 0;
                    for(var page = 1; page < nv; page++) {
                         offset += this.limit;
                    }
                    this.$emit('page', offset);
                }
            }
        }
    }
</script>
