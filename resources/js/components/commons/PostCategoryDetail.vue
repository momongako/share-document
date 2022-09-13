<template>
    <div v-if="hidePageEditPost" class="box-detail-message">
        <div class="scroll-message-detail">
            <div class="create-post position-relative">
                <button v-if="showButtonCreat" class="button-create-post position-absolute" type="button" @click="showPageCreatePost">New
                    post
                </button>
            </div>
            <hr style="margin-top: 95px"/>
            <div v-if="dataPostCategory">
                <span class="title-box-detail">{{ dataPostCategory.title }}</span>
                <div v-if="!showOptionType" class="css-image-detail">
                    <img v-if="dataPostCategory.image" width="100%" height="428px" class="img-message-detail"
                         :src="'/storage/' + dataPostCategory.image">
                    <img v-else width="100%" height="428px" class="img-message-detail"
                         :src="imageDefault">
                </div>
                <div class="comment-message-detail align-items-center position-relative">
                     <span> <span><img src="/assets/img/ic_cmt_28.png"> {{ dataPostCategory.comment.length }} </span>
                     <span><img src="/assets/img/ic_view_28.png"> {{ dataPostCategory.view || 0 }} </span> </span>
                    <svg v-if="(dataPostCategory.user_id === infoUser.id) || (infoUser.is_admin === isAdmin)" @click="openEditDelete" v-click-outside="onClickOutside" xmlns="http://www.w3.org/2000/svg"
                         width="30" height="30" fill="currentColor"
                         class="bi bi-three-dots-vertical more-setting position-absolute" viewBox="0 0 16 16">
                        <path
                            d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    </svg>
                    <div v-if="showCardPopup" class="card-popup">
                        <p class="edit-post-category" @click="showPageEditPost">Edit</p>
                        <p class="delete-post-category" @click="deletePostCategory">Delete</p>
                    </div>
                </div><br/>
                <span v-html="dataPostCategory.content" id="fix-width-image" class="des-new"></span><br/>
                <div class="d-flex align-items-center">
                    <img v-if="dataPostCategory.user.image" class="avatar-user-comment" :src="dataPostCategory.user.image">
                    <img v-else class="avatar-user-comment" src="/assets/img/avatar_default.png">
                    <span class="name-user-message-detail">{{ dataPostCategory.user.name }}&nbsp;|</span>
                    <span class="date-message-detail">&nbsp;{{ dataPostCategory.created_at | formatOnlyDate }}</span>
                </div>
                <div class="list-attach-document">
                <a class="attach-document" v-for="(file, index) in dataPostCategory.file" @click="download(file.path, file.name)">{{
                        file.name || ''
                    }}<svg style="margin-left: 16px; margin-top: -6px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-box-arrow-in-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1h-2z"/>
                    <path fill-rule="evenodd" d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg></a>
                </div>
                <hr/>
                <form class="form-comment position-relative" @submit.prevent="submitComment">
                    <input type="hidden" v-model="commentPost.post_id">
                    <p class="label-comment">Comment</p>
                    <textarea class="form-control input-comment" type="text"
                              v-model="commentPost.comment"
                              name="comment"
                              id="comment"
                              v-validate="'required'"
                              @change="changeInput('comment')"
                    />
                    <div v-show="errors.has('comment')" class="input-group text-danger" role="alert">
                        {{ errors.first('comment') }}
                    </div>
                    <div
                        v-if="sysError.comment"
                        class="input-group text-danger"
                        role="alert"
                    >
                        {{ sysError.comment[0] }}
                    </div>
                    <button class="button-submit-comment position-absolute">Submit</button>
                </form>
                <comment-post v-bind:list-comment-post="listCommentPost" v-bind:info-user="infoUser"></comment-post>
                <b-modal id="modal-1" title="Warning" @ok="handleOk($event, dataPostCategory.id)" content-class="warning-class" header-class="title-warning-class" footer-class="button-warning-class">
                    <p class="warning-pin"> Are you sure you want to delete this post?</p>
                    <template v-slot:modal-footer="{ ok, cancel, hide }">
                        <b-button class="button-close" @click="cancel">Close</b-button>
                        <b-button class="button-submit-comment" @click="ok">Confirm</b-button>
                    </template>
                </b-modal>
                <b-button ref="openModal" class="d-none"  v-b-modal.modal-1>Launch demo modal</b-button>
            </div>
            <div v-else>
                <p class="text-center">No post yet!</p>
            </div>

        </div>
    </div>
    <EditPost v-else
              :titlePage="titlePage"
              :categoryId="categoryId"
              :show-option-type="showOptionType"
              :url-list-post="urlListPost"
              :route-action="routeAction"
              :data-post-category="dataPostCategory"
              :message-toast="messageToast"
              :info-user="infoUser"
    ></EditPost>
</template>
<script>
import CommentPost from "./CommentPost";
import axios from "axios"
import EventBus from "../../EventBus";
import EditPost from "./EditPost"
import BaseDialog from "./BaseDialog"
import {IMAGE_DEFAULT, IS_ADMIN} from "../../const";

export default {
    components: {CommentPost, EditPost, BaseDialog},
    props: ['detailPostCategory', 'showOptionType', 'urlListPost', 'categoryId', 'infoUser', 'showButtonCreat'],
    data() {
        return {
            listCommentPost: [],
            commentPost: {
                post_id: '',
                comment: ''
            },
            sysError: {},
            mess: '',
            showCardPopup: false,
            hidePageEditPost: true,
            titlePage: '',
            routeAction: '',
            dataPostCategory: '',
            messageToast: '',
            isOpen: false,
            customClass: 'title-warning',
            nameDialog: 'dialog-warning',
            imageDefault: IMAGE_DEFAULT,
            isAdmin: ''
        }
    },
    created() {
        this.dataPostCategory = this.detailPostCategory;
        this.listCommentPost = this.detailPostCategory.comment;
        this.isAdmin = IS_ADMIN
    },
    mounted() {
        EventBus.$on('pushDetailPostCategory', this.updateDetailPost);
    },
    watch: {
        detailPostCategory() {
            this.dataPostCategory = this.detailPostCategory;
            this.listCommentPost = this.detailPostCategory.comment
        }
    },
    methods: {
        download(filePath, fileName) {
            axios.get("/download-file", {params: {file: filePath}, responseType: 'blob'})
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', fileName);
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(e => {
                    console.log(e);
                });
        },
        updateDetailPost(e) {
            this.dataPostCategory = e;
        },
        submitComment() {
            this.commentPost.post_id = this.detailPostCategory.id;
            var input = this.commentPost;
            var that = this;
            this.$validator.validateAll().then((valid) => {
                if (valid) {
                    axios
                        .post('/create-comment', input)
                        .then((response) => {
                            if (response.data.result) {
                                that.listCommentPost.unshift(response.data.comment)
                                that.dataPostCategory = response.data.infoPost
                                EventBus.$emit('updateDetailCommentPost', {
                                    detailPostCategory: that.detailPostCategory,
                                    listCommentPost: response.data.comment
                                })
                                that.$forceUpdate();
                                that.commentPost.comment = ""
                                that.$nextTick(() => that.$validator.reset())
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
            });
        },
        changeInput(fieldName) {
            this.$set(this.sysError, fieldName, null);
            this.mess = null;
        },
        openEditDelete() {
            this.showCardPopup = !this.showCardPopup;
        },
        showPageEditPost() {
            this.hidePageEditPost = !this.hidePageEditPost
            this.titlePage = 'Edit Post'
            this.dataPostCategory = this.detailPostCategory
            this.routeAction = 'edit-post'
            this.messageToast = 'Edit post successfully!'
        },
        showPageCreatePost() {
            this.hidePageEditPost = !this.hidePageEditPost
            this.titlePage = 'Create Post'
            this.routeAction = 'create-post'
            this.messageToast = 'Create post successfully!'
            this.dataPostCategory = ''
        },
        onClickOutside() {
            this.showCardPopup = false;
        },
        deletePostCategory() {
            this.$refs.openModal.click();
        },
        closeDeletePost(e) {
            this.isOpen = false;
        },
        handleOk(e, postId) {
            axios
                .delete('/delete-post', {data: {postId: postId}})
                .then((response) => {
                    if (response.data.result) {
                        this.$toast.success('Delete post successfully!', {
                            position: "top-right",
                            timeout: 5000,
                            closeOnClick: true,
                            pauseOnFocusLoss: true,
                            pauseOnHover: true,
                            draggable: true,
                            draggablePercent: 0.6,
                            showCloseButtonOnHover: false,
                            hideProgressBar: true,
                            closeButton: "button",
                            icon: true,
                            rtl: false
                        });
                        window.location = this.urlListPost
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
        likePost(e, dataPostCategory) {

        }
    },
    destroyed() {
        EventBus.$off('pushDetailPostCategory', this.updateHidePageEdit);
    }
}
</script>
<style>
.warning-class {
    width: 488px !important;
    right: 0 !important;
}
.warning-pin {
    text-align:center;
}
.title-warning-class {
    font-style: normal;
    font-weight: 600;
    font-size: 20px;
    line-height: 23px;
    color: #FC3840;
}
.button-warning-class {
    justify-content: center !important;
}
.button-close {
    border: none;
    width: 99px;
    height: 42px;
    background: #FFFFFF;
    font-style: normal;
    font-weight: 500;
    font-size: 14px;
    line-height: 125%;
    color: #757575 !important;
}
.button-close:hover {
    background-color: #FFFFFF;
}
</style>
