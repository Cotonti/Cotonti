import {ServerSentEvents} from './ServerSentEvents';

/**
 * Server Sent Events driver implementation via SharedWorker.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events
 * @link https://developer.mozilla.org/en-US/docs/Web/API/SharedWorker
 */
export class ServerSentEventsSharedWorker extends ServerSentEvents {
    init() {
        if (this.mode !== 'production') {
            console.log('init ServerSentEventsSharedWorker driver');
        }

        try {
            const worker = new SharedWorker('/js/sharedWorkerSSE.min.js');
            const port = worker.port;

            port.postMessage({config: {url: this.eventsUrl, mode: this.mode}});

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