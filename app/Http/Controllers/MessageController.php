<?php

namespace App\Http\Controllers;

use App\Helper\MessageBird;
use App\Helper\TextCleaner;
use App\Models\Site;
use Illuminate\Http\Request;


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
        dump($response);
    }

}
