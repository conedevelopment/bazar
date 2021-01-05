import Vue from 'vue';
import Bazar from './Plugins/Bazar';
import Http from './Plugins/Http';
import Translator from './Plugins/Translator';
import 'simplebar';

Vue.use(Bazar);
Vue.use(Http);
Vue.use(Translator, {
    translations: window.translations
});

import App from './Components/App';
const app = document.getElementById('app');

new Vue({
    render(h) {
        return h(App, {
            props: {
                page: JSON.parse(app.dataset.page)
            }
        });
    }
}).$mount(app);
