<script>
    import DataChart from './../Chart';

    export default {
        components: {
            DataChart,
        },

        mounted() {
            this.fetch();
        },

        data() {
            return {
                busy: false,
                sales: {},
            };
        },

        methods: {
            fetch() {
                this.busy = true;
                this.$http.get('/bazar/widgets/sales').then((response) => {
                    this.sales = response.data;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.busy = false;
                });
            },
        },
    }
</script>

<template>
    <section class="card">
        <div class="card__header">
            <h2 class="card__title">{{ __('Sales') }}</h2>
        </div>
        <div class="card__inner">
            <div v-if="busy" class="alert alert-light mb-0">
                {{ __('Loading') }}...
            </div>
            <data-chart v-else :labels="sales.labels" :data="sales.data"></data-chart>
        </div>
    </section>
</template>
