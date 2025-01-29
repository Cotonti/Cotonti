import {BaseServerEventsClient} from "./BaseServerEventsClient";
import {ServerEventsDriverFactory} from "../driver/ServerEventsDriverFactory";

/**
 * Server events client
 * @package Cotonti
 * @copyright (c) Cotonti Team
 */
export class ServerEventsClient extends BaseServerEventsClient {
    init() {
        if (this.mode !== 'production') {
            console.log('init ServerEventsClient');
        }

        const factory = new ServerEventsDriverFactory(getBaseHref(), this.mode);

        const driver = factory.getByType(this.driverType, this.mode);
        driver.addEventListener('event', (event) => {
            this.onEventTriggered(event.detail);
        });
        driver.init();
    }
}