import {ServerSentEventsSharedWorker} from "./driver/ServerSentEventsSharedWorker";
import {ServerSentEvents} from "./driver/ServerSentEvents";

/**
 * Server events
 * @package Cotonti
 * @copyright (c) Cotonti Team
 */
export class ServerEvents {
    /**
     * @type {null|ServerSentEventsSharedWorker|ServerSentEvents}
     */
    #driver = null;

    #observersRegistry = null;

    mode = 'production';

    constructor() {
        this.#observersRegistry = new Map();
    }

    /**
     * @param {string} name
     * @param {string} event
     * @param {function} callback
     */
    addObserver(name, event, callback) {
        this.#observersRegistry.set(name, {
            name: name,
            event: event,
            onEvent: callback
        });

        if (this.#driver === null) {
            this.#initDriver();
        }
    }

    #initDriver() {
        const url = getBaseHref() + 'index.php?n=server-events';
        if (typeof SharedWorker !== undefined) {
            if (this.mode !== 'production') {
                console.log('creating ServerSentEventsSharedWorker driver');
            }
            this.#driver = new ServerSentEventsSharedWorker(url, this.mode);
        } else {
            if (this.mode !== 'production') {
                console.log('creating ServerSentEvents driver');
            }
            this.#driver = new ServerSentEvents(url, this.mode);
        }

        this.#driver.onEvent = (eventData) => {
            this.#eventTriggered(eventData);
        }
        this.#driver.mode = this.mode;
    }

    #eventTriggered(eventData) {
        if (this.mode !== 'production') {
            console.log('Server triggered event', eventData);
        }

        this.#observersRegistry.forEach((observer) => {
            if (
                observer.event === eventData.event
                && typeof observer.onEvent === 'function'
            ) {
                observer.onEvent(eventData);
            }
        });
    }
}