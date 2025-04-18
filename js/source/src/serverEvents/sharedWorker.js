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
    #usingDriverType = null;

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

    #initialisationInProgress = false;

    /**
     * @todo Save the initialized driverType.
     * When changing the driver type, remove the old one and initialize the new one.
     * This may be unnecessary, but it could be useful during configuration.
     */
    init() {
        if (this.mode !== 'production') {
            console.log('Shared worker: ');
            console.log('Running driver: ', this.#usingDriverType);
            console.log('New driver: ', this.driverType);
        }

        if (this.#usingDriverType === this.driverType) {
            if (this.mode !== 'production') {
                console.log('Shared worker: драйвер "' + this.#usingDriverType, + '"уже инициализирован. действий не требуется');
            }
            return
        }

        if (this.#initialisationInProgress) {
            return;
        }

        this.#initialisationInProgress = true;

        if (this.mode !== 'production') {
            console.log('Shared worker: ServerSentEvents initialization.');
        }

        if (this.#driver !== null) {
            if (this.mode !== 'production') {
                console.log('Shared worker: отключаем старый драйвер: ', this.#usingDriverType);
            }
            this.#driver = null;
        }

        try {
            const factory = new ServerEventsDriverFactory(this.baseUrl, this.mode);

            this.#driver = factory.getByType(this.driverType);
            this.#usingDriverType = this.driverType;

            this.#driver.addEventListener('event', (event) => {
                if (typeof this.onEvent !== 'function') {
                    console.error(this.constructor.name + '.onEvent is not defined');
                }
                this.onEvent(event.detail);
            });
            this.#driver.init();
        } catch (error) {
            console.error(error);
        }

        this.#initialisationInProgress = false;
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

        if (events.mode !== 'production') {
            console.log('Shared worker message', message);
        }

        if (message.config !== undefined) {
            if (events.driverType !== message.config.driver) {
                events.driverType = message.config.driver;
                events.mode = message.config.mode;
                events.baseUrl = message.config.baseUrl;
                events.init();
            }
        }
    }
}
