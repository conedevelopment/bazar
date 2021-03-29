<template>
    <div class="row">
        <div class="col-12">
            <section class="card">
                <div class="card__header">
                    <h2 class="card__title">{{ __('Package Information') }}</h2>
                </div>
                <div class="card__inner">
                    <table class="table table-sm is-headless mb-0">
                        <tr>
                            <th>{{ __('Version') }}:</th>
                            <td>
                                <span class="badge badge-light">v{{ $page.props.version }}</span>
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
                </div>
            </section>
        </div>
    </div>
</template>

<script>
    export default {
        inheritAttrs: false,
    }
</script>
