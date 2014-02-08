<?php
//error_reporting(E_ALL);

class Encryption
{
    public static function VBulletin($password, $salt)
    {
        $md5Pass = md5($password);
        $md5PassUtf = utf8_encode($md5Pass);
        $finalPassword = md5($md5PassUtf.$salt);
        
        return $finalPassword;
    }
    
    public static function Joomla($password, $DBpassword)
    {      
        $parts	= explode(':', $DBpassword);
        $crypt	= $parts[0];
        $salt	= @$parts[1];
        
        if ($salt != "")
        {
          $finalPassword = md5($password.$salt).":".$salt;  
        } 
        else
        {
          $finalPassword = md5($password);  
        }
     
        return $finalPassword;
    }
    
    public static function SocialEngine($password, $salt)
    {
        $CoreSecret = @mysql_fetch_assoc(@mysql_query("SELECT * FROM `".CMS_SOCIALENGINE_CORE_SECRET_TABLE."` WHERE name = 'core.secret';"));
        
		$finalPassword = md5($CoreSecret['value'].$password.$salt);
         
        return $finalPassword;
    }
    
    public static function SKADate($password)
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/internals/$config.php'; //SK_PASSWORD_SALT is here
        $finalPassword = sha1(SK_PASSWORD_SALT . $password);
         
        return $finalPassword;
    }

//---------------------------------------------------WordPress-------------------------------------------------------------------    

public static $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    public static function WordPress($password, $DBpassword)
	{
	   //self::$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		
        //debug line
        //echo "DBpassword: ".$DBpassword;
            
        $output = '*0';
		if (substr($DBpassword, 0, 2) == $output)
			$output = '*1';

		if (substr($DBpassword, 0, 3) != '$P$')
			return $output;

		$count_log2 = strpos(self::$itoa64, $DBpassword[3]);
        //debug line
        //echo "count_log2: ".$count_log2;
		if ($count_log2 < 7 || $count_log2 > 30)
			return $output;

		$count = 1 << $count_log2;

		$salt = substr($DBpassword, 4, 8);
		if (strlen($salt) != 8)
			return $output;

		# We're kind of forced to use MD5 here since it's the only
		# cryptographic primitive available in all versions of PHP
		# currently in use.  To implement our own low-level crypto
		# in PHP would result in much worse performance and
		# consequently in lower iteration counts and hashes that are
		# quicker to crack (by non-PHP code).
		if (PHP_VERSION >= '5') {
			$hash = md5($salt . $password, TRUE);
			do {
				$hash = md5($hash . $password, TRUE);
			} while (--$count);
		} else {
			$hash = pack('H*', md5($salt . $password));
			do {
				$hash = pack('H*', md5($hash . $password));
			} while (--$count);
		}

		$output = substr($DBpassword, 0, 12);
		$output .= Encryption::WordPress_encode64($hash, 16);

		return $output;
	}
    
    private function WordPress_encode64($input, $count)
	{
    
		$output = '';
		$i = 0;
		do {
			$value = ord($input[$i++]);
			$output .= self::$itoa64[$value & 0x3f];
			if ($i < $count)
				$value |= ord($input[$i]) << 8;
			$output .= self::$itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
				break;
			if ($i < $count)
				$value |= ord($input[$i]) << 16;
			$output .= self::$itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
				break;
			$output .= self::$itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);

		return $output;
	}

//-------------------------------------------------XenForo------------------------------------------------------------------
	/* XenForo FORUM */
	/*
		PASSWORD HASH INTO table xf_user_authenticate, field data
		
		/public_html/c/library/XenForo/Authentication/Core.php
	*/
		private function XenForoEncode($password, $salt)
		{
	
			switch ($salt['hashFunc'])
			{
				case 'sha256':
					return hash('sha256', $password);
				break;
				
				case 'sha1':
					return sha1($password);
				break;	
			}
		}
		
		public static function XenForo($password, $salt)
		{
			$data = $salt;
			
			$salt = unserialize($salt);
			
			$hash = Encryption::XenForoEncode(Encryption::XenForoEncode($password, $salt).$salt['salt'], $salt);	
			
			if ($hash != $salt['hash']) return FALSE;
			else return $data;
		}
 //-------------------------------------------------PHPBB--------------------------------------------------------------------
 
        public static function PHPBB($password, $DBpassword)
        {     
            $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            
            $output = '*';
            
            	// Check for correct hash
            	if (substr($DBpassword, 0, 3) != '$H$')
            	{
            		return $output;
            	}
            
            	$count_log2 = strpos($itoa64, $DBpassword[3]);
            
            	if ($count_log2 < 7 || $count_log2 > 30)
            	{
            		return $output;
            	}
            
            	$count = 1 << $count_log2;
            	$salt = substr($DBpassword, 4, 8);
            
            	if (strlen($salt) != 8)
            	{
            		return $output;
            	}
            
            	/**
            	* We're kind of forced to use MD5 here since it's the only
            	* cryptographic primitive available in all versions of PHP
            	* currently in use.  To implement our own low-level crypto
            	* in PHP would result in much worse performance and
            	* consequently in lower iteration counts and hashes that are
            	* quicker to crack (by non-PHP code).
            	*/
            	if (PHP_VERSION >= 5)
            	{
            		$hash = md5($salt . $password, true);
            		do
            		{
            			$hash = md5($hash . $password, true);
            		}
            		while (--$count);
            	}
            	else
            	{
            		$hash = pack('H*', md5($salt . $password));
            		do
            		{
            			$hash = pack('H*', md5($hash . $password));
            		}
            		while (--$count);
            	}
            
            	$output = substr($DBpassword, 0, 12);
            	$output .= Encryption::PHPBB_encode64($hash, 16, $itoa64);
             
            	return $output;
        }
        
        private function PHPBB_encode64($input, $count, $itoa64)
    	{
        
    		$output = '';
    		$i = 0;
    		do {
    			$value = ord($input[$i++]);
    			$output .= $itoa64[$value & 0x3f];
    			if ($i < $count)
    				$value |= ord($input[$i]) << 8;
    			$output .= $itoa64[($value >> 6) & 0x3f];
    			if ($i++ >= $count)
    				break;
    			if ($i < $count)
    				$value |= ord($input[$i]) << 16;
    			$output .= $itoa64[($value >> 12) & 0x3f];
    			if ($i++ >= $count)
    				break;
    			$output .= $itoa64[($value >> 18) & 0x3f];
    		} while ($i < $count);
    
    		return $output;
    	}

//----------------------------------------Drupal 7---------------------------------------------------

        public static function Drupal7($password, $DBpassword, $algo = 'sha512') 
        {
          // The first 12 characters of an existing hash are its setting string.
          $DBpassword = substr($DBpassword, 0, 12);
        
          if ($DBpassword[0] != '$' || $DBpassword[2] != '$') {
            return FALSE;
          }
          $count_log2 = Encryption::Drupal7_password_get_count_log2($DBpassword);
        
          $salt = substr($DBpassword, 4, 8);
          // Hashes must have an 8 character salt.
          if (strlen($salt) != 8) {
            return FALSE;
          }
        
          // Convert the base 2 logarithm into an integer.
          $count = 1 << $count_log2;
        
          // We rely on the hash() function being available in PHP 5.2+.
          $hash = hash($algo, $salt . $password, TRUE);
          do {
            $hash = hash($algo, $hash . $password, TRUE);
          } while (--$count);
        
          $len = strlen($hash);

          $output =  $DBpassword . Encryption::Drupal7_password_base64_encode($hash, $len);
          
          return substr($output, 0, DRUPAL_HASH_LENGTH);
        }
        
        private function Drupal7_password_get_count_log2($setting) 
        {
          $itoa64 = Encryption::Drupal7_password_itoa64();
          return strpos($itoa64, $setting[3]);
        }
        
        private function Drupal7_password_itoa64() 
        {
          return './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        }
        
        private function Drupal7_password_base64_encode($input, $count) {
          $output = '';
          $i = 0;
          $itoa64 = Encryption::Drupal7_password_itoa64();
          do {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];
            if ($i < $count) {
              $value |= ord($input[$i]) << 8;
            }
            $output .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count) {
              break;
            }
            if ($i < $count) {
              $value |= ord($input[$i]) << 16;
            }
            $output .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count) {
              break;
            }
            $output .= $itoa64[($value >> 18) & 0x3f];

          } while ($i < $count);
        
        //debug_line
        //var_dump($output);
        
          return $output;
        }
        
        public static function Invision_Power_Services($password, $DBpassword) 
        {
          
          $output = md5(md5($DBpassword).md5($password));   
          return $output;
        }
        
        public static function Concrete5($password) 
        {
          $output = md5($password . ':' . CONCRETE_PASSWORD_SALT);
          return $output;
        }
        
        public static function PHPFox($password, $DBpassword) 
        {
          $output = md5(md5($password).md5($DBpassword));
          return $output;
        }
		
}

//echo Encryption::WordPress('123456', '$P$BzJVkQxhpzxUe7M7wpEo57R7j1URuh0');
//echo Encryption::Invision_Power_Services('123456', '\'6Tu|');

//echo Encryption::VBulletin('pwflash', 'gwl[p\'D;d32hOQ7\'u}Xvi*k8V$U|HK');


?>