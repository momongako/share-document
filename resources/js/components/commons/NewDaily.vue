<template>
    <div class="news-daily">
        <div class="title-question">What's happening in my LDCV?</div>
        <div class="grid-container">
            <div class="item-new" v-for="(item, index) in dataListNew" @click="viewDetailNew($event, item.id)">
                <img v-if="item.image" class="img-new"
                     :src="'/storage/' + item.image">
                <img v-else class="img-new"
                     :src="imageDefault">
                <div class="info-new">
                    <div class="label-date position-relative">
                        <span :class="item.label_cate">{{
                                item.category.parents ? item.category.parents.name : item.category.name
                            }}</span>
                        <span class="date-new">{{ item.created_at | formatOnlyDate }}</span>
                    </div>
                    <div class="content-new">
                        <p class="title-new">{{ item.title }}</p>
                        <p class="des-new">{{ item.summary  }}</p>
                    </div>
                    <p class="read-more-new">Readmore</p>
                    <div class="img-comment align-items-center position-relative">
                        <img v-if="item.user.image" class="avatar-user-comment" :src="item.user.image">
                        <img v-else class="avatar-user-comment" src="/assets/img/avatar_default.png">
                        <span class="user-name-post">{{ item.user.name }}</span>
                        <div class="comment-new">
                            <span> <span><img src="/assets/img/ic_cmt_28.png"> {{ item.comment.length }} </span>
                            <span><img src="/assets/img/ic_view_28.png"> {{ item.view || 0 }} </span> </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {IMAGE_DEFAULT} from "../../const";
import EventBus from "../../EventBus";

export default {
    props: ['listNewDaily'],
    inject: ['openDialog'],
    created() {
        this.dataListNew = this.listNewDaily
        EventBus.$on('updateCommentDialog', this.handler);
        EventBus.$on('updateViewPost', this.updateViewPost);
    },
    methods: {
        viewDetailNew(e, id) {
            this.openDialog(true, id);
        },
        handler(e) {
            this.dataListNew.map(function (item) {
                if (item.id === e.detailPostCategory.id) {
                    item.comment.unshift(e.listCommentPost)
                }
            })
        },
        updateViewPost(e) {
            this.dataListNew.map(function (item) {
                if (item.id === e.detailPostCategory.id) {
                    item.view = e.detailPostCategory.view
                }
            })
        }
    },
    mounted() {

    },
    data() {
        return {
            avatarUser: 'assets/img/slider.png',
            imageDefault: IMAGE_DEFAULT,
            dataListNew: ''
        }
    },
    destroyed() {
        EventBus.$off('updateCommentDialog', this.handler);
        EventBus.$off('updateViewPost', this.updateViewPost);
    },
}
</script>
<style scoped>
.news-daily {
    margin-top: 40px;

}
.grid-container {
    width: calc(100% + 24px);
    flex-wrap: wrap;
    margin-top: 20px;
    margin-left: -24px;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 10px;
}

.grid-container > div {
    background-color: rgba(255, 255, 255, 0.8);
    padding: 20px 0;
}

.img-new {
    object-fit: cover;
    height: 166px;
    width: 100%;
    border-radius: 6px 6px 0px 0px;
    flex: none;
    order: 0;
    flex-grow: 0;
}

.item-new {
    margin-bottom: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0px;
    /*height: 419px*/
    height: auto;
    background: #FFFFFF;
    border-radius: 10px;
}

.list-news {
    margin-top: 20px;
}

.img-comment {
    display: flex;
    margin-bottom: 16px;
}

.user-name-post {
    font-size: 11px;
    word-break: break-word;
    width: 130px;
    line-height: 16px;
    color: #757575;
}

.title-question {
    font-style: normal;
    font-weight: 700;
    font-size: 32px;
    color: #101010;
}

.label-date {
    display: flex;
    align-items: center;
    margin-top: 16px;
}

.label-cate-community {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 4px 12px;
    gap: 10px;
    width: fit-content;
    background: #FC3840;
    border-radius: 30px;
    font-style: normal;
    font-weight: 500;
    font-size: 12px;
    line-height: 14px;
    color: #FFFFFF;
}

.label-cate-hr {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 4px 12px;
    gap: 10px;
    width: fit-content;
    background: #62E02C;
    border-radius: 30px;
    font-style: normal;
    font-weight: 500;
    font-size: 12px;
    line-height: 14px;
    color: #FFFFFF;
}

.label-cate-notification {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 4px 12px;
    gap: 10px;
    width: fit-content;
    background: #0085F9;
    border-radius: 30px;
    font-style: normal;
    font-weight: 500;
    font-size: 12px;
    line-height: 14px;
    color: #FFFFFF;
}

.date-new {
    font-style: normal;
    font-weight: 400;
    font-size: 12px;
    line-height: 14px;
    text-align: center;
    color: #757575;
    position: absolute;
    right: 0;
}

.content-new {
    margin-top: 12px;
}

.title-new {
    font-style: normal;
    font-weight: bold;
    font-size: 20px;
    line-height: 23px;
    color: #101010;
}

.des-new {
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    line-height: 16px;
    color: #757575;
    width: 300px;
    word-wrap: break-word;
}

.avatar-comment {
    box-sizing: border-box;
    vertical-align: middle;
    border-radius: 50%;
}

.comment-new {
    position: absolute;
    right: 0;
    color: #757575;
}

.read-more-new {
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    line-height: 16px;
    color: #0085F9;
}

</style>
