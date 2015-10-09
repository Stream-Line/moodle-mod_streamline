<html>
<head>
   <link rel="stylesheet" href="css/kube.min.css" />
	 <link rel="stylesheet" href="Chat/style.css" type="text/css" />
	 <script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
	 <script src="http://code.jquery.com/jquery-1.11.1.js"></script>

   <script type="text/javascript">	
	var id = <?=json_encode($cm->id)?>;
	var hst = <?=json_encode($stuval)?>;
	var sid = id.toString();

	var socket = io.connect();

	socket.emit('Check',id);

	socket.on('co',function(data) {
		var Hstu = <?=json_encode($HStuList)?>;
		if(data == 'Y'){
			var stu = <?=json_encode($StuList)?>;
			if(stu.indexOf("-") > -1 && Hstu.indexOf("-") > -1){
				socket.emit('Sending',stu,Hstu,id);
				$('#chat-area').append("New Chat");
			}
		}else
			$('#chat-area').append("Old Chat");
        $('#chat-area').append("Join triggred");
		socket.emit('Join',sid,id,hst); 
	});

	socket.on('Running',function(data) {
		$('#chat-area').append(data);
	});
			
   </script>
</head>
<body>




</body>

</html>
<?php
/*
waiting:
<div id="chat-area"></div>
*/


?>
