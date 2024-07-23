<?php

namespace App\Console\Commands;

use App\Helper\SmsHelper;
use App\Models\Campaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-scheduled-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled messages based on active campaigns';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $campaigns = Campaign::where('status', true)->get();

        foreach ($campaigns as $campaign) {

            $day = $campaign->day;
            $frequency = $campaign->frequency;

            if (strtolower($frequency) === 'test') {
                SmsHelper::formatSms($campaign);
                $campaign->last_exec = date('Y-m-d');
                $campaign->save();
            }

            if (strtolower($day) === strtolower(date(1))) {

                if (strtolower($frequency) === 'weekly' && $campaign->last_exec < date('Y-m-d', strtotime('+6 days'))) {

                    SmsHelper::formatSms($campaign);
                    $campaign->last_exec = date('Y-m-d');
                    $campaign->save();

                } else if (strtolower($frequency) === 'monthly' && date('Y-m-d', strtotime('+30 days'))) {

                    SmsHelper::formatSms($campaign);
                    $campaign->last_exec = date('Y-m-d');
                    $campaign->save();
                }

            }

            Log::info('Scheduled messages have been dispatched.');
        }
    }
}
