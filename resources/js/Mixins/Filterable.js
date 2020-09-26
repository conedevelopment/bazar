export default {
    computed: {
        filters() {
            const state = {
                all: this.__('All'),
                available: this.__('Available'),
                trashed: this.__('Trashed')
            };

            return Object.assign({ state }, this.$page.filters || {});
        }
    }
}
