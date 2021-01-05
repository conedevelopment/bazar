export default class Dispatcher
{
    /**
     * Initialize a new event bus instance.
     *
     * @return {void}
     */
    constructor()
    {
        this.bus = document.createElement('dispatcher');
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
        this.bus.addEventListener(event, callback, options);
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
        this.bus.removeEventListener(event, callback);
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
        this.bus.dispatchEvent(new CustomEvent(event, { detail }));
    }
}
