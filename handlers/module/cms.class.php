<?php

class Cms
{

	protected function sqlQuery($queryType = "", $action = "", $uid = "")
	{

		if ($queryType == 'AUTHORIZATION'){
		
			//SQL for Authorization
			$sqlAuthorization = SQL_AUTHORIZATION;

			switch ( $action ){
			
				case "AUTOLOGIN" 	: 	//$sqlWhereAuthorization = SQL_USERID." = '".$this->_uid."'";
                                        $sqlAuthorization = str_replace(array('[condition]','[uid]'), 
                                                                        array(SQL_AUTOLOGIN_CASE,$this->_uid), 
                                                                        SQL_AUTHORIZATION);
                                        break;
				
				case "LOGIN" 		: 	//$userSalt = Db::dbGetRows($sqlAuthorization.SQL_USERNAME." = '".$this->_login."'");
                                        $userSalt = Db::dbGetRows(str_replace(array('[condition]','[login]'), 
                                                                              array(SQL_GUEST_CASE,$this->_login), 
                                                                              SQL_AUTHORIZATION));
                                        //debug line
                                        //var_dump(str_replace(array('[condition]','[login]'), array(SQL_GUEST_CASE,$this->_login), SQL_AUTHORIZATION));
                                        //var_dump($userSalt);                    
                                       
                                       switch (CMS) {
                                                        case 'VBulletin':
                                                            $finalPassword = Encryption::VBulletin($this->_password, $userSalt[0]['salt']);
                                                            break;
                                                        case 'Joomla':
                                                            $finalPassword = Encryption::Joomla($this->_password, $userSalt[0]['password']);
                                                            break; 
                                                        case 'SKADate':
                                                            $finalPassword = Encryption::SKADate($this->_password);
                                                            break;  
                                                        case 'SocialEngine':
                                                            $finalPassword = Encryption::SocialEngine($this->_password, $userSalt[0]['salt']);
                                                            break; 
                                                        case 'WordPress':
                                                            $finalPassword = Encryption::WordPress($this->_password, $userSalt[0]['password']);
                                                            break;
                                                        case 'PHPBB':
                                                            $finalPassword = Encryption::PHPBB($this->_password, $userSalt[0]['password']);
                                                            break;
                                                        case 'PGDatingPro':
                                                            $finalPassword = md5($this->_password);
                                                            break; 
														case 'XenForo':
                                                            $finalPassword = Encryption::XenForo($this->_password, $userSalt[0]['password']);
                                                            break;
                                                        case 'Drupal6':
                                                            $finalPassword = md5($this->_password);
                                                            break;        
                                                        case 'Drupal7':
                                                            $finalPassword = Encryption::Drupal7($this->_password, $userSalt[0]['password']);
                                                            break;  
                                                        case 'Invision Power Services':
                                                            $finalPassword = Encryption::Invision_Power_Services($this->_password, $userSalt[0]['salt']);
                                                            break;
                                                        case 'PHPProLance':
                                                            $finalPassword = Encryption::PHPProLance($this->_password, $userSalt[0]['salt']);
                                                            break;
                                                        case 'Concrete5':
                                                            // config/site.php: define('PASSWORD_SALT', 'bMg33SRZrRIjZ8KAClxh5WtghmOrtgiM7HfAqz76Qs4kW6K5XJOBGFBESYyeYq18');
                                                            $finalPassword = Encryption::Concrete5($this->_password);
                                                            break;  
                                                        case 'PHPFox':
                                                            $finalPassword = Encryption::PHPFox($this->_password, $userSalt[0]['salt']);
                                                            break;    
                                                        case 'osDate':
                                                            $finalPassword = md5($this->_password);
                                                            break;    
                                                        case 'erotik-real.eu':
                                                            $finalPassword = hash('sha256', trim($this->_password));
                                                            break;        
                                                        default:
                                                            $finalPassword = md5($this->_password);
                                                            break;   
                                                    }
                                        //debug line
                                        //var_dump($finalPassword);
                                                    
                                       if (NO_PASSWORD_ENCRYPTION)
                                       {
                                            $finalPassword = $this->_password;
                                       }
										                                        
                                        //$sqlWhereAuthorization = SQL_USERNAME." = '".$this->_login."' AND ".SQL_USER_PASSWORD." = '".$finalPassword."'"; 
                                        $sqlAuthorization = str_replace(array('[condition]','[login]','[password]'), 
                                                                        array(SQL_LOGIN_CASE, $this->_login, $finalPassword), 
                                                                        SQL_AUTHORIZATION);
                                                                        
                                        break;
				
				case "GUEST" 		: 	//$sqlWhereAuthorization = SQL_USERNAME." = '".$this->_login."'";
                                        $sqlAuthorization = str_replace(array('[condition]','[login]'), 
                                                                        array(SQL_GUEST_CASE, $this->_userName), 
                                                                        SQL_AUTHORIZATION);
                                        break;
			}	
			
			//$returnSql = $sqlAuthorization.$sqlWhereAuthorization;
            $returnSql = $sqlAuthorization;
			
		}
		
		else if ( $queryType == 'PROFILE' ){
			//SQL for Profile
            $sqlProfile = str_replace('[uid]', $uid, SQL_PROFILE);
		    //debug line
            //var_dump($sqlProfile);
			$returnSql = $sqlProfile;
		}
		
		
		else if ( $queryType == 'FRIENDS' ){
            $sqlFriends = str_replace('[uid]', $uid, SQL_FRIENDS);
			//debug line
            //var_dump(str_replace('[uid]', $uid, SQL_FRIENDS));
			$returnSql = $sqlFriends;
		}

		
		else if ( $queryType == 'BLOCKS' ){
            $sqlBlocks = str_replace('[uid]', $uid, SQL_BLOCKS);		
			$returnSql = $sqlBlocks;
		}
		
		else if ( $queryType == 'GET_ID_BY_LOGIN' ){
            $sql = str_replace('[uid]', $uid, SQL_GET_ID_BY_LOGIN);	
			$returnSql = $sql;
		}
		
		else if ( $queryType == 'GET_LOGIN_BY_ID' ){
			$sql = str_replace('[uid]', $uid, SQL_GET_ID_BY_LOGIN);
			$returnSql = $sql;
		}
		
		return $returnSql;
	
	}
	
    public function getUserInfoChat7($uid, $friends = "", $blocks = "")
	{

        $userInfo = array();
        if ($uid != -1)
            {
                $userInfo = Db::dbGetRows($this->sqlQuery('PROFILE', '', $uid));
                //debug line
                //var_dump($this->sqlQuery('PROFILE', '', $uid));
            }
        else
            {
                 return $DataForXml = array(
                                            "DataBegin" => 		"<userData>",
                            				"id" => 			md5($userName.time()),
                            				"name" => 			$_REQUEST['userName'],
                            				"gender" => 		$_REQUEST['gender'],
                            				"location" => 		DEFAULT_EMPTY_VALUE,
                            				"age" => 			DEFAULT_EMPTY_VALUE,
                            				"photo" => 			NO_PHOTO,
                            				"thumbnail" => 		NO_PHOTO,
                            				"details" => 		"",
                            				"level" => 			APPLICATION_GUEST_LEVEL,
                                            "profileUrl" =>     "",
                            			    "DataEnd" => 		"</userData>"	
                                           );
            }
        
            //debug line
            //var_dump($userInfo);
        
        $this->getProfileRows($userInfo);
        
        /** XML FOR CHAT 7 */
		if ( $this->application == "chat7" ){
		
			if ( $friends != "" ) $groupData = "friend";
			else if ( $blocks != "" ) $groupData = "block";
			else $groupData = "userData";
		
			$DataForXml = array(
				"DataBegin" => 		'<'.$groupData.'>',
				"id" => 			$userInfo[0]["id"],
				"name" => 			$this->getName($userInfo[0]["login"]),
				"gender" => 		$this->getGender($userInfo[0]["gender"]),
				"location" => 		$this->getLocation($userInfo[0]["country"], $userInfo[0]["state"], $userInfo[0]["city"]),
				"age" => 			$this->getAge($userInfo[0]["birthday"], "YMD", $userInfo),
				"photo" => 			$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
				"thumbnail" => 		$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
				"details" => 		$this->getDetails($userInfo[0]["description"]),
				"level" => 			$this->getLevel($userInfo[0]["lvl"]),
			    "ADMIN_MODER_CHAT"=> $this->getAdminORModerator($userInfo, ADMINSTRATORS_ARRAY, MODERATORS_ARRAY),
                "profileUrl" =>     str_replace(array('[id]','[login]'),array($userInfo[0]["id"], $userInfo[0]["login"]),SQL_PROFILE_URL),
				"DataEnd" => 		'</'.$groupData.'>'	
			);
			
			return $DataForXml;

		}

	}
	
	
	
	public function getUserInfoMess7($uid, $friends = "", $blocks = "")
	{

		$userInfo = array();
		
		if ($uid != -1)
            {
                $userInfo = Db::dbGetRows($this->sqlQuery('PROFILE', '', $uid));
            }
        else
            {
                $userID = md5($userName.time());
                 return $DataForXml = array(
                                            "DataBegin" => 		"<user>",
                                        	"uid" => 			$userID,
                                         	"userID" => 		$userID,
                                        	"userName" => 		$_REQUEST['userName'],
                                        	"smallAvatar" => 	NO_PHOTO,
                                        	"bigAvatar" => 		NO_PHOTO,
                                        	"age" => 			"unknown",
                                        	"gender" => 		$_REQUEST['gender'],
                                            "country" => 		"unknown",
                                        	"about" => 			"",
                                        	"xStatus" => 		"",
                                        	"level" => 			APPLICATION_GUEST_LEVEL,
                                        	"DataEnd" => 		"</user>"
                                           );
                                           
            }
        
        $this->getProfileRows($userInfo);
        
        //debug line
        //var_dump($this->application);
        
		/** XML FOR MESSENGER 7 */
		if ( $this->application == "messenger7" && $friends == "" && $blocks == ""  ){
		
			$DataForXml = array(
			    "DataBegin" => 		'<user>',
				"uid" => 			$userInfo[0]["id"],
			    "userID" => 		$userInfo[0]["id"],
				"userName" => 		$this->getName($userInfo[0]["login"]),
				"smallAvatar" => 	$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
				"bigAvatar" => 		$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
				"age" => 			$this->getAge($userInfo[0]["birthday"], "YMD"),
				"gender" => 		$this->getGender($userInfo[0]["gender"]),
                "country" => 		$this->getLocation($userInfo[0]["country"], $userInfo[0]["state"], $userInfo[0]["city"]),
				"about" => 			$this->getDetails($userInfo[0]["description"]),
				"xStatus" => 		$this->getStatus(""),
				"level" => 			$this->getLevel($userInfo[0]["lvl"]),
				"DataEnd" => 		'</user>'	
			);
			
            //debug line
            //var_dump($DataForXml);
			return $DataForXml;

		}
		
		else if ( $friends == 1 ){
		
			$DataForXml = array(
			    "DataBegin" => 		'<friend>',
				"userID" => 		$userInfo[0]["id"],
				"userName" => 		$this->getName($userInfo[0]["login"]),
				"smallAvatar" => 	$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["gender"]),
                "xStatus" => 		$this->getStatus(""),
				"lastOnlineTime" => "",
				"DataEnd" => 		'</friend>'	
			);
			
			return $DataForXml;
		
		}
		
		else if ( $blocks == 1){
		
		}
		
	}

    public function getUserInfoPPV($uid)
	{
        
        $userInfo = array();
		$userInfo = Db::dbGetRows($this->sqlQuery('PROFILE', '', $uid));
            //debug line
            //var_dump($userInfo);
            
        $this->getProfileRows($userInfo);
        
        /** XML FOR PPV */
		if ( $this->application == "ppv" ){
		
           //debug line
           //var_dump($this->_action); 
           
		    if (($this->_action == 'OPERATOR_LOGIN') || ($this->_action == 'OPERATOR_AUTOLOGIN'))
            {
                //debug line
                //var_dump(OPEARTORS_ARRAY); 
                if ($this->getOperator($userInfo, OPEARTORS_ARRAY))
                {
                $DataForXml = array(
			            "DataBegin" => 		'<operatorLogin result="OK" login="'.$this->getName($userInfo[0]["login"]).'" additionalID="'.$userInfo[0]["id"].'"><op>',
				        "baseID" => 		$userInfo[0]["id"],
				        "login" => 			$this->getName($userInfo[0]["login"]),
                        "pass" => 			$this->getName($userInfo[0]["password"]),
                        "age" => 			$this->getAge($userInfo[0]["birthday"], "YMD"),
        				"gender" => 		$this->getGender($userInfo[0]["gender"]),
                        "photo" => 			$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], ""),
                        "groups" => 		$this->getLevel($userInfo[0]["lvl"]),
                        "avatarID" => 		NULL,
                        "sexuality" => 		NULL,
                        "price" => 		    $this->getPricePerMinute($userInfo[0]["priceperminute"]),
                        "location" => 		$this->getLocation($userInfo[0]["city"]),
                        "country" => 		$this->getLocation($userInfo[0]["country"]),
                        "multiprivate" => 	'true',
				        "moreInfo" => 		$this->getDetails($userInfo[0]["description"]),
				        "DataEnd" => 		'</op></operatorLogin>'	
				        );
                }
                else
                {
                    $DataForXml = array("DataBegin" => '<operatorLogin result="ERROR"/>');
                }
            }
            
            if (($this->_action == 'CLIENT_LOGIN') || ($this->_action == 'CLIENT_AUTOLOGIN'))
            {
                if ($this->getVisitor($userInfo, VISITORS_ARRAY))
                {
                $DataForXml = array(
    				"DataBegin" => 		'<client>',
    				"id" => 		    $userInfo[0]["id"],
    				"login" => 			$this->getName($userInfo[0]["login"]),
                    "pass" => 			$this->getName($userInfo[0]["password"]),
                    "type" => 			'visitor',
    				"credits" => 		$this->getCredits($userInfo[0]["credits"]),
    				"DataEnd" => 		'</client>'	
			        );
                }
                else
                $DataForXml = array("DataBegin" => '<client>null</client>');
            }
			return $DataForXml;
		}
	}
    
        public function getUserInfoRoulette7($uid, $friends = "", $blocks = "")
	{
        $userInfo = array();
		
        if ($uid != -1)
            {
                $userInfo = Db::dbGetRows($this->sqlQuery('PROFILE', '', $uid));
                
                //debug line
                //var_dump($this->sqlQuery('PROFILE', '', $uid));
            }
        else
            {
                 return $DataForXml = array(
                                            "DataBegin" => 		"<userData>",
                            				"id" => 			md5($userName.time()),
                            				"name" => 			md5($userName.time()),
                            				"gender" => 		$_REQUEST['gender'],
                            				"location" => 		"unknown",
                            				"age" => 			"unknown",
                            				"photo" => 			NO_PHOTO,
                            				"thumbnail" => 		NO_PHOTO,
                            				"details" => 		"",
                            				"level" => 			"guest",
                            			    "DataEnd" => 		"</userData>"	
                                           );
            }
        
            //debug line
            //var_dump($userInfo);
            
        $this->getProfileRows($userInfo);
        
		/** XML FOR ROULETTE7 */
		if ( $this->application == "roulette7" ){
		
			if ( $friends != "" ) $groupData = "friend";
			else if ( $blocks != "" ) $groupData = "block";
			else $groupData = "userData";
			
			
			$DataForXml = array(
				"DataBegin" => 		'<'.$groupData.'>',
				"id" => 			$userInfo[0]["id"],
				"name" => 			$this->getName($userInfo[0]["login"]),
				"gender" => 		$this->getGender($userInfo[0]["gender"]),
				"location" => 		$this->getLocation($userInfo[0]["country"], $userInfo[0]["state"], $userInfo[0]["city"]),
				"age" => 			$this->getAge($userInfo[0]["birthday"], "YMD"),
				"photo" => 			$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["gender"]),
				"thumbnail" => 		$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["gender"]),
				"details" => 		$this->getDetails($userInfo[0]["description"]),
				"level" => 			'1',
				"DataEnd" => 		'</'.$groupData.'>'	
				
			);
			
			return $DataForXml;

		}

	}
    
    public function getUserInfoVchat6($uid)
	{
        /** XMLS FOR CHAT 6 */
        $userInfo = array();

                if ($uid != -1) 
                {
                    $userInfo = Db::dbGetRows($this->sqlQuery('PROFILE', '', $uid));
                    //debug line
                    //var_dump($this->sqlQuery('PROFILE', '', $uid));
                    $this->getProfileRows($userInfo);
                }
                
                switch($this->_action)
                {
                    case 'auth': 
                        $groupData = "auth";
                        
                        //debug line
                        //var_dump($uid);
		                if ($uid == 0)
                        { 
                            $DataForXml = array("DataBegin" => '<auth error="AUTH_ERROR" />');
                        }
                        else
                        {  
                			$DataForXml = array(
                                				"DataBegin" => 		'<'.$groupData.'>',
                                				"userName" => 		$this->getName($userInfo[0]["login"]),
                                				"gender" => 		$this->getGender($userInfo[0]["gender"]),
                                                "level" => 			$this->getLevel($userInfo[0]["lvl"]),
                                				"photo" => 			$this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
                                				"photoModeImage" => $this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
                                				"DataEnd" => 		'</'.$groupData.'>'	
                                			);
                        }
                    break;
                    
                    case 'check_guest_name':
                        $invalidNames = array('SYSTEM', 'ADMIN', 'ADMINISTRATOR', 'MODERATOR', 'ROBOT');
                    	$isInvalid = in_array(strtoupper($userName), $invalidNames); 
                    
                        $groupData = "checkGuestName";
                        
                    	if($uid != -1) $DataForXml = array("DataBegin" => 		'<'.$groupData.' error="AUTH_ERROR" />');
                                                   
                    	if($isInvalid) $DataForXml = array("DataBegin" => '<'.$groupData.' error="ADMIN_NEED_PASSWORD" />');
                        else $DataForXml = array(
                                                    "DataBegin" => 		'<'.$groupData.'>',
                                    				"photo" => 		    NO_PHOTO_6,
                                    				"photoModeImage" => NO_PHOTO_6,
                                                    "DataEnd" => 		'</'.$groupData.'>'	
                                                   );
                    break;
                    
                	case 'get_user_photo': 
                        $query = $this->sqlQuery('AUTHORIZATION', 'GUEST');
                        $Authorization = Db::dbGetRows($query);
                        $uid = $Authorization[0]["id"];
                        
                        $userInfo = Db::dbGetRows($this->sqlQuery('PROFILE', '', $uid));
                        //debug line
                        //var_dump($this->sqlQuery('PROFILE', '', $uid));
                        $this->getProfileRows($userInfo);
                    
                        $groupData = "userPhoto";
                        $DataForXml = array(
                                                    "DataBegin" => 		'<'.$groupData.'>',
                                    				"photo" => 		    $this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
                                    				"photoModeImage" => $this->getPhoto($userInfo[0]["photo"], PHOTO_IS_FILE, $userInfo[0]["id"], $userInfo[0]["login"], $userInfo[0]["index"]),
                                                    "DataEnd" => 		'</'.$groupData.'>'	
                                                   );
                    break;
                    
                    case 'profile': 
                        $query = $this->sqlQuery('AUTHORIZATION', 'GUEST');
                        $Authorization = Db::dbGetRows($query);
                        $uid = $Authorization[0]["id"];
                        
                        $userInfo = Db::dbGetRows($this->sqlQuery('PROFILE', '', $uid));
                        //debug line
                        //var_dump($this->sqlQuery('PROFILE', '', $uid));
                        $this->getProfileRows($userInfo);
                        
                        $groupData = "profile";
                        $DataForXml = array(
                            				"DataBegin" => 		'<'.$groupData.' photoWidth="120" photoHeight="120">',
                            				"name" => 		    $this->getName($userInfo[0]["login"]),
                                            "age" => 		    $this->getAge($userInfo[0]["birthday"], "YMD"),
                            				"gender" => 		$this->getGender($userInfo[0]["gender"]),
                                            "country" => 		$this->getLocation($userInfo[0]["country"], "", ""),
                                            "city" => 		    $this->getLocation("", "", $userInfo[0]["city"]),
                                            "info" => 		    $this->getDetails($userInfo[0]["description"]),
                                     		"DataEnd" => 		'</'.$groupData.'>'
                                            );
                    break;
                    
                    case 'logout': 
                        $DataForXml = array("DataBegin" => '<logout />');
                    break;
                }
            
            //debug line
            //var_dump($DataForXml);

        return $DataForXml;
	}
    
    protected function getProfileRows(&$userInfo)
	{
        if (SQL_PROFILE_ROWS != NULL)
        {
            
           $sql_profile_rows = explode(", ", SQL_PROFILE_ROWS);
           $sql_profile_rows = str_replace(" ", "",$sql_profile_rows); 
           
           //debug line
           //var_dump($sql_profile_rows);
           
           unset ($val_rows);
           
           foreach ($sql_profile_rows as $row)
           {
             $val = explode(":", $row);
             $val_rows[$val[0]][] = $val[1]; 
           }  
             //debug line
             //var_dump($val_rows);
           
           for ($i = 0; $i < count($userInfo); $i++)
           {
                //debug line
                //var_dump($userInfo[$i]);
                for ($j=0; $j < count($val_rows[$userInfo[$i]['val_id']]); $j++)
                {
                    $val_name = $val_rows[$userInfo[$i]['val_id']][$j];
                    $userInfo[0][$val_name] = $userInfo[$i]['val'];
                }
           } 
          
           //debug line
           //var_dump($userInfo);
           //var_dump($userInfo[0]);
            
        }
	}
	
	protected function getFriends($uid)
	{

        //debug line
        //var_dump($this->sqlQuery('FRIENDS', '', $uid));
        
		$userFriends = Db::dbGetRows($this->sqlQuery('FRIENDS', '', $uid));
		
        sort($userFriends);
        
        for($i=1;$i<count($userFriends);$i++)
        {
            if($userFriends[$i] == $userFriends[$i-1])
            unset($userFriends[$i]);
        }
        //debug line
        //var_dump($userFriends);
		/* return array where array = $friend["partner"] */
		
		return $userFriends;
	}
	

	protected function getBlocks($uid)
	{
		
		$userBlocks = Db::dbGetRows($this->sqlQuery('BLOCKS', '', $uid));
		        
        sort($userBlocks);
        
        for($i=1;$i<count($userBlocks);$i++)
        {
            if($userBlocks[$i] == $userBlocks[$i-1])
            unset($userBlocks[$i]);
        }
		/* return array where array = $block["partner"] */
		
		return $userBlocks;
		
	}
	
	/*
	* FUNCTION FOR HANDLER DATA 
	*/
	
	protected function chars_convert($string)
    {
        if (function_exists('mb_convert_encoding'))
        {
            $out = mb_convert_encoding($string, mb_detect_encoding($string), "UTF-8"); 
        }
        else
        {
            $out = $string;
        }
        
       return $out;            
    }  
      
	protected function getName($name)
	{       
	   //debug line
       //var_dump($name);
	   return $this->chars_convert($name);
	}
    
    protected function getCredits($credits)
	{       
	   return $credits;
	}
    
    protected function getPricePerMinute($price)
	{       
	   if (empty($price)) $price = PPV_DEFAULT_PRICE_PER_MINUTE;
	   return $price;
	}
	
	protected function getAdminORModerator($userInfo, $admins = "", $moders = "")
	{
            switch(CMS)
            {
                case "WordPress":
                	break;
				
				 case "XenForo": 
				 	if ($userInfo[0]['is_admin'] == 1) return '<isAdmin>true</isAdmin>';
					elseif ($userInfo[0]['is_moderator'] == 1) return '<isAdmin>true</isAdmin>';
				 	break;
					
                case "Joomla":
                case "PGDatingPro":
                case "SocialEngine":
                     $userInfo[0]["usergroupid"] = $userInfo[0]["lvl"];
                    break;
					
				case "SKADate": 
				
					$count = 0;
       				$count = Db::dbSQL_action(SQL_SELECT_ADMINS, array('[login]'), array($userInfo[0]["login"]));
			
					if ($count > 0)
						return '<isAdmin>true</isAdmin>';
					else return '';	
            }

            $admins = explode(", ", $admins);
            $moders = explode(", ", $moders);
                        
            $type = $userInfo[0]["usergroupid"];
                        		
			if ( $type != "" ){
			
				foreach ( $admins as $column){
					if ( $column == $type){
						return "<isAdmin>true</isAdmin>";
						break;
					 }
				}
				
				foreach ( $moders as $column ){
					if ( $column == $type){
						return "<isModerator>true</isModerator>";
						break;
					 }
				}
				
			}
	}
    
    protected function getOperator($userInfo, $operators = "")
	{
			
            $operators = explode(", ", $operators);
            //debug lime
            //var_dump($operators);
                        
            $type = $userInfo[0]["usergroupid"];

				foreach ($operators as $column)
                {
					if ($column == $type) return true;
                }  
                return false;  
	}
    
    protected function getVisitor($userInfo, $visitors = "")
	{
			
            $visitors = explode(", ", $visitors);
            //debug lime
            //var_dump($operators);
                        
            $type = $userInfo[0]["usergroupid"];

				foreach ($visitors as $column)
                {
					if ($column == $type) return true;
                }  
                return false;  
	}
	
	protected function getAge($birthday, $typeDate="YMD", $userInfo = NULL)
	{
	   if (SEPARATE_AGES_FOR_GENDERS)
       {
           foreach ($userInfo as $line)
           {
             $age[] = $line["age"];
           }
       }
       else
	   $age = $userInfo[0]["age"];
	   //debug line
       //var_dump($age);
       
       if (CMS == "WordPress")
       {
           $age = date('Y') - date('Y', $birthday);
       }

	   if (empty($age))
       {
    	   if (CMS == 'Drupal6')
           {
            preg_match_all("/\"(.+)\"/sUi", $birthday, $matches);
            //debug line
            //var_dump($matches);        
            $typeDate="DMY";
            $birthday = $matches[1][1].'-'.$matches[1][3].'-'.$matches[1][5];
           }
    	   
           //debug line
           //var_dump($birthday);
            
    	   if (CMS == 'PHPBB')
            preg_match_all("/(.{4}-.{1,2}-.{1,2})|(.{1,2}-.{1,2}-.{4})/", $birthday, $matches);
           else
    	    preg_match_all("/(\d{4}-\d{1,2}-\d{1,2})|(\d{1,2}-\d{1,2}-\d{4})/", $birthday, $matches);
            
            if ($matches[1][0]) {$typeDate="YMD"; define("BIRTH_SEPARATOR", "-");} 
            if ($matches[2][0]) {$typeDate="DMY"; define("BIRTH_SEPARATOR", "-");}
            
            if (!$matches[1][0] && !$matches[2][0])        
            preg_match_all("/(\d{4}\/\d{1,2}\/\d{1,2})|(\d{1,2}\/\d{1,2}\/\d{4})/", $birthday, $matches);
            
            if ($matches[1][0]) {$typeDate="YMD"; define("BIRTH_SEPARATOR", "/");}
            if ($matches[2][0]) {$typeDate="DMY"; define("BIRTH_SEPARATOR", "/");}
           
           //debug line
           //var_dump($matches);
           
    		if (!empty($birthday) && $birthday!="NULL")
    		{
    					switch ($typeDate){
    						case "YMD" : list($year,$month,$day) = explode(BIRTH_SEPARATOR,$birthday); break;
    						case "DMY" : list($day, $month, $year) = explode(BIRTH_SEPARATOR,$birthday); break;
    						case "MDY" : list($month, $day, $year) = explode(BIRTH_SEPARATOR,$birthday); break;
    						case "YDM" : list($year, $day, $month) = explode(BIRTH_SEPARATOR,$birthday); break;
    						default : list($year,$month,$day) = explode(BIRTH_SEPARATOR,$birthday);
    					}
    						
    					$year_diff = date("Y") - $year;
    					$month_diff = date("m") - $month;
    					$day_diff = date("d") - $day;
    					if ($month_diff < 0) $year_diff--;
    					elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
    					$out = $year_diff;
                        if ($out < 1) $out = DEFAULT_EMPTY_VALUE;
    					
    		} else $out = DEFAULT_EMPTY_VALUE; 
		}
        else
        {
            if (is_array($age))
                $out = implode('/',$age);
            else
                $out = $age;
        }
        	
		return $out; 
	}
	
	
	protected function getGender($gender)
	{
	   //debug line
	   //var_dump($gender);
       		
	    if (GENDERS != NULL)
        {
           $genders = explode(", ", GENDERS);
           $genders = str_replace(" ", "",$genders); 
           
           //debug line
           //var_dump($genders);
           
           unset ($val_rows);
           
           foreach ($genders as $row)
           {
             $val = explode(":", $row);
             $genders_array[$val[0]] = $val[1]; 
           }  
             //debug line
             //var_dump($genders_array);     
        }
            
			 switch(CMS)
             {
                case "Drupal6": 
                case "XenForo":  
				break;
				
				default:
                    if ((GENDERS != NULL) || (GENDERS != ''))
                        $gender = $genders_array[$gender];
                break;
             }
			
        $gender = $this->chars_convert($gender);
        if (empty($gender) && $gender!=='0') $gender = DEFAULT_EMPTY_VALUE;
        
		return $gender;
	}
	

	
	protected function getLocation($country = "", $state = "", $city = "", $default = DEFAULT_EMPTY_VALUE) {
		
        $location = "";
		if ($city and $city != $default)			{$location .= trim($city).", ";}
		if ($state and $state != $default) 			{$location .= trim($state).", ";} 
		if ($country and $country != $default)		{$location .= trim($country).", ";} 
		if (!$location)								{$location = $default;}
		if (substr($location, -2) == ", ") 			{$location = substr($location, 0 , -2);}
	
        $location = $this->chars_convert($location);
		return $location;
	}
	
	
	protected function getDetails( $details = "")
	{
	    $details = strip_tags($details);
	    $details = stripslashes($details);
		$details = substr($details, 0, 250);
                
        $details = $this->chars_convert($details);
        $details = html_entity_decode($details,ENT_QUOTES,'UTF-8');
        return $details;
		//return utf8_encode($details);
    }
	
	
	protected function getPhoto($photo = "", $isfile = FALSE, $uid = "", $login = "", $index = "")
	{
	   //debug line 
       //var_dump($photo);

            $photo_path = str_replace(array('[id]','[login]'), array($uid, $login), PHOTO_PATH);
            $root_photo_path = str_replace(array('[id]','[login]'), array($uid, $login), ROOT_PHOTO_PATH);
                    
	   if (CMS == "WordPress")
       {
            if (!file_exists(DOCUMENT_ROOT."wp-content/uploads/avatars/".$uid)) $uid = "";
            
            $av_dir = opendir($root_photo_path);
                        
            $avatar_files = array();
			while ( false !== ( $avatar_file = readdir($av_dir) ) ) {
				// Only add files to the array (skip directories)
				if ( 2 < strlen( $avatar_file ) && strpos($avatar_file, '-bpfull'))
					$avatar_files[] = $avatar_file;
			}
            //debug line
            //var_dump($avatar_files);
            $photo = $avatar_files[0];
       }
       
       if (CMS == "Dolphin")
       {
            if ($photo)
				return PHOTO_PATH.$photo.".jpg";
			else return NO_PHOTO;

       }
       
       if (CMS == "PHPFox")
       {
            $photo = "file/pic/user/".$photo = str_replace(array('{file/pic/user/','}', '%s'), array('','', ''), $photo);
       }
                
       //debug lines
       //var_dump($root_photo_path.$photo);
       //var_dump($isfile);
       //var_dump($photo);
       //var_dump($index);
       
		    if (!empty($photo))
            {
		      	switch ($isfile)
                {
					case FALSE :  
                                switch (CMS)
                                {
                                    //old word press
//                                    case "WordPress":
//                                        $out = SITE."user-avatar-pic.php?id=".$uid."&w=150";
//                                        break;
										
									case "SKADate":
                                        //return SITE."user-avatar-pic.php?id=".$uid."&w=150";
										$out = $photo_path."original_".$uid."_".$photo."_".$index.".jpg";
                                        if (USE_PHOTOCONVERTER) $out = FLASHCOMS_HTTP_ROOT."photoconverter.php?pic=thumb_".$uid."_".$photo."_".$index.".jpg&r=4x3";
                                        break;	
										
									case "XenForo":  
										$out = (string)$photo_path.(intval((int)$uid/1000)).'/'.$uid.'.jpg?'.$photo;
										break;	
                                    
                                    default:

                                        if (USE_PHOTOCONVERTER) $out = FLASHCOMS_HTTP_ROOT."photoconverter.php?pic=".$photo."&uid=".$uid."&login=".$login."&r=4x3";  
                                        else $out = $photo_path.$photo;
                                        
                                        break;
                                }
                        break;
                        
					case TRUE  :
                    
                                if ( is_file( $root_photo_path.$photo ) ) 
                                {
                                 if (USE_PHOTOCONVERTER) $out = FLASHCOMS_HTTP_ROOT."photoconverter.php?pic=".$photo."&uid=".$uid."&login=".$login."&r=4x3";  
                                 else $out = $photo_path.$photo;
                                }
                                    else $out = NO_PHOTO;
                        break;
				}
			
			
			}
			else 
			{
			 //debug line
			 //var_dump('photo empty');
             
                if(PHOTO_COMPLEX && !$isfile)
                {
                    $out = $photo_path;           
                }
                else
                {
                    switch ( $index )
                    {
    					case "male" 	: $out = NO_PHOTO_MALE; break;
    					case "female" 	: $out = NO_PHOTO_FEMALE; break;
    					default 	: $out = NO_PHOTO; break;
    				}
                }
			}
        
        //debug line 
        //var_dump($out);    
		return $out;
	}
	
	protected function getLevel($level, $level_list = NULL)
	{
		if (CMS == "WordPress")
        {
           preg_match_all("/\"(.+)\"/sUi", $level, $matches);
           //debug line
           //var_dump($matches);
           $level = $matches[1][0];
        }
        
        if (CMS == "SocialEngine")
        {
            $level_list = array('1'=>'Administrator',
                                '4'=>APPLICATION_REGULAR_LEVEL);
        }
        
        if (empty($level) && $level != '0') $level = APPLICATION_REGULAR_LEVEL;
        
        if ($level_list == NULL)
            $out = $level;
        else
            $out = $level_list[$level];
            
        $out = $this->chars_convert($out);
        return $out;
	}
	
	protected function getStatus()
	{
		return "-";
	}
	

	/*
	* FUNCTION ADD/REMOVE FREIENDS & BLOCKS
	*/
	
	protected function addFriend($user, $partner)
	{
		
		/* SEND REQUEST */
		
		$count = 1;
        
        $count = Db::dbSQL_action(SQL_SELECT_FRIENDS, array('[user]','[partner]'), array($user,$partner));
        

         
		if ( $count < 1 )
        {
		  
          //debug line
		  //var_dump(str_replace(array('[user]','[partner]'), array($user,$partner), SQL_SELECT_BLOCKS));
          switch (CMS)
          {
            case "SocialEngine":
                $count = Db::dbSQL_action(SQL_SELECT_BLOCKS, array('[user]','[partner]'), array($partner, $user));
                if ($count < 1)
                {
                    Db::dbSQL_action(SQL_INSERT_FRIENDS, array('[user]','[partner]', '[accepted_r]', '[accepted_u]', '[created]'), array($user, $partner, 0, 1, date("Y-m-d h:i:s")));
                    Db::dbSQL_action(SQL_INSERT_FRIENDS, array('[user]','[partner]', '[accepted_r]', '[accepted_u]', '[created]'), array($partner, $user, 1, 0, date("Y-m-d h:i:s")));
                    Db::dbSQL_action(SOCIAL_ENGINE_FRIEND_REQUEST_ADD, array('[user]','[partner]', '[created]'), array($user, $partner, date("Y-m-d h:i:s")));
                }
            break;
            
            case "PHPFox":
                $count = Db::dbSQL_action(SQL_SELECT_BLOCKS, array('[user]','[partner]'), array($partner, $user));
                if ($count < 1)
                {
                    Db::dbSQL_action(SQL_INSERT_FRIENDS, array('[user]','[partner]'), array($user, $partner));
                    Db::dbSQL_action(PHPFOX_FRIEND_REQUEST_ADD, array('[user]','[partner]'), array($user, $partner));
                }
            break;
            
            default:
                if ((Db::dbSQL_action(SQL_SELECT_BLOCKS, array('[user]','[partner]'), array($user,$partner)) > 0) && FB_SAME_TABLE )
                {
                   Db::dbSQL_action(SQL_UPDATE_FRIENDS, array('[user]','[partner]'), array($user,$partner));
                }
                else
                {
                    //debug line
    		        //var_dump(str_replace(array('[partner]','[user]'), array($user,$partner), SQL_SELECT_BLOCKS));
              
                   if (Db::dbSQL_action(SQL_SELECT_BLOCKS, array('[partner]','[user]'), array($user,$partner)) == 0)
                   {
              	     Db::dbSQL_action(SQL_INSERT_FRIENDS, array('[user]','[partner]', '[accepted]', '[pending]', '[created]', '[created Y-m-d]'), array($user, $partner, 0, 0, mktime(), date("Y-m-d")));
                   }
                //debug line
    		    //var_dump(str_replace(array('[user]','[partner]'), array($user,$partner), SQL_INSERT_FRIENDS));
              
                }
            break;
          }
            
            echo "<".$this->_action."_partner_add />";
			
		}
		else echo "<".$this->_action."_partner_exists />";
	}


	protected function removeFriend($user, $partner)
	{
		/* REMOVE FRIEND one to one */
		
		$count = 0;
        
        $count = Db::dbSQL_action(SQL_SELECT_FRIENDS, array('[user]','[partner]'), array($user,$partner));                                    
        
        //debug line
		//var_dump(str_replace(array('[user]','[partner]'), array($user,$partner), SQL_SELECT_FRIENDS));
		
        if ( $count > 0 )
        {
            
            //debug line
		    //var_dump($count);
        
            $deleted = Db::dbSQL_action(SQL_DELETE_FRIENDS, array('[user]','[partner]'), array($user,$partner));
            
            //debug line
		    //var_dump(str_replace(array('[user]','[partner]'), array($user,$partner), SQL_DELETE_FRIENDS));
                              
            if ($deleted)
            {
                $this->updateConnection($user, $partner);
            } 
			
            $deleted = Db::dbSQL_action(SQL_DELETE_FRIENDS, array('[user]','[partner]'), array($partner,$user));
             
             //debug line
	         //var_dump($deleted.' > ');
			
            if ($deleted) 
			{
			    $this->updateConnection($partner, $user);
			}
				
			echo "<".$this->_action."_partner_remove />";
		}
		else echo "<".$this->_action."_partner_not_exists />";
	}
	

	protected function addBlock($user, $partner)
	{
		$count = 1;
		
        $count = Db::dbSQL_action(SQL_SELECT_BLOCKS, array('[user]','[partner]'), array($user,$partner));  
        

        
		if ( $count < 1 )
        {                         
			 //debug line
		     //var_dump(str_replace(array('[user]','[partner]'), array($user,$partner), SQL_SELECT_FRIENDS));
                
            if ((Db::dbSQL_action(SQL_SELECT_FRIENDS, array('[user]','[partner]'), array($user,$partner)) > 0) && FB_SAME_TABLE )
            {
                //debug line
		        //var_dump(str_replace(array('[user]','[partner]'), array($user,$partner), SQL_UPDATE_BLOCKS));
               Db::dbSQL_action(SQL_UPDATE_BLOCKS, array('[user]','[partner]'), array($user,$partner));
            }
            else
            {
                //debug line
                //var_dump(str_replace(array('[user]','[partner]'), array($user,$partner), SQL_INSERT_BLOCKS));

               Db::dbSQL_action(SQL_INSERT_BLOCKS, array('[user]','[partner]','[created]'), array($user,$partner,mktime())); 
            }				 
			
			echo "<".$this->_action."_partner_add />";
			
		}
		else 
        {
                if (CMS == "ABKSoft")
                {
                   Db::dbSQL_action(SQL_UPDATE_BLOCKS, array('[user]','[partner]', '[im]'), array($user,$partner, 1)); 
                   echo "<".$this->_action."_partner_add />";
                }
                else
        echo "<".$this->_action."_partner_exists />";
        }

	}
	

	protected function removeBlock($user, $partner)
	{
		$count = 0;
		
		$count = Db::dbSQL_action(SQL_SELECT_BLOCKS, array('[user]','[partner]'), array($user,$partner));
		
		if ( $count > 0 )
        {
                            
                if (CMS == "ABKSoft")
                {
                   Db::dbSQL_action(SQL_UPDATE_BLOCKS, array('[user]','[partner]', '[im]'), array($user,$partner, 0)); 
                }
                else
			    Db::dbSQL_action(SQL_DELETE_BLOCKS, array('[user]','[partner]'), array($user,$partner));				 
				
			echo "<".$this->_action."_partner_remove />";
		}
		else echo "<".$this->_action."_partner_no_exists />";
	}
	
	protected function updateConnection($user, $partner)
	{	
		/* UPDATE FIELD COUNT FRIENDS */
        // Count friends
        $count = Db::dbSQL_action(SQL_FRIENDS, array('[user]'), array($user));
		
        // Update friends count
        Db::dbSQL_action(SQL_UPDATE_FRIENDS_COUNT, array('[count]', '[user]', '[partner]'), array($count, $user, $partner));
        
        //debug line
	    //var_dump(str_replace(array('[count]', '[user]', '[partner]'), array($count, $user, $partner), SQL_UPDATE_FRIENDS));
        
        //Add the same for BLOCKS if needed
            
	}
 
}

?>