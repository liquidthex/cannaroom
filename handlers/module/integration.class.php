<?php

//includes
require_once "cms.config.handler.php";
require_once "utils.class.php";
require_once "db.class.php";
require_once "encryption.class.php";
require_once "cms.class.php";
//includes end


class Integration extends Cms
{
	protected $_login;
	
	public $_profileID;
        
    protected $_userName;
	
	protected $_password;
	
	public $_uid;
	
	public $_user;
	
	public $_partner;
	
	public $_action;
	
	public $application;

	protected $showFriends = FALSE;
	
	protected $showBlocks = FALSE;
	
	
	public function __construct()
	{

		Db::dbConnect();
	}
	
	public function getUid()
	{
		var_dump($this->_uid);
	}
	
	public function setParam($param, $value)
	{
		
		switch ($param)
		{
			case "uid" : 
                if (defined('ENCRYPTION_STRING')) $this->_uid = trim(Utils::Decrypt($value)); 
                else $this->_uid = trim($value);
            break;
            
			case "login" : $this->_login = trim($value); break;
			
			case "password" : $this->_password = trim($value);
                        //debug line
                        //echo "password: ".$this->_password;
                        break;

			case "profileID" : $this->_profileID = trim($value); break;
			
			case "user" : $this->_user = trim($value); break;
            
            case "userName" : $this->_userName = trim($value); break;
			
			case "partner" : $this->_partner = trim($value); break;
			
			case "action" : $this->_action = trim($value); break;
		}
	}
	


	public function authorization()
	{
	   if ($this->_uid){
		  
            $query = $this->sqlQuery('AUTHORIZATION', 'AUTOLOGIN');
            //debug line
            //echo "query: ".$query;
		}
	    else if (($this->_login && $this->_password) || ($this->_userName && $this->_password)){
			$query = $this->sqlQuery('AUTHORIZATION', 'LOGIN');
            //debug line
            //echo "query: ".$query;
		}
         
        //the following is for roulette7         
        else if ($this->_action == "guestLogin")
        {
            $this->_uid = -1;
            
            return -1;
        }
			
		$Authorization = Db::dbGetRows($query);
		//debug line
        //var_dump($Authorization);
        
		if ($Authorization[0]["id"]){ 
            
			$this->_uid = $Authorization[0]["id"];
			return $this->_uid;
			
		}
		else 
		{
            
		  if ($this->_userName && !$this->_password)
          {
            
            $query = $this->sqlQuery('AUTHORIZATION', 'GUEST');
            $Authorization = Db::dbGetRows($query);
            
            //debug line
            //var_dump($Authorization[0]["id"]);
            if ($Authorization[0]["id"])
            {
               $this->_uid = 0;
			   return 0; 
            }
            else
            {
               $this->_uid = -1;
               return -1; 
            }
          }
          else
          {
            $this->_uid = 0;
			return 0;
          }	
		} 

	}
	
	
	public function actionOnUser($action, $uid, $who)
	{
		switch ($action)
		{
			case "ADD_FRIEND" : $this->addFriend($uid, $who); break;
			
			case "REMOVE_FRIEND" : $this->removeFriend($uid, $who); break;
			
			case "ADD_BLOCK" : $this->addBlock($uid, $who); break;
			
			case "REMOVE_BLOCK" : $this->removeBlock($uid, $who); break;
		}
	}
	
    public function readyXml()
    {
        switch ( $this->application )
            {
	
    		case "chat7" : return $this->readyXmlChat7($this->_uid); break;
    		
    		case "messenger7" : return $this->readyXmlMessenger7($this->_uid); break;
            
            case "ppv" : return $this->readyXmlPPV($this->_uid); break;
        
            case "roulette7" : return $this->readyXmlRoulette7($this->_uid); break; 
            
            case "vchat6" : return $this->readyXmlVchat6($this->_uid); break;   
	       }
    }
	
	public function readyXmlChat7($uid)
	{
		/* PRINT XML FOR CHAT 7 */

		if ( $this->application == "chat7" ){
		
				$answer = 
					'<login result="OK">';
				
                $answer .= 
					$this->createXml($this->getUserInfoChat7($uid, "", ""));
						
				if (empty($this->_profileID))
				{
				/* PRINT FRIENDS */		
				$answer .= 
					'<friends>';
				
				if ( $this->showFriends == TRUE ){
					
					$friends = $this->getFriends($uid);
						
                    //debug line
                    //var_dump($friends);
                        
					if ( is_array($friends) ){
						
						foreach ( $friends as $friend){
							
							if ( $friend["partner"] != "" ){
								
									$answer .= $this->createXml($this->getUserInfoChat7($friend["partner"], "1", ""));
							}
						} 
					}
				}
				
				$answer .= 
					'</friends>';
						
					
				/* PRINT BLOCKS */
				$answer .= 
					'<blocks>';
				
				if ( $this->showBlocks == TRUE ){
				
					$blocks = $this->getBlocks($uid);
					
					if ( is_array($blocks) ){
					
						foreach ( $blocks as $block ){
							
							if ( $block["partner"] != "" ){
						
								$answer .= $this->createXml($this->getUserInfoChat7($block["partner"], "", "1"));
							}
						}
					} 
				}
				$answer .= 
					'</blocks>';	
				
				}
				$answer .= 
					'</login>';
				
			return $answer;	
		}
		
	}
	
	
	public function readyXmlMessenger7($uid)
	{
		

		/* PRINT XML FOR MESSENGER 7 */

		if ( $this->application == "messenger7" ){
		
				$answer = 
					'<auth result="OK">';
					
				$answer .= 
					$this->createXml($this->getUserInfoMess7($uid, "", ""));
					
				
				$answer .= 
					'<friends>';
				
				/* PRINT FRIENDS */
				
				if ( $this->showFriends == TRUE )
                {
				
					//$this->getFriends($uid);
					
					$friends = $this->getFriends($uid);
					
					if ( is_array($friends) ){
					
						foreach ( $friends as $friend ){
							
							if ( $friend["partner"] != "" ){
						
								$answer .= $this->createXml($this->getUserInfoMess7($friend["partner"], 1, ""));
							}
						}
					} 
					
					
				}
					
				$answer .= 
					'</friends>';
					
					
				$answer .= 
					'<blocks>';
				
				/* PRINT BLOCKS */
				
				if ( $this->showBlocks == TRUE )
                {

					$blocks = $this->getBlocks($uid);
					
					if ( is_array($blocks) ){
					
						foreach ( $blocks as $block ){
							
							if ( $block["partner"] != "" ){
						
								$answer .= '<id>'.$block["partner"].'</id>';
							}
						
						}
					} 
					

				}
					
				$answer .= 
					'</blocks>';
						
					
					
				$answer .= 
					'</auth>';

		}
		
		return $answer;
		
	}
    
    public function readyXmlPPV($uid)
	{
		/* PRINT XML FOR PPV */
		
		if ( $this->application == "ppv" )
        {

				$answer = '';
				
                $answer .= $this->createXml($this->getUserInfoPPV($uid, "", ""));
				
			return $answer;	
		}
		
	}
	
  	public function readyXmlRoulette7($uid)
	{
		/* PRINT XML FOR ROULETTE 7 */
		
		if ( $this->application == "roulette7" )
        {
		  
                if ($this->_action == "autoLogin" && $this->_uid)
                {
                    $mode = "autoLogin";
                }
                else
                {
                    $mode = "guestLogin";
                }
		
				$answer = 
					'<'.$mode.' result="OK">';
				
                
                if ($this->_action == "guestLogin")
                {
                    $answer .= 
					   $this->createXml($this->getUserInfoRoulette7("-1", "", ""));
                }
                else
                {
                    $answer .= 
					   $this->createXml($this->getUserInfoRoulette7($uid, "", ""));
                }
                
				
				$answer .= 
					'</'.$mode.'>';
				
			return $answer;	
		}
		
	}
    
    public function readyXmlVchat6($uid)
	{
		/* PRINT XML FOR VCHAT 6 */
		
		if ( $this->application == "vchat6" )
        {

            $answer = $this->createXml($this->getUserInfoVchat6($uid));
            
			return $answer;	
		}
		
	}
	
	protected function createXml($data)
	{
		
		foreach ($data as $colum => $value){
		
			if ($colum == "DataBegin") {
				$xml = $data["DataBegin"];
				continue;
			}	
			
			else if ($colum == "DataEnd") {
				$xml .= $data["DataEnd"];
				continue;
			}
			
			if ( $colum === "ADMIN_MODER_CHAT" && $value != ""){
				$xml .= $value; 
				continue;
			}
			
			if ( $colum === "ADMIN_MODER_CHAT" && $value == "")
				continue;
				
			
			$xml .= '<'.$colum.'><![CDATA[' . $value . ']]></'.$colum.'>' ;
			

		}
		return $xml;
	}
	
};




class Application extends Integration
{
	
	public function init($application = "chat7", $friends = 0, $blocks = 0)
	{
		
		$this->application = $application;
		
		if ( $friends == 1 ){
			$this->showFriends = TRUE;
		}
		
		if ( $blocks == 1){
			$this->showBlocks = TRUE;
		}
	
	} 
	
	public function error($application = "")
	{
		
		switch ( $application ){
			case "chat7" : 		return '<login result="FAIL" error="error_authentication_failed"/>'; break; 
			case "messenger7" : return '<auth result="FAIL" error="error_authentication_failed"/>'; break; 
			case "roulette7" : 	return '<autologin result="FAIL" error="error_authentication_failed"/>'; break; 
            case "ppv" : 
          		
                if (($this->_action == 'OPERATOR_LOGIN') || ($this->_action == 'OPERATOR_AUTOLOGIN'))
                    return '<operatorLogin result="ERROR"/>'; 
                if (($this->_action == 'CLIENT_LOGIN') || ($this->_action == 'CLIENT_AUTOLOGIN'))
                    return '<client>null</client>'; 
          
            break; 
            case "vchat6" : 	return '<auth error="AUTH_ERROR" />'; break; 
			default : 			return "<invalid_params />";
		}
	}
	
	public function action()
	{
		switch ( $this->_action )
		{
			case "addFriend" : $this->addFriend($this->_user, $this->_partner); break;
			case "removeFriend" : $this->removeFriend($this->_user, $this->_partner); break;
			case "block" : $this->addBlock($this->_user, $this->_partner); break;
			case "unblock" : $this->removeBlock($this->_user, $this->_partner); break;
		}
		
		
	}
	
	public function getUserProfileXML()
	{
		return $this->readyXmlChat7($this->_profileID);
	}

};



header('Content-type: text/xml');

$application = new Application();



if ( $_REQUEST['uid'] ){
    
    if ($_REQUEST['uid'] == 'undefined' || $_REQUEST['uid'] == 'Test')
    {
        session_start();
        $application->setParam("uid", Utils::EscapeInjection($_SESSION['Zend_Horiconnect']['idHC']));
    }
    else
	$application->setParam("uid", Utils::EscapeInjection($_REQUEST['uid']));
}

if ( $_REQUEST['login'] ){
	$application->setParam("login", Utils::EscapeInjection($_REQUEST['login']));
}

if ( $_REQUEST['profileID'] ){
	$application->setParam("profileID", Utils::EscapeInjection($_REQUEST['profileID']));
}

if ( $_REQUEST['userName'] ){
	$application->setParam("userName", Utils::EscapeInjection($_REQUEST['userName']));
}

if ( $_REQUEST['user_name'] ){
	$application->setParam("userName", Utils::EscapeInjection($_REQUEST['user_name']));
    $application->setParam("login", Utils::EscapeInjection($_REQUEST['user_name']));
}

if ( $_REQUEST['pass'] ){
	$application->setParam("password", Utils::EscapeInjection($_REQUEST['pass'], true));
}

if ( $_REQUEST['password'] ){
	$application->setParam("password", Utils::EscapeInjection($_REQUEST['password'], true));
}

if ( $_REQUEST['user'] ){
	$application->setParam("user", Utils::EscapeInjection($_REQUEST['user']));
}

if ( $_REQUEST['partner'] ){
	$application->setParam("partner", Utils::EscapeInjection($_REQUEST['partner']));
}

if ( $_REQUEST['action'] ){
	$application->setParam("action", Utils::EscapeInjection($_REQUEST['action']));
}


?>
