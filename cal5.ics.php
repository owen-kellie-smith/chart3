<?php
require_once("Gig.php");
require_once("Connection.php");
require_once("Arrangement.php");

  function timeToCal($timestamp) {
  return date('Ymd\THis\Z', $timestamp);
  }

  function dateToCal($timestamp) {
  return date('Ymd', $timestamp);
  }

  function escapeString($string) {
    return preg_replace('/([\,;])/','\\\$1', $string);
    }    

  function getDateTime($date, $time){
  // ttps://stackoverflow.com/questions/20174294/add-current-time-to-datetime
//  echo $date.$time."\r\n";
  $format = 'Y-m-dH:i:s';
  $tzel = new  DateTimeZone('Europe/London') ;
  return DateTime::createFromFormat($format, $date.$time, $tzel );
  }



function cal( $events = array() ){
//print_r($events);
$eol = "\r\n";
$load = "BEGIN:VCALENDAR" . $eol;
$load .="VERSION:2.0" . $eol ;
$load .="PRODID:-//project/author//NONSGML v1.0//EN" . $eol ;
$load .="CALSCALE:GREGORIAN" . $eol ;
foreach( $events AS $index=>$input){
$load .="BEGIN:VEVENT" . $eol ;
if( isset($input['updateTime'])){
	$load .="DTSTAMP:" . timeToCal($input['updateTime']) . $eol ;
} else {
	$load .="DTSTAMP:" . timeToCal(time()) . $eol ;
	}
//if( isset($input['end'])){
//	$load .="DTEND:" . dateToCal($input['end']) . $eol ;
//}
if( isset($input['id'])){
	$id = $input['id'];
	$load .="UID:" . $id . $eol ;
}
if( isset($input['location'])){
	if( strlen($input['location']) > 0 ){
	$load .="LOCATION:" . str_replace(",", "\,",htmlspecialchars($input['location']))  . $eol ;
	}
}
if( isset($input['notes'])){
	if( strlen($input['notes']) > 0 ){
	$load .="DESCRIPTION:" . htmlspecialchars($input['notes']) . $eol ;
	}
}
if( isset($input['url'])){
	$load .="URL;VALUE=URI:" . $input['url'] . $eol ;
}
if( isset($input['description'])){
	$load .="SUMMARY:" . htmlspecialchars($input['description']) . $eol ;
}
if( isset($input['startTime'])){
	$load .="DTSTART:" . $input['startTime']->format('Ymd\THis\Z') . $eol ;
} elseif( isset($input['date'])){
	$load .="DTSTART;VALUE=DATE:" . dateToCal($input['date']) . $eol ;
}
$load .="END:VEVENT" . $eol ;
} // end foreach
$load .="END:VCALENDAR";
return $load;
}

$filename="Event";

// Set the headers
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$in = array();

include "mysql-cred.php";
$g = new Gig();

$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 
$sql = "SELECT gigID, name , gigDate, location, notes, sound, unix_timestamp(updateTime), IFNULL(isGig,0) FROM gig WHERE gigDate > 0  ORDER BY gigDate DESC ";
$result = mysqli_query($link, $sql);
if ($result){
	$i = 1;
    	while($row = mysqli_fetch_row( $result )) {
		$practiceStart='19:30:00';
		$in['sound'] = $row[5];
		if (strlen($in['sound']) > 0 && $in['sound'] <> '00:00:00'){
			$in['startTime']=getDateTime($row[2],$in['sound']);
		} elseif (0==$row[7] ){
			$in['startTime']=getDateTime($row[2],$practiceStart);
		}
		$in['id'] = md5(uniqid(mt_rand() + $i++, true));
		$in['location'] = $row[3];
		$in['notes'] = $row[4];
		$in['notes'] .= " " . $g->getChartsForGigForCal( $row[0] );
		$in['updateTime'] = $row[6];
		$s = "";
		if (strlen($in['sound']) > 0 && $in['sound'] <> '00:00:00'){
			$s = "Soundcheck " . $in['sound'];
			if (strlen($in['notes']) > 0){
				$in['notes'] .= ". " . $s;
			} else {
				$in['notes'] = $s;
			}
		}
		$in['date'] = strtotime($row[2]);
//		$in['end'] = strtotime($row[2]) + 22 * 60 * 60;
		$in['description'] = "TSB " . $row[1];
		$in['url'] = "http://tsbchart.com/?action=changeGig&gigID=" . $row[0];
		$events[] = $in;
    	}

}

// Dump load
echo cal($events);
