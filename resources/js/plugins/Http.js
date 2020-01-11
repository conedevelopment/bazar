import axios from 'axios';

export default {
    install(Vue) {
        Vue.prototype.$http = axios.create({
            headers: {
                'X-Bazar-Api': true,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }
}
