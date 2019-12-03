<?php

class Render{

private $gig;
private $arrangement;

    function __construct() {
        $this->gig = New Gig();
        $this->arrangement = New Arrangement();
    }

function getFooter(){

$form = "<p>Any bugs, please tell Owen, or create an issue at  <a href='https://github.com/owen-kellie-smith/chart3'>Github</a>.</p>";
$form .= "<p><a href='webcal://tsbchart.com/cal5.ics.php'>Subscribe to gig list as calendar</a></p>";
$form .= "<p><a href='cal5.ics.php'>Get gig list as calendar events</a></p>";
$form .= "<p><a href='.'>Main menu</a></p>";
$form .= "<p><a href='.?action=logout'>Logout</a></p>";
$form .= "<p>If you want to print an A3 landscape pdf onto to 2 A4 portrait pages, here's one way that worked in June 2018. 
<ol><li>Split the pdf into single pages via <a href='https://www.splitpdf.com'>www.splitpdf.com</a> . Select 'Extract all pages to separate files'.  Splitpdf gives you a zip file which you download and extract on your computer.</li>
<li>Upload one of the separate A3 pages <a href='https://www.sejda.com/split-pdf-down-the-middle'>www.sejda.com</a>. Click on Upload pdf files, then when it's uploaded (took a minute on my machine) click on split vertically.  Sejda lets you split 3 times in an hour for free.</li></ol></p>";
$form .= "<div>
            <strong>Other links</strong>

          <p><a href='http://thornburyswingband.weebly.com/'>Thornbury Swing Band website</a></p>
            <p><a href='https://padlet.com/andyh/TSB_Tunes'>TSB_Tunes (Andy's padlet)</a></p>
          <p><a href='https://www.youtube.com/channel/UCX2K1BCZ6PR3AsjbFsi88og'>You tube (Thornbury Swing Band)</a></p>
          </div>";
return $form;
}

function getOutputLink( $filename, $bDownload=true ){
	$out = "";
	$out .= "<fieldset><legend>Your requested  charts</legend>";
	if ($bDownload){
	   $out .= "<a href='" . $filename . "'>Download pdf</a>";
	}
	$out .= "<form action='' method='POST'>";
//	$out .= "<form action='' method='GET'>";
	$out .= "<input type='hidden' name='fileToSend' value='" . $filename . "' >";
	$out .= "<input type='hidden' name='action' value='emailFile' >";
	$out .= "<input type='submit' value='Email the file' >";
	$out .= "</form>";
	$out .= "</fieldset>";
	return $out;
	}


function getRequestForm( $arrangementID = -1, $gigID = -1, $input=array()){
	$showTitles = false;
	$showUrls = false;
	if (isset($input['showTitles'])) $showTitles = true;
	if (isset($input['showUrls'])) $showUrls = true;
	$out = "";
	if ($arrangementID > 0){
	    $out .= $this->arrangement->getArrangementForm($arrangementID);
	}
	$out .= $this->gig->getGigForm( $gigID, $input);
//	$out .= "<fieldset><legend>Alphabetical order (* = not in pads)</legend>";
	$out .= $this->gig->getForm( $gigID, $input);
//	$out .= "</fieldset>";
	$out .= "<fieldset><legend>Get all titles and comments (no music)</legend>";
	$out .= $this->arrangement->getChartListForm();
	$out .= "</fieldset>";
	$out .= "<fieldset><legend>Paired links</legend>";
if ($showUrls){
	$out .= "<p><a href='./?showTitles'>Show Titles</a></p>";
} else {
	$out .= "<p><a href='./?showUrls'>Show urls</a></p>";
}
	$out .= $this->arrangement->getUrlList(!$showUrls);
	$out .= "</fieldset>";
	$out .= "<fieldset><legend>Paired mp3's</legend>";
	$out .= $this->arrangement->getMP3List();
	$out .= "</fieldset>";
	$out .= "<p><a href='maintenance/'>Maintenance</a></p>";
	$out .= "<p><a href='images/'>Images</a></p>";

	return $out;
	}



} // end class Render
