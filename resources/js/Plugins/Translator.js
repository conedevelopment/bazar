import Translator from './../Support/Translator';

export default {
    install(app, options) {
        const translator = new Translator(options.translations);

        app.config.globalProperties.__ = (string, replace = {}) => {
            return translator.__(string, replace);
        };
    },
}
