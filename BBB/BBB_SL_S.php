<head>	<script>
	
		/* PHP Variables */
		var meetingRunningUrl = <?php echo json_encode($meetingRunningUrl); ?>;
		var recordings = <?php echo json_encode($recordingsURL); ?>;
		var meetingRunningUrl = <?php echo json_encode($meetingRunningUrl); ?>;
		var moderator = <?php echo json_encode($moderator); ?>;
		var administrator = <?php echo json_encode($administrator); ?>;
		var teacher = <?php echo json_encode($teacher); ?>;
		var end_meeting_url =  <?php echo json_encode($end_meeting_url); ?>; // This is the url request that ends the meeting; MATT
		var meetingEnded = <?php echo json_encode($meetingEnded); ?>;
		
		/* Variables */
		var sessionRunning = false;
		var recordingURL = "";
		var containerHeight;

		var mobile = false;
		
		//Mobile Device Use
		function removeMoodleDock() {
			setTimeout(function () {
				$('#dock').remove();
			}, 5000);
		}
		
		function loadCSS(url) {
			if (!$('link[href="' + url + '"]').length)
				$('head').append('<link rel="stylesheet" type="text/css" href="' + url + '">');
		}

		$( document ).ready(function() {
		
			mobile = mobileCheck();
			if(mobile) {
				loadCSS('./css/materialize.css');
				
				$( "header" ).remove( ".navbar" );
				$('#page-header').remove();
				$('#middleContainer').remove();
				$('#top_liveView').remove();
				removeMoodleDock();
				$('#page-footer').remove();
				$('#maincontent').remove();
				
				$('#desktop_UI').empty();
				$('#mobile_UI').css("display", "block");

				var mobile_option = false;
				$('#chat_mobile_button').click(function() {
					$('.button-collapse').sideNav('hide');
						$('#forum_module').css("display",'none');
						$('#chat_module').css("display",'block');
				});
				
				$('#forum_mobile_button').click(function() {
					$('.button-collapse').sideNav('hide');
						$('#forum_module').css("display",'block');
						$('#chat_module').css("display",'none');
				});				
				
				
				$('#exit_mobile_button').click(function() {
					$('.button-collapse').sideNav('hide');
					window.location.href = "http://60.241.60.47:9999/course/view.php?id=" + <?=json_encode($COURSE->id)?>;
				});
				
				$('#quiz_mobile_menu').click(function() {
					$('.button-collapse').sideNav('hide');
				});
				
				$(".button-collapse").sideNav();

				$('#chat_module').css("position",'relative');
				$('#chat_module').css("z-index",'9997');
				$('#chat_module').css("padding","0");
				
				$('.fullscreen_button').css("display", "none");
				$('.leave_button').css("display", "none");

				$('.comm_button').css("display", "inline-block");	
				$('.back_button').css("display", "inline-block");								

				$('#webinar_buttons').css("position",'relative');
				$('#webinar_buttons').css("z-index",'9998');
				$('#webinar_buttons').css("padding","0");
				$('#webinar_buttons').css("background-color","#42444C");

				$('#forum_module').css("z-index",'9998');
				$('#forum_module').css("padding",'0');
				
				$('#forum_title').css("font-size", "1em");				
				$('#forum_title').css("font-weight", "bold");
				
				$('#quizModal').css("z-index",'9999');
				
				$('#rightContainer').removeClass('unit-25');
				$('#rightContainer').addClass('unit-100');
				$('#rightContainer').css("height", "auto");
				
				$('#mainContainer').css("margin-bottom","0");
				
				$('#forum_module').css("display",'none');
				$('#page').css("padding","0");
				
				$('#quizModal').css("width", "100%");
				$('#quizModal').css("left", "0px");
				$('#quizModal').css("top", "0px");

			}
			//Adjust webinar buttons if required
			adjustWebinarButtons();
					
			/* 	There are 3 screens which may be displayed for the user. There are 3 cases for each screen to be loaded as described below
				CASE 1 - Meeting has not ended, there exists a recording and the user has the relevant permissions (admin, moderator, teacher)
						Load the options screen
				CASE 2 - Meeting has not ended and either:
					The session is running (moderator has joined meeting) OR
					The user has relevant permissions (admin, moderator, teacher)
						Load the live screen
				CASE 3 - All other cases - Meeting has ended or (session not running and user does not have permissions to start a session)
					CASE 3.1 - Recording exists
						Load the playback screen with the recording
					CASE 3.2 - Recording does not exist
						Load the playback screen displaying no recordings exist
			*/		
			
			var windowHeight;
			var navHeight;
			BBBSessionRunning();
			var hasRecording = isRecording(); 
			/* 	Case 1 - Load options screen */
			if(!meetingEnded && hasRecording && (administrator || moderator || teacher)) {
				console.log("loading options screen");
				$("#liveView").css("height", "0px");
				$("#recordingView").css("display", "none");	
				$("#optionView").css("visibility", "visible");
				$("#top_liveView").css("display", "none");
			/* 	Case 2 - Load live screen */
			} else if(!meetingEnded && (sessionRunning || administrator || moderator || teacher)) {
				console.log("loading live screen");
				$("#liveView").css("height", "100%");
				$("#recordingView").css("display", "none");
				$("#optionView").css("display", "none");
				$("#top_liveView").css("display", "block");
				
				windowHeight = window.innerHeight;
				navHeight = $('.navbar').height()
				containerHeight = windowHeight - navHeight;
				
				if(!mobile) {
					$('#middleContainer').height(containerHeight);
					$('#rightContainer').height(containerHeight);
				}
			/* 	Case 3 - Load playback screen */
			} else {
				console.log("loading playback screen");
				$("#liveView").css("display", "none");
				$("#optionView").css("display", "none");
				$("#recordingView").css("visibility", "visible");
				$("#top_liveView").css("display", "none");
				/* Case 3.1 - Load playback displaying the recording */
				if(recordingURL != "") {
					console.log("Recording Response");
					console.log(recordings);
					initRecordings();
				}
				else { /* Case 3.2 - Load playback with no recording message */
					if(meetingEnded) {
						$("#recordingView").html("<div class='session_no_record'><img src='./images/Logo.png' alt='Smiley face' width='20%'><br>Sorry, this webinar session has been ended! <br> If the session was recorded, please wait while the recording is processed... </div>");
					} else {
						$("#recordingView").html("<div class='session_no_record'><img src='./images/Logo.png' alt='Smiley face' width='20%'><br>The webinar session is currently not running, please wait for the lecturer and/or moderator to join. <br> No recordings are available for this lecture at this time. </div>");
					}
				}				
			}
			
			//Set webinar height dynamically 
			var webinarButtonHeight = $('.fullscreen_button').outerHeight();
			$('#webinar_buttons').height(webinarButtonHeight);
			
			//Set chat module height dynamically
			var chatModuleHeight = containerHeight - webinarButtonHeight;
			$('#chat_module').outerHeight(chatModuleHeight);
			
			//Set chat height dynamically
			var sendBox = $('#sendie').outerHeight();
			var sendBoxTitle = $('.chat_send_msg').outerHeight();
			console.log("Send Box: " + sendBox);
			console.log("Send Msg: " + sendBoxTitle);
			console.log("Chat Mod: " + $('#chat_module').height());
			
			var chatHeight = $('#chat_module').height() - sendBox - sendBoxTitle;
			console.log("Chat Height: " + chatHeight);
			$('#chat').outerHeight(chatHeight);
			
			console.log("Setting Middle Container Height");
					
			$("#liveView").hover(function(){
				var scrollT = $(document).scrollTop();
				$(document).on("scroll", function(e){
					$(document).scrollTop(scrollT);
				});
			}, function(){
				$(document).off("scroll");
			});
			
			console.log(sessionRunning);
			$(".playback_button").click(function() {
				console.log("Clicked Live Button .. now loading live screen");
				$("#recordingView").css("display", "block");
				$("#liveView").css("display", "none");
				$("#optionView").css("display", "none");
				$("#top_liveView").css("display", "none");
				initRecordings();
			});
		
			$(".live_button").click(function() {
				console.log("Clicked Live Button .. now loading live screen");
				$("#liveView").css("height", "100%");
				
				//TODO: find better method to fix the BBB overflow issue
				setTimeout(function() {reSizeFlashClient("99%");}, 3000);
				setTimeout(function() {reSizeFlashClient("100%");}, 6000);
				
				$("#recordingView").css("display", "none");
				$("#optionView").css("display", "none")		
				$("#top_liveView").css("display", "block");
			});
			
			//Scroll to the sessionRecording
			if(!mobile) {
				var navHeight = $('.navbar').height() - 5;
				$('html, body').animate({
					scrollTop: $('#middleContainer').offset().top-navHeight}, 
				1000);
			}
		});
		
		function mobileCheck() {
		  var check = false;
		  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
		  return check;
		}
		
		function adjustWebinarButtons() {
			if(administrator || moderator || teacher) {
				//Do nothing
				$("#webinar_buttons").css("visibility","visible");
			} else {
				$(".leave_button").css("display", "none");	
				$(".quiz_button").css("width", "50%");
				$(".fullscreen_button").css("width", "50%");
				$("#webinar_buttons").css("visibility","visible");
			}
		}
		
		function reSizeFlashClient(value) {
			document.getElementById("flashclient").style.width = value;		
			console.log("Setting Flash Client Width to: " + value);			
		}
		function initRecordings() {
			loadIframe(recordingURL);
			
			var i=0;
			var recordingURLs = []
			
			var createClickHandler = function(url) {
				return function() { 
					document.getElementById('streamline_recording').src=url
				};		
			}
			
			for(i=0;i<recordings.length;i++) {
				recordingURLs[i]=recordings[i].playbacks.presentation.url;				
				
				var div = document.createElement('div');
				div.className = 'section';
				div.id = 's'+(i+1);
				div.innerHTML = 'Section ' + (i+1);
				div.onclick = createClickHandler(recordingURLs[i]);
				document.getElementById('sectionContainer').appendChild(div);
			}
			
			var sectionHeight = $("#sectionContainer").outerHeight();
			var iframeHeight = $("#streamline_recording").outerHeight();
			containerHeight = sectionHeight + iframeHeight;
				
			$('#middleContainer').height(containerHeight);
			$('#rightContainer').height(containerHeight);
				
			console.log("RECORDING LIST");
			console.log(recordingURLs);
		}
		
		function loadIframe(url) {
			document.getElementById('streamline_recording').src=url;		
		}
	
		function isRecording() {
				try{
					var url=recordings[0].playbacks.presentation.url;
					recordingURL = url;
					return true;
				}
				catch(e) // This runs when there is error
				{
					return false;
				}			
		}
		
		//added by Matt, ends the meeting completely
		function exitMeeting(){
			
			//Only end the session if the user has the correct permissions - I believe the check should be in the endmeeting.php
			if(administrator || moderator || teacher) {

				var xmlHttp = new XMLHttpRequest();
				xmlHttp.open( "GET", end_meeting_url, false );
				xmlHttp.send( null );	
				$.get('BBB/endmeeting.php?id=<?php echo $id; ?>', function(){
					//successful ajax request
				}).error(function(){
					alert('error... ohh no!');
				});
				
				alert("The webinar session has been ended!");
				window.location.href = "<?php echo($moodle_dir);?>/mod/streamline/view.php?id=<?php echo $id; ?>";  
				
				//Closes the iframe
				//$('#iframe_box').remove();
				
			}
			
		}
		
		
		function BBBSessionRunning() {
			$.ajax({
				  type: "GET",
				  url: meetingRunningUrl,
				  dataType: "xml",
				  contentType: "text/xml; charset=\"utf-8\"",
				  complete: function(xmlResponse) {
					meetingResponse=xmlResponse.responseXML;
					meetingRunning=meetingResponse.getElementsByTagName("running")[0];
					running=meetingRunning.childNodes[0].nodeValue;
					if(running == "false") {
						sessionRunning = false;
						console.log("Session is not running");
						return false;
					} else if(running == "true") {
						sessionRunning = true;
						console.log("Session is running");
						return true;
					}
				  },
				   async:   false
			});
		}
		
		var sessionRecording = false;
		function BBBRecordRequest() {

			if(sessionRecording) {
				$("#recordStatus").removeClass("recordStatus_On");
				$("#recordStatus").addClass("recordStatus_Off");
				$("#recordStatus").html("This Lecture is not being recorded");
				console.log("Recording has been switched off");
				sessionRecording = false;			
			} else {
				$("#recordStatus").removeClass("recordStatus_Off");
				$("#recordStatus").addClass("recordStatus_On");
				$("#recordStatus").html("Recording currently in progress");
				console.log("Recording has been switched on");
				sessionRecording = true;
			}
		}
				
		</script>
	
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css" media="screen">
      html, body, #flashclient                { height:50%;}
      body                                    { margin:0; padding:0; }
      #altContent                             { /* style alt content */ }
    </style>
    <script type="text/javascript" src="<?php Print($variable); ?>client/swfobject/swfobject.js"></script>
	
	
    <script type="text/javascript">
      swfobject.registerObject("ChatModule", "11", "expressInstall.swf");
      swfobject.registerObject("BigBlueButton", "11", "expressInstall.swf");
      swfobject.registerObject("WebcamPreviewStandalone", "11", "expressInstall.swf");
      swfobject.registerObject("WebcamViewStandalone", "11", "expressInstall.swf");
    </script>
    <script src="<?php Print($variable);?>/client/lib/bigbluebutton.js" language="javascript"></script>
    <script src="<?php Print($variable);?>client/lib/bbb_localization.js" language="javascript"></script>
    <script src="<?php Print($variable);?>client/lib/bbb_blinker.js" language="javascript"></script>
    <script src="<?php Print($variable);?>client/lib/bbb_deskshare.js" language="javascript"></script>
    <script type="text/javascript" src="<?php Print($variable);?>client/lib/bbb_api_bridge.js"></script>
    <script type="text/javascript" src="<?php Print($variable);?>client/lib/bbb_api_cam_preview.js"></script>
    <script type="text/javascript" src="<?php Print($variable);?>client/lib/bbb_api_cam_view.js"></script>
    <script type="text/javascript" src="<?php Print($moodle_dir);?>/mod/streamline/3rd-party.js"></script>
  
    <script>
      /*window.chatLinkClicked = function(url) {
        window.open(url, '_blank');
        window.focus();
      }
      window.displayBBBClient = function() {
        var bbbc = document.getElementById("flashclient");
        var wcpc = document.getElementById("webcampreviewclient");
        wcpc.style.display = "none";
        bbbc.style.display = "block";
      }
      window.displayWCClient = function() {
        console.log("Displaying webcam preview client");
        var wcpc = document.getElementById("webcampreview");
        wcpc.style.display = "block";
      }
      window.onload = function() {
         registerListeners();
      }*/
	  //window.location.href="<?php Print($variable); ?>bigbluebutton/api/create?meetingID=test-105&checksum=6de5d773b1768d17f30765e606f1869561e2cce0";
	  
	  

    </script>
</head>

