/**
 * Server Sent Events client
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 */
export class ServerSentEventsClient {
    /**
     * @type {string|null}
     */
    eventsUrl = null;

    /**
     * @type {EventSource|null}
     */
    #eventSource = null;

    /**
     * @type {function|null}
     */
    onEvent = null;

    constructor(eventsUrl, mode = null) {
        this.eventsUrl = eventsUrl;
        if (mode !== null) {
            this.mode = mode;
        }
        this.init();
    }

    init() {
        if (this.#eventSource !== null) {
            return;
        }

        if (this.mode !== 'production') {
            console.log('init ServerSentEventsClient');
        }

        this.#eventSource = new EventSource(this.eventsUrl);

        if (this.mode !== 'production') {
            this.#eventSource.onopen = function (event) {
                console.log('ServerSentEventsClient connection is open');
            };
        }

        this.#eventSource.onmessage = (event) => {
            const eventData = JSON.parse(event.data);
            if (eventData.data !== undefined && (typeof eventData.data === 'string') && eventData.data !== '') {
                eventData.data = JSON.parse(eventData.data);
            }
            eventData.id = event.lastEventId;
            this.#onEventTriggered(eventData);
        };

        if (this.mode !== 'production') {
            this.#eventSource.onerror = function (event) {
                console.log('ServerSentEventsClient connection error');
            }
        }
    }

    #onEventTriggered(eventData) {
        if (this.mode !== 'production') {
            console.log('ServerSentEventsClient: Server triggered event', eventData);
        }

        if (typeof this.onEvent !== 'function') {
            console.error(this.constructor.name + '.onEvent is not defined');
        }
        this.onEvent(eventData);
    }
}
