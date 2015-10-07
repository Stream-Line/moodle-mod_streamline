<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
	require_once(dirname(dirname(__FILE__)).'/locallib.php');
	require_once(dirname(dirname(__FILE__)).'/lib.php');
	
	$bbbsession['salt'] = trim($CFG->BigBlueButtonSaltKey);
	$bbbsession['url'] = trim(trim($CFG->ServerURLforBigBlueButton),'/').'/';
	$id = optional_param('id', 0, PARAM_INT);
	$cm = get_coursemodule_from_id('streamline', $id, 0, false, MUST_EXIST);
	$streamline = $DB->get_record('streamline', array('id' => $cm->instance), '*', MUST_EXIST);
	$meeting = $streamline -> meetingid;
	$course = $streamline -> course;
	$ids = $streamline -> id;
	$dash = "-";
	$meetingid=$meeting.$dash.$course.$dash.$ids;
	$meetingRunningUrl = bigbluebuttonbn_getIsMeetingRunningURL( $meetingid, $bbbsession['url'], $bbbsession['salt'] );
	$recordingsURL = bigbluebuttonbn_getRecordingsArray($meetingid, $bbbsession['url'], $bbbsession['salt'] );
	$end_meeting_url = end_meeting();
	
	$userID = $USER->id;
    $context = context_module::instance($cm->id);
	$context = context_course::instance($COURSE->id);
	$roles = get_user_roles($context, $USER->id, true);
	
	$participants = $streamline->participants;
	
	if( $streamline->participants == null || $streamline->participants == "[]" ){
    //The room that is being used comes from a previous version
		$moderator = has_capability('mod/streamline:moderate', $context);
	} else {
		$moderator = bigbluebuttonbn_is_moderator($userID, $roles, $participants);
	}
	$administrator = has_capability('moodle/category:manage', $context);
	//104.155.215.138
	$ipaddress = trim($CFG->ServerURLforBigBlueButton);
	$variable2 = substr($ipaddress, 0, strpos($ipaddress, "b"));
	$variable = trim(trim($variable2),'/').'/';
	$moodle_dir = $CFG->wwwroot;
	
	//Determine if user is the teacher
	$teacher_role = user_has_role_assignment($USER->id,3);
	if($teacher_role == 1) {
		$teacher = true;
	} else {
		$teacher = false;
	}

	//Determine if the meeting has ended based on on the 'meetingended' field in the DB
	if($streamline -> meetingended == 1) {
		$meetingEnded = true;
	} else {
		$meetingEnded = false;
	}

	?>
	
	<link rel="stylesheet" type="text/css" href="streamline.css">

 <!--All the Javascript-->
 <?php include 'BBB/BBB_SL_S.php';?>

  <body>
	
  </body>

  <?php include 'BBB/BBB_SL_E.php';?>
  
</html>

