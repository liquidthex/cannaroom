<?php
define('APPLICATION', 'vchat6'); //Allowed values: vchat6

//require_once '../../../common/server/php/settings.php';
//require_once FLASHCOMS_ROOT.'common/server/php/core/core.php';

require_once "./module/integration.class.php";


//debug line
//echo "auth: ".$application->authorization();

$application->authorization();
$application->init(APPLICATION);
echo $application->readyXml();

?>
