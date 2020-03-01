<?php

use \setasign\Fpdi;

class Gig{

private $conn;
private $arrangement;
private $user;

    function __construct() {
        $this->conn = New Connection();
        $this->arrangement = New Arrangement();
        $this->user = New User();
    }

function sHeader( $input ){
     return $this->arrangement->sHeader($input);
}



function addToSet( $gigID, $order, $arrangementID){
    
    if ($arrangementID > 0 && $gigID > 0){
        $sql = "INSERT INTO setList2 (arrangementID, gigID, setListOrder) VALUES('". $arrangementID . "',". $gigID . "," . $order . ");";
        $result = $this->conn->my_execute( $sql );
}
 
}

function arrangementsInGig( $gigID ){
    $arr = array();
    // get features (if any) of virtualGig
    $includesAll = "";
    $hasWhere = "";
    $whereText = "";

    $sqlV = "SELECT includesAll, hasWhere, whereText FROM gig WHERE gig.gigID=" . $gigID; 
    foreach ($this->conn->listMultiple($sqlV) AS $count=>$res){
    	$includesAll = $res[0];
    	$hasWhere = $res[1];
    	$whereText = $res[2];
    }
    
	if (1==$includesAll){
		$whereGig = "1 " ;
                $sqlV = "SELECT arrangementID FROM  arrangement   where " . $whereGig;
	} elseif (1==$hasWhere){
		$whereGig = " " . $whereText . " " ;
                $sqlV = "SELECT arrangementID FROM  arrangement   where " . $whereGig;
//		$whereGig = "gigID IN (SELECT gigID FROM gig WHERE " . $whereText . ") " ;
	} else {
		$whereGig = "gigID = " . $gigID;
                $sqlV = "SELECT arrangementID FROM setList2   where " . $whereGig;
	}

    foreach ($this->conn->listMultiple($sqlV) AS $count=>$res){
    	$arr[] = $res[0];
    }
    return $arr;
}

function arrayToList( $arr, $dud=-1 ){
    $list = "(";
    foreach ($arr AS $count=>$res){
    	$list .=  $res . ", ";
    }
    $list .= $dud . ")";
    return $list;
}

function arrInGigList( $gigID ){
	return $this->arrayToList( $this->arrangementsInGig( $gigID ));
}

function copySetList( $sourceGigID, $targetGigID){

$sqlDeleteSetList = "DELETE FROM setList2 WHERE gigID = " . $targetGigID;
$result = $this->conn->my_execute( $sqlDeleteSetList);
$sqlCopySetList = "INSERT INTO setList2(arrangementID,  gigID, setListOrder) SELECT arrangementID,  " . $targetGigID . ", setListOrder FROM setList2 WHERE gigID = " . $sourceGigID;
$result = $this->conn->my_execute( $sqlCopySetList );

}



function deleteOutput( $directoryBase ){
$files = glob($directoryBase . '/output/*.pdf'); // get all file names but not the index.php
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}
} 



function deleteSet( $input=array()){

if (isset($input['gigID'])){    
$gigID = $input['gigID'];
$sql1 = "delete from setList2 where gigID = " . $gigID . ";";
$sql2 = "delete from gig where gigID = " . $gigID . ";";
$result = $this->conn->my_execute( $sql1 );
$result = $this->conn->my_execute( $sql2 );
}

}


function deleteSetListPart( $setListID){
    
        $sql = "DELETE FROM  setList2 where setListID = ". $setListID . ";";
        $result = $this->conn->my_execute( $sql );
 
}

function getUrlsForArrangementGig( $arrangementID, $gigID ){
        $ret = array();
	$sql = "SELECT urlurl, urlTSB, urlTitle, urlTypeName FROM url INNER JOIN urlType ON url.urlTypeID = urlType.urlTypeID  WHERE urlGigID=" . $gigID . " AND urlArrangementID = " . $arrangementID ;
    	foreach ($this->conn->listMultiple($sql) AS $count=>$res){
		$ret[] = $res;
	}
	return $ret;
}

function getFilesForArrangementGig( $arrangementID, $gigID ){
        $ret = array();
	$sql = "SELECT name, efileID FROM efile WHERE gigID=" . $gigID . " AND arrangementID = " . $arrangementID ;
    	foreach ($this->conn->listMultiple($sql) AS $count=>$res){
		$ret[] = $res;
	}
	return $ret;
}

function getStylesForArrangement( $arrangementID ){
        $ret = array();
	$sql = "SELECT name, gigID from gig WHERE isStyle=1 AND gigID IN (SELECT gigID FROM setList2 WHERE arrangementID = " . $arrangementID . ")";
    	foreach ($this->conn->listMultiple($sql) AS $count=>$res){
		$ret[] = $res;
	}
	return $ret;
}

function testStyle(){
echo $this->getStyleLabelForArrangement( 270 );
}

function getUrlLabelForArrangementGig( $arrangementID, $gigID ){
//	$sql = "SELECT urlurl, urlTSB, urlTitle, urlTypeName FROM url INNER JOIN urlType ON url.urlTypeID = urlType.urlTypeID  WHERE urlGigID=" . $gigID . " AND urlArrangementID = " . $arrangementID ;
	$ret = "";
	$list = $this->getUrlsForArrangementGig( $arrangementID, $gigID );
	if (count($list) > 0){
		$ret = " ";

		for ($i = 0, $ii = count($list) ; $i < $ii; $i++){
			$ret .= "<a href='" . $list[$i][0] . "'>";
			$ret .= $list[$i][3]  ;
			$ret .= " " . $list[$i][2]  ;
			$ret .= "</a>";
			if ($i < $ii - 1){
				$ret .= ", ";	
			}
		}
 	$ret .= " ";
	}
	return $ret;
}

function getFileLabelForArrangementGig( $arrangementID, $gigID ){
	$ret = "";
	$list = $this->getFilesForArrangementGig( $arrangementID, $gigID );
	if (count($list) > 0){
		$ret = " ";

		for ($i = 0, $ii = count($list) ; $i < $ii; $i++){
			$ret .= "<a href='audio?efileID=" . $list[$i][1] . "'>";
			$ret .= "mp3"  ;
			$ret .= "</a>";
			if ($i < $ii - 1){
				$ret .= ", ";	
			}
		}
 	$ret .= " ";
	}
	return $ret;
}

function getStyleLabelForArrangement( $arrangementID ){
	$ret = "";
	$list = $this->getStylesForArrangement( $arrangementID );
//	print_r($list);
//	print_r($list[0][0]);
	if (count($list) > 0){
		$ret = "(";

		for ($i = 0, $ii = count($list) ; $i < $ii; $i++){
			$ret .= "<a href='.?action=getGig&gigID=" . $list[$i][1] . "'>";
			$ret .= $list[$i][0] ;
			$ret .= "</a>";
			if ($i < $ii - 1){
				$ret .= ", ";	
			}
		}
 	$ret .= ")";
	}
	return $ret;
}


function getChartsForGigArray( $gigID = -1, $input=array()){
    $return = "";
    if ($gigID < 1){
        $gigID = $this->getLatestGigID();
    }
    // get features (if any) of virtualGig
    $includesAll = "";
    $hasWhere = "";
    $whereText = "";
    $isStyle = "";

    $sqlV = "SELECT includesAll, hasWhere, whereText, isStyle FROM gig WHERE gig.gigID=" . $gigID; 
    foreach ($this->conn->listMultiple($sqlV) AS $count=>$res){
    	$includesAll = $res[0];
    	$hasWhere = $res[1];
    	$whereText = $res[2];
    	$isStyle = $res[3];
    }
    
    $whereFilter = " 1  ";
    $labelFilter = "";
    if (isset($input['filter'])){
    	foreach ($input['filter'] AS $count=>$res){
		if (1==$res){
			$conj = " IN ";
		} else {
			$conj = " NOT IN ";
		}
    		if (isset($input['filterGig'][$count])){
			$whereFilter .= " AND  V.arrangementID " . $conj . $this->arrInGigList($input['filterGig'][$count]) ;
			$labelFilter .= " AND " . $conj . " " . $this->getGigLabel($input['filterGig'][$count]) . " ";
		}
	}
    }

	if (1==$includesAll){
		$whereGig = "1 " ;
    		$orderHow = "V.name ASC";
    		$sql = "SELECT  'unnecessary T.setListID', 'unnecessary T.setListOrder', V.name, V.arrangementID, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName), AC.arrCount, IF(AC.arrCount>1, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName), V.name), A.isBackedUp, V.name, A.isInPads FROM view_arrangement AS V, (SELECT COUNT(*) as arrCount, songID FROM arrangement AS A GROUP BY songID) AS AC, arrangement AS A WHERE AC.songID = A.songID AND A.arrangementID = V.arrangementID AND " . $whereGig . " AND " . $whereFilter . " order by " . $orderHow;
	} elseif (1==$hasWhere){
		$whereGig = " V.arrangementID IN " . $this->arrInGigList($gigID) ;
//		$whereGig = "T.gigID IN (SELECT gigID FROM gig WHERE " . $whereText . ") " ;
    		$orderHow = "V.name ASC";
    $sql = "SELECT  'unnecessary T.setListID', 'unnecessary T.setListOrder', V.name, V.arrangementID, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName), AC.arrCount, IF(AC.arrCount>1, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName), V.name), A.isBackedUp, V.name, A.isInPads FROM  view_arrangement AS V, (SELECT COUNT(*) as arrCount, songID FROM arrangement AS A GROUP BY songID) AS AC, arrangement AS A WHERE AC.songID = A.songID AND A.arrangementID = V.arrangementID  AND " . $whereGig . " AND " . $whereFilter . " order by " . $orderHow;
	} elseif (1==$isStyle){
		$whereGig = "T.gigID = " . $gigID;
    		$orderHow = "V.name ASC";
	} else {
		$whereGig = "T.gigID = " . $gigID;
    		$orderHow = "T.setListOrder ASC";
	}

	if (1!=$includesAll && 1!=$hasWhere){
    $sql = "SELECT 'unnecessary T.setListID', 'unnecessary T.setListOrder', V.name, V.arrangementID, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName), AC.arrCount, IF(AC.arrCount>1, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName), V.name), A.isBackedUp, V.name, A.isInPads FROM setList2 AS T, view_arrangement AS V, (SELECT COUNT(*) as arrCount, songID FROM arrangement AS A GROUP BY songID) AS AC, arrangement AS A WHERE AC.songID = A.songID AND T.arrangementID=A.arrangementID AND A.arrangementID = V.arrangementID  AND " . $whereGig . " AND " . $whereFilter . " order by " . $orderHow;
    }

 $i = 1;
 	$ret = array();
	$ret['labelFilter'] = $labelFilter;
	$ret['gigID'] = $gigID;
	$ret['list'] = array();
    foreach ($this->conn->listMultiple($sql) AS $count=>$res){
        $retlist = array();
	$retlist['label'] = $res[6] ;
	$retlist['inPads'] = $res[9] ;
	$retlist['backup'] = $res[7] ;
	$retlist['arrangementID'] = $res[3];
	$ret['list'][] = $retlist;
    }
    return $ret;
}

function getChartsForGig( $gigID = -1, $input=array()){
 $ret = $this->getChartsForGigArray( $gigID, $input );
    $return = "<p>" . $ret['labelFilter'] . "</p>";
    $return.= "<ol>";
    foreach ($ret['list'] AS $count=>$retlist){
        $label = $retlist['label'];
        $labelPads = "*";
        if( $retlist['inPads']) $labelPads = "";
        $label2 = "";
//        if( !$retlist['backup']) $label2 .= " (no back-up)";
        $check = $labelPads . "<a href='.?gigID=". $gigID . "&arrangementID=" . $retlist['arrangementID'] . "'>".$label . "</a>". " " . $this->getStyleLabelForArrangement( $retlist['arrangementID'] ) .  $label2 . $this->getFileLabelForArrangementGig( $retlist['arrangementID'], $gigID ) . $this->getUrlLabelForArrangementGig( $retlist['arrangementID'], $gigID ) . "\n" . " ";
        $return .= "<li><p>" . $check . "</p></li>";
    }
    $return .= "</ol>";
    return $return;
}

function getChartsForGigForCal( $gigID = -1, $input=array()){
 $ret = $this->getChartsForGigArray( $gigID, $input );
    $return = "";
    $i = 1;
    foreach ($ret['list'] AS $count=>$retlist){
        $label = $retlist['label'];
        $label2 = "";
	$check = "";
//        $check.= $i++;
	$check .= $label ; 
//        $return .= "\n" . $check ;
        $return .= $check . "\, " ;
    }
    return $return;
}


function getCopySetForm(){

$counter = 0;
$sqlCountTargets = "SELECT COUNT(*) FROM (SELECT gig.gigID, COALESCE(S.countCharts,0) AS counter FROM gig LEFT JOIN (SELECT COUNT(*) as countCharts, gigID from setList2 GROUP BY gigID) AS S ON S.gigID=gig.gigID WHERE ( hasWhere IS NULL OR hasWhere!=1) AND ( includesAll IS NULL OR includesAll!=1) AND COALESCE(S.countCharts,0)=0) AS C";
    	foreach( $this->conn->listMultiple( $sqlCountTargets ) AS $index=>$row ){
        	$counter = $row[0];
    	}

if (0 == $counter) return "";

$form = "<fieldset><legend>Copy set</legend><form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='copySetList' />";
$form .= "<p>Source <select name='sourceGigID'>";

$sqlSource = "SELECT gig.gigID, gig.name, gig.gigDate FROM gig LEFT JOIN (SELECT COUNT(*) as countCharts, gigID from setList2 GROUP BY gigID) AS S ON S.gigID=gig.gigID WHERE COALESCE(S.countCharts,0)>0 ORDER BY gigDate DESC, name ASC";
	foreach( $this->conn->listMultiple( $sqlSource ) AS $index=>$row ){
        	$check = "<option value=" . $row[0] . ">" . $row[1] . " " . $row[2] . "";
        	$form = $form . $check;
    	}
$form .= "</select>";
$form .= "<p>Target <select name='targetGigID'>";
$sqlTarget = "SELECT gig.gigID, gig.name, gig.gigDate FROM gig LEFT JOIN (SELECT COUNT(*) as countCharts, gigID from setList2 GROUP BY gigID) AS S ON S.gigID=gig.gigID WHERE (hasWhere IS NULL OR hasWHERE!=1) AND (includesAll IS NULL or includesAll!=1) AND COALESCE(S.countCharts,0)=0 ORDER BY gigDate DESC, name ASC";
	foreach( $this->conn->listMultiple( $sqlTarget ) AS $index=>$row ){
        		$check = "<option value=" . $row[0] . ">" . $row[1] . " " . $row[2] . "";
        		$form = $form . $check;
    	}
$form .= "</select>";
$form .= "</p><p><input type='submit' value='COPY SET'></p></form></fieldset>";
return $form;

}





function getDeleteSetForm(){

$form = "<fieldset><legend>Delete set</legend><form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='deleteSetList' />";
$form .= "<p><select name='gigID'>";

$sql = "SELECT  gigID, name, gigDate FROM gig ORDER BY gigDate DESC, name ASC";
	$i = 1;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
    	    if(11!=$row[0]){
        		$check = "<p><option value=" . $row[0] . ">" . $row[1] . " " . $row[2] . "</p>";
        		$form = $form . $check;
    	    }
    	}

$form .= "</p><p><input type='submit' value='DELETE SET'></p></form></fieldset>";
$sql = "SELECT name, countPlays, arrangementID from view_popular";
	$form .= "<fieldset><legend>Appearances in set lists</legend>";
        $form .= "<table>";
        $form .= "<tr><th>Song</th><th>Appearances</th></tr>";
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
        	$tr = "<tr><td><a href='../?arrangementID=" . $row[2]   . "'>" . $row[0] . "</a></td><td>" . $row[1] . "</td></tr>";
        	$form = $form . $tr;
    	}
        $form .= "</table>";
	$form .= "</fieldset>";
return $form;
}


function getEditGigForm( $gigID){

$form = "";
$form .= "<input type='hidden' name='action' value='getSetList' />";
$form .= "<p><select name='gigID'>";

$sql = "SELECT name, location, notes, gigDate, sound, isGig, isStyle FROM gig WHERE gigID = " . $gigID;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
		$form = "<fieldset><legend>Edit gig " . $gigID . " " . $row[0] . "</legend><form action = '' method='POST'>";
        	$form .= "<p>Name<textarea name='name'>" . $row[0] . "</textarea></p>";
        	$form .= "<p>Location<textarea name='location'>" . $row[1] . "</textarea></p>";
        	$form .= "<p>Notes<textarea name='notes'>" . $row[2] . "</textarea></p>";
        	$form .= "<p>Date<input type='date' name='gigDate' value=" . $row[3] . "></p>";
        	$form .= "<p>Soundcheck<input type='time' name='sound' value=" . $row[4] . "></p>";
		$form .= "<p>Is Performance<input type='checkbox' name='isGig' value='isPublic'";
		if ($row[5]==1){ $form .= " CHECKED ";}
		$form .= " ></p>";
		$form .= "<p>Is Style<input type='checkbox' name='isStyle' value='isStyle'";
		if ($row[6]==1){ $form .= " CHECKED ";}
		$form .= " ></p>";
		$form .= "<input type='hidden' name='gigID' value='" . $gigID . "' >";
		$form .= "<input type='hidden' name='action' value='updateGig' >";
    	    }

$form .= "<p><input type='submit' value='Update gig details'></p></form></fieldset>";
return $form;
}

function getEditSetForm(){

$form = "<fieldset><legend>Get set to edit</legend><form action = '' method='GET'>";
$form .= "<input type='hidden' name='action' value='getSetList' />";
$form .= "<p><select name='gigID'>";

$sql = "SELECT  gigID, name, gigDate FROM gig WHERE (hasWhere IS NULL OR hasWhere!=1) AND  (includesAll IS NULL OR includesAll!=1)  ORDER BY gigDate DESC, name ASC";
	$i = 1;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
    	    if(11!=$row[0]){
        		$check = "<p><option value=" . $row[0] . ">" . $row[1] . " " . $row[2] . "</p>";
        		$form = $form . $check;
    	    }
    	}

$form .= "</p><p><input type='submit' value='Get setlist'></p></form></fieldset>";
return $form;
}




function getForm( $gigID, $input ){

$out = "";
include "mysql-cred.php";

if (isset($input['byDate'])){
   $orderBy = " V.arrangementID DESC ";
   $orderLabel = "Newest at the top";
   $chartLabel = " IF(AC.arrCount<2,V.songName,CONCAT(V.songName,', ',A.arrangerFirstName, ' ',A.arrangerLastName)) ";
} elseif (isset($input['byArranger'])){
   $orderBy = " A.arrangerLastName ASC, A.arrangerFirstName ASC, V.songName ASC ";
   $chartLabel = " CONCAT(A.arrangerLastName,', ',A.arrangerFirstName,': ',V.songName) ";
   $orderLabel = "Ordered by arranger";
} else {
   $orderBy = " V.songName ASC ";
   $orderLabel = "Alphabetical order";
   $chartLabel = " IF(AC.arrCount<2,V.songName,CONCAT(V.songName,', ',A.arrangerFirstName, ' ',A.arrangerLastName)) ";
}

$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 
$form = "";

//$sql = "SELECT  V.arrangementID, CONCAT('', ' ', IF(AA.isInPads=1,'','*')), " . $chartLabel . " , V.songName FROM arrangement AS AAA, (SELECT COUNT(*) AS arrCount, songID FROM arrangement GROUP BY songID) AS AC, (SELECT COUNT(*), arrangementID, songName FROM view_efilePart GROUP BY arrangementID, songName) AS V, view_arrangement AS A, arrangement AS AA WHERE A.arrangementID=V.arrangementID AND AA.arrangementID=A.arrangementID AND  AAA.arrangementID=A.arrangementID AND AC.songID=AAA.songID ORDER BY " . $orderBy;
$sql = "SELECT  V.arrangementID, CONCAT('', ' ', IF(AA.isInPads=1,'','*')), " . $chartLabel . " , V.songName FROM arrangement AS AAA, (SELECT COUNT(*) AS arrCount, songID FROM arrangement GROUP BY songID) AS AC, (SELECT COUNT(*), arrangementID, name as songName FROM view_arrangement GROUP BY arrangementID, name) AS V, view_arrangement AS A, arrangement AS AA WHERE A.arrangementID=V.arrangementID AND AA.arrangementID=A.arrangementID AND  AAA.arrangementID=A.arrangementID AND AC.songID=AAA.songID ORDER BY " . $orderBy;
//echo $sql;
$result = mysqli_query($link, $sql);
if ($result){
    $form .= "<div>";
    $form .= "<ol>";

	$i = 1;
    	while($row = mysqli_fetch_row( $result )) {
		$check = "";
		$check .= "<li>";
		$check .= "<p>";
		$check .= $row[1] . "<a href='.?arrangementID=" . $row[0] . "&gigID=". $gigID . "'>".$row[2] . "</a>" . " ";
		$check .= "</p>";
		$check .= "</li>";
		$form = $form . $check;
    	}
    $form .= "</ol>";
    $form .= "<p>Order by ";
    $form .= "<a href=./>title</a> ";
    $form .= "<a href=./?byDate>date uploaded</a> ";
    $form .= "<a href=./?byArranger>arranger</a> ";
    $form .= "</p>";
    $form .= "</div>";

}

mysqli_close( $link );

	$out .= "<fieldset><legend>" . $orderLabel . " (* = not in pads)</legend>";
	$out .= $form;
	$out .= "</fieldset>";

return $out;


}


function getGigForm( $gigID = -1, $input=array()){

	if (isset($input['gigID'])){
		$gigID = trim($input['gigID']);
	}
    if ($gigID < 1){
        $gigID = $this->getLatestGigID();
    }

include "mysql-cred.php";

$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 
$form = "<form action = '' method='GET'>";
$form .= "<input type='hidden' name='action' value='changeGig' />";

$sql = "SELECT gigID, name, gigDate FROM gig WHERE (includesAll IS NULL OR includesAll!=1) ORDER BY gigDate DESC, name ASC";
$result = mysqli_query($link, $sql);
$sform = "";
if ($result){
        $i = 1;
	$form .= "<p><select name='gigID'>";
    	while($row = mysqli_fetch_row( $result )) {
    	    if ($gigID==$row[0]){
    	        $selected = " selected ";
    	    } else {
    	        $selected = "";
    	    }
		//$check = "<option value='" . $row[0] . "'" . $selected . ">" . $row[1] . " "  . ". " . $row[2] . "</option>";
		$check = "<option value='" . $row[0] . "'" . $selected . ">" . $row[2] . " "  . ". " . $row[1] . "</option>";
		$form = $form . $check;
		$sform .= $check;
                $i++;
    	}
	$form .= "</select>";
}
$form .= "<input type='submit' value='Change gig'></form>";

$form .= $this->getNotesForGig( $gigID);
$form .= $this->getChartsForGig( $gigID, $input);

$fform = "<form action = '' method='GET'>";
$fform .= "<input type='hidden' name='gigID' value='" . $gigID . "' />";
if (isset($input['action'])){
	$fform .= "<input type='hidden' name='action' value='" . $input['action'] . "' />";
}
$fform .= $this->getHidden($input, 'filter','filterGig');
$fform .= "<input type='radio' name='filter[]' value='1' checked> In<br>";
$fform .= "<input type='radio' name='filter[]' value='0' checked> Not in<br>";
$fform .= "<select name='filterGig[]'>";
$fform .= $sform;
$fform .= "</select>";
$fform .= "<input type='submit' value='Add filter'></form>";
$form .= $fform;


$form .= "<form action = '' method='GET'>";
$form .= "<input type='hidden' name='action' value='getGig' />";
$form .= "<input type='hidden' name='gigID' value='" . $gigID . "' />";


$form .= "<p><select name='part'>";
$sql = "SELECT V.partName, V.partID FROM (SELECT DISTINCT partName, partID FROM view_efilePart) as V INNER JOIN (SELECT P.partID, S.printOrder from part as P INNER JOIN section AS S on P.minSectionID = S.sectionID) AS PP ON PP.partID= V.partID order by PP.printOrder ASC,  PP.partID ASC";
$result = mysqli_query($link, $sql);
if ($result){
    	while($row = mysqli_fetch_row( $result )) {
		$check = "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
		$form = $form . $check;
    	}
}

$sql = "SELECT S.name FROM section AS S order by S.printOrder ASC";
$result = mysqli_query($link, $sql);
if ($result){
    	while($row = mysqli_fetch_row( $result )) {
		$check = "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
		$form = $form . $check;
    	}
}

$form .= "</select>";



mysqli_close( $link );
$form .= "<input type = 'checkbox' name='includeMusic' value='include' checked>Include Music";
$form .= $this->getHidden($input, 'filter','filterGig');
$form .= "<input type = 'checkbox' name='includeFiller' value='include' checked>Pad music with blank pages to print on A3";
$form .= "<input type='submit' value='Get pdf of whole set (" . $this->getGigLabel($gigID) . $this->getFilterLabel($input) . ")'></form>";
if ($gigID > 0){
	$form .= "<p><a href=./maintenance/?action=getSetList&gigID=" . $gigID . ">Edit set  list</a></p>";
}

	$out = "<fieldset><legend>" . $this->getGigLabel($gigID) . "</legend>";
	$out .= $form . "</fieldset>";

return $out;
    
}


function getFilterLabel( $input ){
  $sRet = "";
  if (isset($input['filter']) && isset($input['filterGig'])){
     for ($i = 0, $ii = count($input['filter']); $i < $ii; $i++){
     //  assume length of filter = length of filterGig
        if ($input['filter'][$i]==1){
	    $sRet .= " AND ALSO IN ";
	} else {
	    $sRet .= " BUT NOT IN ";
	}
	$sRet .= $this->getGigLabel( $input['filterGig'][$i]);
     }
  }
  return $sRet;

}



function getGigLabel( $gigID){
    foreach ($this->conn->listMultiple("SELECT name, gigDate  from gig WHERE gigID = " . $gigID) AS $count=>$res){
        return $res[0] . " " . $res[1];
    }

}


function getGigSetForm($gigID){
 
    $lastOrder = -999;
    $gigLabel = "";
    foreach ($this->conn->listMultiple("SELECT G.name, G.gigDate FROM gig as G WHERE  G.gigID = " . $gigID . "")  as $key=>$song){
        $gigLabel = $song[0] . " " . $song[1];
    }   
    $return = "";
    $return .= "<fieldset><legend>Edit set list for " . $gigLabel . "</legend>";
    $return .= "<div><table>";
    $return .= "<tr><th>Song<th> </tr>";
    $order = 999;
    foreach ($this->conn->listMultiple("SELECT T.setListID, T.setListOrder, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName)  FROM setList2 AS T, view_arrangement AS V WHERE T.arrangementID = V.arrangementID AND T.gigID = " . $gigID . " order by T.setListOrder ASC")  as $key=>$song){
        $order = $song[1];
        $midOrder = 0.5 * ($lastOrder + $order);
        $lastOrder = $order;
        $return .= "<tr><td>";
        $return .= "<form action='' method='POST'>";
        $return .= "<input type='hidden' name='action' value='addSetListPart'>";
        $return .= "<input type='hidden' name='gigID' value='" . $gigID . "'>";
        $return .= "<input type='hidden' name='setListOrder' value='" . $midOrder . "'>";
        $return .= "<select name='arrangementID'>";
        $return .= "<option value='" . -1 . "'>" . "" . "</option>";
        foreach ($this->conn->listMultiple("SELECT V.arrangementID, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName)  FROM view_arrangement AS V  order by V.name  ASC")  as $keyy=>$songg){
            $return .= "<option value='" . $songg[0] . "'>" . $songg[1] . "</option>";
        }
        $return .= "</select>";
        $return .= "<input type='submit' value='INSERT'>";
        $return .= "</form>";
        $return .= "</td></tr>";
        $return .= "<tr>";
        $return .= "<td>";
        $return .= "<form action='' method='POST'>";
        $return .= "<input type='hidden' name='action' value='deleteSetListPart'>";
        $return .= "<input type='hidden' name='setListID' value='" . $song[0] . "'>";
        $return .= "<input type='submit' value='(DELETE) " . $song[2] . "'>";
        $return .= "</form>";
        $return .= "</td>";
        $return .= "</tr>";

    }
        $lastOrder = $order + 10;
        $return .= "<tr><td>";
        $return .= "<form action='' method='POST'>";
        $return .= "<input type='hidden' name='action' value='addSetListPart'>";
        $return .= "<input type='hidden' name='gigID' value='" . $gigID . "'>";
        $return .= "<input type='hidden' name='setListOrder' value='" . $lastOrder . "'>";
        $return .= "<select name='arrangementID'>";
        $return .= "<option value='" . -1 . "'>" . "" . "</option>";
        foreach ($this->conn->listMultiple("SELECT V.arrangementID, CONCAT(V.name, ', ', V.arrangerFirstName, ' ', V.arrangerLastName)  FROM view_arrangement AS V  order by V.name  ASC")  as $key=>$song){
            $return .= "<option value='" . $song[0] . "'>" . $song[1] . "</option>";
        }
        $return .= "</select>";
        $return .= "<input type='submit' value='INSERT'>";
        $return .= "</form>";
        $return .= "</td></tr>";
    $return .= "</table></div>";
    $return .= "</fieldset>";
    $return .= $this->getEditGigForm( $gigID);
    return $return;
}


function getHidden( $input=array(), $firstKey, $secondKey){
    $return = "";
    if (isset($input[$firstKey])){
    	foreach ($input[$firstKey] AS $count=>$res){
		if (isset($input[$secondKey][$count])){
			$return .= "<input type='hidden' name='" . $firstKey . "[]', value='" . $input[$firstKey][$count] . "'>";
			$return .= "<input type='hidden' name='" . $secondKey . "[]', value='" . $input[$secondKey][$count] . "'>";
		}
	}
    }
     return $return;
}


function getLatestGigID(){
$sql = "SELECT gigID from gig WHERE (gigDate * 1000000) >= (NOW()-1000000) ORDER BY gigDate ASC LIMIT 1";
$ret = -1;
    foreach ($this->conn->listMultiple($sql) AS $count=>$res){
        $ret = $res[0];
    }
if ($ret > -1){
   return $ret;
   }

$sql = "SELECT gigID from gig ORDER BY gigDate DESC LIMIT 1";
    foreach ($this->conn->listMultiple($sql) AS $count=>$res){
        return $res[0];
	}

}


function getNewSetListForm(){

$form = "<fieldset><legend>New set</legend>";
$form .= "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='addSetList' />";
$form .= "<p>Gig name<textarea name='gigName'></textarea></p> ";
$form .= "<p>Gig date<input type='date' name='gigDate' ></p> ";
$form .= "<p>Performance (leave unticked if it's a practice)<input type='checkbox' name='isGig' value='isPublic' ></p> ";
$form .= "<p>Is dance style (leave unticked if you don't want it next to each chart on a setlist)<input type='checkbox' name='isStyle' value='isStyle' ></p> ";
$form .= "<input type='submit' value='ADD SET'></form>";
$form .= "</fieldset>";
return $form;
}


function getSetPartsForm(){

$form = "<fieldset><legend>Output parts for set</legend><form action = '' method='GET'>";
$form .= "<input type='hidden' name='action' value='getPartsForSet' />";
$setList = "<p><select name='gigID'>";

$sql = "SELECT DISTINCT gigID, name, gigDate FROM gig ORDER BY gigDate DESC, name ASC";
	$i = 1;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
    	    if(11!=$row[0]){
        		$check = "<p><option value=" . $row[0] . ">" . $row[1] . " " . $row[2] . "</p>";
        		$setList = $setList . $check;
    	    }
    	}
$setList = $setList . "</select></p>";	
$form .= $setList;
$form .= "<p><input type='submit' value='Get parts (output to output folder)'></p></form></fieldset>";
$form .= "<fieldset><legend>Email parts for set</legend><form action = '' method='GET'>";
$form .= "<input type='hidden' name='action' value='emailPartsForSet' />";
$emails = array(); $parts=array();
$sql = " SELECT plainEmail, name, userP.userID, userP.partID, IFNULL(C.counter,0), nickName from (select plainEmail, userID, part.name, partID, nickName FROM user, part WHERE user.okToMail=true) AS userP LEFT JOIN (SELECT COUNT(*) as counter, userID, partID FROM userPart group BY userID, partID) as C on userP.userID = C.userID and userP.partID = C.partID ORDER BY plainEmail ASC, userP.name ASC";
$checks = "";
foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
   if ($row[4] > 0){
     $checks .= "<p>" . $row[5] . " " .$row[0] . " " . $row[1] . "<input type='checkbox' name='email[" . $row[0] . "][" . $row[1] . "]' value=true ><p>";
     }
}
$form .= $checks;
$sqlU = " SELECT plainEmail, plainEmail from user WHERE okToMail=true ORDER BY plainEmail ASC";
$sqlP = " SELECT name, name from part  ORDER BY name ASC";
$form .= "<p>" . $this->selectList($sqlU, 'user[1][name]') . $this->selectList($sqlP,'user[1][part]') . "</p>";
$form .= "<p>" . $this->selectList($sqlU, 'user[2][name]') . $this->selectList($sqlP,'user[2][part]') . "</p>";
$form .= "<p>" . $this->selectList($sqlU, 'user[3][name]') . $this->selectList($sqlP,'user[3][part]') . "</p>";
$form .= "<p>" . $this->selectList($sqlU, 'user[4][name]') . $this->selectList($sqlP,'user[4][part]') . "</p>";
$form .= "<p>" . $this->selectList($sqlU, 'user[5][name]') . $this->selectList($sqlP,'user[5][part]') . "</p>";
$form .= "<p>Optional message<textarea name='message'></textarea></p>" ; 
$form .= $setList;
$form .= "<p><input type='submit' value='Email parts'></p></form></fieldset>";
return $form;
}


function selectList( $sql, $name){
$setList = "<select name='" . $name . "'>";
   $check = "<p><option ></p>";
   $setList = $setList . $check;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
        		$check = "<p><option value='" . $row[0] . "'>" . $row[1] . "</p>";
        		$setList = $setList . $check;
    	}
$setList .= "</select>";
return $setList;
}

function getSetPartsOutput( $gigID, $directoryBase, $includeFiller=false ){

$this->deleteOutput($directoryBase);

$sql = "SELECT name from part ORDER BY name ASC ";
$sql = "SELECT name from part WHERE name NOT IN('Conductor','Piano') ORDER BY name ASC ";
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
         $inp = array();
	 $inp['gigID'] = $gigID;
	 $inp['part'] = $row[0];
	 $inp['includeFiller'] = $includeFiller;
	 $inp['includeMusic'] = 'include';

//	 $file = $this->pdfFromGigExplicit($inp, $directoryBase, "Gig" . $gigID . str_replace(" ", "", trim($row[0])) );
//	 $file = $this->pdfFromGigExplicit($inp, $directoryBase );
//	 sleep(9);
	 $message = "";
//	 $message = $this->user->sendFileToAllUsers( $file, $inp['part'], "Gig ". $gigID .  " " . $this->getGigLabel( $gigID ). " for  " . $inp['part'] );
       // 		echo $row[0] . " " . $file . " " . $message . "<br/>";
    	}

$sql = "SELECT part.name, arrangementID, V.name, part.partID  from part, view_arrangement  as V WHERE arrangementID in "  . $this->arrInGigList( $gigID ) ."    ORDER BY part.name desC, V.name desC";
//echo $sql;
//echo $gigID;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	$label = preg_replace('/\s+/','',$row[0] .  $row[2]);
	 $inp['part'] = array(0=>$row[3]);
	 $inp['arrangement'] = array(0=>$row[1]);
	 $inp['stream']="../";
	 	$inp['filesuf']=$label;
		$inp['outputBlank']=false;
 $this->arrangement->pdfFromGet($inp);
	echo $label . "<br/>";
	}
}

function getSetPartsEmailed( $input, $directoryBase, $includeFiller=false ){
$this->deleteOutput($directoryBase);
$gigID = $input['gigID'];
    	foreach( $input['email'] AS $emailTo=>$arr ){
    	foreach( $arr AS $part=>$unused ){
         $inp = array();
	 $inp['gigID'] = $input['gigID'];
	 $inp['part'] = $part;
	 $inp['includeFiller'] = $includeFiller;
	 $inp['includeMusic'] = 'exclude';
//	 $fileNotes = $this->pdfFromGigExplicit($inp, $directoryBase );
	 $fileNotes = $this->pdfFromGigExplicit($inp, $directoryBase, "Gig" . $gigID . str_replace(" ", "", trim($part)) );
	 $pagesNotesOnly =  $this->arrangement->numPages('../' . $fileNotes);
	 $inp['includeMusic'] = 'include';
//	 $file = $this->pdfFromGigExplicit($inp, $directoryBase );
	 $file = $this->pdfFromGigExplicit($inp, $directoryBase, "Gig" . $gigID . str_replace(" ", "", trim($part)) );
	 if ($this->arrangement->numPages('../' . $file) > $pagesNotesOnly){
	    $sBody = "Music for " . $inp['part'] . " part for " . $this->getGigLabel( $gigID ). " " . $input['message'] ;
	    $sSubject = "TSB: " . $inp['part'] . " " . $this->getGigLabel( $gigID ) ;
	    $message = $this->user->sendFileToUser( $emailTo, $file,  $sBody, $sSubject);
            echo $emailTo . " " . $inp['part'] . " " . $file . " " . $message . "<br/>";
	 } else {
	    echo $inp['part'] . " has " . $this->arrangement->numPages('../' . $file) . " pages but without music has " . $pagesNotesOnly . " pages so no mail.<br/>";
	 }
    	}
	}
    	foreach( $input['user'] AS $unused=>$arr ){
	 if (isset($arr['name']) && isset($arr['part'])){
	 if (strlen($arr['name']) > 1 && strlen($arr['part']) > 1){
         $inp = array();
	 $inp['gigID'] = $input['gigID'];
	 $inp['part'] = $arr['part'];
	 $emailTo = $arr['name'];
	 $inp['includeFiller'] = $includeFiller;
	 $fileNotes = $this->pdfFromGigExplicit($inp, $directoryBase );
	 $pagesNotesOnly =  $this->arrangement->numPages('../' . $fileNotes);
	 $inp['includeMusic'] = 'include';
	 $file = $this->pdfFromGigExplicit($inp, $directoryBase );
	 if ($this->arrangement->numPages('../' . $file) > $pagesNotesOnly){
	    $sBody = "Music for " . $inp['part'] . " part for " . $this->getGigLabel( $gigID ). " " . $input['message'] ;
	    $sSubject = "TSB: " . $inp['part'] . " " . $this->getGigLabel( $gigID ) ;
	    $message = $this->user->sendFileToUser( $emailTo, $file,  $sBody, $sSubject);
            echo $emailTo . " " . $inp['part'] . " " . $file . " " . $message . "<br/>";
	 } else {
	    echo $inp['part'] . " has " . $this->arrangement->numPages('../' . $file) . " pages but without music has " . $pagesNotesOnly . " pages so no mail.<br/>";
	 }
	} // if
	} // if
	}

}


function getDateTimeNow(){
  $tzel = new  DateTimeZone('Europe/London') ;
  $dt = new DateTime('now',$tzel);
  return $dt;
  }




  function getDateTime($date, $time){
  // ttps://stackoverflow.com/questions/20174294/add-current-time-to-datetime
//  echo $date.$time."\r\n";
  $format = 'Y-m-dH:i:s';
  $tzel = new  DateTimeZone('Europe/London') ;
  return DateTime::createFromFormat($format, $date.$time, $tzel );
  }



function getNotesForGig($gigID){

$sql = "SELECT gigID, name , gigDate, location, notes, sound, unix_timestamp(updateTime), IFNULL(isGig,0) FROM gig WHERE gigID = " . $gigID;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
		$practiceStart='19:30:00';
		$in['sound'] = $row[5];
		if (strlen($in['sound']) > 0 && $in['sound'] <> '00:00:00'){
			$in['startTime']=$this->getDateTime($row[2],$in['sound']);
		} elseif (0==$row[7] ){
			$in['startTime']=$this->getDateTime($row[2],$practiceStart);
		}
		$in['location'] = $row[3];
		$in['notes'] = $row[4];
		$in['updateTime'] = new DateTime('@'.$row[6]);
                  $tzel = new  DateTimeZone('Europe/London') ;
//		  $in['updateTime']::setTimezone($tzel); 
		  date_timezone_set( $in['updateTime'],$tzel);
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
    	}
//    print_r($in);
   $ret = "<ul>";
   	if (isset($in['startTime'])){
	$ret .= "<li>Start: " . $in['startTime']->format('Y-m-d H:i') . "</li>";
	}

   $ret .= "<li>Location: " . $in['location'] . "</li>";
   $ret .= "<li>Notes: " . $in['notes'] . "</li>";
   $ret .= "<li>Updated: " . $in['updateTime']->format('Y-m-d H:i') . "</li>";
   $ret .= "</ul>";
   return $ret;
}




function pdfFromGig( $input, $dummyGigID=-1, $dummyPart=-1){
/*    $includeMusic = false;
if (isset($input['includeFiller'])){
    if ( 'include' == $input['includeFiller']){
        $includeFiller = true;
    } 
}    
if (isset($input['includeMusic'])){
    if ( 'include' == $input['includeMusic']){
        $includeMusic = true;
    } 
}    
*/
if (isset($input['gigID']) && isset($input['part'])){
    $this->deleteOutput( getcwd() );
//    $this->conn->saveRequest($input);    
    return $this->pdfFromGigExplicit($input, getcwd() );
}
}

private function pdfFromGigExplicit($input, $directoryBase, $outputStem=''){
try{
    $gigID = $input['gigID'];
    $partName = $input['part'];
if (isset($input['includeFiller'])){
    if ( 'include' == $input['includeFiller']){
        $includeFiller = true;
    } 
}    
if (isset($input['includeMusic'])){
    if ( 'include' == $input['includeMusic']){
        $includeMusic = true;
    } 
}    
      $orderAlpha = false;
if (isset($input['orderAlpha'])){
       $orderAlpha = true;
}    
$where="";
$partWhere="";
$distinctOrder = " ,v.setListOrder ";
$orderByFile = " ORDER BY setListOrder ASC, partName ASC ";
$orderByList = " ORDER BY v.setListOrder ASC, partName ASC ";
if (isset($gigID)){
    $where .= " OR g.gigID = '" . $gigID . "' ";
}
if (isset($partName)){
    $partWhere .= " OR partName='" . $partName . "' OR partName IN (SELECT Sub from view_part WHERE Super='" . $partName . "') ";
}

    $sqlV = "SELECT includesAll, isStyle, hasWhere FROM gig WHERE gig.gigID=" . $gigID; 
    foreach ($this->conn->listMultiple($sqlV) AS $count=>$res){
    	$includesAll = $res[0];
    	$isStyle = $res[1];
    	$hasWhere = $res[2];
    }
    
    $whereFilterIndex = " 1  ";
    $whereFilter = " 1  ";
    $labelFilter = "";
    if (isset($input['filter'])){
    	foreach ($input['filter'] AS $count=>$res){
		if (1==$res){
			$conj = " IN ";
		} else {
			$conj = " NOT IN ";
		}
    		if (isset($input['filterGig'][$count])){
			$whereFilterIndex .= " AND  V2.arrangementID " . $conj . $this->arrInGigList($input['filterGig'][$count]) ;
			$whereFilter .= " AND  g.arrangementID " . $conj . $this->arrInGigList($input['filterGig'][$count]) ;
			$labelFilter .= " AND " . $conj . " " . $this->getGigLabel($input['filterGig'][$count]) . " ";
		}
	}
    }

	if (1==$includesAll){
		$whereGig = "1 " ;
	} elseif (1==$isStyle){
		$whereGig = " g.arrangementID IN " . $this->arrInGigList( $gigID) ;
	} else {
		$whereGig = " g.gigID =".  $gigID . " " ;
	}
	if (1==$includesAll || 1==$hasWhere || 1==$isStyle || $orderAlpha){
		$orderByFile = " ORDER BY IFNULL(formatID, -1) ASC, V.name ASC, partName ASC ";
	}
	if (1==$hasWhere || 1==$isStyle || 1==$includesAll ){
		$whereGig2 = " V.arrangementID IN " . $this->arrInGigList( $gigID) ;
		$whereGig = " g.arrangementID IN " . $this->arrInGigList( $gigID) ;
$sql = "SELECT  'unnecessary fileName', 'unuseded startPage', 'unused endPage', IFNULL(formatID,-1), 'unused setListOrder', IFNULL(partName,-1), V.name, V.arrangementID  FROM ( view_arrangement AS V )  LEFT JOIN (SELECT  formatID, partName, arrangementID, count(*) from view_efilePart as g WHERE ( 0 " . $partWhere . " ) AND ( 0 OR " . $whereGig . " ) GROUP BY formatID, partName, arrangementID) AS V2 ON V2.arrangementID = V.arrangementID WHERE ( 0 OR " . $whereGig2 . " ) AND " . $whereFilterIndex . $orderByFile  . ";";
    $sqlIncludeMusic= "SELECT  fileName, startPage, endPage, formatID, 'unused setListOrder', V.arrangementID FROM view_efilePart as g INNER JOIN view_arrangement AS V on V.arrangementID = g.arrangementID WHERE  ( 0 " . $partWhere . ") AND ( 0 OR " . $whereGig . " )   AND " . $whereFilter .  $orderByFile . ";";
        } else {
$sql = "SELECT  'unnecessary fileName', 'unuseded startPage', 'unused endPage', IFNULL(formatID,-1), 'unused setListOrder', IFNULL(partName,-1), V.name, V.arrangementID  FROM (setList2 as g INNER JOIN view_arrangement AS V on V.arrangementID = g.arrangementID)  LEFT JOIN (SELECT gigID, formatID, partName, arrangementID from view_efilePartSetList2 as g WHERE ( 0 " . $partWhere . " ) AND ( 0 OR " . $whereGig . " )) AS V2 ON V2.arrangementID = g.arrangementID WHERE ( 0 OR " . $whereGig . " ) AND " . $whereFilterIndex . $orderByFile  . ";";
    $sqlIncludeMusic= "SELECT fileName, startPage, endPage, formatID, 'unused setListOrder', V.arrangementID FROM view_efilePartSetList2 as g INNER JOIN view_arrangement AS V on V.arrangementID = g.arrangementID WHERE  ( 0 " . $partWhere . ") AND ( 0 OR " . $whereGig . " )   AND " . $whereFilter .  $orderByFile . ";";
        }

//echo $sql;
 $i = 1;
    $return = "<p>" . $labelFilter . "</p>";

$pdf = new Fpdi\Fpdi();

$arrange = array();

$pdf = new Fpdi\Fpdi();

	$pdf->AddPage();
	$pdf->SetFont('Arial','',14);
    $sqlGig = "SELECT 'BLANL', g.name, g.gigDate FROM gig as g WHERE gigID=" . $gigID . ";";
    	foreach( $this->conn->listMultiple( $sqlGig ) AS $index=>$row ){
	$pdf->SetFont('Arial','',14);
		if ('0000-00-00' == $row[2] ){ 
			$timePrinted = $this->getDateTimeNow()->format('Y-M-d H:i ') ;
			$row[2]="Printed on " .$timePrinted;
		}
            $pdf->Write(5,$row[1] . " " . $row[2] . "\n\n\n");
        }

 	if (strlen($labelFilter) > 0 ){
		$pdf->Write(5,$labelFilter);
 	    	$pdf->Write(5,"\n\n");
		}
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
// 	$pdf->Write(5,$pageCount . "  (" . $row[4] . ") ");
		if (0 == $row[3]){
 	        $pdf->Write(5,"P");
 	      } elseif (1== $row[3]) {
 	          $pdf->Write(5,"L");
 	      }
 	    $pdf->Write(5," ");
		if (-1 != $row[5]){
 	    $pdf->Write(5,"(" . $row[5] . ") ");
	    	}
        $pdf->Write(5,$row[6] . "\n");
	$arrange[] = $row[7];
//	$pageCount = $pageCount + 1 + $row[2] - $row[1];
	}


$this->arrangement->getAllNotes($pdf, $arrange);

if ($includeMusic){
    foreach( $this->conn->listMultiple( $sqlIncludeMusic ) AS $index=>$row ){


	  if((memory_get_usage() / 1024 /1024) > 9){
	       // throw a "too much memory" error
               echo "<pre>";
                debug_print_backtrace();
               echo "</pre>";
	       throw new Exception('Memory will probably overload. Memory allocated to script is ' . memory_get_usage() / 1024 / 1024 . "Mb");
	  }


	$pdf->setSourceFile( $directoryBase .  "/" .  "pdf/" . $row[0]);
	  $jj = 0;
	for ($i = $row[1], $ii = $row[2]; $i <= $ii; $i++){
		$tplIdx = $pdf->importPage($i);
		if ( 0 == $row[3] ){
			$pdf->AddPage();
			$jj++;
			$pdf->useImportedPage($tplIdx, 10, 10, 200);
       			$pdf->setXY(5,1);
       			$pdf->setFontSize(10);
       			$pdf->Write(5,$this->sHeader($input) . " \n");
		} else {
			$pdf->AddPage('L');
			$jj++;
			$pdf->useImportedPage($tplIdx, 10, -2, 280);
       			$pdf->setXY(5,1);
       			$pdf->setFontSize(10);
       			$pdf->Write(5,$this->sHeader($input) . " \n");
		}
		// use the imported page and place it at point 10,10 with a width of 200 mm
	}
	// add motes
	$pagesSoFar = $pdf->PageNo();
	$singleArr = array();
	$singleArr[]=$row[5]; // arrangementID
        $this->arrangement->getAllNotes($pdf, $singleArr);
	$newNotePages = $pdf->PageNo() - $pagesSoFar;
	$jj = $jj + $newNotePages;
          // pad out with empty pages
	  if (0 == $row[3]){
		$jtarget = ceil($jj/4) * 4;
		} else {
		$jtarget = ceil($jj/2) * 2;
          }
          if ($includeFiller){
	  for ($i = $jj, $ii = $jtarget; $i < $ii; $i++){
		if (0 == $row[3]){
			$pdf->AddPage();
       			$pdf->Write(5,"Blank on purpose \n");
		} else {
			$pdf->AddPage('L');
       			$pdf->Write(5,"Blank on purpose \n");
		}
	  }
          } // end if ($includeFiller){

    }
} else { // end if ($includeMusic)
$pdf->Write(5,"\n(music excluded)\n");
}
$yourFile =  'output/'. $outputStem . md5(time()) . 'myfile.pdf';
$pdf->Output($directoryBase . "/" . $yourFile,'F');            
return $yourFile;
} catch(Exception $e) {
echo "Error! Pdf will probably be junk or not exist.  Error message is: " . $e->getMessage();
}
}


function postNewSetList( $input=array()){

$isGig = 0;    
if (isset($input['isGig'])){
	if ('isPublic'==$input['isGig']){
		$isGig = 1;
	}
}

$isStyle = 0;
if (isset($input['isStyle'])){
	if ('isStyle'==$input['isStyle']){
		$isStyle = 1;
	}
}

$sqlNewGig = "insert into gig (name, gigDate, isGIG, isStyle) VALUES( '".$input['gigName'] ."', '".$input['gigDate']."', " . $isGig . ", " . $isStyle . ");";
$result = $this->conn->my_execute( $sqlNewGig);


}

function updateGig( $input=array()){

$isGig = 0;    
if (isset($input['isGig'])){
	if ('isPublic'==$input['isGig']){
		$isGig = 1;
	}
}

$isStyle = 0;
if (isset($input['isStyle'])){
	if ('isStyle'==$input['isStyle']){
		$isStyle = 1;
	}
}

$sqlNewGig = "update gig set name='" . $input['name'] . "'";
$sqlNewGig.= ", location='" . $input['location'] . "'" ;
$sqlNewGig.= ", notes='" . $input['notes'] . "'" ;
$sqlNewGig.= ", gigDate='" . $input['gigDate'] ."'" ;
$sqlNewGig.= ", sound='" . $input['sound'] ."'" ;
$sqlNewGig.= ", isGig=" . $isGig;
$sqlNewGig.= ", isStyle=" . $isStyle;
$sqlNewGig.= " WHERE gigID=" . $input['gigID'];

$result = $this->conn->my_execute( $sqlNewGig);


}
} // end class Gig
