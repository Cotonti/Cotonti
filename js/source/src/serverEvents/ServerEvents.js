import {ServerEventsClient} from "./client/ServerEventsClient";
import {ServerEventsSharedWorkerClient} from "./client/ServerEventsSharedWorkerClient";

/**
 * Server events
 * @package Cotonti
 * @copyright (c) Cotonti Team
 */
export class ServerEvents {
    mode = 'production';

    /**
     * @type {null|ServerEventsSharedWorkerClient|ServerEventsClient}
     */
    #client = null;

    /**
     * @type {string}
     */
    #clientType = 'sharedWorker';

    /**
     * @type {string} 'sse' (for Server-Sent events) , 'ajax' or 'websocket' (not implemented yet)
     */
    #driverType = 'sse';

    #observersRegistry = null;

    constructor() {
        this.#observersRegistry = new Map();
        this.#driverType = cot.cfg.serverEvents;
    }

    /**
     * @param {string} name
     * @param {string} event
     * @param {function} callback
     */
    addObserver(name, event, callback) {
        if (cot.cfg.serverEvents === 'none') {
            return;
        }

        this.#observersRegistry.set(name, {
            name: name,
            event: event,
            onEvent: callback
        });

        if (this.#client === null) {
            this.#initClient();
        }
    }

    #initClient() {
        if (cot.cfg.serverEvents === 'none') {
            return;
        }

        if (this.#clientType === 'sharedWorker' && (typeof SharedWorker !== undefined)) {
            if (this.mode !== 'production') {
                console.log('creating ServerEventsSharedWorkerClient');
            }
            this.#client = new ServerEventsSharedWorkerClient(this.#driverType, this.mode);
        } else {
            if (this.mode !== 'production') {
                console.log('creating ServerEventsClient');
            }
            this.#client = new ServerEventsClient(this.#driverType, this.mode);
        }

        this.#client.addEventListener('event', (event) => {
            this.#onEventTriggered(event.detail);
        });
    }

    #onEventTriggered(eventData) {
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