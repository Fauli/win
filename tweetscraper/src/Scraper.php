<?php

namespace TweetScraper;

use \Artax\Client;
use \Artax\Request;

class Scraper
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function scrape($url)
    {
        $request = new Request;
        $request->setUri($url . '?q=bitcoin&offset=10&perpage=50&window=a&sort_method=-date&apikey=09C43A9B270A470B8EB8F2946A9369F3');
        $request->setProtocol('1.1');
        $request->setMethod('GET');

        $response = $this->client->request($request);

        return $response->getBody();
    }
}
