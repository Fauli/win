<?php

/* 
 * Output stuff for win 
 *
 * basically just the visualization, preparing data for output
 * main logic is on the server
 *
 */

$db = json_decode(file_get_contents('../db.json'));

$host = $db->host;
$dbname = $db->dbname;
$user = $db->user;
$password = $db->password;

$mysqli = mysqli_connect($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
  die('Connect Error (' . $mysqli->connect_errno . ') '
    . $mysqli->connect_error);
}

$dataset = "google_raw";

//getData($mysqli, $dataset);

// load template and end of main logic
include "index.html";
exit;


?>
