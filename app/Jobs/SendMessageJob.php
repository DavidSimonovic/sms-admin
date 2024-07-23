<?php

namespace App\Jobs;

use App\Helper\MessageBird;
use App\Models\Message;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MessageBird\Client;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $recipient;
    protected $name;
    protected $city;
    protected $sender;
    protected $client;

    public function __construct($recipient, $message, $name, $city, $sender)
    {
        $this->recipient = $recipient; // Assumed to be a string (phone number)
        $this->message = $message;
        $this->name = $name;
        $this->city = $city;
        $this->sender = $sender;
        $this->client = new Client(env('MESSAGEBIRD_API_KEY'));
    }

    public function handle(MessageBird $messageBirdService)
    {
        try {
            $response = $messageBirdService->sendSms($this->recipient->number, $this->sender, $this->message);

            Log::info('MessageBird response', ['response' => json_encode($response)]);

            Message::create([
                'type' => 'sms',
                'number_id' => $this->recipient->id, // Assuming you have a field for the number
                'message' => $this->message,
                'status' => $response->recipients->items[0]->status ?? 'unknown',
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send SMS via MessageBird', ['error' => $e->getMessage()]);
        }
    }
}
