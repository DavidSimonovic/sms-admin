<?php

namespace App\Http\Controllers;

use App\Jobs\SixprofisGetNumbers;
use App\Jobs\SixProfisScrapingJob;
use App\Models\Number;
use App\Models\Sixprofi;
use App\Services\SixprofisScraperService;
use Illuminate\Support\Facades\Log;

class SixprofisScraper extends Controller
{
    protected $links = [];

    protected $result = [];
    public function __construct(protected SixprofisScraperService $scraper)
    {
    }

    public function scrape()
    {
        SixProfisScrapingJob::dispatch();

        Log::log('info','Sixprofis job started');
    }

    public function links()
    {
        $links = Sixprofi::all();

        return response()->json($links);
    }

    public function deleteLinks()
    {
        $links = Sixprofi::truncate();

        return response()->json($links);
    }
}
