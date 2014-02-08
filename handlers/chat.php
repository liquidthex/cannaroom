<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Community Chat</title>

	<script type="text/javascript" src="http://<?=$_SERVER['HTTP_HOST'];?>/chat7/js/flashcoms.js"></script>
	<script type="text/javascript">

		function Z5ChatEvent(event, param1, param2)
		{
			switch(event)
			{
				case 'addFriend':
					var userID = param1;
					var friendID = param2;
					
										
					var url = 'http://<?=$_SERVER['HTTP_HOST'];?>/chat7/handlers/common.handler.php?action=addFriend&user_id=' +userID+ '&friend_id=' +friendID;
					Z5CallHandler(url);
					
					break;
				case 'removeFriend':
					var userID = param1;
					var friendID = param2;
					
					var url = 'http://<?=$_SERVER['HTTP_HOST'];?>/chat7/handlers/common.handler.php?action=removeFriend&user_id=' +userID+ '&friend_id=' +friendID;
					Z5CallHandler(url);
					
					
					break;
				case 'block':
					var userID = param1;
					var blockID = param2;
					// TODO add your code here
					break;
				case 'unblock':
					var userID = param1;
					var blockID = param2;
					// TODO add your code here
					break;
			}
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
		
		function Z5CallHandler(url)
		{
			var img = new Image();
			img.src = url + '&rand=' + Math.random();
		}
		

		function Z5Close() { self.close(); }

		z5chat.uid = "<? if ($_COOKIE['phpfox1user_id']) print $_COOKIE['phpfox1user_id']; ?>";
		//z5chat.roomID = 'room2';
        //z5chat.roomPassword = 'password';
        //z5chat.logoURL = 'http://youdomain/logo.png';
		//z5chat.preloaderBgColor = 0xff0000;
		//z5chat.bgColor = 0x0000ff;
		z5chat.ShowChat();

	</script>

</head>
<body style="margin:0px; padding:0px;">

</body>
</html>
