<template>

</template>

<script>
    import VueRouter from "vue-router";

    Vue.use(VueRouter);

    const Router = new VueRouter({
        mode: 'history',
    });


    export default {
        name: "url",

        router: Router,

        methods: {
            getUrlFilterOrSet: function(name, defaultValue) {
                let value = this.getUrlFilter(name);
            },

            setUrlFilter: function(name, defaultValue = null) {
                const $fullFilterName = 'filter['+name+']';
                return this.setUrlParameter($fullFilterName, defaultValue);
            },

            getUrlFilter: function(name, defaultValue = null) {
                const $fullFilterName = 'filter['+name+']';
                return this.getUrlParameter($fullFilterName, defaultValue);
            },

            getUrlParameter: function(param, defaultValue = null) {
                const $urlParameters = this.$router.currentRoute.query;
                return this.getValueOrDefault($urlParameters[param], defaultValue);
            },

            setUrlParameter: function(param, value) {
                this.$router.currentRoute.query[param] = value;
                this.updateUrl(this.$router.currentRoute.query);

                return this;
            },

            updateUrlParameters(params) {
                for (let parameter in params) {
                    this.setUrlParameter(parameter, params[parameter]);
                }

                return this;
            },

            pushUrl: function (url) {
                this.$router.push(url).catch(err => {
                    // Ignore the vuex err regarding  navigating to the page they are already on.
                    if (
                        err.name !== 'NavigationDuplicated' &&
                        !err.message.includes('Avoided redundant navigation to current location')
                    ) {
                        // But print any other errors to the console
                        console.error(err);
                    }
                });
            },

            updateUrl: function(params) {
                let url = this.$router.currentRoute.path + '?';

                for (let element in params) {
                    if( params[element] != null) {
                        url += element +'=' + params[element] + '&';
                    }
                }

                // we setting url twice because sometimes when only parameter is updated
                // but path stays NavigationDuplicated error might occur
                this.pushUrl('/');
                this.pushUrl(url);

                return this;
            },

            isSet: function (value) {
                return (value === undefined) || (value === null);
            },

            getValueOrDefault: function (value, defaultValue){
                return this.isSet(value) ? defaultValue : value;
            },
        }
    }
</script>

<style scoped>

</style>
