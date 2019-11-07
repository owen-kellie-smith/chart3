<?php

class Connection{


function getIP(){
    
    // Get user IP address
if ( isset($_SERVER['HTTP_CLIENT_IP']) && ! empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

$ip = filter_var($ip, FILTER_VALIDATE_IP);
$ip = ($ip === false) ? '0.0.0.0' : $ip;
return $ip;
}


function listSingle($sql){

$pairedFiles = array();
    	foreach(listMultiple( $sql ) AS $index=>$row ){
    	$pairedFiles[] = $row[0];
    	}
return $pairedFiles;
}

function listMultiple($sql){

include "mysql-cred.php";


$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 
$result = mysqli_query($link, $sql);
$pairedFiles = array();
if ($result){
    	while($row = mysqli_fetch_row( $result )) {
    	$pairedFiles[] = $row;
    	}
}
mysqli_close( $link );
return $pairedFiles;
}


function my_execute( $sql ){

include "mysql-cred.php";
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 
//echo "sql: " . $sql;
$statement = mysqli_prepare($link, $sql);
$result = mysqli_execute($statement);
if (!$result){
   die(mysqli_error($link));
}
mysqli_close($link);
return $result;    
}


function my_insert_id( $sql ){

include "mysql-cred.php";
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 
$result = mysqli_execute(mysqli_prepare($link, $sql));
$lastID = mysqli_insert_id( $link );
mysqli_close($link);
return $lastID;    
}


function saveRequest($input){
    	$now = date("Ymd");
	$ip = $this->getIP();
	$get = print_r($input,1);

	$sql = "INSERT INTO request( requestWhen, requestGet ) VALUES( '" .$now . "','" . $get . "');";
	$result = $this->my_execute( $sql);

	return $result;
}

} // end class Connection
