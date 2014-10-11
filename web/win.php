<?php

/* 
 * Output stuff for win 
 *
 * basically just the visualization, preparing data for output
 * main logic is on the server
 *
 */

$mysqli = mysqli_connect('151.236.222.251', 'yolo', '#YOLOswag1337', 'win');

if ($mysqli->connect_error) {
  die('Connect Error (' . $mysqli->connect_errno . ') '
    . $mysqli->connect_error);
}

$dataset = "google_raw";

//getData($mysqli, $dataset);

// load template and end of main logic
include "index.html";
exit;

/*
 * functions
 */



?>
