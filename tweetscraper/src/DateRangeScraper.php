<?php
 
namespace TweetScraper;

use DateTime;
use DateInterval;
use DatePeriod;

class DateRangeScraper
{
    private $amount = 50;
    private $sleepInSeconds = 1;
    private $identifier = 'btc';
    private $scraper;
    private $parser;
    private $persister;

    public function __construct(
        Scraper $scraper, 
        Parser $parser, 
        Persister $persister
    ) {
        $this->scraper = $scraper;
        $this->parser = $parser;
        $this->persister = $persister;
    }

    public function setAmount($amount)
    {
        $this->amount = (integer) $amount;
    }

    public function setSleepInSeconds($seconds)
    {
        $this->sleepInSeconds = (integer) $seconds;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function scrape(DateTime $from, DateTime $to)
    {
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($from, $interval, $to);

        $this->scraper->setAmount($this->amount);

	$result = array();
	foreach ($period as $revDate) {
	 	array_unshift ($result, $revDate);
		#echo 'Pushing'.$revDate->format('Y-m-d') . PHP_EOL;
	}
 	
        foreach ($result as $date) {
            $content = $this->scraper->scrape($date);
            $data = $this->parser->parse($content);
            $this->persister->persist($this->identifier, $data);
            echo 'Finished scraping for ' . $date->format('Y-m-d') . PHP_EOL;
            sleep($this->sleepInSeconds);
        }
    }
}
