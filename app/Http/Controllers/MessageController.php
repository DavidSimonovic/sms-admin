<?php

namespace App\Http\Controllers;

use App\Helper\MessageBird;
use App\Helper\TextCleaner;
use App\Jobs\SendMessageJob;
use App\Models\Campaign;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;


class MessageController extends Controller
{

    public function __construct(protected MessageBird $messageBird)
    {

    }
    public function index(Request $request)
    {
        $sites = Site::all();
        return view('message.write', compact('sites'));
    }


    public function sendSms(Request $request)
    {
        $text = TextCleaner::cleanText($request->message, 'test', 'test');

        $response  = $this->messageBird->sendSms($request->number, 'test', $text);
    }

    public function startSending($id)
    {

        $campaign = Campaign::find($id);

        SendMessageJob::dispatch($campaign);

        return redirect()->back();
    }

    public function stopSending($id)
    {
        Artisan::call('queue:flush');

        $campaign = Campaign::find($id);
        $campaign->sending_status = false;
        $campaign->save();

        return redirect()->back();
    }

}
