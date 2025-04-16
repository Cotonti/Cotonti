import {BaseServerEventsDriver} from "./BaseServerEventsDriver";

/**
 * Server events ajax driver
 * @package Cotonti
 * @copyright (c) Cotonti Team
 */
export class ServerEventsAjaxDriver extends BaseServerEventsDriver {
    #timeOutPeriod = 6000; // 6 sec.

    #timerId = null;

    #inited = false;

    init() {
        if (this.#inited) {
            return;
        }

        this.#inited = true;

        if (this.mode !== 'production') {
            console.log('init ServerEventsAjaxDriver ', this.eventsUrl);
        }

        this.#timerId = setTimeout(() => this.#getEvents(), 500);
    }

    async #getEvents() {
        try {
            const url = this.eventsUrl + '&ts=' + Date.now();
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            const result = await response.json();

            if (result.error !== undefined) {
                if (result.error.code === 'driverDisabled') {
                    if (this.mode !== 'production') {
                        console.error(result.error.message);
                    }
                    // Do not send more requests
                    return;
                }
            }

            if (result.events !== undefined) {
                result.events.forEach((event) => {
                    event.eventId = event.id !== undefined ? event.id : null;
                    this.onEventTriggered(event);
                });
            }

            this.#timerId = setTimeout(() => this.#getEvents(), this.#timeOutPeriod);
        } catch (error) {
            console.error('ServerEventsAjaxDriver get events error: ' + error.message);
            this.#timerId = setTimeout(() => this.#getEvents(), this.#timeOutPeriod);
        }
    }
}