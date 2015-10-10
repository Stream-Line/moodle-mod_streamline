<?php
	if(has_capability("moodle/course:manageactivities", context_module::instance($cm->id), $USER->id, false)){
		// get enroled users with role id 1 to 5
		$enroled = get_role_users(1, context_course::instance($COURSE->id))
				 + get_role_users(2, context_course::instance($COURSE->id))
				 + get_role_users(3, context_course::instance($COURSE->id))
				 + get_role_users(4, context_course::instance($COURSE->id))
				 + get_role_users(5, context_course::instance($COURSE->id));
		
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
