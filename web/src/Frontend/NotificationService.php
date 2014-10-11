<?php

namespace Web\Frontend;

use PDO;

class NotificationService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getNotifications()
    {
        $sql = 'SELECT Date, Message, glyph as Glyph FROM win.notifications ORDER by Date DESC limit 10'; 

        $query = $this->pdo->prepare($sql);
        $query->execute();

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                "Date" => $row["Date"],
                "Message" => $row["Message"],
                "Glyph" => $row["Glyph"],
            ];
        }
        return $out;
    }
}