<?php 
//echo "<pre>POST " . print_r($_POST,1) . "</pre>";
//echo "<pre>FILES " . print_r($_FILES,1) . "</pre>";
// do all post stuff first then redirect
//
// authenticate user.  Valid cookie or no valid cookie
// if valid cookie, provide content
// if no valid cookie, go to main menu
include_once "../include_refsC.php";
$user = new User();
$render = new Render();
$arrangement = new Arrangement();
$gig = new Gig();
echo "<pre>POST" . print_r($_POST,1) . "GET" . print_r($_GET,1) . "</pre>";
if ($_POST){
if ($user->hasValidCookie()){
if (isset($_POST['action'])){

if ($user->hasAdminCookie()){
    if ('uploadPDF'==$_POST['action']){
    	if (isset($_FILES['myUpload'])){
        	$arrangement->receiveFile( $_FILES['myUpload']);
	}
    }

    if (('deleteMP3'==$_POST['action'] || 'deletePDF'==$_POST['action']) && isset( $_POST['fileNameExclPath'])){
        $arrangement->deleteFile($_POST['fileNameExclPath']);
    }

    if ('deleteArrangement'==$_POST['action'] && isset( $_POST['arrangementID'])){
        $arrangement->deleteArrangement($_POST['arrangementID']);
    }

    if ('deleteEfile'==$_POST['action'] && isset( $_POST['efileID'])){
        $arrangement->deleteEfile($_POST['efileID']);
    }


    if ('addPerson'==$_POST['action']){
        $arrangement->postNewPerson($_POST);
    }


    if ('deleteURL'==$_POST['action']){
        $arrangement->deleteURL($_POST);
    }

    if ('postNewURL'==$_POST['action']){
        $arrangement->postNewURL($_POST);
    }

    if ('addNewUser'==$_POST['action']){
        if (isset($_POST['newEmail']) && isset($_POST['newNickName'])){
            if (strlen($_POST['newNickName']) > 3){
                $user->storeNewUser($_POST['newEmail'],$_POST['newNickName']);
            }
        }
    }
    
    if ('addSong'==$_POST['action']){
        $arrangement->postNewSong($_POST);
    }

    if ('setMP3'==$_REQUEST['action']){
        $arrangement->setMP3($_POST);
    }
    if ('setPublication'==$_REQUEST['action']){
        $arrangement->setPublication($_POST);
    }
}

    if ('deleteEfilePart'==$_POST['action']){
        if(isset( $_POST['efilePartID']) ){
            $arrangement->deletePartPage( $_POST['efilePartID'] );
        }
    }

    if ('addEfilePart'==$_POST['action']){
        if(isset( $_POST['efileID']) && isset($_POST['partID']) && isset($_POST['startPage']) && isset($_POST['endPage'])){
            $arrangement->setPartPage( $_POST['efileID'], $_POST['partID'], $_POST['startPage'], $_POST['endPage']);
        }
    }

    if ('addNote'==$_POST['action']){
        if(isset( $_POST['noteText'])  && isset( $_POST['publicationID'])){
            $arrangement->addNote($_POST['publicationID'], $_POST['noteText']);
        }
    }

    if ('deleteNote'==$_POST['action']){
        if( isset( $_POST['noteID'])){
            $arrangement->deleteNote($_POST['noteID']);
        }
    }

    if ('updateNote'==$_POST['action']){
        if(isset( $_POST['noteText'])  && isset( $_POST['noteID'])){
            $arrangement->updateNote($_POST['noteID'], $_POST['noteText']);
        }
    }


    if ('addToBackup'==$_POST['action']){
        if(isset( $_POST['arrangementID']) ){
            $arrangement->addToBackup( $_POST['arrangementID'], 1 );
        }
    }

    if ('removeFromBackup'==$_POST['action']){
        if(isset( $_POST['arrangementID']) ){
            $arrangement->addToBackup( $_POST['arrangementID'], 0 );
        }
    }


    if ('addToPads'==$_POST['action']){
        if(isset( $_POST['arrangementID']) ){
            $arrangement->addToPads( $_POST['arrangementID'], 1 );
        }
    }

    if ('removeFromPads'==$_POST['action']){
        if(isset( $_POST['arrangementID']) ){
            $arrangement->addToPads( $_POST['arrangementID'], 0 );
        }
    }

    if(isset( $_POST['action']) && 'deleteSetListPart'==$_POST['action']){
        if(isset( $_POST['setListID'])) {
            $gig->deleteSetListPart($_POST['setListID']);
        }
    }

    if(isset( $_POST['action']) && 'addSetListPart'==$_POST['action']){
        if(isset( $_POST['gigID'])) {
            $gig->addToSet($_POST['gigID'], $_POST['setListOrder'], $_POST['arrangementID']);
        }
    }


    if ('updateGig'==$_POST['action']){
        $gig->updateGig($_POST);
    }

    if ('addSetList'==$_POST['action']){
        $gig->postNewSetList($_POST);
    }


    if ('copySetList'==$_POST['action']){
        if (isset($_POST['sourceGigID']) && isset($_POST['targetGigID'])){
            $gig->copySetList( $_POST['sourceGigID'], $_POST['targetGigID']);
        }
    }



    if ('deleteSetList'==$_REQUEST['action']){
        $gig->deleteSet($_POST);
    }
}
}
header("Location: " . $_SERVER['REQUEST_URI']);
exit();
}
?>      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
         <head>
          <title>Maintenance</title>
	  <link rel="stylesheet" type="text/css" href="../mystyle.css">
        </head>

        <body>
<?php 

// authenticate user.  Valid cookie or no valid cookie
// if valid cookie, provide content
// if no valid cookie, go to main menu
//include_once "../include_refsB.php";
if ($user->hasValidCookie()){
echo "<p><a href='../'>Main menu</a></p>";
echo "<p><a href='./?action=getNewSetListForm'>Edit set</a></p>";
echo "<p><a href='./?action=getNotes'>Edit notes</a></p>";
echo "<p><a href='./?action=checkURLs'>Check URLs exist</a></p>";
if ($user->hasAdminCookie()){
echo "<p><a href='./?action=getParts'>Assign parts</a></p>";
echo "<p><a href='./?action=listPdf'>Add pdf</a></p>";
echo "<p><a href='./?action=getNewPersonForm'>Add song/person</a></p>";
}

if (isset($_GET['action'])){
    if ('getParts'==$_GET['action']){
        if (isset($_GET['publicationID'])){
            echo $arrangement->getEFileForm($_GET['publicationID']);
        } else {
            echo $arrangement->getEFileForm();
        }    
    }

    if ('checkURLs'==$_GET['action']){
            $arrangement->checkURLs();
    }

    if ('getPartsForSet'==$_GET['action']){
        if (isset($_GET['gigID'])){
            $gig->getSetPartsOutput( $_GET, dirname(getcwd()));
            echo "<a href='../output/'>Output directory</a>";

        }
    }

    if ('emailPartsForSet'==$_GET['action']){
        if (isset($_GET['gigID'])){
            $gig->getSetPartsEmailed( $_GET, dirname(getcwd()));
        }
    }

    if ('getNotes'==$_GET['action']){
        echo $arrangement->getNewNoteForm($_GET);
        echo $arrangement->getEditNoteForm($_GET);
        
    }

    if ('getEfileParts'==$_GET['action']){
        if (isset($_GET['efileID']) && $_GET['efileID'] > 0){
            echo $arrangement->getPartForm($_GET['efileID']);
        }
        echo $arrangement->getEFileForm();
    }

    if ('listPdf'==$_GET['action']){
        echo $arrangement->listPdf();
        echo $arrangement->listMP3();
        echo $arrangement->getPublicationForm();
        echo $arrangement->getMP3Form();
        echo $arrangement->getURLForm();
        echo $arrangement->getUploadFileForm();
    }

    if ('getSetList'==$_GET['action']){
        if (isset($_REQUEST['gigID']) && $_REQUEST['gigID'] > 0){
            echo $gig->getGigSetForm($_REQUEST['gigID']);
        }
        echo $gig->getEditSetForm();
        echo $gig->getSetPartsForm();
        echo $gig->getCopySetForm();
        echo $gig->getNewSetListForm();
        echo $gig->getDeleteSetForm();
    }

    if ('getNewSetListForm'==$_GET['action']){
        echo $gig->getEditSetForm();
        echo $gig->getSetPartsForm();
        echo $gig->getCopySetForm();
        echo $gig->getNewSetListForm();
        echo $gig->getDeleteSetForm();
    }

    if ('getNewPersonForm'==$_GET['action']){
        echo $arrangement->getNewSongForm();
        echo $arrangement->getSongs();
        echo $arrangement->getNewPersonForm();
        echo $arrangement->getPeople();
        echo $user->getNewUserForm();
    }
    
    

}

} else{
    echo "<p><a href='../'>Index</a></p>";
}
?>
        </body>

</html>
