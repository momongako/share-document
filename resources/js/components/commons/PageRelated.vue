<template>
    <div class="related-page">
        <ul class="list-item-page">
            <li class="item-page" v-for="(item, index) in listPageRelated" :key="index">
                <a :href="item.link" target="_blank">
                    <img width="44px" height="44px"
                         v-bind:src="item.src"
                    />
                    <p class="text-page">{{ item.title }}</p>
                </a>
            </li>
        </ul>
    </div>

</template>

<script>
export default {
    props: ['listPageRelated'],
    created() {

    },
    mounted() {

    },
    data() {
        return {

        }
    },
}
</script>

<style>
.related-page ul {
    display: flex;
    flex-wrap: wrap;
    padding: 0px;
    /*padding-left: 0;*/
}

.related-page ul li {
    list-style: none;
    flex: 0 0 33.333333%;
}

.related-page {
    padding-top: 20px;
    flex-direction: column;
    background: #FFFFFF;
    border-radius: 10px;
}
.text-page {
    font-style: normal;
    font-weight: bold;
    font-size: 16px;
    line-height: 19px;
    color: #101010;
    padding-top: 16px;
}
</style>
