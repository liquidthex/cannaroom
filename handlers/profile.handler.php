<?php

define('APPLICATION', 'messenger7'); //Allowed values: chat7, messenger7

require_once $_SERVER['DOCUMENT_ROOT']."/Live-Chat/handlers/module/integration.class.php";

if ( $application->_action != "" && $application->_user != "" && $application->_partner !=""){
	
	
	/* Action : 
	@ addFriend
	@ removeFriend
	@ addBlock
	@ removeBlock
	*/
	
	$application->action();
	
	exit;
}

//debug line
//echo "auth: ".$application->authorization();

switch ($application->authorization())
{
    case "-1":
        $application->init(APPLICATION);
        echo $application->readyXml();
        break;
    case "0":
        $application->init(APPLICATION);
        echo $application->error(APPLICATION);    
        break;
    default:
        $application->init(APPLICATION, $friends = 1, $blocks = 1);
        echo $application->readyXml();            
        break;                        
    
}


?>