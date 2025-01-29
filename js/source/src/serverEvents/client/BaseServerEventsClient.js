/**
 * Base server events client
 * @package Cotonti
 * @copyright (c) Cotonti Team
 */
export class BaseServerEventsClient extends EventTarget {
    /**
     * @type {string|null}
     */
    mode = 'production';

    /**
     * @type {string}
     */
    driverType = '';

    /**
     * @param {string} driverType
     * @param {string|null} mode
     */
    constructor(driverType, mode = null) {
        super();

        this.driverType = driverType;

        if (mode !== null) {
            this.mode = mode;
        }

        this.init();
    }

    onEventTriggered(eventData) {
        if (this.mode !== 'production') {
            console.log('ServerEventsClient: Server triggered event', eventData);
        }

        const event = new CustomEvent('event', {detail: eventData});
        this.dispatchEvent(event);
    }
}