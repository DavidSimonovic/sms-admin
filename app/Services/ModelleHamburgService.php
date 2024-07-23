<?php

namespace App\Services;

use Goutte\Client;

class ModelleHamburgService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function scrape($url)
    {
        $crawler = $this->client->request('GET', $url);

        // Extract other information
        $postcode = $crawler->filter('div.model-phone m-r')->text();


        // Return the scraped data
        return [
            'postcode' => $postcode,
        ];
    }
}
