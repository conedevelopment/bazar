<script>
    export default {
        data() {
            return {
                title: this.$page.user.name
            };
        }
    }
</script>

<template>
    <data-form :action="$page.action" :model="$page.user">
        <template #default="form">
            <card :title="__('General')">
                <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
                <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
            </card>
        </template>
        <template #aside>
            <card :title="__('Addresses')" class="mb-5">
                <inertia-link :href="`${$page.action}/addresses`" class="btn btn-primary">
                    {{ __('Manage Addresses') }}
                </inertia-link>
            </card>
        </template>
    </data-form>
</template>
