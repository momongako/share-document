<template>
    <div class="list-message">
        <div class="item-message d-flex justify-content-between align-items-center" v-for="(item, index) in dataListPostCategory" :class="index == indexActive ? className : ''" @click="viewPost(index, item)">
            <div class="content-message">
                <span class="title-box-message" :class="index == indexActive ? classTextTitle : ''">{{ item.title }}</span><br/>
                <span class="des-new">{{ item.content | stripHTML  }}</span>
                <div class="comment-message">
                     <span> <span><img src="/assets/img/ic_cmt_28.png"> {{ item.comment.length }} </span>
                     <span><img src="/assets/img/ic_view_28.png"> {{ item.view || 0 }} </span> </span>
                </div>
                <span class="name-user-message">{{ item.user.name }}</span>
                <span class="date-message">{{ item.created_at | formatOnlyDate }}</span>
            </div>
            <div v-if="showOptionType" class="right-img-message">
                <img v-if="item.type == postBirthDay" width="56px" height="56px"
                     src="/assets/img/icon_birthday.png">
                <img v-if="item.type == postOnboard" width="56px" height="56px"
                     src="/assets/img/icon_onboard.png">
                <img v-if="item.type == postMarriage" width="56px" height="56px"
                     src="/assets/img/icon_marriage.png">
            </div>
            <div v-else class="right-img-message">
                <img v-if="item.image" class="img-message" :src="'/storage/' + item.image">
                <img v-else class="img-message" :src="imageDefault">
            </div>
        </div>
    </div>
</template>

<script>
import EventBus from "../../EventBus";
import {IMAGE_DEFAULT} from "../../const";
import * as Const from "../../const";
export default {
    props: ['listPostCategory', 'titlePage', 'firstPostCategory', 'showOptionType'],
    emits: ['data-post-category'],
    created() {
        this.dataListPostCategory = this.listPostCategory
        EventBus.$on('updateDetailCommentPost', this.handler);
        EventBus.$on('pushDataListPostCategory', this.updateListPost);
        this.postBirthDay = Const.POST_BIRTH_DAY;
        this.postOnboard = Const.POST_ONBOARD;
        this.postMarriage = Const.POST_MARRIAGE;
    },
    methods: {
        viewPost(index, item) {
            var that =this
            axios
                .get('/view-post', {params: {postId: item.id}})
                .then((response) => {
                    if (response.data.result) {
                        that.indexActive = index;
                        that.dataListPostCategory.map(function (postCategory) {
                            if (postCategory.id == item.id) {
                                postCategory.view = response.data.detailPostCategory.view
                            }
                        })
                        that.$emit('data-post-category', response.data.detailPostCategory)
                        EventBus.$emit('detailPostCategory', response.data.detailPostCategory);
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
        handler(e) {
            this.dataListPostCategory.map(function (item) {
                if (item.id === e.detailPostCategory.id) {
                    item.comment.unshift(e.listCommentPost)
                }
            })
        },
        updateListPost(e) {
            this.dataListPostCategory = e;
        }
    },
    data() {
        return {
            indexActive: 0,
            className: 'item-message-active',
            classTextTitle: 'highlight-title',
            dataListPostCategory: '',
            imageDefault: IMAGE_DEFAULT,
            postBirthDay: '',
            postOnboard: '',
            postMarriage: '',
        }
    },
    destroyed() {
        EventBus.$off('updateDetailCommentPost', this.handler);
        EventBus.$off('pushDataListPostCategory', this.updateListPost);
    },
}
</script>
