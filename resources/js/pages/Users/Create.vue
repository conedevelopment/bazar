<script>
    export default {
        //
    }
</script>

<template>
    <layout :title="__('Create User')">
        <data-form :action="$page.action" :model="$page.user">
            <template #default="form">
                <card :title="__('General')">
                    <form-input name="name" :label="__('Name')" v-model="form.fields.name"></form-input>
                    <form-input name="email" type="email" :label="__('Email')" v-model="form.fields.email"></form-input>
                </card>
            </template>
        </data-form>
    </layout>
</template>
