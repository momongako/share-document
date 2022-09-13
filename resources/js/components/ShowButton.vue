<template>
    <div class="col-sm-9">
        <router-link
                class="btn btn-primary"
                :to="{name: 'addCategory'}"
        >
            New Category
        </router-link>
    </div>
</template>

<script>
  export default {
    name: 'ShowButton'
  }
</script>