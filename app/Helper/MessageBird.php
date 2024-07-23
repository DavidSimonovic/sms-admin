<?php

namespace App\Helper;

use MessageBird\Client;
use MessageBird\Objects\Message;

class MessageBird
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(env('MESSAGEBIRD_API_KEY'));
    }

    public function sendSms($recipient, $sender, $body)
    {
        try {
            $message = new Message();
            $message->originator = $sender;
            $message->recipients = $recipient;
            $message->body = $body;
            $message->reportUrl = route('webhook.messagebird');

            $response = $this->client->messages->create($message);

            return $response;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
