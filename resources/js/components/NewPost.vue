<template>

        <form class="form-horizontal" enctype="multipart/form-data" @submit.prevent="createPost()">
            <div v-show="isVisible && msgAdded" class="alert alert-success" role="alert">
                {{msgAdded}}
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 lbl-post" name="title">Title
                    <span class="required">*</span>
                </label>
                <div class="col-sm-10">
                    <input v-model="post.title" type="text" class="form-control in-post-title" id="title" placeholder="" name="title">
                    <span class="sysErrors" v-show="isVisibleError && msgErrorTitle">{{msgErrorTitle}}</span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2 lbl-post">Category
                    <span class="required">*</span>
                </label>
                <select name="category" v-model="post.category_id" class="custom-select my-1 mr-sm-2 category-select">
                    <option value="">-- Select category --</option>
                    <option v-for="category in categories" v-bind:value="category.id">{{ category.name }}</option>
                </select>
                <span class="sysErrors" v-show="isVisibleError && msgErrorCategoryId">{{msgErrorCategoryId}}</span>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2 lbl-post">Attachment
                    <span class="required">*</span>
                </label>
            </div>
            <div>
                <table class="tbl-attachment">
                    <tr>
                        <th style="width: 60%">File name</th>
                        <th>Size</th>
                        <th>Remove</th>
                    </tr>
                    <tr>
                        <td>abc.jpg</td>
                        <td>100 kb</td>
                        <td><a class="">
                            <img class="img-logo" src="assets/logo/sign-delete-icon.png">
                        </a></td>
                    </tr>
                    <tr style="height: 70px">

                    </tr>

                    <tr style="border-top: 1px solid #dddddd;">
                        <td><button type="button" class="btn btn-default">Add file</button></td>
                        <td style="color:red">*Maximum 50Mb</td>
                    </tr>

                </table>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2 lbl-post" name="content">Content
                    <span class="required">*</span>
                </label>
            </div>
            <div>
                <vue-editor
                        name="content"
                        id="editor"
                        class="post-content"
                        v-model="post.content"
                        v-validate="'required'"
                >
                </vue-editor>

                <!--<textarea v-model="post.content" name="content" class="form-control post-content" rows="3" id="content"></textarea>-->

                <span class="sysErrors" v-show="isVisibleError && msgErrorContent">{{msgErrorContent}}</span>
            </div>

            <div class="form-group post-referrence">
                <label class="control-label col-sm-2 lbl-post">Referrence
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control in-post-referrence" placeholder="Search for post title" name="name">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10 post-btn">

                    <router-link
                            class="btn btn-default btn-cancel"
                            :to="{name: 'index'}"
                    >
                        Cancel
                    </router-link>
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
</template>

<script>
  export default {
    name: 'NewPost',
    data(){
      return{
        categories: [],
        category: {
          id:0,
          name: '',

        },
        url: '/category',
        posts: [],
        post: {
          id:0,
          title: '',
          content: '',
          category_id: '',
        },
        isVisible:true,
        isVisibleError:false,
        msgErrorTitle:'',
        msgErrorCategoryId:'',
        msgErrorContent:'',
        msgAdded: '',
      }
    },
    mounted()  {
      this.getCategories();
    },
    methods:{

      getCategories()
      {
        var that = this;
        axios.get(that.url).then(response=>{
          that.categories = response.data
          console.log(that.categories)
        })
      },
      createPost(){
        var that = this;
        console.log(this.post.categoryId)
        axios.post('/create-post', {
          title: this.post.title,
          content: this.post.content,
          category_id: this.post.category_id
        })
          .then(function (response) {

            that.isVisible = true;
            that.isVisibleError = false;
            that.msgAdded = 'Added Post';
            setTimeout(function() {
              that.isVisible = false;
              window.location.href= '/';
            }, 1000);

            that.post.title = '',
            that.post.content = '',
            that.post.category_id = ''
          })
          .catch(function (error) {
            if (error.response.status === 422) {
                that.isVisibleError = true;
                that.msgErrorTitle = error.response.data.errors.title[0];
                that.msgErrorCategoryId = error.response.data.errors.category_id[0];
                that.msgErrorContent = error.response.data.errors.content[0];
            }
          });
      }
    },
  }
</script>
<style lang="scss">
    .tbl-attachment {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 92%;
        border: 1px solid #dddddd;
    }

    .tbl-attachment td, th {
        border-top: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    .in-post-title{
        width: 90%;
    }
    .category-select{
        width: 25%;
        height: 34px;
        margin-left: 15px;
        font-size: 14px;
    }
    .post-content{
        width: 92%;
    }
    .lbl-post{
        font-weight: 600;
    }
    .post-referrence{
        margin-top: 15px;
    }
    .in-post-referrence{
        width: 40%;
    }
    .post-btn{
        width: 74%;
    }
    .post-btn button {
        float: right;
        margin-right: 15px;
    }
    .btn-cancel{
        float: right;
        margin-right: 15px;
    }
</style>

