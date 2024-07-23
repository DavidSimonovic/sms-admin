<?php

namespace App\Services;

use Goutte\Client;
use Illuminate\Http\Request;

class LadiesScraperService
{

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function scrape($url)
    {
        $crawler = $this->client->request('GET', $url);

        $numbers = [];

        $crawler->filter('span[itemprop="telephone"]')->each(function ($node) use (&$numbers) {
            $text = preg_replace('/<small[^>]*>.*?<\/small>/', '', $node->html());
            $numbers[] = trim($text);
        });

        // Initialize variables with null
        $postcode = null;
        $city = null;
        $adTitle = null;

        // Check if the elements exist before fetching their text
        if ($crawler->filter('span[itemprop="postalCode"]')->count() > 0) {
            $postcode = $crawler->filter('span[itemprop="postalCode"]')->text();
        }

        if ($crawler->filter('span[itemprop="addressLocality"]')->count() > 0) {
            $city = $crawler->filter('span[itemprop="addressLocality"]')->text();
        }

        if ($crawler->filter('h1')->count() > 0) {
            $adTitle = $crawler->filter('h1')->text();
        }

        $filteredNumbers[] = array_filter($numbers, function ($number) {
            $number = preg_replace('/[^+\d]/', '', $number);
            return preg_match('/^\+491|^01/', $number);
        });

        if (isset($filteredNumbers[0][1])) {
            $cleanNumber = preg_replace('/[^+\d]/', '', $filteredNumbers[0][1]);

            return [
                'number' => $cleanNumber,
                'postcode' => $postcode,
                'city' => $city,
                'ad_title' => $adTitle,
                'site_id' => 1
            ];
        } else {
            // Return null if no valid number is found
            return null;
        }
    }


    public function getAllUrlsPaggination($url)
    {

        $crawler = $this->client->request('GET', $url);

        dd($crawler);

//        $hrefs = [];
//
//// Iterate over each item gallery div
//        $crawler->filter('.item.gallery')->each(function ($node) use (&$hrefs) {
//            // Select the <a> tag inside the current node
//            $aTag = $node->filter('a')->first();
//
//            // Retrieve the href attribute
//            $href = $aTag->attr('href');
//
//            // Push href value into array
//            $hrefs[] = $href;
//        });

        return $crawler;

    }

}
