const debounce = (callback, delay = 300) => {
    var timeoutID = null;

    return function () {
        clearTimeout(timeoutID);

        var args = arguments;
        var that = this;

        timeoutID = setTimeout(function () {
            callback.apply(that, args);
        }, delay);
    }
};

export default (el, binding) => {
    if (binding.value !== binding.oldValue) {
        el.oninput = debounce(event => {
            el.dispatchEvent(new Event('change'));
        }, parseInt(binding.value) || 300);
    }
}
