<template>
    <div class="col-sm-3 sidenav">
        <h4>Document</h4>
        <ul v-for="(category, index) in categories" class="nav flex-column">
            <li class="nav-item">
                <a :href="'/category-list/'+category.id">
                    <span class="glyphicon glyphicon-folder-open"></span>
                    <span class="category-name">{{category.name}}</span>
                </a>
            </li>
        </ul>

        <br>
        <div class=" show-list-category">
            <span>
                <svg v-show="!toggle" @click='toggle = !toggle' xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down" viewBox="0 0 16 16">
                    <path d="M3.204 5h9.592L8 10.481 3.204 5zm-.753.659 4.796 5.48a1 1 0 0 0 1.506 0l4.796-5.48c.566-.647.106-1.659-.753-1.659H3.204a1 1 0 0 0-.753 1.659z"/>
                </svg>
            </span>

            <svg v-show="toggle" @click='toggle = !toggle' xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up" viewBox="0 0 16 16">
                <path d="M3.204 11h9.592L8 5.519 3.204 11zm-.753-.659 4.796-5.48a1 1 0 0 1 1.506 0l4.796 5.48c.566.647.106 1.659-.753 1.659H3.204a1 1 0 0 1-.753-1.659z"/>
            </svg>


            <span><a class="btn-show-category" href="/show-button">Category</a></span>
        </div>

        <ul v-for="(category, index) in categories" v-show='toggle' class="nav flex-column list-category">
            <li class="nav-item">
                <a :href="'/category-edit/'+category.id">
                    <span class="glyphicon glyphicon-folder-open"></span>
                    <span class="category-name">{{category.name}}</span>
                </a>
            </li>

        </ul>
    </div>

</template>

<script>
  export default {
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