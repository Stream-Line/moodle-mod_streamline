<?php 

	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); 
	global $DB, $CFG, $COURSE;
	
	$qid = $_POST['qid'];
	$sid = $_POST['sid'];
	$cid = $_POST['cid'];
	$correct_answers = $_POST["answer"];
	$string_qid = "'". $qid . "'";
	$string_sid = "'". $sid . "'";
	$string_cid = "'". $cid . "'";
	$sql =  "SELECT id, quizid, streamlineid, courseid, userid, answers FROM mdl_streamline_quiz WHERE streamlineid= '".$sid."' AND courseid= '".$cid."' AND quizid= '".$qid."'";
	$records = $DB->get_records_sql($sql); //array($string_sid ,$string_cid ,$string_qid)

	
	$answers = null;
	$answers_count = 0;
	foreach ($records as $id => $record) {
		$answers[$answers_count] = $record->answers;
		$answers_count++;
	}

	//////////////////////////////////////////////////////////////////////////
	/* This section converts all correct answers into a comparable way by splitting
	it into an array of strings where each string is the correct answers for a question.
	Therefore the number of questions there are is the number of elements in this
	array.
	*/
	/////////////////////////////////////////////////////////////////////////
	
	$questions_count = 0;
	$questions = null;
	$group_answers = null;
	$grouped_answer_count = 0;
	
	
	for($i = 0; $i < strlen($correct_answers)-3; $i++)
	{
		if(substr($correct_answers,$i+1,1) == ".")
		{
			$is_unique = 1;
			if($grouped_answer_count == 0)
			{
				$questions[0] = substr($correct_answers,$i,1);
				$questions_count++;
			}
			else{
				
				for($k = 0; $k < sizeof($questions); $k++)
				{
					if($questions[$k] == substr($correct_answers,$i,1))
					{
						$is_unique = 0;
					}
				}
			}
			if($is_unique == 1)
			{
				$questions[$questions_count] = substr($correct_answers,$i,1);
				$questions_count++;
				$group_answers[$grouped_answer_count] = substr($correct_answers,$i,4);
				for($j = ($i + 1); $j < strlen($correct_answers)-3; $j++)
				{
					if(substr($correct_answers,$i,1) == substr($correct_answers,$j,1) && substr($correct_answers,$i+1,1) == '.' && substr($correct_answers,$j+1,1) == '.')
					{
						$group_answers[$grouped_answer_count] = $group_answers[$grouped_answer_count] . substr($correct_answers,$j,4);
					
					}

				}
				$grouped_answer_count++;
			}
		}

	}
	
	//////////////////////////////////////////////////////////////////////////
	/* This section converts all answers from each student in a way to compare
	to the actual answers. This will ultimately provide an array of arrays where
	each array will represent a students answers and within that will be the students
	answers for each question.
	*/
	/////////////////////////////////////////////////////////////////////////

	$group_student_answers_db = null;
	$grouped_answer_count_db = 0;
	
	
	for($m = 0; $m < sizeof($answers); $m++)
	{
	$questions_count = 0;
	$questions_students = null;
	$group_student_answers = null;
	$grouped_answer_count = 0;
	
		for($i = 0; $i < strlen($answers[$m])-3; $i++)
		{
			if(substr($answers[$m],$i+1,1) == ".")
			{
				$is_unique = 1;
				if($grouped_answer_count == 0)
				{
					$questions_students[0] = substr($answers[$m],$i,1);
					$questions_count++;
				}
				else{
					
					for($k = 0; $k < sizeof($questions_students); $k++)
					{
						if($questions_students[$k] == substr($answers[$m],$i,1))
						{
							$is_unique = 0;
						}
					}
				}
				if($is_unique == 1)
				{
					$questions_students[$questions_count] = substr($answers[$m],$i,1);
					$questions_count++;
					$group_student_answers[$grouped_answer_count] = substr($answers[$m],$i,4);
					for($j = ($i + 1); $j < strlen($answers[$m])-3; $j++)
					{
						if(substr($answers[$m],$i,1) == substr($answers[$m],$j,1) && substr($answers[$m],$i+1,1) == '.' && substr($answers[$m],$j+1,1) == '.')
						{
							$group_student_answers[$grouped_answer_count] = $group_student_answers[$grouped_answer_count] . substr($answers[$m],$j,4);
						
						}

					}
					$grouped_answer_count++;
				}
			}
		}
		
		$group_student_answers_db[$grouped_answer_count_db] = $group_student_answers;
		$grouped_answer_count_db++;

	}
	
	///////////////////////////////////////////////////////////////////////////
	//prints arrays for testing
	//print_r($group_answers);
	//print_r($group_student_answers_db);
	//print_r($questions);
	
	$ratio_array = null;
	$ratio_count = 0;
	
	
	for($i = 0; $i < sizeof($group_answers); $i++){
		$student_Sum = 0;
		$correct_sum = 0;
		for($j = 0; $j < sizeof($group_student_answers_db); $j++){
			for($k = 0; $k < sizeof($group_student_answers_db[$j]); $k++){
				if($group_answers[$i] == $group_student_answers_db[$j][$k])
				{
					$correct_sum++;
				}
			}
			$student_Sum++;
		}
		if($student_Sum != 0)
		{
			$ratio_array[$ratio_count] = (int) $correct_sum / (int) $student_Sum;
			$ratio_count++;
		}
		
	}
	//prints ratio array for testing
	//print_r($ratio_array);
	//prints answers array for testing
	//print_r($answers);

	$data_array = null;
	$data_array_count = 0;
	
	for($i = 1; $i < sizeof($questions); $i++){ //need to fix questions array to have initial element as the first
		$data_array[$data_array_count] = array($questions[$i], $ratio_array[$i-1]  );
		$data_array_count++;
	
	}
	
	//prints the data array for testing
	//print_r($data_array); 
	
	$results = new stdClass();
	$results->quiz="Quiz " . $qid;
	$results->data = $data_array;

	echo json_encode($results);

?>