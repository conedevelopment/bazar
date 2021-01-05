import Axios from 'axios';
import Dispatcher from './Dispatcher';

export default class Router
{
    /**
     * Create a new router instance.
     *
     * @return {void}
     */
    constructor()
    {
        this.http = Axios.create({
            headers: {
                'X-Bazar': true,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html, application/xhtml+xml'
            },
            // cancelToken: new CancelToken(function (cancel) {}),
        });

        this.dispatcher = new Dispatcher;

        window.addEventListener('popstate', event => {
            // If bazar...
            this.dispatcher.dispatchEvent('success', event.state);
        });
    }

    /**
     * Visit the given URL using the given config.
     *
     * @param  {string}  url
     * @param  {object}  config
     * @return {void}
     */
    visit(url, { method = 'GET', data = {}, headers = {}, replace = false } = {})
    {
        // this.dispatcher.dispatchEvent('before', state);

        this.http({ url, method, data, headers }).then(response => {
            const state = {
                html: response.data,
                url: response.headers['x-bazar-location'],
                version: response.headers['x-bazar-version'],
                component: response.headers['x-bazar-component']
            };

            replace // || state.pathname === window.location.pathname
                ? this.replace(state.url, null, state)
                : this.push(state.url, null, state);

            this.dispatcher.dispatchEvent('success', state);
        }).catch(error => {
            // this.dispatcher.dispatchEvent('error', state);
        }).finally(() => {
            // this.dispatcher.dispatchEvent('after', state);
        });
    }

    push(url, title = null, state = null)
    {
        window.history.pushState(state, title, url);
    }

    replace(url, title = null, state = null)
    {
        window.history.replaceState(state, title, url);
    }

    go(to = null)
    {
        window.history.go(to);
    }

    forward()
    {
        window.history.forward();
    }

    back()
    {
        window.history.back();
    }

    // reload()
    // cancel()
    // isBjaxResponse()

    // get()
    // post()
    // put()
    // patch()
    // delete()
}
