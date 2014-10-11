<?php

/*
 * Class to give morris chart the data it wants
 */

$db = json_decode(file_get_contents('../db.json'));

$host = $db->host;
$dbname = $db->dbname;
$user = $db->user;
$password = $db->password;

$mysqli = mysqli_connect($host, $user, $password, $dbname);

getNotifications($mysqli, $dataset, $from, $to);

function getNotifications($mysqli, $dataset, $from, $to){

  $query = "SELECT Date, Message FROM win.notifications limit 10";

  $stmt = $mysqli->prepare($query);
  try {
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
  } catch (mysqli_sql_exception $e){
    echo $e->errorMessage();
  }

  $res = $stmt->get_result();
  $out = [];
  while($row = $res->fetch_assoc()){
    $out[] = ["Date" => $row["Date"],
              "Message" => $row["Message"]
             ];
  }
  print json_encode($out, JSON_PRETTY_PRINT);

}
