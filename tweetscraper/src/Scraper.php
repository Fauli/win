<?php

namespace TweetScraper;

use Artax\Client;
use Artax\Request;
use DateTime;

class Scraper
{
    private $client;
    private $amount = 100;
    private $maxTries = 5;
    private $sleepInSeconds = 2;
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

    public function setSleepInSeconds($seconds)
    {
        $this->sleepInSeconds = (integer) $seconds;
    }

    public function setMaxTries($maxTries)
    {
        $this->maxTries = (int) $maxTries;
    }

    public function scrape(DateTime $date)
    {
        $from = $date->getTimestamp();
        $to = $date->modify('+1 day')->getTimestamp();

        $remainingAmount = $this->amount;
        $offset = 0;

        $pages = [];
        while ($remainingAmount > 0) {
            $amount = ($remainingAmount > 100) ? 100 : $remainingAmount;
            $pages[] = $this->getPage($amount, $offset, $from, $to);
            $offset = $offset + $amount;
            $remainingAmount = $remainingAmount - $amount;
            sleep($this->sleepInSeconds);
        }
        return $pages;
    }

    private function getPage($amount, $offset, $from, $to)
    {
        $params = [
            'q' => 'bitcoin',
            'perpage' => $amount,
            'sort_method' => '-date',
            'apikey' => '09C43A9B270A470B8EB8F2946A9369F3',
            'mintime' => $from,
            'maxtime' => $to,
            'allow_lang' => 'en',
        ];

        $request = new Request;
        $request->setUri($this->url . '?' . http_build_query($params));
        $request->setProtocol('1.1');
        $request->setMethod('GET');

        return $this->getResponse($request)->getBody();
    }

    private function getResponse($request)
    {
        $tryCounter = 0;
        while ($tryCounter < $this->maxTries) {
            try {
                $client = new Client;
                $response = $client->request($request);
                return $response;
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