import {ServerEventsSSEDriver} from "./ServerEventsSSEDriver";
import {ServerEventsAjaxDriver} from "./ServerEventsAjaxDriver";

/**
 * Base server events driver
 * @package Cotonti
 * @copyright (c) Cotonti Team
 */
export class ServerEventsDriverFactory {
    #baseUrl = '/';

    #mode = 'production'

    constructor(baseUrl = null, mode = null) {
        if (baseUrl !== null) {
            this.#baseUrl = baseUrl;
        }
        if (mode !== null) {
            this.#mode = mode;
        }
    }

    /**
     * @param {string} driverType
     * @returns {ServerEventsSSEDriver|ServerEventsAjaxDriver}
     */
    getByType(driverType) {
        let driver = null;

        const serverSentEventsUrl = this.#baseUrl + '?n=server-events&a=sse';
        const ajaxEventsUrl = this.#baseUrl + '?n=server-events&a=ajax&_ajax=1';

        switch (driverType) {
            case 'sse':
                // Server-Sent events driver
                driver = new ServerEventsSSEDriver(serverSentEventsUrl, this.#mode);
                if (this.#mode !== 'production') {
                    console.log('using ServerEventsSSEDriver', serverSentEventsUrl);
                }
                break;

            default:
                driver = new ServerEventsAjaxDriver(ajaxEventsUrl, this.#mode);
                if (this.#mode !== 'production') {
                    console.log('using ServerEventsAjaxDriver', ajaxEventsUrl);
                }
        }

        return driver;
    }
}