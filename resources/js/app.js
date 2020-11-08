import Vue from 'vue';
import Bazar from './Plugins/Bazar';
import Http from './Plugins/Http';
import Translator from './Plugins/Translator';
import { InertiaApp } from '@inertiajs/inertia-vue';
import 'simplebar';
import Layout from './Components/Layout';

Vue.use(Bazar);
Vue.use(Http);
Vue.use(InertiaApp);
Vue.use(Translator, {
    translations: window.translations
});

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => {
                return new Promise((resolve, reject) => {
                    resolve(require(`./Pages/${name}`).default);
                }).then(component => {
                    return Object.assign({ layout: Layout }, component);
                }).catch(error => {
                    //
                });
            }
        }
    })
}).$mount(app);
