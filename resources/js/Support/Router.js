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
            // cancelToken: new CancelToken(token => {}),
        });

        this.dispatcher = new Dispatcher;

        window.addEventListener('popstate', event => {
            if (event?.state?.bazar) {
                this.dispatcher.dispatchEvent('success', event.state);
            }
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
        this.dispatcher.dispatchEvent('before');

        this.http({ url, method, data, headers }).then(response => {
            const state = {
                html: response.data,
                url: response.headers['x-bazar-location'],
                version: response.headers['x-bazar-version'],
                component: response.headers['x-bazar-component']
            };

            (replace || state.url === window.location.pathname)
                ? this.replace(state.url, null, state)
                : this.push(state.url, null, state);

            this.dispatcher.dispatchEvent('success', state);
        }).catch(error => {
            this.dispatcher.dispatchEvent('error', error);
        }).finally(() => {
            this.dispatcher.dispatchEvent('after');
        });
    }

    /**
     * Push a state to the history.
     *
     * @param  {string}  url
     * @param  {string|null}  title
     * @param  {object|null}  state
     * @return {void}
     */
    push(url, title = null, state = null)
    {
        window.history.pushState(
            { bazar: true, ...state }, title, url
        );
    }

    /**
     * Replace a state in the history.
     *
     * @param  {string}  url
     * @param  {string|null}  title
     * @param  {object|null}  state
     * @return {void}
     */
    replace(url, title = null, state = null)
    {
        window.history.replaceState(
            { bazar: true, ...state }, title, url
        );
    }

    /**
     * Go to the given state in the history.
     *
     * @param  {int|null}  to
     * @return {void}
     */
    go(to = null)
    {
        window.history.go(to);
    }

    /**
     * Go forward in the history.
     *
     * @return {void}
     */
    forward()
    {
        window.history.forward();
    }

    /**
     * Go back in the history.
     *
     * @return {void}
     */
    back()
    {
        window.history.back();
    }

    /**
     * Reload the currenct state.
     *
     * @return {void}
     */
    reload()
    {
        this.go();
    }

    // cancel()
    // isBjaxResponse()
    // get()
    // post()
    // put()
    // patch()
    // delete()
}
