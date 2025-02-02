/**
 * Base server events driver
 * @package Cotonti
 * @copyright (c) Cotonti Team
 */
export class BaseServerEventsDriver extends EventTarget {
    mode = 'production';

    /**
     * @type {string|null}
     */
    eventsUrl = null;

    constructor(eventsUrl, mode = null) {
        super();

        if (mode !== null && mode !== '') {
            this.mode = mode;
        }
        this.eventsUrl = eventsUrl;
    }

    init() { }

    onEventTriggered(eventData) {
        if (this.mode !== 'production') {
            console.log('ServerEventsDriver: Server triggered event', eventData);
        }

        const event = new CustomEvent('event', {detail: eventData});
        this.dispatchEvent(event);
    }
}