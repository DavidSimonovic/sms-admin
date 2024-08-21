<?php

namespace App\Jobs;

use App\Helper\TextCleaner;
use App\Models\Campaign;
use App\Models\Message;
use App\Models\Number;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MessageBird\Client;
use MessageBird\Objects\Message as MessageBirdMessage;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $messageBirdClient;
    protected $processedNumbers = []; // In-memory array to track processed numbers

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->messageBirdClient = new Client(env('MESSAGEBIRD_API_KEY'));
    }

    public function handle()
    {
        $templateIds = json_decode($this->campaign->template_ids, true);
        $siteIds = json_decode($this->campaign->site_ids, true);

        $templates = Template::whereIn('id', $templateIds)->pluck('text', 'id');

        Number::whereIn('site_id', $siteIds)
            ->where('active', true)
            ->where('bounced', false)
            ->chunk(100, function ($numbers) use ($templates) {
                foreach ($numbers as $number) {
                    if (!$number || is_null($number->number)) {
                        Log::warning('Number is null or invalid for ID:', ['number_id' => $number->id ?? 'unknown']);
                        continue;
                    }

                    // Skip if this number has already been processed
                    if (in_array($number->number, $this->processedNumbers)) {
                        Log::info('Skipping duplicate number:', ['number' => $number->number]);
                        continue;
                    }

                    if (!$this->isValidGermanPhoneNumber($number->number)) {
                        $number->update([
                            'bounced' => true,
                            'active' => false,
                        ]);
                        Log::error('Number invalid', [
                            'number' => $number->number,
                            'campaign_id' => $this->campaign->id,
                        ]);
                        continue;
                    }

                    $template = $templates->random();
                    $messageBody = TextCleaner::cleanText($template, $number->ad_title, $number->city ?? 'Deutschland');

                    Log::info('Sending message', [
                        'number' => $number->number,
                        'campaign_id' => $this->campaign->id,
                    ]);

                    $status = $this->sendSms($number, $this->campaign->originator, $messageBody);

                    Message::create([
                        'type' => 'sms',
                        'number_id' => $number->id,
                        'message' => $messageBody,
                        'status' => $status,
                    ]);

                    if ($status === 'failed' || $status === 'unknown') {
                        $number->update([
                            'bounced' => true,
                            'active' => false,
                        ]);
                    }

                    // Mark this number as processed
                    $this->processedNumbers[] = $number->number;
                }
            });

        // Update campaign sending_status after processing all numbers
        $this->campaign->update(['sending_status' => false]);
    }

    private function isValidGermanPhoneNumber($number)
    {
        $pattern = '/^(?:\+49|0049|0)[1-9][0-9]{4,14}$/';
        return preg_match($pattern, $number);
    }

    private function sendSms($number, $sender, $body)
    {
        if (is_null($number) || is_null($number->number)) {
            Log::error('Invalid number object', ['number' => $number]);
            return 'failed';
        }

        try {
            $message = new MessageBirdMessage();
            $message->originator = $sender;
            $message->recipients = $number->number;
            $message->body = $body;
            $message->reportUrl = route('webhook.messagebird');

            $response = $this->messageBirdClient->messages->create($message);

            if (isset($response->recipients->items[0])) {
                return $response->recipients->items[0]->status ?? 'unknown';
            } else {
                Log::warning('Unexpected MessageBird response structure', ['response' => json_encode($response)]);
                return 'unknown';
            }
        } catch (\Exception $e) {
            Log::error('Failed to send SMS via MessageBird', [
                'error' => $e->getMessage(),
                'number' => $number->number ?? 'unknown',
                'campaign_id' => $this->campaign->id,
            ]);
            return 'failed';
        }
    }
}

