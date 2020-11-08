<script>
    export default {
        data() {
            return {
                title: this.__('Create User')
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
    </data-form>
</template>
