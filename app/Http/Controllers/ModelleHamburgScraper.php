<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;

class ModelleHamburgScraper extends Controller
{

    protected $scraper;

    public function __construct(\App\Services\ModelleHamburgService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function scrape(Request $request)
    {
        $url = $request->input('url', 'https://www.modelle-hamburg.de/modelle.html?data[start]=14&data[view]=list');
        $data = $this->scraper->scrape($url);

        return response()->json($data);
    }
}
