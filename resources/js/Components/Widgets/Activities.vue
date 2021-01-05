<script>
    export default {
        mounted() {
            this.fetch();
        },

        data() {
            return {
                busy: false,
                activities: []
            };
        },

        methods: {
            fetch() {
                this.busy = true;
                this.$http.get('/bazar/widgets/activities').then(response => {
                    this.activities = response.data;
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
    <card :title="__('Recent Activity')">
        <div v-if="activities.length" class="activities-scroll-helper" data-simplebar>
            <div class="activities">
                <div v-for="(activity, index) in activities" :key="index" class="activity">
                    <div class="activity__icon-helper">
                        <div class="activity__icon">
                            <icon :icon="activity.icon"></icon>
                        </div>
                    </div>
                    <div class="activity__content">
                        <h3 class="activity__title">
                            <bazar-link :href="activity.url">
                                {{ activity.title }}
                            </bazar-link>
                        </h3>
                        <p class="activity__description">{{ activity.description }}</p>
                        <div class="activity__meta">
                            <time :datetime="activity.created_at">{{ activity.formatted_created_at }}</time>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="busy && ! activities.length" class="alert alert-light mb-0">
            {{ __('Loading...') }}
        </div>
        <div v-else class="alert alert-info mb-0">
            {{ __('No recent activities.') }}
        </div>
    </card>
</template>
