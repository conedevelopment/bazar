import { createApp, h } from 'vue';
import { App, plugin } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress';
import Layout from './Components/Layout';
import 'simplebar';
import Bazar from './Plugins/Bazar';
import Http from './Plugins/Http';
import Translator from './Plugins/Translator';

InertiaProgress.init({
    delay: 250,
    color: '#91c83e',
    includeCSS: true,
});

const el = document.getElementById('app');

const app = createApp({
    render() {
        return h(App, {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: (name) => {
                const page = window.Bazar.pages[name];

                if (! page) {
                    throw 'Page is not registered.';
                } else if (page instanceof Promise) {
                    return page.then((component) => {
                        return Object.assign({ layout: Layout }, component.default);
                    });
                }

                return Object.assign({ layout: Layout }, page);
            },
        });
    },
});

// Plugins
app.use(plugin);
app.use(Bazar);
app.use(Http);
app.use(Translator, { translations: Bazar.translations });

// Components
import Icon from './Components/Icon';
app.component('icon', Icon);
import Dropdown from './Components/Dropdown';
app.component('dropdown', Dropdown);
import Alert from './Components/Alert';
app.component('alert', Alert);
import Card from './Components/Card';
app.component('Card', Card);

import Form from './Components/Form/Form';
app.component('data-form', Form);
import Input from './Components/Form/FormInput';
app.component('data-form-input', Input);

import Table from './Components/Table/Table';
app.component('data-table', Table);
import Column from './Components/Table/Column';
app.component('data-table-column', Column);

// Directives
import Debounce from './Directives/Debounce';
app.directive('debounce', Debounce);

// Mixins
import DispatchesEvents from './Mixins/DispatchesEvents';
app.mixin(DispatchesEvents);

// Mount
app.mount(el);
el.removeAttribute('data-page');
