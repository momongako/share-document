 <template>
     <div class="row content">
            <div class="col-sm-3 sidenav">
                <h4 class="document">
                    <router-link
                            class=""
                            :class="[{active: $route.name === 'index'}]"
                            :to="{name: 'index'}"
                    >
                    Document
                    </router-link>
                </h4>

                <ul v-for="(category, index) in categories" class="nav flex-column">
                    <li class="nav-item">
                        <router-link
                                :to="{name: 'getListPost', params: {id: category.id}}"
                                :class="[{active: $route.name === 'getListPost' && $route.params.id === category.id}]"
                        >
                            <span class="glyphicon glyphicon-folder-open"></span>
                            <span class="category-name">
                                {{category.name}}

                            </span>
                        </router-link>
                    </li>
                </ul>

                <br>
                <div class=" show-list-category">
                <span>
                        <span style="margin-left: -15px">
                            <svg v-show="!toggle" @click='toggle = !toggle' xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
								<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
							</svg>

							<svg v-show="toggle" @click='toggle = !toggle' xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16">
								<path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>
							</svg>
                        </span>

                        <span class="show-form-add">
                            <router-link
                                    class="nav-link"
                                    :class="[{active: $route.name === 'showButton'
                                        || $route.name === 'addCategory'
                                            || $route.name === 'addPost'
                                            }]"
                                    :to="{name: 'showButton'}"
                            >
                                Category
                            </router-link>
                        </span>
                </span>

                </div>

                <ul v-for="(category, index) in categories" v-show='toggle' class="nav flex-column list-category">
                    <li class="nav-item">
                        <router-link
                                :class="[{active: $route.name === 'editCategory' && $route.params.id === category.id}]"
                                :to="{name: 'editCategory', params: {id: category.id}}"
                        >
                            <span class="glyphicon glyphicon-folder-open"></span>
                            <span class="category-name">{{category.name}}</span>
                        </router-link>

                    </li>

                </ul>
            </div>

            <div class="col-sm-9">
                <router-view :key="$route.fullPath"></router-view>
            </div>
     </div>
    </template>

    <script>
      export default {
        name: 'HomePage',
        data(){
          return{
            toggle: false,
            categories: [],
            category: {
              id:0,
              name: '',
              description:'',
              status: ''
            },
            url: '/category',
            isActive: false
          }
        },

        mounted()  {
          this.getCategories();
          console.log(window.location.pathname);
        },

        methods:{

          getCategories()
          {
            var that = this;
            axios.get(that.url).then(response=>{
              that.categories = response.data
            })
          },
        }
      }
    </script>

<style>
    .container {
        border: 1px solid #eee;
    }
    .active {
        color: #337ab7!important;
        border-bottom: 3px solid #337ab7;
    }
    .show-form-add{
        position: relative;
        top: -26px;
        left: 15px;
    }
    .show-form-add a{
        font-size: 16px;
        color: #333333;
        padding-left: 0px;
    }
    .document a{
        color: #333333;
    }
</style>