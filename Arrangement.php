<?php

use \setasign\Fpdi;

class Arrangement{

private $conn;
private $userX;

    function __construct() {
        $this->conn = New Connection();
        $this->userX = New User();
    }


function sHeader( $input ){
     return PDF_HEADER_MESSAGE . " " . $this->userX->getUserEmail();
}


function addNote($publicationID, $noteText){
    if ($publicationID > 0 && strlen($noteText) > 3){
        $sql = "INSERT INTO note(publicationID, noteText, noteDate) VALUES('" . $publicationID . "','". $noteText . "', NOW())";
        $result = $this->conn->my_execute( $sql);
    }
}


function addToBackup( $arrangementID, $iAdd){

$sqlUpdate = "update arrangement set isBackedUp = " . $iAdd . " WHERE arrangementID = ". $arrangementID . ";";
$result = $this->conn->my_execute( $sqlUpdate);

}


function addToPads( $arrangementID, $iAdd){

$sqlUpdate = "update arrangement set isInPads = " . $iAdd . " WHERE arrangementID = ". $arrangementID . ";";
$result = $this->conn->my_execute( $sqlUpdate);

}

function folderUsage($unit = 'm'){

    $f = dirname(__DIR__);
        $io = popen ( '/usr/bin/du -s' . $unit . ' ' . $f, 'r' );
	    $size = fgets ( $io, 4096);
	        $size = substr ( $size, 0, strpos ( $size, "\t" ) );
		    pclose ( $io );
		    $ret = array();
		    $ret['Directory'] = $f;
		    $ret['Size'] =  $size;
		    $ret['SizeUnit'] =  $unit;
		    return $ret;
		    }

function getFolderUsage($unit = 'm'){
	$limit = '3000m';  // true for live installation;
	$r =$this->folderUsage( $unit );
	return $r['Directory'] . " uses " . $r['Size'] . $r['SizeUnit'] . " out of " . $limit;
	}


function postNewURL($input){
	$urlTSB = 0;
	$urlGigID = 'NULL';
	if (isset($input['urlTypeID']) && isset($input['urlurl']) && isset($input['arrangementID'])){
            if (filter_var($input['urlurl'], FILTER_VALIDATE_URL) === FALSE) {
               die('Not a valid URL');
            }
		if (isset($input['urlGigID']) ){
			$urlGigID=$input['urlGigID'];
		}
		if (isset($input['urlTSB']) ){
			if ('isTSB'==$input['urlTSB']) {
				$urlTSB=1;
			}
		}
		$sql = "INSERT INTO url (urlTypeID, urlurl, urlArrangementID, urlTSB, urlGigID) VALUES(" . $input['urlTypeID'] . ", '" . $input['urlurl'] . "', " . $input['arrangementID'] . ", " . $urlTSB . ", " . $urlGigID . ");";
//		echo $sql;
        	$result = $this->conn->my_execute( $sql);
//		echo $result;
//		die();
	}
	$this->refreshVideoList( true ); // rfresh just Blanks
}

function deleteURL($input){
	if (isset($input['urlID'])){
		$sql = "DELETE FROM url WHERE urlID=" . $input['urlID'];
        	$result = $this->conn->my_execute( $sql);
	}
}


function deleteEfile($efileID){
		$sql = "DELETE FROM efile WHERE efileID=" . $efileID;
        	$result = $this->conn->my_execute( $sql);
}


function deleteFile($fileNameExclPath){

$sql = "SELECT COUNT(*) from efile where name='" . $fileNameExclPath . "'";
$bFound = true;
foreach( $this->conn->listMultiple($sql) as $index=>$row ){
        		if ( $row[0]==0 ){
        		    $bFound = false;
        		 }
        }
if( !$bFound){
	if( file_exists(SITE_ROOT . '/pdf/' . $fileNameExclPath)){
	    unlink(SITE_ROOT . '/pdf/' . $fileNameExclPath);
	}
	if( file_exists(SITE_ROOT . '/mp3/' . $fileNameExclPath)){
	    unlink(SITE_ROOT . '/mp3/' . $fileNameExclPath);
	}
	$sql = "DELETE FROM efile where name='" . $fileNameExclPath . "'";
        $result = $this->conn->my_execute( $sql);
}    

}


function deleteNote($noteID){
    if ($noteID > 0){
        $sql = "DELETE FROM note where noteID='" . $noteID . "'";
        $result = $this->conn->my_execute( $sql);
    }
}


function deletePartPage( $efilePartID){
    
    if ($efilePartID > 0 ){
        $sql = "DELETE FROM  efilePart where eFilePartID = ". $efilePartID . ";";
        $result = $this->conn->my_execute( $sql );
	}
 
}


function getAddToBackupForm( $arrID, $arrLabel){

$form = "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='addToBackup' />";
$form .= "<input type='hidden' name='arrangementID' value='" . $arrID . "' />";
$form .= "<input type='submit' value='Add " . $arrLabel . " to back-up'></form>";
return $form;
}


function getAddToPadsForm( $arrID, $arrLabel){

$form = "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='addToPads' />";
$form .= "<input type='hidden' name='arrangementID' value='" . $arrID . "' />";
$form .= "<input type='submit' value='Add " . $arrLabel . " to pads'></form>";
return $form;
}


function getAllNotes($pdf, $arrangements){
$filter = "(";
foreach ($arrangements as $key=>$value){
	$filter .= $value . ", ";
}
$filter .= "-999)";

$sqlCharts = "SELECT name, description, noteText, date_format(noteDate, '%Y-%m-%d') FROM view_note  WHERE noteID in (SELECT noteID from note where publicationID in (SELECT publicationID from publication where arrangementID IN " . $filter . ")) ORDER BY name ASC, noteDate DESC"; 
//echo $sqlCharts;
	$pdf->AddPage();
	$pdf->SetFont('Arial','',14);
	$rowcount = 0;
    	foreach( $this->conn->listMultiple( $sqlCharts ) AS $index=>$row ){
            $pdf->Write(5,$row[0] . " (" . $row[1] . ")\n\n");
            $pdf->Write(5,$row[2] . "\n\n\n\n"); // no date
	}
            
return $pdf;
}

function getArrangementStyleForm( $arrangementID ){

$form = "<fieldset><form action='' method = 'POST'>";
$sql = "SELECT G.name, C.countStyle, G.gigID FROM gig as G LEFT  JOIN (SELECT Count(*) as countStyle, gigID FROM setList2 WHERE arrangementID = " . $arrangementID . " GROUP BY gigID) AS C ON C.gigID = G.gigID WHERE G.isStyle=1  ORDER BY G.name ASC";
//echo $sql;
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	    $form .= "<p>";
            $form .= "<input type='checkbox' name='gigStyle[]' value='" . $row[2] . "' ";
	    if ($row[1] > 0){
	    $form .= " checked ";
	    }
	    $form .= ">" . $row[0];
	    $form .= "</p>";
	}
	$form .= "<input type='hidden' name='arrangementID' value='" . $arrangementID . "'>";
	$form .= "<input type='hidden' name='action' value='updateStyle'>";
	$form .= "<input type='submit' value='Update styles'>";
	$form .= "</form>";
	$form .= "</fieldset>";
	return $form;
}

function getArrangementForm( $arrangementID){

$form = "";
include "mysql-cred.php";
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 

$sql = "SELECT noteText from note  INNER JOIN publication as PUB on note.publicationID = PUB.publicationID INNER JOIN arrangement as A on A.arrangementID = PUB.arrangementID  WHERE A.arrangementID=" . $arrangementID . " ORDER BY note.noteID ASC ";
$result = mysqli_query($link, $sql);
$noteText = "";
if ($result){
	$i = 1;
    	while($row = mysqli_fetch_row( $result )) {
	        $check = "<p>" . $row[0] . "</p>\n";
		$noteText .= $check;
    	}
}

$form .= $noteText;
$form .= "<p><a href=./maintenance/?arrangementID=" . $arrangementID . "&action=getNotes>Edit notes</a></p>";
//$form .= "<p><a href=./maintenance/?arrangementID=" . $arrangementID . "&action=listPdf>Add MP3</a></p>";


$sql = "SELECT P.partID, P.name as partName, song.name, VA.arrangerFirstName, VA.arrangerLastName, S.printOrder, COUNT(*) as iCOUNT from efilePart as EF INNER JOIN efile as E on E.efileID = EF.efileID INNER JOIN publication as PUB on E.publicationID = PUB.publicationID INNER JOIN arrangement as A on A.arrangementID = PUB.arrangementID INNER JOIN view_arrangement AS VA ON VA.arrangementID = A.arrangementID INNER JOIN song ON song.songID = A.songID INNER JOIN part as P ON EF.partID = P.partID inner join section as S on S.sectionID = P.minSectionID WHERE A.arrangementID=" . $arrangementID . " GROUP BY S.printOrder, P.partID ORDER BY S.printOrder ASC, P.partID ASC ";
$result = mysqli_query($link, $sql);
// echo $sql;
$songName = "NOT FOUND";
if ($result){
	$i = 1;
    	while($row = mysqli_fetch_row( $result )) {
	        $check = "<p>  <a href='.?action=getChart&arrangement[]= " . $arrangementID . "&part[]=" . $row[0] . "'>"  . $row[1] . "</a></p>\n";
		$form = $form . $check;
		$songName = $row[2] ." arranged by " . $row[3] . " " .$row[4];
    	}
}

$sql = "SELECT gigID, name, gigDate from gig WHERE gigID in (SELECT gigID FROM setList2 WHERE arrangementID =" . $arrangementID . ")  ORDER BY gigDate DESC  ";
$result = mysqli_query($link, $sql);
if ($result){
	$olist = "<div>Listed in:<ol>"; $li="";
    	while($row = mysqli_fetch_row( $result )) {
	        $li = "<a href='.?action=getGig&gigID= " . $row[0] . "'>"  . $row[2] . " " . $row[1] . "</a>\n";
		$olist .= "<li>". $li . "</li>\n";
    	}
	$olist .="</ol></div>";
}

$mlist = "";
$sql = "SELECT E.name, IFNULL(concat(G.gigdate, ' ', G.name, ' '),''), LEFT(E.name,15), E.efileID  from efile AS E LEFT JOIN gig as G ON E.gigID = G.gigID where E.efileTypeID=2 AND E.arrangementID =" . $arrangementID . "  ORDER BY E.efileID DESC  ";
//echo $sql;
$result = mysqli_query($link, $sql);
if ($result){
	$li=""; $icount = 0;
    	while($row = mysqli_fetch_row( $result )) {
		if (0==$icount) $mlist .= "<div>MP3:<ol>"; 
	        $li = "<a href='audio?efileID=" . $row[3] . "'>"  .  $row[1] .  $row[3] .  "</a>\n";
		$mlist .= "<li>". $li . "</li>\n";
		$icount ++;
    	}
	if ($icount > 0) $mlist .="</ol></div>";
}

$ulist = "";
$isTSB = "";
$sql = "SELECT urlurl, urlTypeName, urlTSB from url INNER JOIN urlType ON url.urlTypeID = urlType.urlTypeID WHERE urlArrangementID = " . $arrangementID . " ORDER BY url.urlTypeID DESC, urlID ASC  ";
//echo $sql;
$result = mysqli_query($link, $sql);
if ($result){
	$li=""; $icount = 0;
    	while($row = mysqli_fetch_row( $result )) {
		if (0==$icount) $ulist .= "<div>URL:<ol>"; 
		if (1==$row[2]) $isTSB = " TSB ";
	        $li = "<a href='" . $row[0] . "'>"  . $row[0] . "</a> " . $row[1] . $isTSB . "\n";
		$ulist .= "<li>". $li . "</li>\n";
		$isTSB = "";
		$icount ++;
    	}
	if ($icount > 0) $ulist .="</ol></div>";
}

$form = "<fieldset><legend>" . $songName . "</legend>\n" . $form . $olist . $mlist .  $ulist . $this->getArrangementStyleForm( $arrangementID ) . "</fieldset>\n"; 
return $form;
}

function getMP3List($bUnpair=false, $submitLabel='unpair'){
include "mysql-cred.php";
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 

$sql = "SELECT efile.name,   S.name, A.arrangementID, efile.efileID, CONCAT(IFNULL(CONCAT(' ',G.name,' ',G.gigDate,' '),''), efile.efileID)  from song as S INNER JOIN arrangement AS A on S.songID = A.songID INNER JOIN efile ON efile.arrangementID = A.arrangementID LEFT JOIN  gig as G ON G.gigID=efile.gigID  where efileTypeID=2 ORDER BY S.name ASC, efile.efileID DESC  ";
//echo $sql;
$result = mysqli_query($link, $sql);
$oldSong="";
$ulist = "";
if ($result){
	$li=""; $icount = 0;
    	while($row = mysqli_fetch_row( $result )) {
		if (0==$icount) $ulist .= "<ol>"; 
		if ($oldSong<>$row[1]){
			if ($oldSong<>"") $ulist .= "</ul></li>";
			if (!$bUnpair){
				$ulist .= "<li><a href=./?arrangementID=" . $row[2] . ">" . $row[1] . "</a>\n" . "<ul> \n";
			} else {
				$ulist .= "<li>" . $row[1] . "\n" . "<ul> \n";
			}
			$oldSong = $row[1];
		}
		if (!$bUnpair){
	        	$li = "<a href='audio?efileID=" . $row[3] . "'>"  . $row[4] . "</a> \n";
		} else {
			$li = "";
		}

		if ($bUnpair){

    			$li .=  "<form action='' method='post'>";
    			$li .=  "<input type='submit' value='" . $submitLabel . $row[4] .  "' ><input type='hidden' name='action' value='deleteEfile'>";
    			$li .=  "<input type='hidden' name='efileID' value='" . $row['3'] . "'>";
    			$li .=  "</form></p>";

		}

		$ulist .= "<li>". $li . "</li>\n";
		$isTSB = "";
		$icount ++;
    	}
	if ($icount > 0) $ulist .="</ul></li></ol>";
}
return $ulist;
}

function getURLList($showTitles = false){
include "mysql-cred.php";
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 

$sql = "SELECT urlurl, urlTypeName, urlTSB, S.name, A.arrangementID, IFNULL(urlTitle,urlurl), urlYouTubeID from song as S INNER JOIN arrangement AS A on S.songID = A.songID INNER JOIN url ON url.urlArrangementID = A.arrangementID INNER JOIN urlType ON url.urlTypeID = urlType.urlTypeID ORDER BY S.name ASC, url.urlTypeID DESC, urlID ASC  ";
$result = mysqli_query($link, $sql);
$oldSong="";
$ulist = "";
$isTSB = "";
if ($result){
	$li=""; $icount = 0;
    	while($row = mysqli_fetch_row( $result )) {
		if (0==$icount) $ulist .= "<ol>"; 
		if (1==$row[2]) $isTSB = " TSB ";
		if ($oldSong<>$row[3]){
			if ($oldSong<>"") $ulist .= "</ul></li>";
			$ulist .= "<li><a href=./?arrangementID=" . $row[4] . ">" . $row[3] . "</a>\n" . "<ul> \n";
			$oldSong = $row[3];
		}
		if ($showTitles){
	        	$li = "<a href='" . $row[0] . "'>"  . $row[5] . "</a> " . $row[1] . $isTSB . "\n";
		} else {
	        	$li = "<a href='" . $row[0] . "'>"  . $row[0] . "</a> " . $row[1] . $isTSB . "\n";
		}

		$ulist .= "<li>". $li . "</li>\n";
		$isTSB = "";
		$icount ++;
    	}
	if ($icount > 0) $ulist .="</ul></li></ol>";
}
return $ulist;
}

function refreshVideoList( $bJustBlanks = true){
include "mysql-cred.php";
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 

if ($bJustBlanks){ 
	$where = " AND (urlTitle IS NULL OR urlYouTubeID IS NULL) ";
} else {
        $where = "";
}
$sql = "SELECT urlurl from url WHERE 1 " . $where ;
$result = mysqli_query($link, $sql);
if ($result){
    	while($row = mysqli_fetch_row( $result )) {
		$sqlU = "UPDATE url SET urlTitle = '" . $this->getTitle($row[0]) . "', urlYouTubeID='" . $this->getYouTubeID($row[0]) . "' WHERE urlurl='" . $row[0] . "';";
        	$resultU = $this->conn->my_execute( $sqlU);
    	}
}
}

function getTitle( $url ){
if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
    return "Could not find " . $url ;
    }
$doc = new DOMDocument();
$doc->loadHTML(file_get_contents($url));
$t = $doc->getElementsByTagName("title")->item(0)->nodeValue;
$t = str_replace("'", "", $t);
return $t;

}

function getYouTubeID( $url ){
if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
    return "Could not find " . $url ;
    }
$lastslash = strrpos( $url, '/');
//return $url;
$right = substr( $url, 1 + $lastslash );
$lastV = strpos($right, 'v=');
if ($lastV){
	$final = substr( $right, 2+$lastV);
} else {
	$final = $right;
}
return $final;

}


function getChartListForm(){

include "mysql-cred.php";

$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} 
$form = "<form action = '' method='GET'>";
$form .= "<input type='hidden' name='action' value='getChartList' />";

$sql = "SELECT V.partName, V.partID FROM (SELECT partName, partID, COUNT(*) FROM view_efilePart GROUP BY partName, partID) as V INNER JOIN (SELECT P.partID, S.printOrder from part as P INNER JOIN section AS S on P.minSectionID = S.sectionID) AS PP ON PP.partID= V.partID order by PP.printOrder ASC,  PP.partID ASC";
// echo $sql;
$result = mysqli_query($link, $sql);
if ($result){
	$form .= "<p><select name='partID'>";
    	while($row = mysqli_fetch_row( $result )) {
		$check = "<option value='" . $row[1] . "'>" . $row[0] . "</option>";
		$form = $form . $check;
    	}
	$check = "<option value='-123'>Vocals (all)</option>";
	$form = $form . $check;
	$check = "<option value='-999'>All</option>";
	$form = $form . $check;
	$form .= "</select>";
}

mysqli_close( $link );
$form .= "<input type='submit' value='Get Chart List'></form>";
return $form;
}


function getEditNoteForm( $input=array()){
$where = " AND 1 ";
if (isset($input['arrangementID'])){
	if ($input['arrangementID'] > 0){
		$where .= " AND V.arrangementID = " . $input['arrangementID'] . " ";
	}
}

$form = "";
$sqlCharts = "SELECT N.noteID, V.name, V.description, N.noteText, date_format(N.noteDate, '%Y-%m-%d'), N.publicationID FROM note as N, view_publication as V WHERE V.publicationID=N.publicationID " . $where . " ORDER BY name ASC, noteDate DESC"; 
    	foreach( $this->conn->listMultiple( $sqlCharts ) AS $index=>$row ){
            $form .= "<div>";
            $form .= "<fieldset><legend>" . $row[1] . " " . $row[2] . " " . $row[4] . "</legend>";
            $form .= "<form method='POST' action=''>";
            $form .= "<textarea rows='5' cols='60' name='noteText'>" . $row[3] . "</textarea>";
            $form .= "<input type='hidden' value='updateNote' name='action'>";
            $form .= "<input type='hidden' value='" . $row[0] . "' name='noteID'>";
            $form .= "<p><input type='submit' value='UPDATE'></p>";
            $form .= "</form>";
            $form .= "<form method='POST' action=''>";
            $form .= "<input type='hidden' value='deleteNote' name='action'>";
            $form .= "<input type='hidden' value='" . $row[0] . "' name='noteID'>";
            $form .= "<input type='submit' value='Delete'>";
            $form .= "</form>";
            $form .= "</fieldset>";
            $form .= "</div>";
	}
            
return $form;
}



function getEfileForm( $publicationID = -1){
    
    $return =  $this->getEfileFormOrder( $publicationID );

    $return .= $this->getEfileFormOrder( $publicationID, ' order by E.efileID DESC ', ' date');
    $return .= $this->getPubForm();
    return $return;
    
}


function getEfileFormOrder( $publicationID = -1, $orderby = 'order by E.name  ASC', $label = "alphabetical"){
    
    if ( 0 < $publicationID ){
        $wherePub = " AND E.publicationID =" . $publicationID . " ";
    } else {
        $wherePub = " AND 1 ";
    }
    $return = "<form action='' method='GET'>";
    $return .= "<input type='hidden' name='action' value='getEfileParts'>";
    $return .= "<p>Efile " . $label . "<select name='efileID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT E.efileID, CONCAT(E.name,': ', S.name,',',P.description,', ', PP.firstName , ' ' ,PP.lastName), V.countPages, E.name  FROM efile as E, publication as P, arrangement as A, person as PP, song AS S, view_efilePages AS V  WHERE  E.publicationID=P.publicationID and P.arrangementID=A.arrangementID and A.arrangerPersonID=PP.personID AND A.songID = S.songID and E.efileID=V.efileID " . $wherePub . $orderby )  as $key=>$song){
        $return .= "<option value='" . $song[0] . "'>" . $song[2] . "/" . $this->numPages('../pdf/' . $song[3]) . " " . $song[1] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p><input type='submit' value='Get parts'>";
    $return .= "</form>";

    return $return;
}


function getFormBackup( $arrID, $isIn, $arrLabel){
    if ($isIn){
        return $this->getRemoveFromBackupForm( $arrID, $arrLabel);
    }
    else{
        return $this->getAddToBackupForm( $arrID, $arrLabel);
    }
}

function getFormPads( $arrID, $isInPads, $arrLabel){
    if ($isInPads){
        return $this->getRemoveFromPadsForm( $arrID, $arrLabel);
    }
    else{
        return $this->getAddToPadsForm( $arrID, $arrLabel);
    }
}


function getNewNoteForm( $input=array()){
$where = " WHERE 1 ";
if (isset($input['arrangementID'])){
	if ($input['arrangementID'] > 0){
		$where .= " AND V.arrangementID = " . $input['arrangementID'] . " ";
	}
}

$form = "";
$sqlCharts = "SELECT V.publicationID, V.name, V.description FROM view_publication as V " . $where . " ORDER BY V.name ASC"; 
//echo $sqlCharts;
            $form .= "<div>";
            $form .= "<fieldset><legend>New note</legend>";
            $form .= "<form method='POST' action=''>";
            $form .= "<select name='publicationID'>";
	if (!isset($input['arrangementID'])){
            $form .= "<option value='-1'></option>";
	}
    	foreach( $this->conn->listMultiple( $sqlCharts ) AS $index=>$row ){

            $form .= "<option value='". $row[0] . "'>" . $row[1] . " " . $row[2] . "</option>";
    	}
            $form .= "</select>";
            $form .= "<div><textarea rows='5' cols='60' name='noteText'></textarea></div>";
            $form .= "<input type='hidden' value='addNote' name='action'>";
            $form .= "<p><input type='submit' value='Add'></p>";
            $form .= "</form>";
            $form .= "</fieldset>";
            $form .= "</div>";
            
return $form;
}


function getNewPersonForm(){

$form = "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='addPerson' />";
$form .= "<p>First name<textarea name='firstName'></textarea></p> ";
$form .= "<p>Last name<textarea name='lastName'></textarea></p> ";
$form .= "<p>Nickname<textarea name='nickName'></textarea></p> ";
$form .= "<input type='submit' value='Add'></form>";
return $form;
}


function getNewSongForm(){

$form = "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='addSong' />";
$form .= "<p>Song name<textarea name='songName'></textarea></p> ";
$form .= "<input type='submit' value='Add'></form>";
return $form;
}


function getPartForm($efileID){
 
    $fname = "";
    foreach ($this->conn->listMultiple("SELECT E.name AS Ename  FROM efile as E WHERE  E.efileID = " . $efileID . "")  as $key=>$song){
        $fname = $song[0];
    }   
    $return = "";
    $return .= "<fieldset><legend>Delete part/page pairs</legend>";
    $return .= "<div><table>";
    $return .= "<tr><th>Part<th>Start Page<th>End Page<th>Efile</tr>";
    foreach ($this->conn->listMultiple("SELECT X.efilePartID, X.startPage, X.endPage, P.name, E.name AS Ename  FROM efilePart as X, part as P, efile as E WHERE  X.partID=P.partID and X.efileID = E.efileID  and E.efileID = " . $efileID . " order by X.startPage  ASC")  as $key=>$song){
        $return .= "<tr>";
        $return .= "<td>" . $song[3] . "</td>";
        $return .= "<td>" . $song[1] . "</td>";
        $return .= "<td>" . $song[2] . "</td>";
        $return .= "<td>" . $song[4] . "</td>";
        $return .= "<td>";
        $return .= "<form action='' method='POST'>";
        $return .= "<input type='hidden' name='action' value='deleteEfilePart'>";
        $return .= "<input type='hidden' name='efilePartID' value='" . $song[0] . "'>";
        $return .= "<input type='submit' value='DELETE'>";
        $return .= "</form>";
        $return .= "</td>";
        $return .= "</tr>";
    }
    $return .= "</table></div>";
    $return .= "</fieldset>";
    $return .= "<fieldset><legend>Add part/page pair</legend>";
    $return .= "<p><a href='../pdf/" . $fname . "'>" . $fname . "</a></p>";
    $numpages = $this->numPages('../pdf/' . $fname);
    $return .= "<div>";
    $return .= "<form action='' method='POST'>";
    $return .= "<input type='hidden' name='action' value='addEfilePart'>";
    $return .= "<input type='hidden' name='efileID' value='" . $efileID . "'>";
    $return .= "<p>Part <select name='partID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT P.partID, P.name FROM part as P  order by P.name  ASC")  as $key=>$song){
        $return .= "<option value='" . $song[0] . "'>" . $song[1] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>Start Page <select name='startPage'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    for ($i = 1; $i <= $numpages; $i++){
        $return .= "<option value='" . $i . "'>" . $i . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>End Page <select name='endPage'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    for ($i = 1; $i <= $numpages; $i++){
        $return .= "<option value='" . $i . "'>" . $i . "</option>";
    }
    $return .= "</select>";
    $return .= "<p><input type='submit' value='ADD to " . $fname . "'>";
    $return .= "</form>";
    $return .= "</fieldset>";
    return $return;
}


function getPeople(){

$sql = "SELECT firstName, lastName, nickName from person order by lastName  ASC";
$return = "<table> \n <tr><th>First Name<th>Last Name<th>Nick Name</tr> \n";
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
		$return .= "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr> \n";
    	}
$return .= "</table>";
return $return;
}


function getPubForm(){
    $return = "";
    $return .= "<fieldset><legend>Limit pdfs to publication</legend>";
    $return .= "<form action='' method='GET'>";
    $return .= "<input type='hidden' name='action' value='getParts'>";
    $return .= "<p>Publication <select name='publicationID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT P.publicationID, CONCAT(S.name,',',P.description,', ', PP.firstName , ' ' ,PP.lastName) FROM publication as P, arrangement as A, person as PP, song AS S WHERE  P.arrangementID=A.arrangementID and A.arrangerPersonID=PP.personID AND A.songID = S.songID order by S.name  ASC")  as $key=>$song){
        $return .= "<option value='" . $song[0] . "'>" . $song[1] . "</option>";
    }
    $return .= "</select>";
    $return .= "<input type='submit' value='Limit pdfs on offer'>";
    $return .= "</form>";
    $return .= "</p>";
return $return;
}

function getURLForm(){
    
    $return = "<fieldset><legend>Pair URL to arrangement</legend><form action='' method='POST'>";
    $return .= "<input type='hidden' name='action' value='postNewURL'>";
    $return .= "<p>URL address <textarea name='urlurl'></textarea></p>";
    $return .= "<p>URL is of type <select name='urlTypeID'>";
    foreach ($this->conn->listMultiple("SELECT urlTypeID, urlTypeName from urlType ORDER BY urlTypeName DESC")  as $key=>$type){
        $return .= "<option value='" . $type[0] . "'>" . $type[1] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p><input type='checkbox' name='urlTSB' value='isTSB'>Is our band (leave unticked if it's another band)</p> ";
    $return .= "<p>Arrangement <select name='arrangementID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT CONCAT(S.name,', ', PP.firstName , ' ' ,PP.lastName), A.arrangementID FROM arrangement as A, person as PP, song AS S WHERE  A.arrangerPersonID=PP.personID AND A.songID = S.songID order by S.name  ASC")  as $key=>$song){
        $return .= "<option value='" . $song[1] . "'>" . $song[0] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>Gig <select name='urlGigID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT CONCAT(name, '.', gigDate), gigID FROM gig WHERE  (hasWhere IS NULL OR hasWhere=0) and isStyle=0  order by gigDate DESC ")  as $key=>$gig){
        $return .= "<option value='" . $gig[1] . "'>" . $gig[0] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p><input type='submit' value='ADD URL'>";
    $return .= "</form></fieldset>";

    return $return;
}


function getMP3Form( $path = '../mp3'){
    
    $return = "<fieldset><legend>Pair mp3 to arrangement</legend><form action='' method='POST'>";
    $return .= "<input type='hidden' name='action' value='setMP3'>";
    $return .= "<p>Efile <select name='efile'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->listPdfUnlisted( $path ) as $key=>$filename){
        $return .= "<option value='" . $filename . "'>" . $filename . "</option>";
    }
    $return .= "</select>";
    $return .= "<input type='hidden' name='formatID' value='2'>";
    $return .= "<p>Arrangement <select name='arrangementID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT CONCAT(S.name,', ', PP.firstName , ' ' ,PP.lastName), A.arrangementID FROM arrangement as A, person as PP, song AS S WHERE  A.arrangerPersonID=PP.personID AND A.songID = S.songID order by S.name  ASC")  as $key=>$song){
        $return .= "<option value='" . $song[1] . "'>" . $song[0] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>Gig <select name='gigID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT CONCAT(name, '.', gigDate), gigID FROM gig WHERE  (hasWhere IS NULL OR hasWhere=0) and isStyle=0  order by gigDate DESC ")  as $key=>$gig){
        $return .= "<option value='" . $gig[1] . "'>" . $gig[0] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p><input type='submit' value='ADD'>";
    $return .= "</form></fieldset>";

    return $return;
}

function getPublicationForm( $path = '../pdf'){
    
    $return = "<fieldset><legend>Pair pdf to publication</legend><form action='' method='POST'>";
    $return .= "<input type='hidden' name='action' value='setPublication'>";
    $return .= "<p>Efile <select name='efile'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->listPdfUnlisted( $path ) as $key=>$filename){
        $return .= "<option value='" . $filename . "'>" . $filename . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>Format <select name='formatID'>";
    $return .= "<option value='0'>Portrait</option>";
    $return .= "<option value='1'>Landscape</option>";
    $return .= "</select>";    $return .= "<p>Publication <select name='publicationID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT P.publicationID, CONCAT(S.name,',',P.description,', ', PP.firstName , ' ' ,PP.lastName) FROM publication as P, arrangement as A, person as PP, song AS S WHERE  P.arrangementID=A.arrangementID and A.arrangerPersonID=PP.personID AND A.songID = S.songID order by S.name  ASC")  as $key=>$song){
        $return .= "<option value='" . $song[0] . "'>" . $song[1] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>OR (if publication not defined)</p>";
    $return .= "<p>Song <select name='songID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT songID, name from song order by name  ASC")  as $key=>$song){
        $return .= "<option value='" . $song[0] . "'>" . $song[1] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>Arranger <select name='arrangerPersonID'>";
    $return .= "<option value='" . -1 . "'>" . "" . "</option>";
    foreach ($this->conn->listMultiple("SELECT personID, CONCAT(firstName,' ',lastName) from person order by LastName  ASC")  as $key=>$song){
        $return .= "<option value='" . $song[0] . "'>" . $song[1] . "</option>";
    }
    $return .= "</select>";
    $return .= "<p>Publication description<textarea name='description'></textarea></p>";

    $return .= "<p><input type='submit' value='ADD'>";
    $return .= "</form></fieldset>";

    return $return;
}


function getRemoveFromBackupForm( $arrID, $arrLabel){

$form = "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='removeFromBackup' />";
$form .= "<input type='hidden' name='arrangementID' value='" . $arrID . "' />";
$form .= "<input type='submit' value='Remove " . $arrLabel . " from back-up'></form>";
return $form;
}

function getRemoveFromPadsForm( $arrID, $arrLabel){

$form = "<form action = '' method='POST'>";
$form .= "<input type='hidden' name='action' value='removeFromPads' />";
$form .= "<input type='hidden' name='arrangementID' value='" . $arrID . "' />";
$form .= "<input type='submit' value='Remove " . $arrLabel . " from pads'></form>";
return $form;
}


function getSongs(){

$sql = "SELECT S.name, A.arrangementID, A.isInPads, CONCAT(S.Name, ' ', P.firstName, ' ', P.lastName) as ArrLabel, A.isBackedUp from song AS S LEFT JOIN ( arrangement as A INNER JOIN person as P ON A.arrangerPersonID = P.personID)  ON A.songID = S.songID order by S.name  ASC";
$return = "<ol> \n ";
//$return = "<table> \n <tr><th>Pads<th>Back-up<th>Name</tr> \n";
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
		$return .= "<li>". $this->getFormPads($row[1], $row[2], $row[3]) . "". $this->getFormBackup($row[1], $row[4], $row[3]) . ""  . "</li> \n";
//		$return .= "<tr><td>". $this->getFormPads($row[1], $row[2], "") . "</td><td>". $this->getFormBackup($row[1], $row[4], "") . "</td><td>" . $row[3] . "</td></tr> \n";
    	}

$return .= "</ol>";
//$return .= "</table>";
return $return;
}


function getUploadFileForm(){
    $form = '
        <fieldset>
        <legend>Upload file</legend>
        <form action="" method="POST" role="form" class="uploadForm" enctype="multipart/form-data" >
        <input type="hidden" name="action" value="uploadPDF">
        <input type="file" name = "myUpload" class = "file">
        <input type="submit" value="Upload file" id="boton" class = "btn btn-success btn-lg">
        </form>
        </fieldset>
        ';
        return $form;
}


function listAll( $partID=''){

$title = "";



$where="";
$partWhere="";
$distinctOrder = " ,v.setListOrder ";
$orderByFile = " ORDER BY setListOrder ASC ";
$orderByList = " ORDER BY v.setListOrder ASC ";
        $where .= " OR 1  ";
        $distinctOrder = "  ";
        $orderByFile = " ORDER BY songname ASC";
        $orderByList = " ORDER BY songname ASC";
    $partWhere .= " OR partName='Drums' ";

$pdf = new myListAllPDF();
include "mysql-cred.php";

if ('' == $partID){
    $partID = 0;
}

    $partList = array();

if (-999==$partID){
    $wherePart = " 1 ";   
    $bIsSection = false;
} elseif (-123==$partID){
    $partList = array(6); ///hard-coded Database ID (OK, probably only labels change)
    $bIsSection = true; 
} else {
    $wherePart = " partID = " . $partID;
    $bIsSection = false;
}
$link  = mysqli_connect( $servername, $username, $password, $database);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error);
} 


if (!$bIsSection){
    $sqlParts = "SELECT P.partID, P.name from part as P INNER JOIN section AS S on P.minSectionID = S.sectionID WHERE " . $wherePart . "  order by S.printOrder ASC,  P.partID ASC";
$arrange = array();
    $resultP = mysqli_query($link, $sqlParts);
    $rowCount = 0;
    if ($resultP){
    	while($rowP = mysqli_fetch_row( $resultP )) {
    	    $partList[] =  $rowP[0];
    	    $rowCount++;
    	}
    }
}

foreach ($partList AS $partID){
$title = "";
if ($partID > 0){
    if (!$bIsSection){
        $sqlCharts = "SELECT shortName, name from part where partid=" . $partID . ";";
    } else {
        $sqlCharts = "SELECT shortName, name from section where sectionid=" . $partID . ";";
    }
    $result = mysqli_query($link, $sqlCharts);
    if ($result){
    	while($row = mysqli_fetch_row( $result )) {
    	    $partShortName =  $row[0];
    	    $partLongName =  $row[1];
	    }
    }
    if (!$bIsSection){
        $sqlCharts = "SELECT DISTINCTROW CONCAT(IF(A.isInPads=1,'','*'), IF(AC.arrCount>1, CONCAT(S.name, ', ', VA.arrangerFirstName, ' ', VA.arrangerLastName), S.name), IF(c2.countParts>0,'',' (No ".$partShortName.")')) as songName, 'Thornbury Swing Band (". $partLongName . ")', NOW(), c.countParts, A.arrangementID, 1+X.countPages, S.name  FROM (arrangement AS A INNER JOIN song as S on S.songID = A.songID  
            INNER JOIN view_arrangement AS VA on VA.arrangementID = A.arrangementID
            INNER JOIN (SELECT COUNT(*) as arrCount, songID FROM arrangement AS A GROUP BY songID) AS AC ON AC.songID = S.songID 
        ) LEFT JOIN (SELECT count(*) as countParts, arrangementID from  view_efilePart GROUP BY arrangementID) as c on c.arrangementID = A.arrangementID 
        LEFT JOIN (SELECT count(*) as countParts, arrangementID from  view_efilePart WHERE partID = " . $partID . " GROUP BY arrangementID) as c2 on c2.arrangementID = A.arrangementID 
        LEFT JOIN (SELECT SUM(endPage)-SUM(startPage) as countPages, arrangementID FROM (SELECT DISTINCTROW fileName, startPage, endPage, arrangementID FROM view_efilePart as g ) AS PP GROUP BY arrangementID ) AS X ON X.arrangementID = A.arrangementID  WHERE ( c.countParts > 0   ) ORDER BY S.Name ASC;";
    } else {
        $sqlCharts = "SELECT DISTINCTROW CONCAT(IF(A.isInPads=1,'','*'), IF(AC.arrCount>1, CONCAT(S.name, ', ', VA.arrangerFirstName, ' ', VA.arrangerLastName), S.name), IF(c2.countParts>0,'',' (No ".$partShortName.")')) as songName, 'Thornbury Swing Band (". $partLongName . ")', NOW(), c.countParts, A.arrangementID, 1+X.countPages, S.name  FROM (arrangement AS A INNER JOIN song as S on S.songID = A.songID  
            INNER JOIN view_arrangement AS VA on VA.arrangementID = A.arrangementID
            INNER JOIN (SELECT COUNT(*) as arrCount, songID FROM arrangement AS A GROUP BY songID) AS AC ON AC.songID = S.songID) LEFT JOIN (SELECT count(*) as countParts, arrangementID from  view_efilePart GROUP BY arrangementID) as c on c.arrangementID = A.arrangementID 
        LEFT JOIN (SELECT count(*) as countParts, VVV.arrangementID from  view_efilePart AS VVV INNER JOIN part as PPP on VVV.partID=PPP.partID INNER JOIN section as SSS ON SSS.sectionID = PPP.minSectionID WHERE sectionID = " . $partID . " GROUP BY arrangementID) as c2 on c2.arrangementID = A.arrangementID 
        LEFT JOIN (SELECT SUM(endPage)-SUM(startPage) as countPages, arrangementID FROM (SELECT DISTINCTROW fileName, startPage, endPage, arrangementID FROM view_efilePart as g ) AS PP GROUP BY arrangementID ) AS X ON X.arrangementID = A.arrangementID  WHERE ( c.countParts > 0   ) ORDER BY S.Name ASC;";
    }
    
}
//echo $sqlCharts;
$result = mysqli_query($link, $sqlCharts);
$pageCount=1;
$txtIndex = "";
$i =1;
if ($result){
	$pdf->AddPage();
	$pdf->SetFont('Arial','',14);
	$rowcount = 0;
    	while($row = mysqli_fetch_row( $result )) {
	$arrange[] = $row[4];
        if (0==$rowcount){
            $title .= $row[1] . " (* = not in pads) " . $row[2] . "\n\n\n";
        }
        if ((NULL === $row[3])){
		$pdf->SetTextColor(255,255,255);
	} else {
		$pdf->SetTextColor(0,0,0);
	}
    
	$pdf->SetTextColor(0,0,0);
            if ($i < 10) $txtIndex .=  "  ";
            $txtIndex .= $i++ . ".  " . $row[0] . "\n";
        if (!(NULL === $row[3])){
		$pageCount = $pageCount + $row[5];
	}
        $rowcount++;
	}
}

    $pdf->SetFont('Arial','',12);
    $pdf->SetFillColor(200,220,255);
    
    $pdf->Cell(0,6,$title,0,1,'L',true);
    $pdf->Ln(4);
    // Save ordinate
    $pdf->y0 = $pdf->GetY();
    
	$pdf->MultiCell(100,5,$txtIndex);
    $pdf->Ln(4);

$sqlCharts = "SELECT name, description, noteText, date_format(noteDate, '%Y-%m-%d') FROM view_note  ORDER BY name ASC, noteDate DESC"; 
$notes = "";
$oldTitle = "";
$result = mysqli_query($link, $sqlCharts);
if ($result){
    	while($row = mysqli_fetch_row( $result )) {
    	    if( $oldTitle <> $row[0] ){
    	        if( $oldTitle<>""){
    	            $notes .= "\n";
    	        }
    	        $oldTitle = $row[0] ;
    	        $notes .= $oldTitle . ": ";
    	    }
            $notes .=  $row[2] . "\n"; // no date
	}
}
    $pdf->SetCol(0);
    	$pdf->AddPage();
	$pdf->SetFont('Arial','',14);
        
    $pdf->SetFont('Arial','',8);
    $pdf->SetFillColor(200,220,255);
    
    $pdf->Cell(0,6,"Notes",0,1,'L',true);
    $pdf->Ln();
    // Save ordinate
    $pdf->y0 = $pdf->GetY();
    
	$pdf->MultiCell(90,5,$notes);
    $pdf->SetCol(0);

} // foreach ($partList AS $partID){

$yourFile =  'output/'. md5(time()) . 'index.pdf';
$pdf->Output(getcwd() . "/" . $yourFile,'F');
return $yourFile;

}


function listMP3( $path = '../mp3' ){
if ( $path =='../pdf' ){
	$echo =  "<fieldset><legend>Unpaired pdfs</legend>";
} else {
	$echo =  "<fieldset><legend>Unpaired " . $path . "</legend>";
}
$echo .= $this->getFolderUsage();

foreach ($this->listPdfUnlisted( $path ) as $key=>$filename){
    $echo .= "<p><a href='" . $path . "/" . $filename . "'>" . $filename . "</a> " .  "\n\n";
    $echo .=  "<form action='' method='post'>";
    $echo .=  "<input type='submit' value='delete " . $filename . "' ><input type='hidden' name='action' value='deleteMP3'>";
    $echo .=  "<input type='hidden' name='fileNameExclPath' value='" . $filename . "'>";
    $echo .=  "</form></p>";
    
}
$echo .=  "</fieldset>";
$echo .=  "<fieldset><legend>Paired MP3</legend>";

$echo .= $this->getMP3List( true, 'unpair');

$echo .=  "</fieldset>";
return $echo;
}


function listPdf( $path = '../pdf' ){
if ( $path == '../pdf' ){
	$echo =  "<fieldset><legend>Unpaired pdfs</legend>";
} else {
	$echo =  "<fieldset><legend>Unpaired " . $path . "</legend>";
}

foreach ($this->listPdfUnlisted( $path ) as $key=>$filename){
    $echo .= "<p><a href='" . $path . "/" . $filename . "'>" . $filename . "</a> " . $this->numPages($path . "/" . $filename) . "\n\n";
    $echo .=  "<form action='' method='post'>";
    $echo .=  "<input type='submit' value='delete " . $filename . "' ><input type='hidden' name='action' value='deletePDF'>";
    $echo .=  "<input type='hidden' name='fileNameExclPath' value='" . $filename . "'>";
    $echo .=  "</form></p>";
    
}
$echo .=  "</fieldset>";
$echo .=  "<fieldset><legend>Paired with no parts</legend>";
foreach ($this->listPdfWithNoParts() as $key=>$row){
    $echo .= "<p><a href='" . $path . "/" . $row['filename'] . "'>" . $row['chart']  . " " . $row['filename'] . "</a> \n\n";
    $echo .=  "<form action='' method='post'>";
    $echo .=  "<input type='submit' value='delete " . $row['filename'] . "' ><input type='hidden' name='action' value='deleteEfile'>";
    $echo .=  "<input type='hidden' name='efileID' value='" . $row['efileID'] . "'>";
    $echo .=  "</form></p>";
    
}
$echo .=  "</fieldset>";
return $echo;
}

function listPdfWithNoParts(){
	$sql = "SELECT E.name,  V.name, E.efileID from efile as E INNER JOIN view_publication as V ON V.publicationID=E.publicationID WHERE efileID NOT IN (SELECT efileID from efilePart) ORDER BY V.name ASC";
	$ret = array();
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
		$red1 = array();
		$red1['filename'] = $row[0];
		$red1['chart'] = $row[1];
		$red1['efileID'] = $row[2];
		$ret[] = $red1;
		}

	return $ret;
}

function listPdfUnlisted( $path = '../pdf' ){

$files = scandir($path);
//echo "<pre>Files1" . print_r($files,1) . "</pre>";
$files = array_diff(scandir($path), array('.', '..'));
asort($files);
//echo "<pre>Files2" . print_r($files,1) . "</pre>";
$sql = "SELECT name from efile";
$pairedFiles = array();
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
    	$pairedFiles[] = $row[0];
    	}

//echo "<pre>pairedFiles2" . print_r($pairedFiles,1) . "</pre>";

$unpaired = array_diff($files, $pairedFiles);
//echo "<pre>unpairedFiles1" . print_r($unpaired,1) . "</pre>";
$unpaired2 = array();
foreach ($unpaired as $key=>$filename){
//    echo $this->numPages($path . "/" . $filename);
    if ( $filename!="index.php" && ($path!="../pdf" || $this->numPages($path . "/" . $filename) > 0)){
        $unpaired2[] = $filename;
    }
}
asort($unpaired2);
//echo "<pre>unpaired2" . print_r($unpaired2,1) . "</pre>";
return $unpaired2;    
}


function newName($oldName, $countDigits){
    $newName = preg_replace('/[^A-Za-z1-9]/u','', strip_tags($oldName));
    for( $i = 1; $i <= $countDigits; $i++){
        $newName .= intval(rand(0,20));
    }
    return $newName;
}


function numPages($filename){
    try {
        $pdf = new Fpdi\Fpdi();
        $numPages = $pdf->setSourceFile("$filename");
    }
    catch(Exception $e) {
        $numPages = -1;
    }
    return $numPages;
}


function pdfFromGet( $input){

$where=" OR V.efileID=-999 ";
$partWhere=" OR V.partID=-999 ";
$arrangeWhere=" OR V.efileID in (SELECT efileID from efile where publicationID in (SELECT publicationID from publication WHERE arrangementID IN (-999";
if (isset($input['chart'])){
   foreach ($input['chart'] AS $index=>$value){
      $where .= " OR V.efileID='" . $value . "' ";
   }
}
$padding = true;
if (isset($input['noPad'])){
	$padding = false;
}
if (isset($input['part'])){
    foreach ($input['part'] AS $index=>$value){
      $partWhere .= " OR P.partID='" . $value . "' ";
    }
}
if (isset($input['allParts'])){
      $partWhere .= " OR 1 ";
}
if (isset($input['arrangement'])){
    foreach ($input['arrangement'] AS $index=>$value){
      $arrangeWhere .= "," .   $value;
    }
}
$arrangeWhere .=")))";
$where .=   $arrangeWhere;
$sql = "SELECT V.fileName, V.startPage, V.endPage, V.formatID, V.partName, V.songName FROM view_efilePart AS V INNER JOIN part as P on P.partID = V.partID INNER JOIN section AS S on P.minSectionID = S.sectionID where 1 AND ( 0 " . $partWhere . ") AND ( 0 " . $where . " ) ORDER BY V.songName, S.printOrder ASC, P.partID ASC";
//echo $sql;
$pdf = new Fpdi\Fpdi();

$pageCount = 1;
	$pdf->AddPage();
	$pdf->SetFont('Arial','',14);
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
// 	$pdf->Write(5,$pageCount . "  (" . $row[4] . ") ");
		if (0 == $row[3]){
 	        $pdf->Write(5,"P");
 	      } else {
 	          $pdf->Write(5,"L");
 	      }
 	    $pdf->Write(5," ");
 	    $pdf->Write(5,"(" . $row[4] . ") ");
        $pdf->Write(5,$row[5] . "\n");
	$pageCount = $pageCount + 1 + $row[2] - $row[1];
	}
if (isset($input['arrangement'])){
	$this->getAllNotes($pdf, $input['arrangement']);
}
    	foreach( $this->conn->listMultiple( $sql ) AS $index=>$row ){
	  $pdf->setSourceFile("pdf/" . $row[0]);
	  $jj = 0;
	  for ($i = $row[1], $ii = $row[2]; $i <= $ii; $i++){
		$tplIdx = $pdf->importPage($i);
		if (0 == $row[3]){
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
	  }
          // pad out with empty pages
	  if ($padding){
	  if (0 == $row[3]){
		$jtarget = ceil($jj/4) * 4;
		} else {
		$jtarget = ceil($jj/2) * 2;
          }
	  for ($i = $jj, $ii = $jtarget; $i < $ii; $i++){
		if (0 == $row[3]){
			$pdf->AddPage();
       			$pdf->Write(5,"Blank on purpose \n");
		} else {
			$pdf->AddPage('L');
       			$pdf->Write(5,"Blank on purpose \n");
		}
	  }
	  } // end if $padding

        }
$this->conn->saveRequest($input);
$yourFile =  'output/'. md5(time()) . 'myfile.pdf';
$pdf->Output(getcwd() . "/" . $yourFile,'F');
return $yourFile;
}

function postStyle( $input ){
	if (isset( $input['arrangementID']) ){
		$sqlD = "DELETE FROM setList2 WHERE gigID IN (SELECT gigID FROM gig WHERE isStyle=1)  AND arrangementID=" . trim($input['arrangementID']) . "";
		$result = $this->conn->my_execute( $sqlD);
		}
	if (isset( $input['arrangementID']) && isset( $input['gigStyle'] )){
	foreach ($input['gigStyle'] AS $index=>$gigID){
		$sqlI = "INSERT INTO setList2 (arrangementID, gigID, setListOrder) select " . trim($input['arrangementID']) . ", " . $gigID . ", 1 + MAX(setListOrder) from setList2 where gigID=" . $gigID . "";
		$result = $this->conn->my_execute( $sqlI);
	}
}
}

function postNewPerson($person=array()){

	if (isset($person['firstName']) && isset($person['lastName']) && isset($person['nickName'])){

$sql = "insert into person (firstName, lastName, nickName) VALUES( '".$person['firstName'] ."', '".$person['lastName']."', '".$person['nickName']."');";
$result = $this->conn->my_execute( $sql);
}

}


function postNewSong($song=array()){
    if (isset($song['songName'])){
$sqlNewSong = "insert into song  (name) VALUES( '".$song['songName'] ."');";
$result = $this->conn->my_execute( $sqlNewSong );
	}
}


function receiveFile( $file=array()){
//echo "<pre>" .     print_r($file,1) . "</pre>";
   if (!isset($file['error']) || !isset($file['tmp_name']) || !isset($file['name'])){
	return false;
   }
 
// https://secure.php.net/manual/en/function.move-uploaded-file.php'    


if ($file['error'] == UPLOAD_ERR_OK) {
//    echo "OK";
    $tmp_name = $file['tmp_name'];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
    $name = basename($file['name']);
    if(mime_content_type($file['tmp_name']) == "audio/mpeg" ){
   	$uploads_dir = "mp3";
    	$newName = $this->newName($name,10) . ".mp3";
    	if(!file_exists(SITE_ROOT . "/$uploads_dir/$newName")){
        	move_uploaded_file($tmp_name, SITE_ROOT . "/$uploads_dir/$newName");
        	return true;
    	} else {
       		return false;
    	}
    } elseif(mime_content_type($file['tmp_name']) == "application/pdf" ){
//    echo "pdf";
    	$newName = $this->newName($name,10) . ".pdf";
   	$uploads_dir = "pdf";
    	if(!file_exists(SITE_ROOT . "/$uploads_dir/$newName")){
       		move_uploaded_file($tmp_name, SITE_ROOT . "/$uploads_dir/$newName");
        	return true;
    	} else {
        	return false;
    	}
    } else {
       	return false;
    }

}
}


function setPartPage( $efileID, $partID, $startPage, $endPage){
    
    if ($endPage >= $startPage && $efileID > 0 && $partID > 0){
        $sql = "INSERT INTO efilePart (efileID, partID, startPage, endPage) VALUES(". $efileID . ",". $partID . "," . $startPage . "," . $endPage . ");";
        $result = $this->conn->my_execute( $sql );
}
 
}


function setMP3($input = array()){
    
  if (isset($input['arrangementID']) && isset($input['efile']) && isset($input['formatID']) && strlen($input['efile'])>4 ){
        
    if ($input['arrangementID'] > 0){
        $sql = "INSERT INTO efile (name, efileTypeID, formatID, arrangementID) VALUES('". $input['efile'] . "',2,". $input['formatID'] . "," . $input['arrangementID'] . ");";

  	if (isset($input['gigID']) ){
    		if ($input['gigID'] > 0){
        		$sql = "INSERT INTO efile (gigID, name, efileTypeID, formatID, arrangementID) VALUES('". $input['gigID'] . "','" . $input['efile'] . "',2,". $input['formatID'] . "," . $input['arrangementID'] . ");";
		}
	}

        $result = $this->conn->my_execute( $sql );
    } 
  }
 
}

function setPublication($input = array()){
    
  if (isset($input['efile']) && isset($input['formatID']) && strlen($input['efile'])>4 ){
        
    if (isset($input['publicationID']) && $input['publicationID'] > 0){
        $sql = "INSERT INTO efile (name, efileTypeID, formatID, publicationID) VALUES('". $input['efile'] . "',1,". $input['formatID'] . "," . $input['publicationID'] . ");";
        $result = $this->conn->my_execute( $sql );

    } elseif (isset($input['description']) && strlen($input['description']) > 0 && isset($input['songID']) && isset($input['arrangerPersonID'])  && $input['songID']>0 && $input['arrangerPersonID']>0 ){
        $sql = "INSERT INTO arrangement (songID, arrangerPersonID) VALUES(". $input['songID'] . ",". $input['arrangerPersonID'] . ");";
        $last_id = $this->conn->my_insert_id($sql);
        $sql = "INSERT INTO publication (arrangementID, description) VALUES(". $last_id . ",'". $input['description'] . "');";
        $last_id = $this->conn->my_insert_id($sql);
        $sql = "INSERT INTO efile (name, efileTypeID, formatID, publicationID) VALUES('". $input['efile'] . "',1,". $input['formatID'] . "," . $last_id . ");";
        $result = $this->conn->my_execute( $sql);
    }
  }
 
}



function updateNote($noteID, $noteText){
    if ($noteID > 0 && strlen($noteText) > 3){
        $sql = "UPDATE note SET noteText='" . $noteText . "', noteDate = NOW() where noteID='" . $noteID . "'";
        $result = $this->conn->my_execute( $sql);
    }
}


} // end class Arrangement

//test
/*
require_once "Connection.php";
$a = new Arrangement();
$a->refreshVideoList();
echo $a->getYouTubeID("bvlah/blah/abc");
echo "\n";
echo $a->getYouTubeID("bvlah/blah/?v=123abc");
*/
