<?php
#
# Copyright © 2004-2012 Chat software by www.flashcoms.com
#

define('FILES_DIR', '../files');

$answer = '<invalidParameters />';

switch(getInputParam('action'))
{
	case 'upload': $answer = Upload(); break;
	case 'download': $answer = Download(); break;
	case 'image': $answer = UploadImage(); break;
    case 'screenshot': $answer = UploadScreenshot(); break;
}

header('Content-type:	text/xml');
echo $answer;


function Upload()
{
	Clear();

	if(!isset($_FILES['Filedata'])) return '<error msg="NO_FILE_SENDED" />';

	checkInputParams('id');
	$id = MakeFileName(getInputParam('id'));

	checkInputParams('name');
	$name = MakeFileName(getInputParam('name'));

	if(!file_exists(FILES_DIR) && !is_dir(FILES_DIR)) return '<error msg="files dir not exists" />';
	if(!is_writable(FILES_DIR)) return '<error msg="files dir is not writeable" />';

	mkdir(FILES_DIR.'/'.$id);
	chmod(FILES_DIR.'/'.$id, 0777);
	if(move_uploaded_file($_FILES['Filedata']['tmp_name'], FILES_DIR.'/'.$id.'/'.$name))
	{
		return '<uploaded msg="SUCCESS"/>';
	}
	else return '<error msg="UPLOAD_FAIL" />';
}

function Download()
{
	error_reporting(0);

	if(!isset($_REQUEST['fileId'])) return '<error msg="NO_FILE_ID_DEFINED" />';

	$fileName = MakeFileName($_REQUEST['fileId']).'/'.MakeFileName($_REQUEST['fileName']);
	$file = FILES_DIR.'/'.$fileName;

	if(!file_exists($file))
	{
		header("HTTP/1.1 404 Not Found");
		exit;
	}

	header("Content-Length: " . filesize($file));
	readfile($file);
	@unlink($file);
}

function UploadImage()
{
	if(!isset($_FILES['Filedata'])) return '<error msg="NO_FILE_SENDED" />';
	if(!file_exists(FILES_DIR) && !is_dir(FILES_DIR)) return '<error msg="files dir not exists" />';
	if(!is_writable(FILES_DIR)) return '<error msg="files dir is not writeable" />';

	checkInputParams('id,userID');
	$id = MakeFileName(getInputParam('id'));
	$userID = MakeFileName(getInputParam('userID')); // use this param if you need to know who sends the image

    $arr = explode('.', $_FILES['Filedata']['name']);
    $ext = strtolower(array_pop($arr));
    if(!in_array($ext, array('jpeg','jpg','gif','png'))) return '<error msg="INVALID_FILE_TYPE" />';

	$image = FILES_DIR.'/images/'.$id.'.'.$ext;
	$thumbnail = FILES_DIR.'/images/'.$id.'_s.'.$ext;

	if(!file_exists(FILES_DIR.'/images'))
	{
		mkdir(FILES_DIR.'/images');
		chmod(FILES_DIR.'/images', 0777);
	}

	if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $image))
	{
		chmod($image, 0666);

		require_once 'imagelibrary.class.php';
		$imgLibrary = new ImageLibrary();
		$imgLibrary->Resize($image, $thumbnail, 62, 43, true, true);

		chmod($thumbnail, 0666);

		return '<uploaded />';
	}
	else return '<error msg="UPLOAD_FAIL" />';
}

function UploadScreenshot()
{
	if(!file_exists(FILES_DIR) && !is_dir(FILES_DIR)) return '<error msg="files dir not exists" />';
	if(!is_writable(FILES_DIR)) return '<error msg="files dir is not writeable" />';

	checkInputParams('binary,id,userID');
    $binary = $_REQUEST['binary'];
	$id = MakeFileName(getInputParam('id'));
	$userID = MakeFileName(getInputParam('userID')); // use this param if you need to know who sends the image

    $img_data = base64_decode($binary);
    $img_size = strlen($img_data);
    $image = FILES_DIR.'/images/'.$id.'.jpg';
	$thumbnail = FILES_DIR.'/images/'.$id.'_s.jpg';

	if(!file_exists(FILES_DIR.'/images'))
	{
		mkdir(FILES_DIR.'/images');
		chmod(FILES_DIR.'/images', 0777);
	}

    $img_file = fopen($image,"w");
    fwrite($img_file, $img_data);
    fclose($img_file);
    
	if($img_file)
	{
		chmod($image, 0666);

		require_once 'imagelibrary.class.php';
		$imgLibrary = new ImageLibrary();
		$imgLibrary->Resize($image, $thumbnail, 62, 43, true, true);

		chmod($thumbnail, 0666);

		return '<uploaded />';
	}
	else return '<error msg="UPLOAD_FAIL" />';
}

function CheckInputParams($strParams)
{
	$arrParams = explode(',', $strParams);
	$len = count($arrParams);
	for($i = 0; $i < $len; $i++)
	{
		$param = $arrParams[$i];
		if(!isset($_GET[$param]) && !isset($_POST[$param]))
		{
			die('invalid parameters - '.$arrParams[$i].' is undefined');
		}
	}
}

function GetInputParam($param, $defaultValue = '')
{
	$value = $defaultValue;

	if( isset($_POST[$param]) ) $value = $_POST[$param];
	else if( isset($_GET[$param]) ) $value = $_GET[$param];

	if(get_magic_quotes_gpc()) $value = stripslashes($value);

	return $value;
}

function Clear()
{
	if( file_exists(FILES_DIR) && is_dir(FILES_DIR) && is_writable(FILES_DIR) )
	{
		Remove(FILES_DIR);
	}
}

function Remove($dir)
{
	if($objs = glob($dir.'/*'))
	{
		foreach($objs as $obj)
		{
			if( is_dir($obj) )
			{
				Remove($obj);
				$files = glob($obj.'/*');
				if (count($files) == 0) rmdir($obj);;
			}
			else
			{
				$diff = time() - filemtime($obj);
				if($diff > 3600 * 2) unlink($obj);
			}
		}
	}
}

function MakeFileName($name)
{
    $name = str_replace("..", "_", $name);
    $name = str_replace("/", "_", $name);
    $name = str_replace("\\", "_", $name);
    $name = str_replace(" ", "_", $name);
    return $name;
}

?>
