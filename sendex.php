<?php

require 'include_refsC.php';

$a = new Arrangement();
echo $a->getArrangementLabel($_GET['arrangementID']). "\r\n <br/>";
echo $a->sendAllParts($_GET['arrangementID']);
