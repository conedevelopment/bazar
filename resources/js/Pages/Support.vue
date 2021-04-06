<template>
    <div class="row">
        <div class="col-12">
            <card :title="__('Package Information')">
                <table class="table table-sm is-headless mb-0">
                    <tr>
                        <th>{{ __('Version') }}:</th>
                        <td>
                            <span class="badge badge-light">v{{ version }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>GitHub:</th>
                        <td>
                            <a href="https://github.com/conedevelopment/bazar">
                                https://github.com/conedevelopment/bazar
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Docs:</th>
                        <td>
                            <a href="https://bazar.condevelopment.com">
                                https://bazar.condevelopment.com
                            </a>
                        </td>
                    </tr>
                </table>
            </card>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            version: {
                type: String,
                required: true,
            },
        },

        inheritAttrs: false,

        mounted() {
            this.$parent.icon = 'support';
            this.$parent.title = this.__('Support');
        },
    }
</script>
