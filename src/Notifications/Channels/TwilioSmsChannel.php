<?php

namespace Guesl\Admin\Notifications\Channels;

use Guesl\Admin\Notifications\Messages\TwilioMessage;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

/**
 * Class TwilioSmsChannel
 * @package Guesl\Admin\Notifications\Channels
 */
class TwilioSmsChannel
{

    /**
     * @var Client
     */
    protected $twilio;

    /**
     * The phone number notifications should be sent from.
     *
     * @var string
     */
    protected $from;

    /**
     * Create a new Twilio channel instance.
     *
     * @param  $twilio
     * @param  string $from
     */
    public function __construct(Client $twilio, $from)
    {
        $this->from = $from;
        $this->twilio = $twilio;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return MessageInstance
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$to = $notifiable->routeNotificationFor(TwilioSmsChannel::class, $notification)) {
            return;
        }

        $message = $notification->toTwilio($notifiable);

        if (is_string($message)) {
            $message = new TwilioMessage($message);
        }

        return $this->twilio->messages->create(
            $to,
            array(
                'from' => $message->from ?: $this->from,
                'body' => trim($message->content),
            )
        );
    }
}
