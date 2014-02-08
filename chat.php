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
<style>
#bar {
 top: 0;
 left: 0;
 width: 100%;
 height: 32px;
 background-color: #aeaeae;
 color: white;
}
#chatbox {
 width: 100%;
 height: 100%;
}
#tokebutton {
 width: 32px;
 height: 32px;
 float: left;
}
#tokebutton:hover {
 cursor: pointer;
}
#progressBar {
 position: relative;
 display: inline-block;
 top: 2px;
 left: 6px;
 width: 400px;
 height: 26px;
 border: 1px solid #000;
 background-color: #666;
 opacity: 0;
 float: left;
}

#progressFill {
 position: relative;
 display: inline-block;
 left: 1px;
 width: 0px;
 height: 26px;
 overflow: hidden;
 background-color: #0a0;
 font-family: Arial, Helvetica, sans-serif;
 font-size: 24px;
}
#countdown {
 position: relative;
 display: inline-block;
 left: 8px;
 border: 1px solid #000;
 background-color: #666;
 font-family: Arial, Helvetica, sans-serif;
 font-size: 24px;
 padding-left: 4px;
 padding-right: 4px;
 top: 2px;
 height: 26px;
 width: 28px;
 text-align: center;
 opacity: 0;
}
#status {
 position: relative;
 top: 4px;
 left: 6px;
 font-family: Arial, Helvetica, sans-serif;
 font-size: 18px;
}
.bar {
 vertical-align: top;
 max-height: 32px;
}
#pausebutton {
 position: relative;
 top: 6px;
 float: right;
}
#pausebuttonText {
 color: #999;
 text-decoration: none;
}
body,html,div {
 margin: 0;
 padding: 0;
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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

</div>

<iframe src="chatroom.php" id=chatbox frameborder=0></iframe>

</body>
