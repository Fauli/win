<?php

namespace TweetScraper;

use Artax\Client;
use Artax\Request;
use DateTime;

class Scraper
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function scrape($url, DateTime $date)
    {
        $from = $date->getTimestamp();
        $to = $date->modify('+1 day')->getTimestamp();

        $params = [
            'q' => 'bitcoin',
            'perpage' => 50,
            'sort_method' => '-date',
            'apikey' => '09C43A9B270A470B8EB8F2946A9369F3',
            'mintime' => $from,
            'maxtime' => $to,
        ];

        $request = new Request;
        $request->setUri($url . '?' . http_build_query($params));
        $request->setProtocol('1.1');
        $request->setMethod('GET');

        $response = $this->client->request($request);

        return $response->getBody();
    }
}
