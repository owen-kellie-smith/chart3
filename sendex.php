<?php

require 'include_refsC.php';

$a = new Arrangement();
echo $a->sendAllParts($_GET['arrangementID']);
