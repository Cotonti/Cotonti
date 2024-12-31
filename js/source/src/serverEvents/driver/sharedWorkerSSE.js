/**
 * Shared worker for Server Sent Events
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 * @link https://developer.mozilla.org/en-US/docs/Web/API/SharedWorker
 */
import {ServerSentEventsClient} from "../ServerSentEventsClient";

class ServerSentEvents {
    /**
     * @type {ServerSentEventsClient|null}
     */
    #client = null;

    /**
     * @type {string|null}
     */
    mode = 'production';

    /**
     * @type {string|null}
     */
    eventsUrl = null;

    /**
     * @type {function|null}
     */
    onEvent = null;

    init() {
        if (this.#client !== null) {
            return;
        }

        if (this.mode !== 'production') {
            console.log('init Shared worker SSE');
        }

        this.#client = new ServerSentEventsClient(this.eventsUrl, this.mode);
        this.#client.onEvent = (eventData) => {
            if (typeof this.onEvent !== 'function') {
                console.error(this.constructor.name + '.onEvent is not defined');
            }
            this.onEvent(eventData);
        }
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

    // console.log('Shared worker connected');

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
            events.eventsUrl = message.config.url;
            events.mode = message.config.mode;

            events.init();
        }

        if (events.mode !== 'production') {
            console.log('Shared worker message', message);
        }
    }
}
