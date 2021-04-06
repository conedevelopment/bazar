import Dispatcher from './../Support/Dispatcher';

export default {
    beforeCreate() {
        const dispatcher = new Dispatcher();

        this.$dispatcher = {
            on: (...args) => dispatcher.addEventListener(...args),
            once: (event, callback, options = {}) => dispatcher.addEventListener(event, callback, { once: true, ...options }),
            off: (...args) => dispatcher.removeEventListener(...args),
            emit: (...args) => dispatcher.dispatchEvent(...args),
        };
    },
}
