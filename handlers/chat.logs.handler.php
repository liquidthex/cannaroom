<?php
/**
ts -- timestump in seconds (not miliseconds)
f -- from user id
to -- to user id
dt -- timestamp in seconds (not miliseconds)
c -- content

<chatLogs ts="123456">
	<m>
		<f>1</f>
		<to>2</to>
		<dt>123456</dt>
		<c><![CDATA[Hello :)]]></c>
	</m>
	<m>
		<f>2</f>
		<to>1</to>
		<dt>123458</dt>
		<c><![CDATA[How do you do?)]]></c>
	</m>
</chatLogs>

CREATE TABLE `messenger7_logs` (
  `from_user_id` char(15) NOT NULL,
  `to_user_id` char(15) NOT NULL,
  `timestamp` char(15) NOT NULL,
  `content` char(250) NOT NULL
);

CREATE TABLE `ppv_logs` (
  `from_user_id` char(15) NOT NULL,
  `timestamp` char(15) NOT NULL,
  `content` char(250) NOT NULL
);

#
# Dumping data for table `messenger7_logs`
#

*/

$xml = file_get_contents("php://input");

//require_once $_SERVER['DOCUMENT_ROOT'].'/admin/config.php';
//require "db.class.php";

define('APPLICATION', 'messenger7'); //Allowed values: chat7, messenger7, ppv

require_once $_SERVER['DOCUMENT_ROOT']."/".APPLICATION."/handlers/module/cms.config.handler.php";
require_once $_SERVER['DOCUMENT_ROOT']."/".APPLICATION."/handlers/module/db.class.php";

Db::dbConnect();

if($_REQUEST['test'] == 1)
{
   $xml = '<chatLogs ts="123456">
            	<m>
            		<f>1</f>
            		<to>2</to>
            		<dt>123456</dt>
            		<c><![CDATA[Hello :)]]></c>
            	</m>
            	<m>
            		<f>2</f>
            		<to>1</to>
            		<dt>123458</dt>
            		<c><![CDATA[How do you do?)]]></c>
            	</m>
            </chatLogs>';
}


if (substr($xml, 0, 9) == "<chatLogs")
{
	
	$xmlParse = simplexml_load_string($xml);
	
	function logQuery($rows, $values)
	{
		$mainSql = 'INSERT INTO '.APPLICATION.'_logs('.$rows.') VALUES ('.$values.')';
        var_dump($mainSql);
		mysql_query($mainSql);
	}
	
    if ((APPLICATION == 'chat7') || (APPLICATION == 'messenger7'))
    {
		if ($xmlParse->m)
		{	
			for ($i = 0; $i<count($xmlParse); $i++)
			{
	
				$rows = 'from_user_id, to_user_id, timestamp, content'; ;
				$values = "'".$xmlParse->m[$i]->f."', ";
				$values.= "'".$xmlParse->m[$i]->to."', ";
				$values.= "'".$xmlParse->m[$i]->dt."', ";
				$values.= "'".htmlentities($xmlParse->m[$i]->c, ENT_QUOTES)."'";
				logQuery($rows, $values);
			}
	     }
    }
    
    if (APPLICATION == 'ppv')
    {
		if ($xmlParse->m)
		{	
		  
          
 			for ($i = 0; $i<count($xmlParse->m); $i++)
			{
	           	$rows = 'from_user_id, timestamp, content'; ;
				$values = "'".$xmlParse->m[$i]->f."', ";
				$values.= "'".$xmlParse->m[$i]->dt."', ";
				$values.= "'".htmlentities($xmlParse->m[$i]->c, ENT_QUOTES)."'";
				logQuery($rows, $values);
			}
	    }
    }
}
echo 'Done';
?>