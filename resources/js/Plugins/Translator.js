import Translator from './../Support/Translator';

export default {
    install(Vue) {
        let translator;

        Vue.prototype.__ = function (string, replace = {}) {
            if (! translator) {
                translator = new Translator(this.$page.translations);
            }

            return translator.__(string, replace);
        }
    }
}
