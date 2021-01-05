import axios from 'axios';

export default {
    install(Vue) {
        Vue.prototype.$http = axios.create({
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }
}
