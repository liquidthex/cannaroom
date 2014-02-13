<?php
session_start();

require_once "./handlers/module/cms.config.handler.php";
require_once "./handlers/module/db.class.php";
require_once "./handlers/module/utils.class.php";

error_reporting(0);
Db::dbConnect();

$uid = Utils::myUID();
if (!$uid) header("Location: http://cannaroom.tv/user/login");
?>
<html><head>
<title>Cannaroom</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="css/thexchat.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.color-2.1.2.min.js"></script>
<script src="js/soundmanager2-nodebug-jsmin.js"></script>
<script src="js/thexchat.js"></script>
<script type="text/javascript">
 $(function() { thexInit(); });
</script>
</head>
<body>
<div id=bar class=bar>

<span id="tokebutton" onclick="toke();" class=bar title="Press to toke Cannabis."><img src="images/Cannabis_leaf.png" onMouseOver="this.src='images/Cannabis_leaf2.png';" onMouseOut="this.src='images/Cannabis_leaf.png';"></span>
<span id="progressBar"><span id="progressFill">Get&nbsp;Ready...</span></span>
<span id="countdown">&nbsp;</span>
<span id="status" class=bar>&nbsp;</span>
<span id="pausebutton" class=bar><a href="#" id="pausebuttonText" onclick="pauseUpdate();"><img src="images/pause.png" id="pausebuttonImage" width=20 height=20 title="Pause/Resume Updates"></a></span>
<span id="controlPanelButton" class=bar><a href="#" id="controlPanelButtonText" onclick="controlPanel();"><img src="images/Gear.png" width=20 height=20 title="Control Panel"></a></span>

</div>
<div id=controlPanelDialog title="Control Panel">
Please wait, loading ye olden control panel
</div>
<? if ($_GET['dev']) $url = "blank.html"; else $url = "chatroom.php"; ?>

<iframe src="<?echo $url;?>" id=chatbox frameborder=0></iframe>

</body>
