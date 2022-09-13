<template>
    <div class="col-sm-9">
        <div v-show="isVisible && msg" class="alert alert-success" role="alert">
            {{msg}}
        </div>
        <h4 class="new-category-title">Create a new Category</h4>

        <form class="form-horizontal" enctype="multipart/form-data" @submit.prevent="createCategory()">
            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Category Name <span class="required">*</span></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" placeholder="" name="name" v-model="category.name">
                    <span class="sysErrors" v-show="isVisibleError && errorName">{{errorName}}</span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="description">Description</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="description" placeholder="" name="description" v-model="category.description">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="description">Status</label>
                <div class="col-sm-10">
                    <div class="radio">
                        <label class="category-status-public"><input type="radio" value="1" name="status" checked v-model="category.status">Public</label>
                        <label><input type="radio" value="0" name="status" v-model="category.status">Private</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Submit</button>
                    <router-link
                            class="btn btn-default"
                            :to="{name: 'index'}"
                    >
                        Cancel
                    </router-link>
                </div>
            </div>
        </form>
    </div>

</template>

<script>
  export default {
    name: 'AddCategory',
    data(){
      return{
        category:{
          id:0,
          name: '',
          description:'',
          status: '1'
        },
        msg: '',
        errorName:'',
        isVisible:true,
        isVisibleError:false
      }
    },
    methods:{

      createCategory(){
        var that = this;
        axios.post('/category', {
              name: this.category.name,
              description: this.category.description,
              status: this.category.status
        })
          .then(function (response) {

            that.isVisible = true;
            that.isVisibleError = false;
            that.msg = 'Added Category';
            setTimeout(function() {
              that.isVisible = false;
              window.location.href= '/';
            }, 1000);

            that.category.name = '',
            that.category.description = '',
            that.category.status = 1
          })
          .catch(function (error) {
            if (error.response.status === 422) {
              that.isVisibleError = true;
              that.errorName = error.response.data.errors.name[0];
              // setTimeout(function() {
              //   that.isVisibleError = false;
              // }, 3000);
            }
          });
      }
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
    .new-category-title{
        width: 186px;
        margin-bottom: 35px;
        border-bottom: 3px solid #337ab7;
        color: #337ab7;
    }
    .required, .sysErrors{
        color: red;
    }
    .category-status-public{
        margin-right: 30px;
    }
</style>