<?php
/*
ts -- timestamp in seconds (not milliseconds)
e -- user enter
l -- user leave
*/

/** This table has to be created first
 
  CREATE TABLE `chat7_presence` (
    `id` int(10) unsigned NOT NULL,
    PRIMARY KEY  (`id`)
  );

// OR
  
  CREATE TABLE `messenger7_presence` (
    `id` int(10) unsigned NOT NULL,
    PRIMARY KEY  (`id`)
  );
  
 */
 
$xml = file_get_contents("php://input");

define('APPLICATION', 'messenger7'); //Allowed values: chat7, messenger7

require_once "./module/cms.config.handler.php";
require_once "./module/db.class.php";
require_once "./module/utils.class.php";

Db::dbConnect();

switch ($_REQUEST['test'])
{
    case 1:
        $xml = '<presenceDelta ts="123456">
                	<e>1</e>
                </presenceDelta>';
    break;
    
    case 11:
        $xml = '<presenceDelta ts="123456">
                	<e>1</e>
                	<e>2</e>
                	<l>1</l>
                </presenceDelta>';
    break;

    case 2:
        $xml = '<presenceFull ts="123456">
                	<id>123</id>
                </presenceFull>';
    break;

    case 22:
        $xml = '<presenceFull ts="123456">
                	<id>1</id>
                	<id>2</id>
                	<id>3</id>
                </presenceFull>';
    break;
}


	function insertQuery($fields, $values)
	{
 		$mainSql = "INSERT INTO ".APPLICATION."_presence ([fields]) VALUES ([values])";
		mysql_query(str_replace(array('[fields]','[values]'), array($fields, $values), $mainSql));
        //echo str_replace(array('[fields]','[values]'), array($fields, $values), $mainSql)."<br>";
	}
    
   	function deleteQuery($fields, $values)
	{
 		$mainSql = "DELETE FROM ".APPLICATION."_presence WHERE id = [values]";
		mysql_query(str_replace(array('[values]'), array($values), $mainSql));
        //echo str_replace(array('[values]'), array($values), $mainSql)."<br>";
        
        //for broadcasting
        $uid = $values;
        $mainSql = "DELETE FROM ".APPLICATION."_broadcasting WHERE uid = [uid]";
            //Utils::trace(str_replace(array('[uid]'), array($uid), $mainSql));
    	mysql_query(str_replace(array('[uid]'), array($uid), $mainSql)); 
 
	}
    
if (substr($xml, 0, 14) == "<presenceDelta")
{
	
	$xmlParse = simplexml_load_string($xml);
	
    $fields = 'id';
    
   	$users = array();
    
	preg_match_all('/\<([le])\>(.*?)\<\/[le]\>/', $xml, $users);

	foreach ($users[1] as $key => $uAction)
	{
	   $uid = $users[2][$key];
       
       $fields = 'id';
       
		if ($uAction == 'e')
		{
			// user enters the site
            $values = "'".$uid."'";
            insertQuery($fields, $values);
		}

		if ($uAction == 'l')
		{
			// user leaves the site
            $values = "'".$uid."'";
            deleteQuery($fields, $values);
		}
	}    
}

if (substr($xml, 0, 13) == "<presenceFull")
{
	mysql_query('DELETE FROM '.APPLICATION.'_presence');
    
	$xmlParse = simplexml_load_string($xml);
	
    $fields = 'id';
    
          foreach($xmlParse->id as $id)
          {
				$values = "'".$id."'";
				insertQuery($fields, $values);
                
                
                //for broadcasting
                $users_presence[] = $id;
          }

//for broadcasting
$result = mysql_query('SELECT uid FROM '.APPLICATION.'_broadcasting');

while($row = mysql_fetch_assoc($result))
{
    $uid = $row['uid'];
    
    if (!in_array($uid, $users_presence))
    {
        $mainSql = "DELETE FROM ".APPLICATION."_broadcasting WHERE uid = '[uid]'";
    	mysql_query(str_replace(array('[uid]'), array($uid), $mainSql));
    }
    
}





}
echo 'Done';

?>