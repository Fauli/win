<?php

/* 
 * Output stuff for win 
 *
 * basically just the visualization, preparing data for output
 * main logic is on the server
 *
 */

$mysqli = mysqli_connect("localhost", "root", "#YOLOswag1337", "win");

$dataset = "google_raw";

getData($mysqli, $dataset);

// load template and end of main logic
include "index.html";
exit;

/*
 * functions
 */

// get shit from db

function getData($mysqli, $dataset){

  $table = array("google_raw" => "google_raw",
                 "twitter_raw" => "twitter_raw");

  $query = " SELECT * FROM ".$table[$dataset].
           "";

  try {
    $stmt = $mysqli->prepare($query)
    $stmt->execute();
    $res = $stmt->get_result();
  } catch (mysqli_sql_exception $e){
    echo $e->errorMessage();
  }

  while($row = $res->fetch_row_assoc()){
    print_r $row;
  }

}


?>
