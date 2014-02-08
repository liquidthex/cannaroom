<?php

define('MALE', 0);
define('FEMALE', 1);

define('ROOT', 'http://cannaroom.tv/chat71/');

// Pre-created user accounts
$users = array(
	array('id' => 'admin',
		'userName' => 'admin',
		'password' => 'admin',
		'gender' => MALE,
		'location' => 'London, UK',
		'age' => 35,
		'details' => 'Hello, I am Admin'),
	array('id' => '1',
		'userName' => 'David',
		'password' => 'test',
		'gender' => MALE,
		'location' => 'London, UK',
		'age' => 22,
		'details' => 'Hello, I am David'),
	array('id' => '2',
		'userName' => 'Amanda',
		'password' => 'test',
		'gender' => FEMALE,
		'location' => 'Los Angeles, California',
		'age' => 19,
		'details' => 'Hello, I am Amanda'),
	array('id' => '3',
		'userName' => 'Steven',
		'password' => 'test',
		'gender' => MALE,
		'location' => 'Sun Valley, Nevada',
		'age' => 31,
		'details' => 'Hello, I am Steven'),
	array('id' => '4',
		'userName' => 'Helen',
		'password' => 'test',
		'gender' => FEMALE,
		'location' => 'New York, US',
		'age' => 25,
		'details' => 'Hello, I am Helen'),
	array('id' => '5',
		'userName' => 'mod',
		'password' => 'mod',
		'gender' => FEMALE,
		'location' => 'London, UK',
		'age' => 30,
		'details' => 'Hello, I am Moderator'),
);

function ParseUserData($user, $getProfile = false)
{
	global $users;

	$res = "<login result=\"OK\">";
	$res .= '<userData>'.GetUser($user).'</userData>';
	/*$res .= '<friends>';
	for($i = 1; $i < 40; $i++)
	{
		$u = $users[4];
		$u['id'] = $i;
		$res .= '<friend>'.GetUser($u).'</friend>';
	}
	$res .= '</friends>';
	$res .= '<blocks>';
	for($i = 0; $i < 40; $i++)
	{
		$u = $users[4];
		$u['id'] = $i;
		$res .= '<block>'.GetUser($u).'</block>';
	}
	$res .= '</blocks>';*/
    if (!$getProfile)
    {
	$res .= '<friends>'
    .'<friend id = "'.$users[2]['id'].'" name = "'.$users[2]['userName'].'" thumbnail = "'.ROOT.'photos/'.$users[2]['id'].'_small.png'.'"/>'
    .'<friend id = "'.$users[3]['id'].'" name = "'.$users[3]['userName'].'" thumbnail = "'.ROOT.'photos/'.$users[3]['id'].'_small.png'.'"/>'
    .'</friends>';
    $res .= '<blocks>'
    .'<block id = "'.$users[4]['id'].'" name = "'.$users[4]['userName'].'" thumbnail = "'.ROOT.'photos/'.$users[4]['id'].'_small.png'.'"/>'
    .'</blocks>';
    $res .= '<additionalData>'.GetAdditionalData($user).'</additionalData>';
    }
	$res .= "</login>";

	return $res;
}

function GetUser($user)
{
	if(!isset($user['photo'])) $user['photo'] = ROOT.'photos/'.$user['id'].'_big.png';
	if(!isset($user['thumbnail']) ) $user['thumbnail'] = ROOT.'photos/'.$user['id'].'_small.png';
	if(!isset($user['level']) ) $user['level'] = 'regular';

	if($user['id'] == '3')
	{
		$user['photo'] = '';
		$user['thumbnail'] = '';
	}

	return <<<USER_DATA
<id>${user['id']}</id>
<name><![CDATA[${user['userName']}]]></name>
<gender>${user['gender']}</gender>
<location>${user['location']}</location>
<age>${user['age']}</age>
<photo>${user['photo']}</photo>
<thumbnail>${user['thumbnail']}</thumbnail>
<details>${user['details']}</details>
<level>${user['level']}</level>
<profileUrl><![CDATA[http://localhost/${user['userName']}]]></profileUrl>
USER_DATA;
}

function GetAdditionalData($user)
{
	$res = '';
	if($user['id'] == '1')
	{
		$res.= "<imgateway>";
		$res.= "<icq login='634427950' password='111111'/>";
		$res.= "<yahoo login='moshagosha' password='111111'/>";
		$res.= "<msn login='gipercude@live.com' password='111111'/>";
		$res.= "</imgateway>";
	}
	return $res;
}

function GetParam($name, $default = '')
{
	if(isset($_POST[$name])) return $_POST[$name];
	if(isset($_GET[$name])) return $_GET[$name];
	return $default;
}

function trace($message, $isWriteLog = true)
{
    $content = print_r($message, true);
    if($isWriteLog)
    {
        $handle = @fopen('./log.txt', 'a');
        @fwrite($handle, date('Y-m-d H:i:s')."\r\n".$content."\r\n\r\n");
        @fclose($handle);
    }
    else echo '<pre>'.$content.'</pre><br>';
}

?>
