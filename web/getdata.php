<?php

/*
 * Class to give morris chart the data it wants
 */

$mysqli = mysqli_connect('151.236.222.251', 'yolo', '#YOLOswag1337', 'win');

$dataset = $_GET["set"];
getData($mysqli, $dataset);

function getData($mysqli, $dataset){

  $table = array("google" => "google_raw",
                 "twitter" => "twitter_raw");

  $query = " SELECT * FROM ".$table[$dataset];

  $stmt = $mysqli->prepare($query);
  try {
    //$stmt->bind_param("i", 1);
    $stmt->execute();
  } catch (mysqli_sql_exception $e){
    echo $e->errorMessage();
  }

  $res = $stmt->get_result();
  while($row = $res->fetch_assoc()){
    print "{x:'".$row["Date"]."', y:' ".$row["Value"]."'},";
  }

}
