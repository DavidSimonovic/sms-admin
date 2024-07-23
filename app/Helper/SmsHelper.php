<?php

namespace App\Helper;

use App\Jobs\SendMessageJob;
use App\Models\Campaign;
use App\Models\Number;
use App\Models\Template;

class SmsHelper
{
    public static function formatSms(Campaign $campaign)
    {
        $templateIds = json_decode($campaign->template_ids);
        $siteIds = json_decode($campaign->site_ids);

        $templates = Template::whereIn('id', $templateIds)->get();
        $numbers = Number::whereIn('site_id', $siteIds)
            ->where('active', true)
            ->get();

        foreach ($numbers as $number) {
            $template = $templates->random();
            $messageBody = TextCleaner::cleanText($template->text, $number->ad_title, $number->city ?? 'Deutschland');

            SendMessageJob::dispatch(
                $number,
                $messageBody,     // Cleaned message body
                $number->ad_title, // Ad title
                $number->city ?? 'Deutschland', // City with default fallback
                'Test'            // Sender
            );
        }
    }
}
