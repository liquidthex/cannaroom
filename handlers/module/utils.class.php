<?php

class Utils
{
	//Logging function
    function trace($message, $isWriteLog = true)
    {
        $content = print_r($message, true);
        if($isWriteLog)
        {
            //var_dump(LOG_DIR.'log.txt');
            $handle = @fopen(LOG_DIR.'log.txt', 'a');
            //var_dump(error_get_last());
            @fwrite($handle, date("Y-m-d H:i:s")."\r\n".$content."\r\n\r\n");
            @fclose($handle);
        }
        else echo "<pre>".$content."</pre><br>";
    }
    //------------------------------------------------------
	//SQL injection protection
	public function EscapeInjection($text, $pass = false) 
	{
	  $checktext = strtolower($text);
	  if (
		(strpos($checktext, "select ") === FALSE) && 
		(strpos($checktext, " union ") === FALSE) && 
		(strpos($checktext, " like ") === FALSE) && 
		(strpos($checktext, " order ") === FALSE) && 
		(strpos($checktext, " where ") === FALSE) && 
		(strpos($checktext, " char ") === FALSE) && 
		(strpos($checktext, "delete ") === FALSE) && 
		(strpos($checktext, " from ") === FALSE) &&
		(strpos($checktext, "=") === FALSE)
	  ) 
	  {
		if ($pass != true)
		return addslashes($text); // NO SQL injection found
		else
		return $text;
	  } 
	  else 
	  {
		//trace('Possible SQL injection: '.$text);
		return 'SQLinjection'; // SQL injection found
	  }
	}
	//-------------------------------------------------------

	

	public function Encrypt_pass($string, $key, $passes = 1) 
	{
	   $key .= date('dztY');
	   $key = md5($key);
          
	   $result = '';
       
       //$array = str_split($string, ceil(strlen($string)/2));
       //$string = $array[1].$array[0];
             
	    for ($j=0; $j < $passes; $j++)
        {
          for($i=0; $i<strlen($string); $i++) 
		  {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		  }
		
        }
        $result = base64_encode($result).$key;
		return base64_encode($result);
	}
    
    public function Encrypt($string, $key, $passes = 2) 
	{
	   	for ($j=0; $j < $passes; $j++)
        {
            $string = Utils::Encrypt_pass($string, $key);
        }
	  return $string;
	}

	public function Decrypt_pass($string, $key, $passes = 1) 
	{
		  $key .= date('dztY');
		  $key = md5($key);
          
		  $result = '';
		  
          $string = base64_decode($string);
          $string = str_replace($key, '', $string);
          $string = base64_decode($string);
          
        for ($j=0; $j < $passes; $j++)
        {
        
          for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		  }
        }
        
        //$array = str_split($result, ceil(strlen($result)/2));
        //$result = $array[1].$array[0];
        
        
        return $result;
	}
    
    public function Decrypt($string, $key, $passes = 2) 
	{
	   	for ($j=0; $j < $passes; $j++)
        {
            $string = Utils::Decrypt_pass($string, $key);
        }
	  return $string;
	}
    
    public function myUID($uid = NULL)
    {
        //debug line
        //var_dump(empty($uid));
        if (empty($uid))
        {
            switch (CMS)
            {
                case "SocialEngine":
                	if (!empty($_COOKIE['PHPSESSID'])) 
                	{
                		  $sql = ("select user_id from engine4_core_session where id =  '".mysql_real_escape_string($_COOKIE['PHPSESSID'])."'");
                          $query = mysql_query($sql);
                          $session = mysql_fetch_array($query);
                	}else $uid = "";
                	
                	$out = $session['user_id'];
                break;
                
                case "Drupal6":
                $sid = $_COOKIE['SESS1c7af43e235f13a38bcd1c06d1817ae6'];
                	if (!empty($sid)) 
                	{
                		  $sql = ("select uid  from sessions where sid  =  '".mysql_real_escape_string($sid)."'");
                          $query = mysql_query($sql);
                          $session = mysql_fetch_array($query);
                	}
                	
                	$out = $session['uid'];
                break;
                
                case "Interactive Arts Ltd":
                    $out = $_SESSION['Sess_UserId'];
                break;
                
                case "SKADate":
                    $out = $_SESSION["%http_user%"]["profile_id"];
                break;
                    
                case "PGDatingPro":
                    if (!empty($_COOKIE['PHPSESSID'])) 
                	{
                		  $sql = ("SELECT id_user FROM adeadeloye_active_sessions WHERE session = '".$_COOKIE['PHPSESSID']."'");
                          $query = mysql_query($sql);
                          $session = mysql_fetch_array($query);
                	}
                	$out = $session['id_user'];
                break;            
                
                case "Demosite":
                    $out = $_SESSION['user']['id'];
                break;
                
                case "WordPress":
                    $inf = explode('|',$_COOKIE[WORDPRESS_COOKIE_NAME]);
                    $userINF = Db::dbGetRows(str_replace(array('[condition]','[login]'),
                                                                array(SQL_GUEST_CASE,$inf[0]), 
                                                                SQL_AUTHORIZATION));
                    $out = $userINF[0]['id'];
                break;
                
                case "Invision Power Services":
                    $out = $_COOKIE['member_id'];
                break;
                
                case "ChatZone":
                    if (!empty($_SESSION['sid_main']))
                    {
                   		  $sql = ("SELECT id FROM users WHERE sid  = '".$_SESSION['sid_main']."'");
                          $query = mysql_query($sql);
                          $session = mysql_fetch_array($query);
                	}
                    $out = $session['id'];
                break;
                
                case "VBulletin":
                    if (!empty($_COOKIE['bb_sessionhash']))
                    {
                   		  $sql = ("SELECT userid  FROM session WHERE sessionhash = '".$_COOKIE['bb_sessionhash']."'");
                          $query = mysql_query($sql);
                          $session = mysql_fetch_array($query);
                	}
                    $out = $session['userid'];
                break;
                
                case "PHPBB":
                    $out = $_COOKIE['phpbb3_c4vsp_u'];
                break;
                
                case "PHPFox":
#                    if (!empty($_SESSION['corebf79']['session']))  #liquidthex
                    if (!empty($_SESSION['core2719']['session']))
                    {
#                   		  $sql = ("SELECT user_id FROM phpfox_log_session WHERE session_hash  = '".$_SESSION['corebf79']['session']."'");
                   		  $sql = ("SELECT user_id FROM phpfox_log_session WHERE session_hash  = '".$_SESSION['core2719']['session']."'");
                          $query = mysql_query($sql);
                          $session = mysql_fetch_array($query);
                	}
                    $out = $session['user_id'];
                break;
                
                case "Concrete5":
                    $out = $_SESSION['uID'];
                break;

                case "osDate":
                    $out = $_SESSION['UserId'];
                break;

                default:
                    $out = $_COOKIE['USERID'];
                break;
            }
        }
        else
        {
           $out = $uid; 
        }
        if (defined('ENCRYPTION_STRING'))
            $output = Utils::Encrypt($out);
        else
            $output = $out;
        
        return $output;
    }

}

//$a = Utils::Encrypt('219', 'pass');
//
//echo $a;
//echo '<br>';
//echo Utils::Decrypt($a, 'pass');
//echo '<br>';
//$a = Utils::Encrypt('218', 'pass');
//
//echo $a;
//echo '<br>';
//echo Utils::Decrypt($a, 'pass');


?>
