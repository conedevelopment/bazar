export default {
    functional: true,
    props: {
        data: {
            type: Object,
            default: () => ({}),
        },
        href: {
            type: String,
            required: true,
        },
        replace: {
            type: Boolean,
            default: false,
        },
        preserveScroll: {
            type: Boolean,
            default: false,
        },
        preserveState: {
            type: Boolean,
            default: null,
        },
        headers: {
            type: Object,
            default: () => ({}),
        },
    },
    render(h, { props, data, children, parent }) {
        data.on = {
            click: () => ({}),
            cancelToken: () => ({}),
            start: () => ({}),
            progress: () => ({}),
            finish: () => ({}),
            cancel: () => ({}),
            success: () => ({}),
            error: () => ({}),
            ...(data.on || {}),
        }

        return h('a', {
            ...data,
            attrs: {
                ...data.attrs,
                href: props.href,
            },
            on: {
                ...data.on,
                click: event => {
                    event.preventDefault();
                    data.on.click(event);

                    parent.$router.visit(props.href, {
                        data: props.data,
                        method: 'GET',
                        replace: props.replace,
                        // preserveScroll: props.preserveScroll,
                        //preserveState: props.preserveState ?? (method !== 'get'),
                        headers: props.headers,
                        // onCancelToken: data.on.cancelToken,
                        // onBefore: data.on.before,
                        // onStart: data.on.start,
                        // onProgress: data.on.progress,
                        // onFinish: data.on.finish,
                        // onCancel: data.on.cancel,
                        // onSuccess: data.on.success,
                        // onError: data.on.error,
                    })
                },
            },
        }, children)
    },
}
