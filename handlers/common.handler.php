<?php
error_reporting(E_ALL);

define('APPLICATION', 'chat7'); //Allowed values: chat7, messenger7, roulette7, ppv

require_once "./module/integration.class.php";

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

if ( $application->_profileID != ""){	
    
    $application->init(APPLICATION);
	echo $application->getUserProfileXML();
	
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