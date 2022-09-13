<template>
    <div class="box-edit-post">
        <form class="form-edit-post" enctype="multipart/form-data" @submit.prevent="save">
            <div class="header-edit-post position-relative">
                <span class="title-page">{{ titlePage }}</span>
                <div class="button-event position-absolute">
                    <button type="submit" class="button-submit">Submit</button>
                    <button @click="returnListPost" type="button" class="button-cancel">Cancel</button>
                </div>
            </div>
            <hr/>
            <div class="form-group form-input-title">
                <span class="input-title">Title</span>
                <input class="input-text-title" name="title" placeholder="Please enter the post title"
                       v-model="infoPostCategory.title"
                       v-validate="'required'"
                       @change="changeInput('title')"
                />
                <div v-show="errors.has('title')" class="input-group text-danger" role="alert">
                    {{ errors.first('title') }}
                </div>
                <div
                    v-if="sysError.title"
                    class="input-group text-danger"
                    role="alert"
                >
                    {{ sysError.title[0] }}
                </div>
            </div>
            <div class="form-group form-input-summary">
                <span class="input-title">Summary</span>
                <input class="input-text-summary" name="summary" placeholder="Please enter the post summary"
                       v-model="infoPostCategory.summary"
                />
            </div>
            <div class="form-group form-input-active align-items-center d-flex">
                <span class="input-title col-md-2">Active</span>
                <div class="input-radio d-flex align-items-center">
                    <input style="width: 24px; height: 24px" type="radio" id="active" name="status"
                           value="1" v-model="infoPostCategory.status" @change="onChange($event)">
                    <label class="text-radio" for="active">Yes</label>
                    <input style="width: 24px; height: 24px" type="radio" id="deactive" name="status" value="2"
                           v-model="infoPostCategory.status" @change="onChange($event)">
                    <label class="text-radio" for="deactive">No</label>
                </div>
            </div>
            <div v-if="!showOptionType && infoUser.is_admin === isAdmin" class="form-group form-input-pinned align-items-center d-flex">
                <span class="input-title col-md-2">Pinned post?</span>
                <div class="input-radio d-flex align-items-center">
                    <input :disabled="disabled == true" style="width: 24px; height: 24px" type="radio" id="pinned" name="is_pin" value="1"
                           v-model="infoPostCategory.is_pin"/>
                    <label class="text-radio" for="pinned">Yes</label>
                    <input :disabled="disabled == true" style="width: 24px; height: 24px" type="radio" id="notpinned" name="is_pin" value="2"
                           v-model="infoPostCategory.is_pin" />
                    <label class="text-radio" for="notpinned">No</label>
                </div>
            </div>
            <div v-if="showOptionType" class="form-group form-input-type align-items-center">
                <p class="input-title">Post type</p>
                <select class="select-type-post" v-model="selectedType">
                    <option v-for="(item, index) in listOptionType" :value="item.value">{{ item.name }}</option>
                </select>
            </div>
            <div class="form-group form-input-content">
                <span class="input-title">Content</span>
                <vue-editor
                    name="content"
                    id="editor"
                    useCustomImageHandler
                    @image-added="handleImageAdded"
                    v-model="infoPostCategory.postContent"
                    v-validate="'required'"
                >
                </vue-editor>

<!--                <textarea class="input-text-content" name="content" placeholder="Please enter the post content"-->
<!--                          v-model="infoPostCategory.postContent"-->
<!--                          v-validate="'required'"-->
<!--                          @change="changeInput('content')"-->
<!--                />-->
                <div v-show="errors.has('content')" class="input-group text-danger" role="alert">
                    {{ errors.first('content') }}
                </div>
                <div
                    v-if="sysError.postContent"
                    class="input-group text-danger"
                    role="alert"
                >
                    {{ sysError.postContent[0] }}
                </div>
            </div>
            <div class="form-input-img-doc d-flex">
                <div v-if="!showOptionType" class="form-group form-input-image">
                    <span class="input-title">Image</span>
                    <div v-if="!urlImageUpload" class="select-image text-center" @click="$refs.uploader.click()">
                        <img :src="iconUploadImg"><br/>
                        <span class="des-upload-image">Drop your image
                                <span
                                    style="color:#0085F9; font-weight: 500; font-size: 14px; line-height: 16px">here</span> or
                                <span style="color:#0085F9; font-weight: 500; font-size: 14px; line-height: 16px">browser</span>
                            </span><br/>
                        <span class="text-attach-img">Attachment (jpg, png, jpeg only. Maximum file size is 5MB)</span>
                    </div>
                    <div v-else class="select-image uploaded-image text-center" id="preview">
                        <img v-if="dataPostCategory.image && showImageEdit" class="show-image" width="120px" height="90px" :src="'/storage/' + urlImageUpload"/>
<!--                        <img v-else-if="showImageEdit" class="show-image" width="120px" height="90px" :src="imageDefault"/>-->
                        <img v-else class="show-image" width="120px" height="90px" :src="urlImageUpload"/>
                        <div @click="removeImage" class="remove-image align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-trash" viewBox="0 0 16 16">
                                <path
                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                <path fill-rule="evenodd"
                                      d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                            <span class="text-remove">Remove</span>
                        </div>
                    </div>
                    <input name="image" type="file" class="d-none" ref="uploader" @change="onFileChanged"/>
                    <div
                        v-if="sysError.content"
                        class="input-group text-danger"
                        role="alert"
                    >
                        {{ sysError.content[0] }}
                    </div>
                    <div
                        class="text-danger input-group"
                        role="alert"
                        v-if="messErrorImage"
                    >
                        {{ messErrorImage }}
                    </div>
                </div>
                <div class="form-group form-input-doc">
                    <span class="input-title">Attachments</span>
                    <div class="select-file text-center" :class="addClassDocAttach">
                        <button class="button-choose-file" type="button" @click="$refs.attachment.click()">Choose files</button>
                        <br/>
                        <span class="text-attach-doc">Attachment (jpg, pdf, doc, docx, xls, xlsx, ppt, pptx only. Maximum file size is 50MB)</span>
                        <hr/>
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="list-attach-doc" :class="showAttach">
                                <span class="name-attach-doc" v-for="(item, index) in listDocAttach" :key="index"><span class="text-name-file">{{ item.name }}</span> <img @click="removeFileAttach($event,index, item.id)" class="icon-clear-search" width="30px" height="30px" src="/assets/img/clear_search.svg"></span>
                            </div>
                        </div>
                    </div>
                    <input name="filesAttach" multiple type="file" ref="attachment" class="d-none"
                           @change="onDocChanged"
                    />
                    <div
                        v-if="checkFileUpload"
                        class="input-group text-danger"
                        role="alert"
                    >
                        {{ errMessageEncrypt }}
                    </div>
                    <div
                        class="text-danger input-group"
                        role="alert"
                        v-if="messErrorFile"
                    >
                        {{ messErrorFile }}
                    </div>
                </div>
            </div>
        </form>
        <b-modal id="modal-1" title="Warning" @ok="handleOk" content-class="warning-class" header-class="title-warning-class" footer-class="button-warning-class">
            <p class="warning-pin">You have reached the maximum number of Pinned posts, please unpin one/some posts and try again</p>
            <template v-slot:modal-footer="{ ok, cancel, hide }">
                <b-button class="button-close" @click="cancel">Close</b-button>
<!--                <b-button class="button-submit-comment" @click="ok">Submit</b-button>-->
            </template>
        </b-modal>
        <b-button ref="openModal" class="d-none"  v-b-modal.modal-1>Launch demo modal</b-button>
    </div>
</template>

<script>
import axios from "axios"
import EventBus from "../../EventBus";
import * as Const from "../../const";
import BaseDialog from "./BaseDialog"
import {NOT_PIN} from "../../const";

export default {
    props: ['titlePage', 'categoryId', 'showOptionType', 'urlListPost', 'dataPostCategory', 'routeAction', 'messageToast', 'infoUser'],
    components: {Const, BaseDialog},
    created() {
        if (this.routeAction === 'edit-post') {
            this.infoPostCategory.title = this.dataPostCategory.title
            this.infoPostCategory.summary = this.dataPostCategory.summary
            this.infoPostCategory.postContent = this.dataPostCategory.content
            this.infoPostCategory.status = this.dataPostCategory.status
            this.infoPostCategory.is_pin = this.dataPostCategory.is_pin
            this.urlImageUpload = this.dataPostCategory.image
            this.listDocAttach = this.dataPostCategory.file
            this.selectedType = this.dataPostCategory.type ?? this.selectedType

            if (this.dataPostCategory.status === Const.STATUS_DEACTIVE) {
                this.disabled = true
            }
        }
        this.isAdmin = Const.IS_ADMIN;
    },
    data() {
        return {
            iconUploadImg: '/assets/img/icon_upload_image.png',
            urlImageUpload: null,
            listDocAttach: [],
            classDocAttach: '',
            showAttach: 'd-none',
            infoPostCategory: {
                title: '',
                summary: '',
                status: Const.STATUS_ACTIVE,
                is_pin: Const.NOT_PIN,
                postContent: '',
                filesAttach: [],
                categoryId: ''
            },
            selectedType: Const.POST_BIRTH_DAY,
            listOptionType: [
                {name: 'Birthday', value: Const.POST_BIRTH_DAY},
                {name: 'Onboard', value: Const.POST_ONBOARD},
                {name: 'Marriage', value: Const.POST_MARRIAGE}
            ],
            imageUpload: '',
            sysError: {},
            mess: '',
            isShowMessageImage: false,
            isShowMessageFile: false,
            messErrorImage: "",
            messErrorFile: "",
            customClass: 'title-warning',
            nameDialog: 'dialog-warning',
            isOpen: false,
            removeImageUpload: Const.NOT_REMOVE_IMAGE,
            removeFile: Const.NOT_REMOVE_IMAGE,
            listFileRemove: [],
            imageDefault: Const.IMAGE_DEFAULT,
            showImageEdit:  true,
            isAdmin: '',
            disabled: false,
            checkFileUpload: false,
            errMessageEncrypt: ''
        }
    },
    methods: {
        handleImageAdded: function(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("image", file);

            axios.post('/upload-image-ckeditor', formData)
                .then(result => {
                    if (result.data.result) {
                        const url = result.data.path; // Get url from response
                        Editor.insertEmbed(cursorLocation, "image", url);
                        resetUploader();
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        },
        handleOk() {
            location.reload();
        },
        onFileChanged(e) {
            var filePath = e.target.files;
            var file = filePath[0];
            var imageReg = /[\/.](jpg|jpeg|png|JPEG|JPG|PNG)$/i
            if (filePath.length !== 0) {
                this.selectedFile = file
                let fileName = file.name;

                if (!imageReg.test(fileName)) {
                    this.isShowMessageImage = true;
                    this.urlImageUpload = null
                    this.imageUpload = '';
                    this.messErrorImage = 'Uploaded file is not in the correct format'
                } else if ((file.size / (1024 * 1024)) > Const.MAX_UPLOAD_IMAGE) {
                    this.isShowMessageImage = true;
                    this.urlImageUpload = null
                    this.imageUpload = '';
                    this.messErrorImage = 'Maximum file upload size exceeded'
                } else {
                    this.urlImageUpload = URL.createObjectURL(file);
                    this.showImageEdit = false;
                    this.imageUpload = file;
                    this.isShowMessageImage = false;
                    this.messErrorImage = ''
                }
            }
        },
        onDocChanged(e) {
          let files = e.target.files;
          this.checkDocUpload(files);
        },
        save() {
            var that = this;
            this.$validator
                .validateAll()
                .then((valid) => {
                    if (this.listDocAttach.length >= 0) {
                        that.isShowMessageFile = false;
                    }

                    if (valid && !that.isShowMessageImage && !that.isShowMessageFile) {
                        that.submit();
                    }
                })
                .catch(function (e) {
                    this.$toast.error("An error has occurred!", {
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
                });
        },
        submit(e) {
            let data = new FormData();
            var that = this
            this.infoPostCategory.categoryId = this.categoryId
            let dataPostCategory = this.infoPostCategory
            for (let key in dataPostCategory) {
                if (typeof dataPostCategory[key] === "object") {
                    dataPostCategory[key].forEach(item =>
                        data.append(key + "[]", item)
                    );
                } else {
                    data.append(key, dataPostCategory[key]);
                }
            }
            data.append('image', this.imageUpload)
            data.append('type', this.selectedType)

            if(this.routeAction === 'edit-post') {
                data.append("_method", "PUT");
                data.append('id', this.dataPostCategory.id)
                data.append('removeFile', this.removeFile)
                data.append('listFileRemove', this.listFileRemove)
                data.append('removeImageUpload', this.removeImageUpload)
            }

            axios
                .post(this.routeAction, data)
                .then((response) => {
                    if (response.data.result) {
                        this.$toast.success(this.messageToast, {
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
                    if (err.response.status === 403) {
                        this.$toast.error("You do not have access!", {
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
                    } else if (err.response.data.errorPin) {
                        that.$refs.openModal.click();
                    } else if (err.response.data.message) {
                        that.mess = err.response.data.message;
                    } else {
                        for (const [key, value] of Object.entries(err.response.data)) {
                            that.errMessageEncrypt = value[0]
                        }
                        that.checkFileUpload = true;
                    }
                });
        },
        changeInput(fieldName) {
            this.sysError = {};
            this.mess = null;
        },
        removeImage(e) {
            this.removeImageUpload = Const.REMOVE_IMAGE;
            this.urlImageUpload = null
            this.imageUpload = '';
        },
        removeFileAttach(e, index, fileId) {
            this.listDocAttach.splice(index, 1);
            this.infoPostCategory.filesAttach.splice(index, 1);
            this.removeFile = Const.REMOVE_IMAGE;
            this.listFileRemove.push(fileId);
            if (this.listDocAttach.length == 0) {
                this.$refs.attachment.value = null;
                this.errMessageEncrypt = ''
            }
        },
        closeDialogWarning(e) {
            this.isOpen = false;
        },
        returnListPost(e) {
            location.reload();
        },
        onChange(e) {
            if (e.target.value == Const.STATUS_DEACTIVE) {
                this.disabled = true;
                this.infoPostCategory.is_pin = Const.NOT_PIN
            } else {
                this.disabled = false;
            }
        },
        checkDocUpload(files) {
            var totalFileSize = 0;
            var fileReg = /[\/.](pdf|doc|docx|xls|xlsx|jpg|ppt|pptx)$/i
            for (let file of files) {
                let fileName = file.name;
                if (!fileReg.test(fileName)) {
                    this.isShowMessageFile = true
                    this.messErrorFile = "Uploaded file is not in the correct format"
                } else {
                    totalFileSize += file.size;
                    let reader = new FileReader();
                    reader.readAsDataURL(file);
                    this.infoPostCategory.filesAttach.unshift(file);
                    this.listDocAttach.unshift(file);
                    this.isShowMessageFile = false;
                    this.messErrorFile = "";
                    this.sysError = {};
                }
            }

            if ((totalFileSize / (1024 * 1024)) > Const.MAX_UPLOAD_ATTACHMENT) {
                this.infoPostCategory.filesAttach = [];
                this.listDocAttach = [];
                this.isShowMessageFile = true
                this.messErrorFile = 'Maximum file upload size exceeded'
            }
        }
    },
    computed: {
        addClassDocAttach() {
            if (this.listDocAttach.length === 0) {
                return ''
            } else {
                this.showAttach = ''
                return 'doc-attached'
            }
        }
    },
    watch: {

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
