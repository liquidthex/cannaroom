<?php

$xml = file_get_contents("php://input");

define('APPLICATION', 'chat7'); //Allowed values: chat7, messenger7

require_once "./module/cms.config.handler.php";
require_once "./module/db.class.php";
require_once "./module/utils.class.php";

Db::dbConnect();
  
  
//Utils::trace($xml);  
/**

ts -- timestamp in seconds (not milliseconds)
id -- userID
v -- has video
a -- has audio


        CREATE TABLE `chat7_broadcasting` (
          `uid` int(11) unsigned NOT NULL,          
          `video` char(30) default NULL,
          `audio` char(30) default NULL,
          PRIMARY KEY  (`uid`)
        ) 

*/

if ($_REQUEST['test'] == 1)
{
$xml = '<broadcasting ts="12345">
        	<u id="1" v="true" a="true" />
        	<u id="2" v="true" a="true" />
        	<u id="3" v="true" a="false" />
            <u id="4" v="true" a="true" />
        	<u id="5" v="true" a="true" />
        	<u id="6" v="true" a="false" />
            <u id="7" v="true" a="true" />
        	<u id="8" v="false" a="true" />
        	<u id="9" v="true" a="false" />
            <u id="10" v="true" a="true" />
        	<u id="11" v="false" a="true" />
        	<u id="12" v="true" a="false" />
        </broadcasting>';
}



function insertQuery($fields, $values)
{
 		$mainSql = "INSERT INTO ".APPLICATION."_broadcasting ([fields]) VALUES ([values])";
		mysql_query(str_replace(array('[fields]','[values]'), array($fields, $values), $mainSql));
        //echo str_replace(array('[fields]','[values]'), array($fields, $values), $mainSql)."<br>";
        //Utils::trace(str_replace(array('[fields]','[values]'), array($fields, $values), $mainSql));
}

function updateQuery($values, $uid)
{
 		$mainSql = "UPDATE ".APPLICATION."_broadcasting  SET [values] WHERE uid = '[uid]'";
		mysql_query(str_replace(array('[values]','[uid]'), array($values, $uid), $mainSql));
        //echo str_replace(array('[fields]','[values]'), array($fields, $values), $mainSql)."<br>";
        //Utils::trace(str_replace(array('[values]','[uid]'), array($values, $uid), $mainSql));
}

function deleteQuery($uid)
{
        $mainSql = "DELETE FROM ".APPLICATION."_broadcasting WHERE uid = '[uid]'";
    	mysql_query(str_replace(array('[uid]'), array($uid), $mainSql));
        //echo str_replace(array('[fields]','[values]'), array($fields, $values), $mainSql)."<br>";
        //Utils::trace(str_replace(array('[uid]'), array($uid), $mainSql));
}
    
    
function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
        $arrData = array();
       
        // if input is object, convert into array
        if (is_object($arrObjData)) {
            $arrObjData = get_object_vars($arrObjData);
        }
       
        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
                }
                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }
                $arrData[$index] = $value;
            }
        }
        return $arrData;
}

if (substr($xml, 0, 13) == "<broadcasting")
{
    
	



$xml = "<?xml version='1.0' standalone='yes'?>
        ".$xml;

	$xmlArray = objectsIntoArray(simplexml_load_string($xml));
    //debug line
//    Utils::trace($xmlArray);
//    
//    $errors = libxml_get_errors();
//
//    foreach ($errors as $error) {
//       Utils::trace($error->message.' '.$error->line.' '.$error->file);
//    }

$result = mysql_query('SELECT uid FROM '.APPLICATION.'_broadcasting');

while($row = mysql_fetch_assoc($result))
{
    $users_broadcasting[] = $row['uid'];
}



    if (count($xmlArray['u']) != 1)
    {     
        foreach ($xmlArray['u'] as $user)
        {
           broadcastAction($user, $users_broadcasting);     
        }
    }
    else
    {
        $user = $xmlArray['u'];
        broadcastAction($user, $users_broadcasting);
    }
} 

function broadcastAction($user, $users_broadcasting)
{
    //Utils::trace($user);
    //Utils::trace($users_broadcasting);
    //Utils::trace(in_array($user['@attributes']['id'], $users_broadcasting));
    
           if (($user['@attributes']['v'] == 'false') && ($user['@attributes']['a'] == 'false'))
           {
            deleteQuery($user['@attributes']['id']);
           }
           
           
           if (in_array($user['@attributes']['id'], $users_broadcasting))
           {
            $uid = $user['@attributes']['id'];
            $values = "video = '".$user['@attributes']['v']."', ";
            $values .= "audio = '".$user['@attributes']['a']."'"; 
            updateQuery($values, $uid);
           } 
           else
           {
            $fields = 'uid, video, audio';
            $values = "'".$user['@attributes']['id']."'";
            $values .= ",'".$user['@attributes']['v']."'";
            $values .= ",'".$user['@attributes']['a']."'"; 
            insertQuery($fields, $values);
           }
           
}

echo "Done";

?>