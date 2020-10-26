import Translator from './../Support/Translator';

export default {
    install(Vue, options) {
        let translator;

        Vue.prototype.__ = function (string, replace = {}) {
            if (! translator) {
                translator = new Translator(options.translations);
            }

            return translator.__(string, replace);
        }
    }
}
