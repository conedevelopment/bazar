export default class Dispatcher
{
    /**
     * Initialize a new event dispatcher instance.
     *
     * @return {void}
     */
    constructor()
    {
        this.dispatcher = document.createElement('dispatcher');
    }

    /**
     * Add an event listener.
     *
     * @param  {string}  event
     * @param  {function}  callback
     * @param  {object}  options
     * @return {void}
     */
    addEventListener(event, callback, options = {})
    {
        this.dispatcher.addEventListener(event, callback, options);
    }

    /**
     * Remove an event listener.
     *
     * @param  {string}  event
     * @param  {function}  callback
     * @return {void}
     */
    removeEventListener(event, callback)
    {
        this.dispatcher.removeEventListener(event, callback);
    }

    /**
     * Dispatch an event.
     *
     * @param  {string}  event
     * @param  {object}  detail
     * @return {void}
     */
    dispatchEvent(event, detail = {})
    {
        this.dispatcher.dispatchEvent(new CustomEvent(event, { detail }));
    }
}
