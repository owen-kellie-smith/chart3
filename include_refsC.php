<?php 
require_once "vendor/autoload.php";
// alphabetical order as everything is called via indexC.php
define("ALL_USERS_ARE_ADMINS", "No");
//define("ALL_USERS_ARE_ADMINS", "All");
define ('SITE_ROOT', realpath(dirname(__FILE__)));
require_once "Arrangement.php";
require_once "Connection.php";
require_once "Gig.php";
require_once "myListAllPDF.php";
require_once "Render.php";
require_once "User.php";

