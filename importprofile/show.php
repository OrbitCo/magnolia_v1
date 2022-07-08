<?php


//live
$dbHost = 'localhost';
$dbUser = 'loveojzu_loveojzu';
$dbPass = 'Kmlink123';
$dbName = 'loveojzu_loveojzu';

$dbConn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($dbConn->connect_error) {
    die('Connect Error (' . $dbConn->connect_errno . ') '
            . $dbConn->connect_error);
}

global $dbConn;


$updateCountry=mysqli_query($dbConn,"update users set privacy_show_profile_on_google='1', privacy_show_profile_random_users='1', privacy_show_profile_match_profiles='1'");



				