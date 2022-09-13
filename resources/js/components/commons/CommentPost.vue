<template>
    <div class="list-comment">
        <div class="item-comment d-flex" v-for="(item, index) in dataListComment">
            <div>
                <img v-if="item.user.image" class="avatar-user-comment" :src="item.user.image">
                <img v-else class="avatar-user-comment" src="/assets/img/avatar_default.png">
            </div>
            <div v-if="showEditComment && item.id === position" style="width: 100%">
                <textarea class="form-control input-edit-comment" type="text"
                          v-model="commentPost.contentComment"
                          @keyup.enter="submitEdit($event, item)"
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
            </div>

            <div v-else class="content-comment">
                <div class="d-flex align-items-center">
                    <span class="des-comment">{{ item.content }}</span>
                    <svg v-if="item.user_id === infoUser.id || infoUser.is_admin === isAdmin"
                         xmlns="http://www.w3.org/2000/svg"
                         width="15" height="15" fill="currentColor"
                         class="bi bi-three-dots-vertical"
                         viewBox="0 0 16 16"
                         style="margin-left: 20px"
                         id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    >
                        <path
                            d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    </svg>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" @click="editComment($event, item)">Edit</a>
                        <a class="dropdown-item" @click="deleteComment($event, item)">Delete</a>
                    </div>
                </div>

                <div class="user-date-comment">
                    <span class="user-comment">{{ item.user.name }}</span>
                    <span class="date-comment">{{ item.created_at | formatDate }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import EventBus from "../../EventBus";
import axios from "axios";
import {IS_ADMIN} from "../../const";

export default {
    props: ['listCommentPost', 'infoUser'],
    data() {
        return {
            dataListComment: '',
            showEditComment: false,
            position: '',
            commentPost: {
                commentId: '',
                contentComment: ''
            },
            sysError: {},
            mess: '',
            messageToast: '',
            isAdmin: ''
        }
    },
    created() {
        this.dataListComment = this.listCommentPost
        EventBus.$on('detailPostCategory', this.handler);
        this.isAdmin = IS_ADMIN;
    },
    watch: {
        listCommentPost() {
            this.dataListComment = this.listCommentPost;
        }
    },
    methods: {
        handler(e) {
            this.dataListComment = e.comment
        },
        editComment(e, item) {
            this.showEditComment = true;
            this.position = item.id
            this.commentPost.contentComment = item.content
        },
        submitEdit(e, item) {
            this.showEditComment = false;
            this.dataListComment.map((itemComment) => {
                if (itemComment.id === item.id) {
                    itemComment.content = this.commentPost.contentComment;
                }
            });
            this.commentPost.commentId = item.id;
            let data = new FormData();
            let commentPost = this.commentPost
            for (let key in commentPost) {
                if (typeof commentPost[key] === "object") {
                    commentPost[key].forEach(item =>
                        data.append(key + "[]", item)
                    );
                } else {
                    data.append(key, commentPost[key]);
                }
            }
            data.append("_method", "PUT");
            var that = this;
            this.$validator.validateAll().then((valid) => {
                if (valid) {
                    axios
                        .post('/edit-comment', data)
                        .then((response) => {
                            if (response.data.result) {
                                that.commentPost.contentComment = '';
                            }
                        })
                        .catch((err) => {
                            if (err.response.data.message) {
                                this.mess = err.response.data.message;
                            } else {
                                this.sysError = err.response.data;
                            }
                        });
                } else {
                    this.showEditComment = true;
                }
            });

        },
        deleteComment(e, item) {
            const resIndex = this.dataListComment.findIndex(res => res.id === item.id);
            this.dataListComment.splice(resIndex, 1);
            this.messageToast = 'Delete successfully!'
            axios
                .delete('/delete-comment', {data: {commentId: item.id}})
                .then((response) => {
                    if (response.data.result) {
                        this.$toast.success(this.messageToast, {
                            position: "top-right",
                            timeout: 3000,
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
                    } else {
                        this.$toast.error("An error has occurred!", {
                            position: "top-right",
                            timeout: 3000,
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
        changeInput(fieldName) {
            this.$set(this.sysError, fieldName, null);
            this.mess = null;
        },
    },
    destroyed() {
        EventBus.$off('detailPostCategory', this.handler);
    },
}
</script>
<style lang="scss">
.list-comment {
    .item-comment {
        .content-comment {
            #dropdownMenuButton:hover {
                cursor: pointer;
            }
            .dropdown-menu {
                .dropdown-item:hover {
                    cursor: pointer;
                }
            }
        }

    }
}
</style>
