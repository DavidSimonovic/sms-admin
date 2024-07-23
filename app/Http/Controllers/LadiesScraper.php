<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class LadiesScraper extends Controller
{
    public function __construct(\App\Services\LadiesScraperService $scraper)
    {
        $this->scraper = $scraper;
    }


}
