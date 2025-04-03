/**
 * Private Messages module
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

$(document).on( "mouseenter", ".pm-star", function() {
	if (!$(this).hasClass('pm-star-readonly')) {
		$(this).addClass('pm-star-hover');
		if ($(this).hasClass('pm-star-on')) {
			$(this).addClass('pm-star-off').removeClass('pm-star-on');
		}
	}
});

$(document).on( "mouseleave", ".pm-star", function() {
    if (!$(this).hasClass('pm-star-readonly')) {
        $(this).removeClass('pm-star-hover');
        if ($(this).hasClass('pm-star-off')) {
            $(this).addClass('pm-star-on').removeClass('pm-star-off');
        }
    }
});

$(document).on( "click", ".pm-star", function(e) {
    if (!$(this).hasClass('pm-star-readonly') && ajaxEnabled) {
        e.preventDefault();
        var txt = $(this).children('a').attr('href');
        ajaxSend({
            url: txt,
            divId: 'pagePreview'
        });
        $(this).toggleClass('pm-star-off');
        $(this).children('a').attr('title', '');

        return false;
    }
});

(function () {
    class PrivateMessages {
        /**
         * @type {String}
         */
        notificationSound = 'modules/pm/inc/new-message.mp3';

        #played = [];

        /**
         * @type {BroadcastChannel|null}
         */
        #broadcastChannel = null;

        initNotificationHandler() {
            const serverEvents = cot.getServerEvents();
            // serverEvents.mode = 'develop';
            serverEvents.addObserver('pmObserver', 'newPm', (data) => {
                this.handleNewMessage(data);
            });

            // Connection to a broadcast channel
            this.#broadcastChannel = new BroadcastChannel('privateMessages');
            this.#broadcastChannel.onmessage = (event) => {
                if (event.data.event === 'notificationSoundPlayed') {
                    if (!this.#played.includes(event.data.data.id)) {
                        this.#played.push(event.data.data.id);
                    }
                }
            };
        }

        handleNewMessage(event) {
            this.#playNotificationSound(event);

            const data = event.data;

            let title = `<a href="${data.url}">${data.L.newMessage}</a><br>${data.L.from}: `
                + `<a href="${data.fromUser.url}">${data.fromUser.fullName}</a>`;
            let text = `<a href="${data.url}">${data.text}</a>`;

            cot.toast(text, title);
        }

        #playNotificationSound(event) {
            if (this.#played.includes(event.eventId)
                || this.notificationSound === ''
                || this.notificationSound === null
            ) {
                return;
            }

            const audio = new Audio(this.notificationSound);

            if (navigator.getAutoplayPolicy !== undefined) {
                if (navigator.getAutoplayPolicy(audio) !== 'allowed') {
                    return;
                }

                // Tell other tabs that the notification sound will be played in this one.
                this.#broadcastChannel.postMessage(
                    {event: 'notificationSoundPlayed', data: {id: event.eventId}},
                );
                this.#played.push(event.eventId);

                audio.play();

                return;
            }

            audio.play()
                .then(() => {
                    // Tell other tabs that the notification sound has been played in this one.
                    this.#broadcastChannel.postMessage(
                        {event: 'notificationSoundPlayed', data: {id: event.eventId}},
                    );
                    this.#played.push(event.eventId);
                })
                .catch((error) => {

                });
        }
    }

    window.cot.pm = new PrivateMessages();
})();
