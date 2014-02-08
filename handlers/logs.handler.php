<?php
/**
r -- roomID
ts -- timestamp in seconds (not milliseconds)
f -- from user id
t -- to user id
w -- whisper true/false
dt -- timestamp in seconds (not milliseconds)
c -- content

CREATE TABLE `chat7_logs` (
  `room_id` char(15) NOT NULL,
  `from_user_id` char(15) NOT NULL,
  `to_user_id` char(15) NOT NULL,
  `whisper` char(10) NOT NULL,
  `timestamp` char(15) NOT NULL,
  `content` char(250) NOT NULL
);

#
# Dumping data for table `chat7_logs`
#

*/

$xml = file_get_contents("php://input");

//require_once $_SERVER['DOCUMENT_ROOT'].'/admin/config.php';
//require "db.class.php";

define('APPLICATION', 'chat7'); //Allowed values: chat7, messenger7

require_once "./module/cms.config.handler.php";
require_once "./module/db.class.php";

Db::dbConnect();

switch ($_REQUEST['test'])
{
    case 1:
        $xml = '<chatLogs ts="123456">
                	<m>
                		<r>room1</r>
                		<f>1</f>
                		<to>2</to>
                		<w>true</w>
                		<dt>123456</dt>
                		<c><![CDATA[Hello :)]]></c>
                	</m>
                </chatLogs>';
    break;
    
    case 11:
        $xml = '<chatLogs ts="123456">
                	<m>
                		<r>room1</r>
                		<f>1</f>
                		<to>2</to>
                		<w>true</w>
                		<dt>123456</dt>
                		<c><![CDATA[Hello :)]]></c>
                	</m>
                	<m>
                		<r>room2</r>
                		<f>2</f>
                		<to>1</to>
                		<w>false</w>
                		<dt>123458</dt>
                		<c><![CDATA[How do you do?)]]></c>
                	</m>
                </chatLogs>';
    break;
}

if (substr($xml, 0, 9) == "<chatLogs")
{
	
	$xmlParse = simplexml_load_string($xml);
	
	function logQuery($rows, $values)
	{
		$mainSql = 'INSERT INTO chat7_logs('.$rows.') VALUES ('.$values.')';
		mysql_query($mainSql);
	}
	

		if ($xmlParse->m)
		{	
			for ($i = 0; $i<count($xmlParse); $i++)
			{
	
				$rows = 'room_id, from_user_id, to_user_id, whisper, timestamp, content'; ;
				$values = "'".$xmlParse->m[$i]->r."', ";
				$values.= "'".$xmlParse->m[$i]->f."', ";
				$values.= "'".$xmlParse->m[$i]->to."', ";
				$values.= "'".$xmlParse->m[$i]->w."', ";
				$values.= "'".$xmlParse->m[$i]->dt."', ";
                
				$out = mb_convert_encoding($xmlParse->m[$i]->c, mb_detect_encoding($xmlParse->m[$i]->c), "UTF-8");
               
				$values.= "'".htmlentities($out, ENT_QUOTES, mb_detect_encoding($out))."'";
				logQuery($rows, $values);
			}
		}
}
echo 'Done';
?>