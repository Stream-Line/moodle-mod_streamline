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

		$( document ).ready(function() {
		
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
						$("#recordingView").html("<p class='session_no_record'> Sorry, this webinar session has been ended! <br> If the session was recorded, please wait while the recording is processed... </p>");
					} else {
						$("#recordingView").html("<p class='session_no_record'> The webinar session is currently not running, please wait for the lecturer and/or moderator to join. <br> No recordings are available for this lecture at this time. </p>");
					}
				}				
			}
			
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
			var navHeight = $('.navbar').height() - 5;
			$('html, body').animate({
				scrollTop: $('#middleContainer').offset().top-navHeight}, 
			1000);
		});
		
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
				//window.location.href = "<?php echo($moodle_dir);?>/mod/streamline/view.php?id=<?php echo $id; ?>";  
				
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

