<script>
    export default {
        mounted() {
            this.fetch();
        },

        data() {
            return {
                busy: false,
                metrics: {}
            };
        },

        methods: {
            fetch() {
                this.busy = true;
                this.$http.get('/bazar/widgets/metrics').then(response => {
                    this.metrics = response.data;
                }).catch(error => {
                    //
                }).finally(() => {
                    this.busy = false;
                });
            }
        }
    }
</script>

<template>
    <div>
        <div class="col-12 col-sm-6 col-xl-4 mb-5">
            <section class="card is-widget has-full-height">
                <div class="card__inner">
                    <h2 class="card__inner-title">{{ __('Orders') }}</h2>
                    <span v-if="busy">{{ __('Loading...') }}</span>
                    <p class="card__inner-data">{{ metrics.orders }}</p>
                </div>
                <div class="card__footer d-flex justify-content-end">
                    <inertia-link href="/bazar/orders">{{ __('View Orders') }}</inertia-link>
                </div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xl-4 mb-5">
            <section class="card is-widget has-full-height">
                <div class="card__inner">
                    <h2 class="card__inner-title">{{ __('Products') }}</h2>
                    <span v-if="busy">{{ __('Loading...') }}</span>
                    <p v-else class="card__inner-data">{{ metrics.products }}</p>
                </div>
                <div class="card__footer d-flex justify-content-end">
                    <inertia-link href="/bazar/products">{{ __('View Products') }}</inertia-link>
                </div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xl-4 mb-5">
            <section class="card is-widget has-full-height">
                <div class="card__inner">
                    <h2 class="card__inner-title">{{ __('Users') }}</h2>
                    <span v-if="busy">{{ __('Loading...') }}</span>
                    <p v-else class="card__inner-data">{{ metrics.users }}</p>
                </div>
                <div class="card__footer d-flex justify-content-end">
                    <inertia-link href="/bazar/users">{{ __('View Users') }}</inertia-link>
                </div>
            </section>
        </div>
    </div>
</template>
