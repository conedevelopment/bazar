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
    translations: Bazar.translations
});

window.Bazar.Vue = Vue;

const el = document.getElementById('app');

document.addEventListener('bazar:_boot_', event => {
    event.detail.Bazar.app = new Vue({
        render: h => h(InertiaApp, {
            props: {
                initialPage: JSON.parse(el.dataset.page),
                resolveComponent: name => {
                    const page = event.detail.Bazar.pages[name];

                    if (! page) {
                        throw 'Component is not registered.';
                    } else if (page instanceof Promise) {
                        return page.then(component => {
                            return Object.assign({ layout: Layout }, component.default);
                        });
                    }

                    return Object.assign({ layout: Layout }, page);
                }
            }
        })
    }).$mount(el);
});
