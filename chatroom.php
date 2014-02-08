<?php
//ini_set('session.name', 'ercom');
session_start();

require_once "./handlers/module/cms.config.handler.php";
require_once "./handlers/module/db.class.php";
require_once "./handlers/module/utils.class.php";

error_reporting(0);
Db::dbConnect();

$uid = Utils::myUID();
if (!$uid) header("Location: http://cannaroom.tv/user/login");
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Community Chat 7</title>

	<script type="text/javascript" src="./js/flashcoms.js"></script>
	<script type="text/javascript">

        var handlerURL = "./handlers/common.handler.php";
		//z5chat.preloaderBgColor = 0xede8d8;
		<?php if (!empty($uid)) echo "z5chat.uid = ".$uid.";"; ?>
		z5chat.roomID = 'room1';
		z5chat.logoURL = '';
		z5chat.preloaderBgColor = 0x000000;
		//z5chat.bgColor = 0x212121;
        
        
		function Z5ChatEvent(action, user, partner)
		{
		  //actions:
		    //"addFriend"
			//"removeFriend"
			//"block"
			//"unblock"
		  Z5CallHandler(action, user, partner);
		}

		function AIRRegister(registrationUrl)
		{
			try
			{
				// defined in air client
				CGOpen(registrationUrl);
			}
			catch(e)
			{
				// do nothing
			}
		}
		
        function Z5CallHandler(action, user, partner) {

            var url = handlerURL + '?action=' + action + '&user=' + user + '&partner=' + partner;

            var img = new Image();
            img.src = url + '&rand=' + Math.random();
        }
		
		function Z5Close() { self.close(); }

		z5chat.ShowChat();

	</script>

</head>
<body style="margin:0px; padding:0px; background: transparent;">

</body>
</html>
