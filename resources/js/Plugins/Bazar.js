import debounce from './../Directives/Debounce';
import Manager from './../Components/Media/Manager';
import Card from './../Components/Card';
import Icon from './../Components/Icon';
import Link from './../Components/Link';
import Dropdown from './../Components/Dropdown';
import Alert from './../Components/Alert';
import Modal from './../Components/Modal';
import Table from './../Components/Table/Table';
import Column from './../Components/Table/Column';
import Form from './../Components/Form/Form';
import Autocomplete from './../Components/Form/Autocomplete';
import Tag from './../Components/Form/Tag';
import Editor from './../Components/Form/Editor';
import Options from './../Components/Form/Options';
import Input from './../Components/Form/Input';
import Checkbox from './../Components/Form/Checkbox';
import Radio from './../Components/Form/Radio';
import Textarea from './../Components/Form/Textarea';
import Select from './../Components/Form/Select';
import Downloads from './../Components/Form/Downloads';
import Media from './../Components/Form/Media';
import Products from './../Components/Order/Products';
import OrderInfo from './../Components/Order/OrderInfo';
import Transactions from './../Components/Order/Transactions';
import ProductsTable from './../Components/Order/ProductsTable';
import WidgetSales from './../Components/Widgets/Sales';
import WidgetMetrics from './../Components/Widgets/Metrics';
import WidgetActivities from './../Components/Widgets/Activities';

import Router from './../Support/Router';

export default {
    install(Vue) {
        // Directives
        Vue.directive('debounce', debounce);

        // Generic
        Vue.component('media-manager', Manager);
        Vue.component('card', Card);
        Vue.component('icon', Icon);
        Vue.component('dropdown', Dropdown);
        Vue.component('alert', Alert);
        Vue.component('modal', Modal);
        Vue.component('bazar-link', Link);

        // Widgets
        Vue.component('widget-sales', WidgetSales);
        Vue.component('widget-metrics', WidgetMetrics);
        Vue.component('widget-activities', WidgetActivities);

        // Table
        Vue.component('data-table', Table);
        Vue.component('data-column', Column);

        // Form
        Vue.component('data-form', Form);
        Vue.component('form-autocomplete', Autocomplete);
        Vue.component('form-tag', Tag);
        Vue.component('form-editor', Editor);
        Vue.component('form-options', Options);
        Vue.component('form-input', Input);
        Vue.component('form-checkbox', Checkbox);
        Vue.component('form-radio', Radio);
        Vue.component('form-textarea', Textarea);
        Vue.component('form-select', Select);
        Vue.component('form-downloads', Downloads);
        Vue.component('form-media', Media);

        // Order
        Vue.component('products', Products);
        Vue.component('order-info', OrderInfo);
        Vue.component('transactions', Transactions);
        Vue.component('products-table', ProductsTable);

        const router = new Router;
        Object.defineProperty(Vue.prototype, '$router', { get: () => router });
    }
}
