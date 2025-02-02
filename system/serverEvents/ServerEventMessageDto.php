<?php

declare(strict_types=1);

namespace cot\serverEvents;

defined('COT_CODE') or die('Wrong URL');

/**
 * Server Event message DTO
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class ServerEventMessageDto
{
    /**
     * @var ?int The event ID to set the EventSource object's last event ID value.
     */
    public $id = null;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string A string identifying the type of event described.
     *   If this is specified, an event will be dispatched on the browser to the listener for the specified event name;
     *   the website source code should use addEventListener() to listen for named events.
     *   The onmessage handler is called if no event name is specified for a message.
     */
    public $event;

    /**
     * @var string The initial $event
     * @todo unused
     */
    public $initialEvent;

    /**
     * @var string The data field for the message. When the EventSource receives multiple consecutive lines that
     *   begin with data:, it will concatenate them, inserting a newline character between each one.
     *   Trailing newlines are removed.
     */
    public $data;

    /**
     * @var int The reconnection time to use when attempting to send the event. This must be an integer,
     *   specifying the reconnection time in milliseconds. If a non-integer value is specified, the field is ignored.
     *
     * It’s a client instruction: the browser will wait the specified time after it detects a broken connection
     * (because the server closes the connection after a message perhaps) before re-establishing a connection to the
     * SSE resource url.
     */
    public $retry = 10000;

    /**
     * @var string This is just a comment, since it starts with a colon character. As mentioned previously,
     *   this can be useful as a keep-alive if messages may not be sent regularly.
     */
    public $comment;

    public static function fromArray($data): self
    {
        $event = new ServerEventMessageDto();
        $event->id = (int) $data['id'];
        $event->userId = (int) $data['user_id'];
        $event->event = $data['event'];
        $event->data = is_string($data['data']) ? json_decode($data['data'], true) : $data['data'];
        return $event;
    }

    public function toArray(): array
    {
        $event = [];
        if ($this->id !== null) {
            $event['id'] = $this->id;
        }
        if ($this->comment !== '') {
            $event['comment'] = $this->comment;
        }
        $event['event'] = empty($this->event) ? null : $this->event;
        $event['data'] = empty($this->data)
            ? null
            : (is_array($this->data) ? $this->data : json_decode($this->data));

        return $event;
    }

    public function __toString()
    {
        $event = [];
        if ($this->comment !== '') {
            $event[] = sprintf(': %s', $this->comment);
        }
        if ($this->id !== null) {
            $event[] = sprintf('id: %s', $this->id);
        }
        if ($this->retry > 0) {
            $event[] = sprintf('retry: %s', $this->retry);
        }

        // Messages from the server that do have an 'event' field defined are received as events with the name given in
        // 'event'.
        // @see https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events#listening_for_custom_events
        // Will pass eventName in 'data' field
//        if ($this->event !== '') {
//            $event[] = sprintf('event: %s', $this->event);
//        }

        $data = [
            'event' => empty($this->event) ? null : $this->event,
            'data' => empty($this->data)
                ? null
                : (is_array($this->data) ? $this->data : json_decode($this->data)),
        ];

        $event[] = sprintf('data: %s', json_encode($data, JSON_FORCE_OBJECT));

        return implode("\n", $event) . "\n\n";
    }
}