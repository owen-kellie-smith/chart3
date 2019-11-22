<?php 
require_once "vendor/autoload.php";
// alphabetical order as everything is called via indexC.php
define("ALL_USERS_ARE_ADMINS", "No");
//define("ALL_USERS_ARE_ADMINS", "All");


define("EMAIL_FROM_ADDRESS","someone@somewhere.net");
define("EMAIL_FROM_ALT","Site domain");
define("E_KEY","SomeComplicatedKey123");

define("PDF_HEADER_MESSAGE","Some text to put at the top of each pdf");

define ('SITE_ROOT', realpath(dirname(__FILE__)));
require_once "Connection.php";
require_once "Gig.php";
require_once "myListAllPDF.php";
require_once "Render.php";
require_once "User.php";

require_once "Arrangement.php";
