<?php

$bShowEmailForm = true;
include_once "include_refsC.php";
$user = new User();
$render = new Render();
$arrangement = new Arrangement();
$gig = new Gig();
// https://stackoverflow.com/questions/1907653/how-to-force-page-not-to-be-cached-in-php
if ($_POST){
if ($user->hasValidCookie()){
if (isset($_POST['action'])){
    if ('updateStyle'==$_POST['action']){
            $arrangement->postStyle($_POST);
    }

}
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache"); // HTTP/1.0
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Location: " . $_SERVER['REQUEST_URI']);
exit();
}
}
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache"); // HTTP/1.0
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>
<?php 
$bShowEmailForm = true;
include_once "include_refsC.php";
$user = new User();
$render = new Render();
$arrangement = new Arrangement();
$gig = new Gig();

if (isset($_REQUEST['confirmation'])) {
	$user->setValidCookie( $_REQUEST['confirmation'] );
	header('Location: http://tsbchart.com');
} elseif (isset($_REQUEST['action'])) {
    if ( 'logout'==$_REQUEST['action']) {
        
			$user->deleteCookie();
	        header('Location: http://tsbchart.com');
    }
	if ('storeEmail'==$_REQUEST['action']) {
		if (!($user->storeEmail($_REQUEST))){
			echo "<p>Email not recognised.</p>";
			echo $user->getEmailForm();
			echo $render->getFooter();
			exit();
		} else {
			echo "<p>Please check your email for a confirmation code. If it doesn't arrive within 5 mins please contact Owen.</p>";
			echo $render->getFooter();
			exit();
		}

	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
         <head>
          <title>TSB Printer</title>
	  <link rel="stylesheet" type="text/css" href="mystyle.css">
        </head>
        <body>
<?php 
// authenticate user.  Valid cookie or no valid cookie
// if valid cookie, provide content
// if no valid cookie, show disabled forms and offer means of authentication
if ($user->hasValidCookie()){
    echo "<p>You are logged in as " . $user->getUserEmail() . "</p>" ;
    $render->getOutputLink( $arrangement->listAll($_GET['partID']) );
    $arrangementID = -1; $gigID = -1;
	if (isset($_GET['arrangementID'])) {
        $arrangementID = $_GET['arrangementID'];
    }
	if (isset($_GET['gigID'])) {
        $gigID = $_GET['gigID'];
    }
	if (isset($_REQUEST['action'])) {
		if ( 'getChartList'==$_GET['action'] ) {
	        if (isset($_GET['partID'])) {
			echo $render->getOutputLink( $arrangement->listAll($_GET['partID']) );
	        }
			echo $render->getRequestForm($arrangementID, $gigID, $_GET);
			echo $render->getFooter();
			exit();
	       
		} elseif ( 'getChart'==$_GET['action']) {
			echo $render->getOutputLink( $arrangement->pdfFromGet($_GET) );
			echo $render->getRequestForm($arrangementID, $gigID, $_GET);
			echo $render->getFooter();
			exit();
		} elseif ( 'getGig'==$_GET['action']) {
			echo $render->getOutputLink( $gig->pdfFromGig($_GET) );
			echo $render->getRequestForm($arrangementID, $gigID, $_GET);
			echo $render->getFooter();
			exit();
		} else {
			echo $render->getRequestForm($arrangementID, $gigID, $_GET);
			echo $render->getFooter();
echo "<pre> GET" . print_r($_GET,1) . "</pre>"; 
echo "<pre> POST" . print_r($_POST,1) . "</pre>"; 
echo "<pre>" . print_r($_GET,1) . "</pre>"; 
			exit();
		}
	} else {
			echo $render->getRequestForm($arrangementID, $gigID, $_GET);
		echo $render->getFooter();
echo "<pre> GET" . print_r($_GET,1) . "</pre>"; 
echo "<pre> POST" . print_r($_POST,1) . "</pre>"; 
			exit();
	}
} else {
	echo $user->getEmailForm();
	echo $render->getFooter();
			exit();
}

?>

</body>
</html>
