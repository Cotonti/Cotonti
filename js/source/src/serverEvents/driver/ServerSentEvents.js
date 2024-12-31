import {ServerSentEventsClient} from "../ServerSentEventsClient";

/**
 * Server Sent Events driver implementation for browsers that do not support Shared Worker.
 * These are Android mobile device browsers.
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 */
export class ServerSentEvents {
    /**
     * @type {string|null}
     */
    eventsUrl = null;

    /**
     * @type {ServerSentEventsClient|null}
     */
    #client = null;

    /**
     * @type {function|null}
     */
    onEvent = null;

    /**
     * @type {string|null}
     */
    mode = 'production';

    constructor(eventsUrl, mode = null) {
        this.eventsUrl = eventsUrl;
        if (mode !== null) {
            this.mode = mode;
        }
        this.init();
    }

    init() {
        if (this.#client !== null) {
            return;
        }

        if (this.mode !== 'production') {
            console.log('init ServerSentEvents driver');
        }

        this.#client = new ServerSentEventsClient(this.eventsUrl, this.mode);
        this.#client.onEvent = (eventData) => {
            this.onEventTriggered(eventData);
        }
    }

    onEventTriggered(eventData) {
        if (typeof this.onEvent !== 'function') {
            console.error(this.constructor.name + '.onEvent is not defined');
        }
        this.onEvent(eventData);
    }
}