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


$updateCountry=mysqli_query($dbConn,"update users set country='US' where country='United Sta'");



				