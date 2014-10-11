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

$dataset = $_GET["set"];
$from = (isset($_GET["from"]) ? $_GET["from"] : "2012-01-01");
$to = (isset($_GET["to"]) ? $_GET["to"] : "2099-01-01");

getData($mysqli, $dataset, $from, $to);

function getData($mysqli, $dataset, $from, $to){

  $table = array("google" => "google_raw",
                 "twitter" => "twitter_raw",
                 "bitcoin" => "bitcoin_history",
		 "all" => "all_sources");

if($table[$dataset] != "all_resources") {
  $query = " SELECT * FROM ".$table[$dataset].
           " WHERE Date > ? AND Date < ?";
} else {
  /* this needs adaption to support getting all the sources in a Multiple Axes Line Graph 
  it also needs a change below in how it passes the value to JSON and the js/plugins/morris/morris-data script
  last but not least, that script needs to be adapted to support that type of chart....
  but only show it when all is selected */
  $query = " SELECT * FROM ".$table[$dataset].
           " WHERE Date > ? AND Date < ?";
}

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
    $out[] = ["x" => $row["Date"],
              "y" => $row["Value"]
             ];
  }
  print json_encode($out, JSON_PRETTY_PRINT);

}
