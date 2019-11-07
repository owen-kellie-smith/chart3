<?php

class User
{

private $conn;

    function __construct() {
        $this->conn = New Connection();
    }


function addToCookieArray( $newValue, $expiry ){
	$oldarray = array();
	if (isset($_COOKIE['tsbcodearray'])){
		$oldarray = json_decode($_COOKIE['tsbcodearray']);
	}
	$oldarray[] = $newValue;
	$this->setTSBcookieArray( $oldarray, $expiry );
}


function deleteCookie(){
	$this->setTSBcookie( "", time()-3600 );
	$this->setTSBcookieArray( "", time()-3600 );
}


function getAdminEmails(){
    $return = array();
    $sql = "SELECT AES_DECRYPT(aesEmail, UNHEX(SHA2('A String Of Pearls',512))) from user where aesEmail is not null"; 
    foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	$return[] = $row[0];
    }
    return $return;
}


function getEmailForm(){

$form = "<form action = '' method='post'>";
$form .= "<p>Your email <textarea rows='1' cols='50' name='email' ></textarea></p>";
$form .= "<div id='honey'>Leave this blank (really)
          <input type='text' name='email2' value=''>
	  </div>";
$form .= "<input type='hidden' name='action' value='storeEmail'>";
$form .= "<p><input type='submit' value='submit'></p></form>";
$form .= "<p>To get a confirmation code to enable you to use the chart printer please enter your email and hit submit.  The confirmation code will store a cookie on your computer that gives you access.  You can remove the cookie but then you'll need to enter your email again.  If your email isn't recognised, please let us know at a rehearsal.</p>";
return $form;
}





function getOneAdminEmail(){
    $all = $this->getAdminEmails();
    if (count($all) > 0){
        return $all[0]; 
    } else {
        return "null@null";
    }
}


function getNewUserForm(){

$form = "";
$form .= "<fieldset><legend>New user</legend>";
$form .= "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='addNewUser' />";
$form .= "<p>Nickname<textarea name='newNickName'></textarea></p> ";
$form .= "<p>Email<input type='email' name='newEmail'></p> ";
$form .= "<input type='submit' value='Add new user'></form>";
$form .= "</fieldset>";
return $form;
}


function getUserFromTsbcode( $tsbcode ){
$sql = "SELECT userid FROM confirmation where tsbcode = '" . $tsbcode ."' LIMIT 1;";
$ret = -1; // if nothing found
foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	$ret = $row[0];
    } 
return $ret;
}


function hasAdminCookie(){
if (ALL_USERS_ARE_ADMINS == 'All') return true;
$breturn = false;
if (!isset($_COOKIE['tsbcode'])){
	return false;
}
$breturn = $this->isrecognisedAdmin( $_COOKIE['tsbcode'] );
	if (isset($_COOKIE['tsbcodearray'])){
		$oldarray = json_decode($_COOKIE['tsbcodearray']);
		foreach ($oldarray as $ckie){
			$breturn = $breturn || $this->isrecognisedAdmin( $ckie );
		}
	}
return $breturn;
}

function hasCookieForEmail( $email ){
$breturn = false;
if (!isset($_COOKIE['tsbcode'])){
	return false;
}
$breturn = $this->isCookieForEmail( $_COOKIE['tsbcode'], $email );
	if (isset($_COOKIE['tsbcodearray'])){
		$oldarray = json_decode($_COOKIE['tsbcodearray']);
		foreach ($oldarray as $ckie){
			$breturn = $breturn || $this->isCookieForEmail( $ckie, $email );
		}
	}
return $breturn;
}


function hasValidCookie(){
if (ALL_USERS_ARE_ADMINS == 'All') return true;
$breturn = false;
if (!isset($_COOKIE['tsbcode'])){
	return false;
}
$breturn = $this->isrecognisedip( $_COOKIE['tsbcode'] );
	if (isset($_COOKIE['tsbcodearray'])){
		$oldarray = json_decode($_COOKIE['tsbcodearray']);
		foreach ($oldarray as $ckie){
			$breturn = $breturn || $this->isrecognisedip( $ckie );
		}
	}
return $breturn;
}

function isCookieForEmail( $cookie, $email ){
$breturn = false;

$sql = "SELECT COUNT(*) FROM confirmation INNER JOIN user ON confirmation.userID = user.userID where tsbcode = '" . $cookie ."' AND user.md5email = md5(trim(upper('" . $email . "')));";

foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	if ($row[0] > 0){
		$breturn = true;
	}
}

return $breturn;
}

function isrecognisedAdmin( $cookie ){
$breturn = false;

$sql = "SELECT COUNT(*) FROM confirmation INNER JOIN user on confirmation.userID = user.userID where tsbcode = '" . $cookie ."' AND user.aesEmail is not null";
foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	if ($row[0] > 0){
		$breturn = true;
	}
}

return $breturn;
}

function isrecognisedip( $cookie ){
$breturn = false;
$sql = "SELECT COUNT(*) FROM confirmation where tsbcode = '" . $cookie ."';";

foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	if ($row[0] > 0){
		$breturn = true;
	}
}

return $breturn;
}


function sendAdminDudEmail( $dudEmail ){
	$msg = "Unrecognised email.\n  " . $dudEmail;
	$msg = wordwrap($msg, 70);	
	$headers = 'Reply-To: ' . $this->getOneAdminEmail();
	mail( $this->getOneAdminEmail(), "TSB Chart dud email", $msg, $headers);
}

function sendCode( $email ){

$md5now = md5(time());
$userID = -1;
$sql = "SELECT userID from user where md5email = md5(trim(upper(' " . $email . "'))) LIMIT 1";

    foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	$userID = $row[0];
    }

$sql = "INSERT into confirmation (userID, confirmationCode, ip) VALUES( " . $userID . ", '" . $md5now . "', '" . $this->conn->getIP() . "');";
$result = $this->conn->my_execute( $sql);
if ($result){
	$msg = "To use the TSB chart printer please paste this address into your browser.\n  http://tsbchart.com/?confirmation=" . $md5now;
	$msg = wordwrap($msg, 70);
	
	$headers = 'Reply-To: ' . $this->getOneAdminEmail(). "\r\n" . 'Cc: ' .$this->getOneAdminEmail();
	mail( $email, "TSB Chart confirm email", $msg, $headers);
	}

}

function setTSBcookie( $value, $expiry ){
	setcookie("tsbcode", $value, $expiry);
}

function setTSBcookieArray( $value, $expiry ){
	setcookie("tsbcodearray", json_encode($value), $expiry);
}


function setValidCookie( $confirmation ){

include "mysql-cred.php";
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error);
} 
$sql = "SELECT confirmationID FROM confirmation where confirmationCode = '" . $confirmation ."' LIMIT 1;";
$result = mysqli_query($link, $sql);

if ($result) {
	$row = mysqli_fetch_row( $result );
	$confirmationID = $row[0];
	if ($confirmationID > 0){
	$md5now = md5(time());
	$sql = "UPDATE confirmation SET confirmationCode='EXPIRED', tsbcode = '" . $md5now . "' WHERE confirmationID = " . $confirmationID . ";";
	$result = mysqli_query($link, $sql);
	$this->setTSBcookie( $md5now, time() + 365 * 24 * 60 * 60 );
	$this->addToCookieArray( $md5now, time() + 365 * 24 * 60 * 60 );
	}
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($link);
	return false;
}

mysqli_close( $link );
}


function storeEmail( $input = array()){
	if (isset($input['email2'])) {
		if (strlen($input['email2']) > 0) {
		return false;
		}
	}

	if (isset($input['email'])) {
		$email = $input['email'];
		} else {
		return false;
		}

    $sql = "SELECT COUNT(*) from user where md5email = md5(trim(upper('" . $email . "')))";
    foreach($this->conn->listMultiple( $sql ) AS $index=>$row ){
		if ($row[0] > 0) {
			$this->sendCode( $email );
			return true;
		} else {
			$this->sendAdminDudEmail( $email );
			return false;
		}
    }
}



function storeNewUser( $email, $nickName ){

$sql = "INSERT INTO user(md5email, nickName) SELECT md5(trim(upper(' " . $email . "'))), '" . $nickName . "';";
$result = $this->conn->my_execute($sql);
}

} // end class User
