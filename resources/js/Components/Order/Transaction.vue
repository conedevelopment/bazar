<template>
    <tr>
        <td>
            <span :class="className">
                {{ `${sign} ${amount}` }}
            </span>
        </td>
        <td>
            <span>{{ transaction.driver_name }}</span>
            <small v-if="transaction.url">
                (<a :href="transaction.url" target="_blank">{{ transaction.key }}</a>)
            </small>
        </td>
        <td>
            <span v-if="transaction.completed_at">
                {{ completedAt }}
            </span>
            <span v-else class="badge badge-warning">{{ __('Pending') }}</span>
        </td>
        <td>
            <div class="d-flex">
                <button
                    type="button"
                    class="icon-btn"
                    :class="{ 'icon-btn-success': ! completed, 'icon-btn-warning': completed }"
                    :disabled="busy"
                    :aria-label="completed ? __('Mark pending') : __('Mark completed')"
                    @click="update"
                >
                    <icon :name="icon"></icon>
                </button>
                <button
                    type="button"
                    class="icon-btn icon-btn-danger ml-1"
                    :disabled="busy"
                    :aria-label="__('Delete')"
                    @click="destroy"
                >
                    <icon name="close"></icon>
                </button>
            </div>
        </td>
    </tr>
</template>

<script>
    export default {
        props: {
            transaction: {
                type: Object,
                required: true,
            },
        },

        emits: ['delete'],

        data() {
            return {
                busy: false,
            };
        },

        computed: {
            className() {
                return 'text-' + (this.transaction.type === 'refund' ? 'danger' : 'success');
            },
            icon() {
                return this.completed ? 'retry' : 'done';
            },
            sign() {
                return this.transaction.type === 'refund' ? '-' : '+';
            },
            amount() {
                return (new Number(this.transaction.amount)).toFixed(2);
            },
            completed() {
                return !! this.transaction.completed_at;
            },
            action() {
                return `/bazar/orders/${this.transaction.order_id}/transactions/${this.transaction.id}`;
            },
            completedAt() {
                return this.transaction.completed_at.substr(0, 16).replace('T', ' ');
            },
        },

        methods: {
            update() {
                this.busy = true;
                this.$http.patch(this.action).then((response) => {
                    this.transaction.completed_at = this.completed ? null : (new Date).toISOString();
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.busy = false;
                });
            },
            destroy() {
                this.busy = true;
                this.$http.delete(this.action).then((response) => {
                    this.$emit('delete');
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.busy = false;
                });
            },
        },
    }
</script>
