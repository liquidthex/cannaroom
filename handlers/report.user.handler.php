<?php

/**
       CREATE TABLE `messenger7_Report` (
         `id` int(11) unsigned NOT NULL auto_increment, 
         `reporter_id` int(11) unsigned default NULL,
         `abuse_id` int(11) unsigned default NULL,
         `type` char(64) default NULL,
         `reason` char(128) default NULL,
         `time_stamp` int(11) unsigned default NULL,
         `status` char(128) default NULL,
         PRIMARY KEY  (`id`)
       ); 

       CREATE TABLE `chat7_Report` (
         `report_id` int(11) unsigned NOT NULL auto_increment, 
         `offeringUserId` char(64) default NULL,
         `reportingUserId` char(64) default NULL,
         `roomID` char(64) default NULL,
         `reason` char(128) default NULL,
         `history` text default NULL,
         PRIMARY KEY  (`report_id`)
       ) 
 */
require_once "./module/cms.config.handler.php";
require_once "./module/db.class.php";
//require_once "./module/utils.class.php";
//define("LOG_DIR",  './');

define("REPORT_TO_DB",  TRUE);

//messenger 7
$action = $_REQUEST['action'];
$userID = $_REQUEST['user'];
$blockID = $_REQUEST['partner'];

if (($action == "abuse") && $userID && $blockID)
{
    Db::dbConnect();


    	function logQuery_messenger($rows, $values)
    	{
    		$mainSql = 'INSERT INTO messenger7_Report('.$rows.') VALUES ('.$values.')';
    		mysql_query($mainSql);   
    	}
    	
    				$rows = 'reporter_id, abuse_id, type, reason, time_stamp, status'; ;
    				$values.= "'".$userID."', ";
    				$values.= "'".$blockID."', ";
                    $values.= "'profile', ";
                    if ($userID)	
        				$values.= "'messenger report called', ";
                    else
        				$values.= "'roulette report called', ";
                    $values.= "'".mktime()."', ";
                    $values.= "'active'";
    				logQuery_messenger($rows, $values);

    echo 'Done';
}
else
{
    //--------------------------------------------chat7-----------------------------------------------------------
    $xml = file_get_contents("php://input");
    
    $testXML ='<reportUser>
                	<offeringUserId>2</offeringUserId>
                	<reportingUserId>1</reportingUserId>
                	<roomId>room1</roomId>
                	<reason>some reason</reason>
                	<history>
                		<msg><senderID>1</senderID><content>Hello :)</content></msg>
                		<msg><senderID>1</senderID><content>How do you do?</content></msg>
                		<msg><senderID>2</senderID><content>Some rude message</content></msg>
                	</history>
                </reportUser>';
                
    if ($_REQUEST['test'] == 1)
    {
       $xml =  $testXML;
    }
    
    
    Db::dbConnect();
    
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
    
    function logQuery($rows, $values)
    {
    		$mainSql = 'INSERT INTO 
                                chat7_Report ('.$rows.') 
                               VALUES (\''.$values.'\')';
                               
            mysql_query($mainSql);
            //echo $mainSql.'<br>';
    }
    
    function GetUserLogin($uid)
    {
            $mainSql = "SELECT
                            user_login
                        FROM 
                            1_users
                        WHERE
                            ID = '".$uid."'";
            $result = mysql_query($mainSql);
            $login = mysql_fetch_assoc($result);
            $login = $login['user_login'];
            
            if(empty($login)) $login = 'Guest('.$uid.')';
            
            return $login;
    }
    
    function GetReportEmail($email = NULL)
    {
        if ($email == NULL)
        {
            $mainSql = "SELECT
                            option_value as email
                        FROM 
                            1_options
                        WHERE
                            option_name = 'admin_email'";
            $result = mysql_query($mainSql);
            $email = mysql_fetch_assoc($result);
            $email = $email['email'];
         }
            return $email;
    }
        
    if (substr($xml, 1, 10) == "reportUser")
    {
        $xml = str_replace(array('<![CDATA[', ']]>'), '', $xml);
        $xmlArray = objectsIntoArray(simplexml_load_string($xml));
        
        //var_dump($xmlArray);
        unset($rows);
        unset($values);
                
        foreach ($xmlArray as $key=>$value)
        {
                    if (is_array($value))
                    {
                        unset($content);
                        
                        foreach ($value['msg'] as $subvalue)
                        {
                            $content .= htmlentities($subvalue["senderID"].": ".$subvalue["content"]." \n", ENT_QUOTES);
                        }
                        
                        $rows[] = $key;
                        $values[] = $content;
                    }
                    else
                    {
                        $rows[] = $key;
                        $values[] = $value;
                    }
        }
        
        If (REPORT_TO_DB)
        {
            $rows = implode(", ", $rows);
            $values = implode("', '", $values);
            logQuery($rows, $values);
        }
        else
        {
            $offeringUser_login = GetUserLogin($item_id);
            $reportingUser_login = GetUserLogin($reporter_id);
        
            $email = GetReportEmail("fl891254@gmail.com"); //admin@chat-4-ever.nl
            $message = "User ".$offeringUser_login." reports on ".$reportingUser_login." (".$date.") \nRoomID: ".$values['roomId']."\nReason: ".$values['reason']."\n\n messages:\n".$values['history']."\n\n ";
            mail($email, "Chat report", $message);            
        }

    }
    
    echo 'Done';
}
?>