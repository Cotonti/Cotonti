import {ServerEventsDriverFactory} from "./driver/ServerEventsDriverFactory";

/**
 * Shared worker for Server Sent Events
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 */
class ServerSentEvents {
    /**
     * @type {ServerEventsAjaxDriver|ServerEventsSSEDriver|null}
     */
    #driver = null;

    /**
     * @type {string|null}
     */
    mode = 'production';

    /**
     * @type {string|null}
     */
    driverType = null;

    /**
     * @type {string}
     */
    baseUrl = '/';

    /**
     * @type {function|null}
     */
    onEvent = null;

    init() {
        if (this.#driver !== null) {
            return;
        }

        if (this.mode !== 'production') {
            console.log('init server events Shared worker');
        }

        const factory = new ServerEventsDriverFactory(this.baseUrl, this.mode);

        this.#driver = factory.getByType(this.driverType);

        this.#driver.addEventListener('event', (event) => {
            if (typeof this.onEvent !== 'function') {
                console.error(this.constructor.name + '.onEvent is not defined');
            }
            this.onEvent(event.detail);
        });
        this.#driver.init();
    }
}

/**
 * @type {MessagePort[]}
 */
const ports = [];

/**
 * @type {ServerSentEvents|null}
 */
let events = null;

self.onconnect = (event) => {
    const port = event.ports[0];

    ports.push(port);

    if (events === null) {
        events = new ServerSentEvents();
        events.onEvent = (event) => {
            if (events.mode !== 'production') {
                console.log('Shared worker postMessage', event);
            }

            for (const client of ports) {
                client.postMessage(event);
            }
        }
    }

    port.onmessage = (e) => {
        const message = e.data;

        if (message.config !== undefined) {
            if (events.driverType === null) {
                events.driverType = message.config.driver;
            }
            events.mode = message.config.mode;
            events.baseUrl = message.config.baseUrl;
            events.init();
        }

        if (events.mode !== 'production') {
            console.log('Shared worker message', message);
        }
    }
}
