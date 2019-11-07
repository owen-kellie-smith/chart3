<?php

//echo __FILE__;

require_once "../Connection.php";
require_once "../Logo.php";

include_once "../include_refsC.php";

$user = new User();

if ($user->hasValidCookie()){
    $l = new Logo('../');
    if (isset($_GET['efileID'])){
        $l->getMP3($_GET['efileID']);
    } 
    echo "<p><a href='../'>Main menu</a></p>";
} else{
    echo "<p><a href='../'>Index</a></p>";
}
