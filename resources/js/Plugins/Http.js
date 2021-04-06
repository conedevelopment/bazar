import Axios from 'axios';

export default {
    install(app) {
        app.config.globalProperties.$http = Axios.create({
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
    },
}
