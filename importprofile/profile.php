<?php
 error_reporting(E_ALL ^ E_NOTICE);	

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


$userCount = mysqli_query($dbConn,"select count(id) as usersCount from users");
$resultCount=mysqli_fetch_array($userCount);
extract($resultCount);

echo "Total Users Before Upload: ".$usersCount."<br><br><br><br>";
//die();



$profile = mysqli_query($dbConn,"select * from 20K_WW_ELITE LIMIT 1000 OFFSET 22000");



if(mysqli_num_rows($profile)>0){

while($resultprofile=mysqli_fetch_array($profile)){
extract($resultprofile);

$importId = $id;

$avater="upload/photos/import/".$importId."_1.jpg";




mysqli_query($dbConn,"INSERT INTO users (username, email, first_name) VALUES ('$name', '$email', '$name')");


$lastid = mysqli_query($dbConn,"select max(id) as lastID from users");
$resultlastid=mysqli_fetch_array($lastid);
extract($resultlastid);

$userID= $lastID;


$password = '$2y$11$tyVdvRcCtxTfEMGMZEBoO.s.z.3b.RXxbvolZcbnSqfY1yeTyZrSW';


if($userID!=''){


$updatePassword=mysqli_query($dbConn,"update users set password='$password' where id='$userID'");

$updateGender=mysqli_query($dbConn,"update users set gender='$gender' where id='$userID'");

$updateBirthday=mysqli_query($dbConn,"update users set birthday='$birthday' where id='$userID'");

$updateCountry=mysqli_query($dbConn,"update users set country='$country' where id='$userID'");

$updateState=mysqli_query($dbConn,"update users set state='$state' where id='$userID'");

$updateCity=mysqli_query($dbConn,"update users set city='$city' where id='$userID'");

$updateAbout=mysqli_query($dbConn,"update users set about='$Description' where id='$userID'");

$updateAvater=mysqli_query($dbConn,"update users set avater='$avater' where id='$userID'");

$updateInterest=mysqli_query($dbConn,"update users set interest='$PrimaryInterest' where id='$userID'");

$updatePrivilage=mysqli_query($dbConn,"update users set email_code='1234', src='import', type='user', verified='1', active='1', admin='0', status='3', start_up='3' where id='$userID'");

$updateImportId=mysqli_query($dbConn,"update users set import_id='$importId' where id='$userID'");

for($i=1; $i<=3; $i++){
$file="upload/photos/import/".$importId."_".$i.".jpg";
mysqli_query($dbConn,"INSERT INTO mediafiles (user_id, file, is_confirmed) VALUES ('$userID', '$file', '1')");
}
}

}
}



$userCount21 = mysqli_query($dbConn,"select count(id) as usersCount21 from users");
$result21=mysqli_fetch_array($userCount21);
extract($result21);

echo "Total Users Before Upload: ".$usersCount21."<br><br><br><br>";

				