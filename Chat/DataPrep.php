<?php
	if(has_capability("moodle/course:manageactivities", context_module::instance($cm->id), $USER->id, false)){
		// get enroled users with role id 3 or 5
		$enroled = get_role_users(3, context_course::instance($course->id)) 
				 + get_role_users(5, context_course::instance($course->id));
		
		// add each user to the hashed and non-hashed lists
		foreach ($enroled as $uid => $usr) {
			$Sval = bin2hex($uid);
			$HStuList .= $Sval.',';
			$StuList .= $uid.',';
		}

		$HStuList .= '-';
		$StuList .= '-';
	}
?>
