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
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetchForName($name)
    {
        if (!array_key_exists($name, $this->map)) {
            throw new InvalidNameException;
        }

        $dbname = $this->map[$name];

        $sql = 'SELECT unix_timestamp(Date) as date , AVG( VALUE ) as value
            FROM ' . $dbname . '
            GROUP BY YEAR( Date ) , MONTH( Date ), DAY( Date )
            ORDER BY Date DESC';

        $query = $this->pdo->prepare($sql);
        $query->execute();

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $out[] = [$row['date'], $row['value']];
        }
        return $out;
    }
}

class DataServiceException extends \Exception {}
class InvalidNameException extends DataServiceException {}