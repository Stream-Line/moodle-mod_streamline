<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); 
	global $DB, $CFG, $COURSE;
	
	$qid = $_POST['qid'];
	$sid = $_POST['sid'];
	$cid = $_POST['cid'];
	$uid = $_POST['uid'];

	$string_qid = "'". $qid . "'";
	$string_sid = "'". $sid . "'";
	$string_cid = "'". $cid . "'";
	$string_cid = "'". $cid . "'";

	$sql =  "SELECT id, quizid, streamlineid, courseid, userid, answers FROM mdl_streamline_quiz WHERE streamlineid= '".$sid."' AND courseid= '".$cid."' AND quizid= '".$qid."' AND userid= '".$uid."'";
	$record = $DB->get_record_sql($sql); //array($string_sid ,$string_cid ,$string_qid)

	$array = explode(";", $record->answers);
	$answers = array_filter($array);

	echo json_encode($answers);
?>