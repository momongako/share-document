<template>
    <div class="col-sm-9">
        <div class="filter-blog">
            <div class="search-between-date">
                Date
                <span class="from-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                </span>
                ~
                <span class="to-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                </span>
            </div>

            <div class="col-xs-6 col-md-4 box-search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" id="txtSearch"/>
                    <div class="input-group-btn">
                        <button class="btn btn-primary" type="submit">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </div>
                </div>
            </div>
            <router-link
                    class="btn btn-primary btn-new-post"
                    :to="{name: 'addPost'}"
            >

                New Post
            </router-link>

            <table class="table list-post">
                <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Submitler</th>
                    <th scope="col">Division</th>
                    <th scope="col">Created date</th>
                    <th scope="col">Attach</th>
                </tr>
                </thead>
                <tbody v-if="hasPost">
                <tr v-for="(post, index) in posts">
                    <td>{{post.title}}</td>
                    <td>{{post.user_name}}</td>
                    <td>{{post.user_company}}</td>
                    <td>{{post.created_at}}</td>
                    <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                        <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
                    </svg></td>
                </tr>
                </tbody>

                <tbody v-if="!hasPost">
                    <tr>
                        <td colspan="5" style="text-align: center">No results</td>

                    </tr>
                </tbody>

            </table>

            <nav v-if="hasPost" aria-label="Page navigation example" class="post-list-pagination">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>

        </div>

    </div>
</template>

<script>
  export default {
    name: 'ListPost',
    data(){
      return{
        posts: [],
        post: {
          title: '',
          user_name:'',
          user_company: '',
          created_at:''
        },
        url: '/get-list-post',
        hasPost:false,
      }
    },
    mounted()  {
      this.getlistPost();
    },
    methods:{

      getlistPost()
      {
        var that = this;
        axios.get(that.url).then(response=>{
          that.posts = response.data.listpost
          if (that.posts.length > 0) {
                this.hasPost = true;
          }
        })
      },
    }
  }
</script>

<style>
    .box-search{
        margin-left: 100px;
    }
    .post-list-pagination{
        text-align: center;
    }
    .search-between-date{
        float: left;
    }
    .btn-new-post{
        float: right;
    }
</style>