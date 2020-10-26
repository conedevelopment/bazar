import Vue from 'vue';
import Http from './Plugins/Http';
import Translator from './Plugins/Translator';
import { InertiaApp } from '@inertiajs/inertia-vue';
import 'simplebar';

Vue.use(Http);
Vue.use(InertiaApp);
Vue.use(Translator, {
    translations: window.translations
});

import debounce from './Directives/Debounce';
Vue.directive('debounce', debounce);

// Generic Components
import Manager from './Components/Media/Manager';
Vue.component('media-manager', Manager);
import Card from './Components/Card';
Vue.component('card', Card);
import Icon from './Components/Icon';
Vue.component('icon', Icon);
import Dropdown from './Components/Dropdown';
Vue.component('dropdown', Dropdown);
import Alert from './Components/Alert';
Vue.component('alert', Alert);
import Modal from './Components/Modal';
Vue.component('modal', Modal);

// Table Components
import Table from './Components/Table/Table';
Vue.component('data-table', Table);
import Column from './Components/Table/Column';
Vue.component('data-column', Column);

// Form components
import Form from './Components/Form/Form';
Vue.component('data-form', Form);
import Autocomplete from './Components/Form/Autocomplete';
Vue.component('form-autocomplete', Autocomplete);
import Tag from './Components/Form/Tag';
Vue.component('form-tag', Tag);
import Editor from './Components/Form/Editor';
Vue.component('form-editor', Editor);
import Options from './Components/Form/Options';
Vue.component('form-options', Options);
import Input from './Components/Form/Input';
Vue.component('form-input', Input);
import Checkbox from './Components/Form/Checkbox';
Vue.component('form-checkbox', Checkbox);
import Radio from './Components/Form/Radio';
Vue.component('form-radio', Radio);
import Textarea from './Components/Form/Textarea';
Vue.component('form-textarea', Textarea);
import Select from './Components/Form/Select';
Vue.component('form-select', Select);
import Downloads from './Components/Form/Downloads';
Vue.component('form-downloads', Downloads);
import Media from './Components/Form/Media';
Vue.component('form-media', Media);

// Widgets
import Sales from './Components/Widgets/Sales';
Vue.component('widget-sales', Sales);
import Metrics from './Components/Widgets/Metrics';
Vue.component('widget-metrics', Metrics);
import Activities from './Components/Widgets/Activities';
Vue.component('widget-activities', Activities);

// Order Components
import Products from './Components/Order/Products';
Vue.component('order-products', Products);
import Transactions from './Components/Order/Transactions';
Vue.component('order-transactions', Transactions);

// Bazar
import Bazar from './Support/Bazar';
window.Bazar = new Bazar;
