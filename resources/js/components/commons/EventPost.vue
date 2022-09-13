<template>
    <div class="event-post">
        <div class="item-event-post" v-for="(item, index) in items" :key="index">
            <img v-if="item.type == postBirthDay" width="56px" height="56px"
                 src="/assets/img/icon_birthday.png">
            <img v-if="item.type == postOnboard" width="56px" height="56px"
                 src="/assets/img/icon_onboard.png">
            <img v-if="item.type == postMarriage" width="56px" height="56px"
                 src="/assets/img/icon_marriage.png">
            <div class="content-event-post" @click="viewDetailPostEvent($event, item.id)">
                <span class="title-event-post">[{{ item.title }}]</span>
                <span class="date-event-post">{{ item.created_at | formatOnlyDate }}</span><br/>
                <span class="des-event-post">{{ item.summary }}</span>
            </div>
        </div>
        <infinite-loading @infinite="infiniteHandler">
            <div style="margin-top: 20px" slot="no-more">No more post!</div>
            <div style="margin-top: 20px" slot="no-results">No results post!</div>
            <div style="margin-top: 20px" slot="error">An error has occurred!</div>
        </infinite-loading>
    </div>
</template>
<script>
import * as Const from "../../const";
import InfiniteLoading from 'vue-infinite-loading'
import EventBus from "../../EventBus";
export default {
    // props: ['listPostEvent'],
    inject: ['openDetailEventPost'],
    components: {Const, InfiniteLoading},
    created() {
        this.postBirthDay = Const.POST_BIRTH_DAY;
        this.postOnboard = Const.POST_ONBOARD;
        this.postMarriage = Const.POST_MARRIAGE;
    },
    mounted() {
    },
    methods: {
        viewDetailPostEvent(e, id) {
            this.viewModalEvent = true
            this.openDetailEventPost(true, id, this.viewModalEvent);
        },
        infiniteHandler($state) {
            axios.get('/list-post-event', {
                params: {
                    page: this.page,
                },
            }).then(({ data }) => {
                if (data.data.length) {
                    this.page += 1;
                    this.items.push(...data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
    },
    data() {
        return {
            postBirthDay: '',
            postOnboard: '',
            postMarriage: '',
            items: [],
            page: 1,
            viewModalEvent: false
        }
    },
}
</script>
