<?php

namespace TweetScraper;

use Artax\Client;
use Artax\Request;
use DateTime;

class Scraper
{
    private $client;
    private $amount = 100;
    private $tries = 5;
    private $url = 'http://otter.topsy.com/search.js';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setAmount($amount)
    {
        if ((int) $amount > 1000) {
            throw new InvalidAmountScraperException('Amount must be <= 1000');
        }
        $this->amount = (int) $amount;
    }

    public function setTries($tries)
    {
        $this->tries = (int) $tries;
    }

    public function scrape(DateTime $date)
    {
        $from = $date->getTimestamp();
        $to = $date->modify('+1 day')->getTimestamp();

        $params = [
            'q' => 'bitcoin',
            'perpage' => $this->amount,
            'sort_method' => '-date',
            'apikey' => '09C43A9B270A470B8EB8F2946A9369F3',
            'mintime' => $from,
            'maxtime' => $to,
        ];

        $request = new Request;
        $request->setUri($this->url . '?' . http_build_query($params));
        $request->setProtocol('1.1');
        $request->setMethod('GET');

        $tryCounter = 0;
        while ($tryCounter < $this->tries) {
            try {
                $response = $this->client->request($request);
                return $response->getBody();
            } catch (\Exception $e) {
                $tryCounter++;
            }
        }

        throw new ClientScraperException;
    }
}

class ScraperException extends \Exception {}
class ClientScraperException extends ScraperException {}
class InvalidAmountScraperException extends ScraperException {}