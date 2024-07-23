<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageBirdController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        Log::log(1,'MessageBird Webhook Data: ');
        // Log the incoming data for debugging purposes
        Log::info('MessageBird Webhook Data: ', $data);

        // Check if the status is "bounced"
        if (isset($data['recipients']) && is_array($data['recipients'])) {
            foreach ($data['recipients'] as $recipient) {
                if ($recipient['status'] === 'bounced') {
                    // Handle the bounced number
                    $number = $recipient['recipient'];
                    Log::warning('Number bounced: ' . $number);

                    // Update the numbers table to mark the number as bounced
                    DB::table('numbers')
                        ->where('number', $number)
                        ->update(['bounced' => true, 'active' => false]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
