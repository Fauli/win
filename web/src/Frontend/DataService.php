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

        $sql = 'SELECT CONCAT_WS("-", YEAR( Date ) , MONTH( Date ), DAY(Date)) as date , SUM( VALUE ) as value
            FROM ' . $dbname . '
            GROUP BY YEAR( Date ) , MONTH( Date ), MONTH( Date )
            ORDER BY YEAR( Date ) DESC';

        $query = $this->pdo->prepare($sql);
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}

class DataServiceException extends \Exception {}
class InvalidNameException extends DataServiceException {}