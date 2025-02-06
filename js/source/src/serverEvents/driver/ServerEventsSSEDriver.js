import {BaseServerEventsDriver} from "./BaseServerEventsDriver";

/**
 * Server - Sent events driver
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 */
export class ServerEventsSSEDriver extends BaseServerEventsDriver {
    /**
     * @type {EventSource|null}
     */
    #eventSource = null;

    init() {
        if (this.#eventSource !== null) {
            return;
        }

        if (this.mode !== 'production') {
            console.log('init ServerEventsSSEDriver. ', this.eventsUrl);
        }

        const url = this.eventsUrl + '&ts=' + Date.now();
        this.#eventSource = new EventSource(url);

        if (this.mode !== 'production') {
            this.#eventSource.onopen = function (event) {
                console.log('ServerEventsSSEDriver connection is open');
            };
        }

        this.#eventSource.onmessage = (event) => {
            const eventData = JSON.parse(event.data);
            if (eventData.data !== undefined && (typeof eventData.data === 'string') && eventData.data !== '') {
                eventData.data = JSON.parse(eventData.data);
            }
            eventData.eventId = event.lastEventId;
            this.onEventTriggered(eventData);
        };

        if (this.mode !== 'production') {
            this.#eventSource.onerror = function (event) {
                console.log('ServerEventsSSEDriver connection error');
            }
        }
    }
}