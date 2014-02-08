<?php

class Db
{
	public function dbConnect()
    {
        if(!mysql_connect(DB_HOST, DB_USER, DB_PASSWORD))
        {
            //var_dump(mysql_error());
			die("Db::Connect - can`t connect to db: " . mysql_error());
            return false;
        }
        if(!mysql_select_db(DB_NAME))
        {
			die("Db::Connect - can`t select db");
            return false;
        }
        
        if (DB_CHARSET) mysql_query("SET CHARACTER SET ".DB_CHARSET);
        return true;
    }
	
	public function dbQuery($query)
	{
		$result = mysql_query($query);
		//var_dump(mysql_error());
		return $result;	
	}
	
	public function dbGetRows($query)
	{
	    $result = self::dbQuery($query);
        //var_dump(mysql_error());
        $rows = array();
		if ($result){
       	 while($row = mysql_fetch_assoc($result)) $rows[] = $row; 
		}
        return $rows;
	}
//---------------------------------------------SQL ACTION ----------------------------------------    
    public function dbSQL_action($sql, $placeholders = array(), $variables = array())
	{
	   //debug line
	   //var_dump(str_replace($placeholders, $variables, $sql));
       
	   //if sentence for mysql_num_rows() or mysql_affected_rows();
       if (preg_match('/SELECT/',$sql) > 0)
        return mysql_num_rows(mysql_query(str_replace($placeholders, $variables, $sql)));
       else
       {
        mysql_query(str_replace($placeholders, $variables, $sql));
        return mysql_affected_rows();
       }
        
	}
//------------------------------------------------------------------------------------------------	
	public function dbSelect($table, $cond = '1')
    {
        $result = Db::dbQuery("SELECT * FROM `".$table."` WHERE ".$cond);
        $rows = array();
        while($row = mysql_fetch_assoc($result)) $rows[] = $row;
        return $rows;
    }
	
	public function dbCountRows($table, $cond = '1')
	{
		$result = self::dbQuery("SELECT * FROM `".$table."` WHERE ".$cond);
		
		//var_dump("SELECT * FROM `".$table."` WHERE ".$cond);
		
		return mysql_num_rows($result);
	}
	
	public function dbInsert($table, $values, $safeMode = true)
    {
        $params = Db::MakeParams($values, $safeMode);
       // Db::Query("LOCK TABLES `".$table."` WRITE");
        Db::dbQuery("INSERT INTO `".$table."` SET ".implode(", ", $params));
        $result = mysql_insert_id();
        //Db::Query("UNLOCK TABLES");
        return $result;
    }
	
	public function MakeParams($values, $safeMode = true)
	{
        $params = array();
        if($safeMode)
			foreach($values as $field => $value) 
			{
				if($value != 'NOW()') 
					$value = "'" . Db::PrepareStr($value) . "'";
				$params[] = "`" . $field . "`=" . $value;
			}
        else
			foreach($values as $field => $value) 
				$params[] = $field."=".$value;
		return $params;
	}


    public function PrepareStr($str)
    {
        return mysql_escape_string($str);
    }
	
	function Replace($table, $values, $safeMode = true)
    {
        $params = Db::MakeParams($values, $safeMode);
        Db::Query("LOCK TABLES `".$table."` WRITE");
        Db::Query("REPLACE INTO `".$table."` SET ".implode(", ", $params));
        $result = mysql_insert_id();
        Db::Query("UNLOCK TABLES");
        return $result;
    }

    function Update($table, $values, $cond = '1', $safeMode = true)
    {
        $params = Db::MakeParams($values, $safeMode);
        Db::Query("LOCK TABLES `$table` WRITE");
        Db::Query("UPDATE `".$table."` SET ".implode(", ", $params)." WHERE ".$cond);
        $result = mysql_affected_rows();
        Db::Query("UNLOCK TABLES");
        return $result;
    }

    function dbDelete($table, $cond = '1')
    {
       // Db::Query("LOCK TABLES `$table` WRITE");
        Db::dbQuery("DELETE FROM `".$table."` WHERE $cond");
        $result = mysql_affected_rows();
        //Db::Query("UNLOCK TABLES");
        return $result;
    }
	
}


class Db2
{
    function Connect()
    {
        if(!mysql_connect(DB_HOST, DB_USER, DB_PASSWORD))
        {
			die("Db::Connect - can`t connect to db");
            return false;
        }
        if(!mysql_select_db(DB_NAME))
        {
			die("Db::Connect - can`t select db");
            return false;
        }
        return true;
    }

    function Select($table, $cond = '1')
    {
        $result = Db::Query("SELECT * FROM `".$table."` WHERE ".$cond);
        $rows = array();
        while($row = mysql_fetch_assoc($result)) $rows[] = $row;
        return $rows;
    }

    function SelectEx($query)
    {
        $result = Db::Query($query);
        $rows = array();
        while($row = mysql_fetch_assoc($result)) $rows[] = $row;
        return $rows;
    }

    function Get($table, $cond = '1')
    {
        $result = Db::Query("SELECT * FROM `".$table."` WHERE ".$cond." LIMIT 1");
        $row = mysql_fetch_assoc($result);
        return $row;
    }

    function Insert($table, $values, $safeMode = true)
    {
        $params = Db::MakeParams($values, $safeMode);
        Db::Query("LOCK TABLES `".$table."` WRITE");
        Db::Query("INSERT INTO `".$table."` SET ".implode(", ", $params));
        $result = mysql_insert_id();
        Db::Query("UNLOCK TABLES");
        return $result;
    }

    function Replace($table, $values, $safeMode = true)
    {
        $params = Db::MakeParams($values, $safeMode);
        Db::Query("LOCK TABLES `".$table."` WRITE");
        Db::Query("REPLACE INTO `".$table."` SET ".implode(", ", $params));
        $result = mysql_insert_id();
        Db::Query("UNLOCK TABLES");
        return $result;
    }

    function Update($table, $values, $cond = '1', $safeMode = true)
    {
        $params = Db::MakeParams($values, $safeMode);
        Db::Query("LOCK TABLES `$table` WRITE");
        Db::Query("UPDATE `".$table."` SET ".implode(", ", $params)." WHERE ".$cond);
        $result = mysql_affected_rows();
        Db::Query("UNLOCK TABLES");
        return $result;
    }

    function Delete($table, $cond = '1')
    {
        Db::Query("LOCK TABLES `$table` WRITE");
        Db::Query("DELETE FROM `".$table."` WHERE $cond");
        $result = mysql_affected_rows();
        Db::Query("UNLOCK TABLES");
        return $result;
    }

    function Query($query)
    {
        $result = mysql_query($query);
        
		/*
		if($err = mysql_error())
        {
            trace("*MySQL ERROR* ".$err);
            trace($query);
        }*/
        return $result;
    	
	}

	function MakeParams($values, $safeMode = true)
	{
        $params = array();
        if($safeMode)
			foreach($values as $field => $value) 
			{
				if($value != 'NOW()') 
					$value = "'" . Db::PrepareStr($value) . "'";
				$params[] = "`" . $field . "`=" . $value;
			}
        else
			foreach($values as $field => $value) 
				$params[] = $field."=".$value;
		return $params;
	}


    function PrepareStr($str)
    {
        return mysql_escape_string($str);
    }
	
	
	function trace($message, $isWriteLog = true)
	{
		$content = print_r($message, true);
		if($isWriteLog)
		{
			$handle = @fopen(LOG_DIR.'log.txt', 'a');
			@fwrite($handle, date("Y-m-d H:i:s")."\r\n".$content."\r\n\r\n");
			@fclose($handle);
		}
		else echo "<pre>".$content."</pre><br>";
	}
	
	
}

?>
