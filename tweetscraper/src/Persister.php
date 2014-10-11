<?php

namespace TweetScraper;

use PDO;

class Persister
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function persist($term, array $data)
    {
        $sql = 'INSERT INTO win.twitter_raw (Date,Term,Value) VALUES (FROM_UNIXTIME(:timestamp),:term,:value);';
            
        foreach ($data as $row) {
            if (!is_array($row) || !array_key_exists('timestamp', $row) || !array_key_exists('value', $row)) {
                throw new InvalidDataException;
            }

            $query = $this->pdo->prepare($sql);
            $query->execute([
                ':timestamp' => $row['timestamp'], 
                ':term' => (string) $term,
                ':value' => $row['value'],
            ]);
        }
    }
}

class PersisterException extends \Exception {}
class InvalidDataException extends PersisterException {}