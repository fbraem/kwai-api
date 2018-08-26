<template>
    <ul class="uk-pagination uk-flex-center">
        <li v-if="currentPage > 1"><a @click="currentPage -= 1"><span uk-pagination-previous></span></a></li>
        <template v-for="page in pages">
            <li :class="{ 'uk-disabled' : page == '...' || page == currentPage }">
                <a @click="currentPage = page"><span :class="{ 'uk-text-bold' : page == currentPage }">{{ page }}</span></a>
            </li>
        </template>
        <li v-if="currentPage < pageCount"><a @click="currentPage += 1"><span uk-pagination-next></span></a></li>
    </ul>
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
            var pageCount = Math.ceil(this.count / this.limit);
            return {
                currentPage : currentPage,
                pageCount : pageCount,
                pages : this.pagination(currentPage, pageCount)
            };
        },
        watch : {
            currentPage(nv) {
                if (nv) {
                    var offset = 0;
                    for(var page = 1; page < nv; page++) {
                         offset += this.limit;
                    }
                    this.pages = this.pagination(nv, this.pageCount);
                    this.$emit('page', offset);
                }
            }
        },
        methods : {
            pagination(currentPage, pageCount) {
                const delta = 2;

                let range = [];
                for (let i = Math.max(2, currentPage - delta); i <= Math.min(pageCount - 1, currentPage + delta); i++) {
                    range.push(i);
                }
                if (currentPage - delta > 2) {
                    range.unshift("...")
                }
                if (currentPage + delta < pageCount - 1) {
                    range.push("...")
                }

                range.unshift(1);
                range.push(pageCount);

                return range;
            }
        }
    }
</script>
