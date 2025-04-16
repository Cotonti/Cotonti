import {BaseServerEventsClient} from "./BaseServerEventsClient";

/**
 * Shared worker server events client
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @link https://developer.mozilla.org/en-US/docs/Web/API/SharedWorker
 */
export class ServerEventsSharedWorkerClient extends BaseServerEventsClient {
    init() {
        let workerUrl = '/js/sharedWorkerServerEvents.min.js?v=1';

        if (this.mode !== 'production') {
            console.log('init ServerEventsSharedWorkerClient: ' + workerUrl);
        }

        try {
            const worker = new SharedWorker(workerUrl);
            const port = worker.port;

            port.postMessage({config: {mode: this.mode, driver: this.driverType, baseUrl: getBaseHref()}});

            port.onmessage = (event) => {
                if (this.mode !== 'production') {
                    console.log('ServerSentEventsSharedWorker event', event.data);
                }
                this.onEventTriggered(event.data);
            }
        } catch (e) {
            console.error(e);
        }
    }
}