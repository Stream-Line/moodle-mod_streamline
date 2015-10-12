
<html>

    <link rel="stylesheet" href="css/kube.min.css" />
 	<link rel="stylesheet" type="text/css" href="kube.css">
	<link rel="stylesheet" type="text/css" href="Chat/chat.css">
    <script src="js/kube.min.js"></script>
    <link rel="stylesheet" type="text/css" href="streamline.css">

	<script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
	
  <script type="text/javascript">	

/* global getUser, HyperLinks */

	$(document).ready(function() {
		$("#chat").animate({ scrollTop: $('#scroll_down').height() }, "fast");
		return false;
	});

	var id = <?=json_encode($cm->id)?>;
	var cstuid = <?=json_encode($USER->id)?>;
	var chars = [":)",":(",";)",">:)",":'(",":p"];
	var emoti = ["&#128512","&#128542","&#128521","&#128520","&#128546","&#128523"];
	var order = 0; // to change the colour of the messages

	function loading(){
		socket.emit('Load',<?=json_encode($stuval)?>,id);
		socket.emit('LoadF',<?=json_encode($stuval)?>,id);
	}


	function FormatMessage(msg){
		var stuid = msg.substring(msg.lastIndexOf("+")+1, msg.lastIndexOf("@"));
				
		getUser(cid, stuid, "fullname", function(usr_name){
			getUser(cid, stuid, "displaypicture", function(dp_html){

				var list_element  = document.createElement("li"); // main list element
				var dp   = document.createElement("div");
				var Dmain = document.createElement("div");

				if(order % 2 == 0){ 
					list_element.id = "Shader_G";
				}else{
					list_element.id = "Shader_W";
				}

				order = order + 1;

				dp.classList.add("chat-dp");
				list_element.classList.add("chat_msg");
				dp.innerHTML = dp_html;

				var time =  msg.substr(msg.lastIndexOf("@")+1);	
				var text =  msg.substring(0,msg.lastIndexOf("+"));
				text =HyperLinks(text);
				text =Emoticon(text);
				
				var block = 
		"<span class='user_name'>"+usr_name+"</span>"+
		"<p clase='date'>  "+time+"</p> <br>"+
		"<p class='msg_text'>"+text+"</p>";

				Dmain.innerHTML = block;

				list_element.appendChild(dp);
				list_element.appendChild(Dmain);

				

				$('#chat').append(list_element);
			});
		});
	}

	function Emoticon(msg){
		for( x in chars){
			msg = msg.replace(chars[x],emoti[x]);
		}
		return msg;
	}


	$(function() { $("#sendie").keydown(
		function(event) {  
			if(event.keyCode == 13 ){
				socket.emit('Message',$('#sendie').val(),id,<?=json_encode($stuval)?>,sid);
				$('#sendie').val("");
			}
		}); 
	});

	socket.on('messback', function(message){
	    FormatMessage(message); 
	    $("#chat").animate({ scrollTop: 10000000 }, "slow");
	});

	socket.on('loaded', function(history){
		alert('trying to load history :'+history+' <- is there something?');
		history = history.toString();
		var myHis = history.split(",");
		alert('myHistory : '+ myHis);
		for(x in myHis){
 			FormatMessage(myHis[x]);
		  $("#chat").animate({ scrollTop: 10000000 }, "fast");
		}
	});

	window.onbeforeunload = function(e) {
		socket.emit('disconnect',id,<?=json_encode($stuval)?>,sid);
	};


    </script>

<body onload="loading()">
<div id='scroll_down'>
				<ul id="chat">
					<div class="chat_welcome">
						Welcome to StreamLine Chat					
					</div>
				</ul>  
</div>
    <div class="chat_send_msg">Your message: </div>
		<textarea id="sendie" rows="2"></textarea>
</body>
</html>

<script>
	$('.msg_name').text();
</script>
