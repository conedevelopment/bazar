import debounce from './../Directives/Debounce';
import Manager from './../Components/Media/Manager';
import Card from './../Components/Card';
import Icon from './../Components/Icon';
import Dropdown from './../Components/Dropdown';
import Alert from './../Components/Alert';
import Modal from './../Components/Modal';
import Layout from './../Components/Layout';
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

export default {
    install(Vue) {
        // Directives
        Vue.directive('debounce', debounce);

        // Generic Components
        Vue.component('media-manager', Manager);
        Vue.component('card', Card);
        Vue.component('icon', Icon);
        Vue.component('dropdown', Dropdown);
        Vue.component('alert', Alert);
        Vue.component('modal', Modal);
        Vue.component('layout', Layout);

        // Table Components
        Vue.component('data-table', Table);
        Vue.component('data-column', Column);

        // Form components
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
    }
}
