<template>

    <div class="col-sm-9">
        <div v-show="isVisible && msg" class="alert alert-success" role="alert">
            {{msg}}
        </div>
        <div class="detail-title">
            <span class="detail-category-title">
                Details
            </span>
            <span>
                <router-link
                        class="btn btn-primary detail-category-button"
                        :to="{name: 'addCategory'}"
                >
                    New Category
                </router-link>
            </span>
        </div>
        <form class="form-horizontal" enctype="multipart/form-data" @submit.prevent="updateCategory()">
            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Category Name <span class="required">*</span></label>
                <div class="col-sm-10">
                    <input v-model="name" type="text" class="form-control" id="name" placeholder="" name="name">
                    <span class="sysErrors" v-show="isVisibleError && errorName">{{errorName}}</span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="description">Description</label>
                <div class="col-sm-10">
                    <input v-model="description" type="text" class="form-control" id="description" placeholder="" name="description">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="description">Status</label>
                <div class="col-sm-10">
                    <div class="radio">
                        <label class="category-status-public"><input v-model="status" type="radio" value="1" name="status" checked>Public</label>
                        <label><input v-model="status" type="radio" value="0" name="status">Private</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Submit</button>
                    <a href="javascript:;" v-on:click="showDialogConfirm()" class="btn btn-default">Delete</a>
                </div>
            </div>
        </form>

         <!--show dialog confirm-->

        <div id="dialog-container">
            <div class="dimBackground"  v-if=visibility>   </div>
            <div id="dialog" v-if=visibility>

                <h1>
                    {{title}}
                    <div>
                        <button v-on:click="onClose()"  type="button" class="close" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </h1>

                <div class="content">
                    {{content}}
                </div>

                <div class="button-confirm">
                    <button class="btn btn-default btn-confirm-cancel" v-on:click="onCancel()">
                        Close
                    </button>
                    <button v-if="!isDelete && !isSubmit" class="btn btn-default btn-confirm-delete"  v-on:click="onDelete()" >
                        Confirm
                    </button>
                    <button v-if="isSubmit" class="btn btn-default btn-confirm-delete"  v-on:click="onUpdate()" >
                        Confirm
                    </button>
                </div>
            </div>
        </div>


    </div>

</template>

<script>
  export default {
    name: 'EditCategory',
    data(){
      return{
        name:'',
        description:'',
        status : 1,
        msg: '',
        errorName:'',
        isVisible:true,
        isVisibleError:true,
        visibility:false,
        title: 'Warning',
        content:'Are you sure you want to delete this category ?',
        isDelete:false,
        isSubmit:false,
        statusOld:''
      }
    },
    mounted(){
      let app = this;
      let id = app.$route.params.id;

      // get list post by category id
      this.getListPostByCategoryId(id);


      // show
      axios.get('/category/'+id)
        .then(response =>{
            var category = response.data;
            this.name = category.name;
            this.description = category.description;
            this.status = category.status;
            this.statusOld = category.status;

      }).catch(error =>{
        console.log(error);
      });

        this.closeWhenEscPress();

    },

    methods: {
      getListPostByCategoryId(id){
        var that = this;
        axios.get(/get-list-post-by-category-id/+id).then(response=>{
          that.posts = response.data.listpost;
          if (that.posts.length > 0) {
            that.isDelete = true;
            that.content = "You cannot delete the category because there are already posts in this category.";
          }
        })
      },

      updateCategory(){
        var that = this;
        let id = that.$route.params.id;

        if(this.status == 1 && this.statusOld != this.status){
          that.isSubmit= true;
          this.visibility = true;
          that.content = "All the posts of this category will be public. Are you sure you want to proceed?";
        }
        if(this.status == 0 && this.statusOld != this.status){
          that.isSubmit= true;
          this.visibility = true;
          that.content = "All the posts of this category will be hidden from the users. Are you sure you want to proceed?";
        }

        if(this.statusOld == this.status){
          axios.put('/category/'+id,{
            name: this.name,
            description: this.description,
            status: this.status

          }).then(response =>{

            that.isVisible = true;
            that.isVisibleError = false;
            that.msg = 'Updated Category';
            setTimeout(function() {
              that.isVisible = false;
              window.location.href= '/';
            }, 1000);
          }).catch(error =>{
            if (error.response.status === 422) {
              that.isVisibleError = true;
              that.errorName = error.response.data.errors.name[0];
            }
          })
        }
      },

      showDialogConfirm(){
        this.visibility = true;
      },

      onDelete(){
        this.visibility = false;
        var that = this;
        let id = that.$route.params.id;
        axios.delete('/category/'+ id).then(response =>{
          window.location = '/';
        }).catch(error =>{
          console.log(error)
        })

      },

      onUpdate(){
        this.visibility = false;
        var that = this;
        let id = that.$route.params.id;
        axios.put('/category/'+id,{

          name: this.name,
          description: this.description,
          status: this.status

        }).then(response =>{

          that.isVisible = true;
          that.isVisibleError = false;
          that.msg = 'Updated Category';
          setTimeout(function() {
            that.isVisible = false;
            window.location.href= '/';
          }, 1000);
        }).catch(error =>{
          if (error.response.status === 422) {
            that.isVisibleError = true;
            that.errorName = error.response.data.errors.name[0];
          }
        })
      },

      onCancel(){
        this.visibility = false;
      },

      onClose(){
        this.visibility = false;
      },

      closeWhenEscPress(){
        document.addEventListener("keydown", (e) => {
          if (e.keyCode === 27) {
            this.visibility = false;
          }
        });
      },
    }
  }
</script>

<style lang="scss">
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
        .sidenav {
            height: auto;
            padding: 15px;
        }
        .row.content {height: auto;}
    }

    .control-label{
        font-weight: normal;
    }
    .form-control{
        width: 60%;
    }
    .form-horizontal .control-label{
        text-align: left;
    }
    .detail-category-title{
        width: 50px;
        margin-bottom: 35px;
        border-bottom: 3px solid #337ab7;
    }
    .required, .sysErrors{
        color: red;
    }
    .category-status-public{
        margin-right: 30px;
    }

    .dimBackground{
        background:black;
        opacity:0.5;
        z-index:90;
        position:fixed;
        width:100%;
        height:100%;
        left:0;
        top:0;
    }
    #dialog-container > #dialog
    {
        background:white;
        z-index:100;
        min-width:650px;
        min-height: 200px;
        border:1px black solid;
        display:inline-block;
        padding:0;
        position:fixed;
        left:36%;
        top:22%;
        overflow-x:hidden;
        overflow-y:auto;
    }

    #dialog-container > #dialog > h1{
        width:100%;
        color:red;
        margin:0;
        font-size:27px;
        padding-top:20px;
        padding-bottom:5px;
        padding-left:20px;
        height: 70px;
        border-bottom: 1px solid #3333;
        font-weight: bold;
    }

    #dialog-container > #dialog > .content{
        height: 70px;
        border-bottom: 1px solid #3333;
        padding-top: 25px;
        font-size: 16px;
        text-align: center;
    }

    #dialog-container > #dialog > h1 > div{
        display:inline-block;
        float:right;
        position:relative;
        top:-3px;
        padding-right:5px;
    }
    #dialog-container > #dialog > .button-confirm{
        text-align: center;
        padding-top: 15px;
    }
    #dialog-container > #dialog >.button-confirm > .btn-confirm-delete{
        background-color: red;
        color:#ffffff;
        border-radius: 25px;
        width: 90px;
    }
    #dialog-container > #dialog >.button-confirm > .btn-confirm-cancel{
       border: none;
        margin-right: 15px;
    }


</style>