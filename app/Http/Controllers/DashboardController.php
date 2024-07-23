<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalNumbers = Number::count();
        $bouncedNumbers = Number::where('bounced', true)->count();
        $activeNumbers = Number::where('active', true)->count();
        $blockedNumbers = Number::where('active', false)->count();
        $waSent = Message::where('type','wa')->count();
        $smsSent = Message::where('type','sms')->count();

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $smsSentToday = Message::whereDate('created_at', $today)->where('type', 'sms')->count();
        $smsSentMonth = Message::whereBetween('created_at', [$startOfMonth, $today])->where('type', 'sms')->count();

        $waSentToday = Message::whereDate('created_at', $today)->where('type', 'wa')->count();
        $waSentMonth = Message::whereBetween('created_at', [$startOfMonth, $today])->where('type', 'wa')->count();

        $labels = ["Active", "Bounced", "Blocked"];
        $data = [$activeNumbers, $bouncedNumbers, $blockedNumbers];


        return view('dashboard.index', compact('labels', 'data', 'totalNumbers', 'waSent', 'waSentToday', 'waSentMonth','smsSent','smsSentToday', 'smsSentMonth',));
    }
}
