import Vue from 'vue';
import EventBus from './../Support/EventBus';
import { InertiaApp } from '@inertiajs/inertia-vue';

export default class Bazar
{
    /**
     * Initialize a new Bazar instance.
     *
     * @return {void}
     */
    constructor()
    {
        this.app = null;
        this.bus = new EventBus;
    }

    /**
     * Boot the services.
     *
     * @return {void}
     */
    boot()
    {
        this.$emit('booting', { Vue });

        const app = document.getElementById('app');

        this.app = new Vue({
            render: h => h(InertiaApp, {
                props: {
                    initialPage: window.initialPage,
                    resolveComponent: component => {
                        return Vue.component('Layout', { template: component });
                    }
                }
            }),

            data() {
                return {
                    isSidebarOpen: false
                };
            }
        }).$mount(app);

        this.$emit('booted', { app: this.app });
    }

    /**
     * Register an event listner.
     *
     * @param  {string}  event
     * @param  {function}  callback
     * @return {void}
     */
    $on(event, callback)
    {
        this.bus.addEventListener(event, e => {
            callback(...Object.values(e.detail));
        });
    }

    /**
     * Register a one-time event listner.
     *
     * @param  {string}  event
     * @param  {function}  callback
     * @return {void}
     */
    $once(event, callback)
    {
        this.bus.addEventListener(event, e => {
            callback(...Object.values(e.detail));
        }, { once: true });
    }

    /**
     * Dispatch the event with the given payload.
     *
     * @param  {string}  event
     * @param  {object}  payload
     * @return {void}
     */
    $emit(event, payload = {})
    {
        this.bus.dispatchEvent(event, payload);
    }
}
