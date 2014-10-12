<?php

namespace Web\Frontend;

use PDO;

class DataService
{
    private $pdo;
    private $map = [
        'google' => 'google_raw',
        'twitter' => 'twitter_raw',
        'bitcoin' => 'bitcoin_history',
        'bitcoin-analysis' => 'v_bitcoin_analysis'
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetchForName($name, $from, $to)
    {
        if (!array_key_exists($name, $this->map)) {
            throw new InvalidNameException;
        }

        $dbname = $this->map[$name];

        if($name == "bitcoin-analysis"){
            $sql = 'SELECT unix_timestamp(Date) as date , AVG( DIFFERENCE ) as value
                FROM ' . $dbname . '
                WHERE Date >= str_to_date(:from,"%Y-%m-%d") AND Date <= str_to_date(:to,"%Y-%m-%d")
                GROUP BY YEAR( Date ) , MONTH( Date ), WEEK( Date )
                ORDER BY Date DESC';
        } else{
            $sql = 'SELECT unix_timestamp(Date) as date , AVG( VALUE ) as value
                FROM ' . $dbname . '
                WHERE Date >= str_to_date(:from,"%Y-%m-%d") AND Date <= str_to_date(:to,"%Y-%m-%d")
                GROUP BY YEAR( Date ) , MONTH( Date ), WEEK( Date )
                ORDER BY Date DESC';
        }

        $query = $this->pdo->prepare($sql);
        $query->execute([':from' => $from, ':to' => $to]);

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $out[] = [$row['date'] * 1000, $row['value']];
        }
        return $out;
    }

}

class DataServiceException extends \Exception {}
class InvalidNameException extends DataServiceException {}
