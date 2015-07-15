<link rel="stylesheet" href="webrtc_webinar/css/main.css" type="text/css">
<script src="http://simplewebrtc.com/latest.js"></script>
<?php include('server.php'); ?>

<div>
	<button id="startButton">Start</button>
	<button id="stopButton">Stop</button>
</div>

<video id="localVideo"></video>
<div id="remoteVideo"></div>

<script src="webrtc_webinar/scripts/webinar_functions.js"></script>

<script type="text/javascript">
	/* variables and function defined in webinar_functions.js */
	startButton.addEventListener("click", startLiveStream);
	stopButton.addEventListener("click", stopLiveStream);
	
	ids = <?= json_encode(get_ids()); ?>;
	user = <?= json_encode($USER); ?>;
	course = <?= json_encode($COURSE); ?>;
	
	toggleButtons();
	connectToCourseLiveStream(course.shortname);
</script>