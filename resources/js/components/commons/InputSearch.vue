<template>
    <div class="input-group input-search-message align-items-center">
        <input type="text" class="form-control css-input-search" placeholder="" name="search" v-model="search" v-on:keyup.enter="searchInput">
        <div class="input-group-append align-items-center">
            <img v-if="showClearSearch" class="icon-clear-search" width="30px" height="30px" :src="srcClear" @click="clearInputSearch"/>
            <img class="icon-filter" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-left: 5px" width="30px" height="30px" :src="srcFilter"/>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" @click="sortLastPost">Latest post</a>
                <a class="dropdown-item" @click="sortMostView">Most viewed</a>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import EventBus from "../../EventBus";
import {FILTER_ASC, FILTER_DESC} from "../../const";
export default {
    props: ['srcClear', 'srcFilter', 'urlListPost'],
    data() {
        return {
            showClearSearch: false,
            search: '',
            lastPost: false,
            mostView: false
        }
    },
    created() {

    },
    methods: {
        clearInputSearch() {
            this.search = ''
            location.reload();
        },
        sortMostView() {
            this.mostView = !this.mostView;
            var filterMostView = '';
            if (this.mostView) {
                filterMostView = FILTER_DESC;
            } else {
                filterMostView = FILTER_ASC;
            }
            let routeAction = '/' + this.urlListPost.split('/').pop();
            axios
                .get(routeAction, {params: {filterMostView: filterMostView}})
                .then((response) => {
                    if (response.data.result) {
                        EventBus.$emit('pushDetailPostCategory', response.data.firstPostCategory);
                        EventBus.$emit('pushDataListPostCategory', response.data.listPostCategory);
                    }
                })
                .catch((err) => {
                    if (err.response.data.message) {
                        this.mess = err.response.data.message;
                    } else {
                        this.sysError = err.response.data;
                    }
                });
        },
        sortLastPost() {
            this.lastPost = !this.lastPost;
            var filterLastPost = '';
            if (this.lastPost) {
                filterLastPost = FILTER_ASC;
            } else {
                filterLastPost = FILTER_DESC;
            }

            let routeAction = '/' + this.urlListPost.split('/').pop();
            axios
                .get(routeAction, {params: {filterLastPost: filterLastPost}})
                .then((response) => {
                    if (response.data.result) {
                        EventBus.$emit('pushDetailPostCategory', response.data.firstPostCategory);
                        EventBus.$emit('pushDataListPostCategory', response.data.listPostCategory);
                    }
                })
                .catch((err) => {
                    if (err.response.data.message) {
                        this.mess = err.response.data.message;
                    } else {
                        this.sysError = err.response.data;
                    }
                });
        },
        searchInput() {
            this.showClearSearch = true;
            let search = this.search
            let routeAction = '/' + this.urlListPost.split('/').pop();
            axios
                .get(routeAction, {params: {search: search}})
                .then((response) => {
                    if (response.data.result) {
                        EventBus.$emit('pushDetailPostCategory', response.data.firstPostCategory);
                        EventBus.$emit('pushDataListPostCategory', response.data.listPostCategory);
                    }
                })
                .catch((err) => {
                    if (err.response.data.message) {
                        this.mess = err.response.data.message;
                    } else {
                        this.sysError = err.response.data;
                    }
                });
        }
    },
}
</script>

