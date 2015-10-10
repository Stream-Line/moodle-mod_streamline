<?php
// 5 chat include line that have to be placed before the connection
// occurs to load the BBB client. This is because the BBB client
// appears to cause some socket conflicts.
	global $DB, $CFG, $USER, $COURSE;
	
 	$HStuList = null;
	$StuList  = null;
	$stuval   = bin2hex($USER->id);
	include 'Chat/DataPrep.php';
	include 'Chat/StartChat.php';
?>

<!-- Question: Are these the core dependencies required across all modules? Whats with the absolute path to 3rd-party.js? -->
<script type="text/javascript" src="3rd-party.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="Quiz/xml2json.min.js"></script>
<script type="text/javascript" src="global_functions.js"></script>
<script type="text/javascript" src="js/kube.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="streamline.css">
<link rel="stylesheet" type="text/css" href="Quiz/quiz.css">
<link rel="stylesheet" href="css/kube.min.css" />

<body>
	<div id ="top_liveView">
		<div id="recordStatus" class="recordStatus_Off"> This Lecture is not being recorded </div>
	</div>
	<div class="units-row units-split">
		<!-- Container for the webinar/BigBlueButton -->
		<div class="unit-75" id="middleContainer">
			<!-- Question: Why is this not just a one line php include? -->
			<div id="liveView">
				<?php 
				$ipaddress = trim($CFG->ServerURLforBigBlueButton);
				$variable2 = substr($ipaddress, 0, strpos($ipaddress, "b"));
				$variable = trim(trim($variable2),'/').'/';
				?>
				
				<div id="flashclient" style="background-color:#EEEEEE;height:100%;width:100%;float:left;">
				   <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="50%" id="BigBlueButton" name="BigBlueButton" align="middle">
					  <param name="movie" value="<?php Print($variable);?>client/BigBlueButton.swf?v=216" />
					  <param name="quality" value="high" />
					  <param name="allowfullscreen" value="true" />
					  <param name="bgcolor" value="#869ca7" />
					  <param name="wmode" value="window" />
					  <param name="allowScriptAccess" value="always" />
					 
						<object type="application/x-shockwave-flash" data="<?php Print($variable); ?>client/BigBlueButton.swf?v=VERSION" width="100%" height="100%" align="middle">
						  <param name="quality" value="high" />
						  <param name="bgcolor" value="#869ca7" />
						  <param name="allowScriptAccess" value="always" />
						  
							<a href="http://www.adobe.com/go/getflashplayer">
							  <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
							</a>
						 
					   </object>
				   
					</object>
				</div>
				
			</div>
			
			
			<div id="recordingView">
				<div id="sectionContainer"></div>
				<iframe id='streamline_recording' width='100%' height='100%' frameborder='0' scrolling='no' marginheight='0' marginwidth='0'></iframe>
			</div>
			<div id="optionView">
				<div class = "option_button playback_button">View Recording</div>
				<div class = "option_button live_button">Start Session</div>
			</div>
		</div>
		<div class="unit-25" id="rightContainer">
			<!-- Menu buttons above the Chat -->
			<div id="webinar_buttons">
				<div id="std_button" class="fullscreen_button" title="Full screen">
					<img src="./images/fullscreen_button.png" style='width: 100%; object-fit: contain; visibility:hidden' />
				</div>
				<div id="std_button" class="quiz_button" class="dropdown" id="dropdownMenu1" title="Quiz">
				  <div class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<img src="./images/fullscreen_button.png" style='width: 100%; object-fit: contain; visibility:hidden' />
				  </div>
				  <ul id="quiz_menu" class="dropdown-menu" aria-labelledby="dropdownMenu1">
				  </ul>
				</div>
				<div id="std_button" class="leave_button" title="Exit">
					<img src="./images/fullscreen_button.png" style='width: 100%; object-fit: contain; visibility:hidden' />
				</div>
			</div>
			
			<!-- Loads the chat module-->
			<div id="chat_module">
					<?php include 'Chat/Chat.php';?>
			</div>
			
			<!-- Question: Why is this separate from the 'webinar_buttons' div? -->
			<div id="button_handler">
				<!-- Handles the buttons quiz, leave & full screen-->
				<?php include 'BBB/BBB.php';?>
			</div>
		</div>
	</div>
	<!-- Question: What does this div do? What's it for? -->
    <div id="update-display"></div>
	<!-- Question: What does this div do? What's it for? -->
    <div id="notifications" aria-live="polite" role="region" aria-label="Chat Notifications"></div>
    
    <div class="unit-100" id="bottomContainer">
    	<!-- Loads the forum module -->
    	<div id="forum_module">
			<?php include 'Forums/Forum.php';?>
		</div>
	</div>
	
	<?php include 'Quiz/quiz.php';?>
	
</body>