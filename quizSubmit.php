<html>

    <body>
		<?php 
		
		require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
		require_once(dirname(__FILE__).'/lib.php');

		Global $PAGE;
		$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
		$n  = optional_param('n', 0, PARAM_INT);  // ... streamline instance ID - it should be named as the first character of the module.

		if ($id) {
			$cm         = get_coursemodule_from_id('streamline', $id, 0, false, MUST_EXIST);
			$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
			$streamline  = $DB->get_record('streamline', array('id' => $cm->instance), '*', MUST_EXIST);
		} else if ($n) {
			$streamline  = $DB->get_record('streamline', array('id' => $n), '*', MUST_EXIST);
			$course     = $DB->get_record('course', array('id' => $streamline->course), '*', MUST_EXIST);
			$cm         = get_coursemodule_from_instance('streamline', $streamline->id, $course->id, false, MUST_EXIST);
		} else {
			error('You must specify a course_module ID or an instance ID');
		}
		

		$cm = get_coursemodule_from_id('streamline', $id, 0, false, MUST_EXIST);
		$streamline = $DB->get_record('streamline', array('id' => $cm->instance), '*', MUST_EXIST);

		require_login($course, true, $cm);
		
		
			/*if(!is_file("Quiz.xml"))
			{
                $openF = fopen("Quiz.xml", "a");

                if(fwrite($openF, $str)){}
                else
				{
                        echo "write xml error";
                }
            }
			$openF = fopen("Quiz.xml", "r+");
            fseek($openF, -7, SEEK_END);*/
			$str = "<quizzes StreamLine_id='1'> \n"; 
			
			$quiz = $_POST['quiz'];
			$quiz_num = 1;
			foreach( $quiz as $q_count => $q ) {	
				$q_counting = $quiz_num;
				$check_valid_quiz = 0;
				
				while($check_valid_quiz == 0) {
					$q_counting = $quiz_num;
					if(isset($_POST['mytext_question' . $q_counting]))
					{
						$quiz_num = $quiz_num  +1; 
						$check_valid_quiz = 1;
						$name = $_POST['mytext_question' . $q_counting];
						$i = 1;
						$j = 1;
						$count = 0;
						/* Problem where if you delete a question the numbering system will also error*/
						
						//print "Quiz: " . ($q_count+1);
						$str .= "<quiz id='" . ($q_count+1) . "'> \n"; 
						
						foreach( $name as $question_count => $v ) {	
							//print " Question " . ($question_count+1) . ": " .  $v . "\n";
							$str .= "<question text ='" . $v;
							$str .= "' id='" . ($question_count+1) . "'> \n";
							
							
							$check = 0;
							
							while($check == 0)
							{
								if(isset($_POST['mytext_answer'.$i.','.$q_counting])){
									$check = 1;
									$count = 0;
									$answer_Class = 'mytext_answer'.$i.','.$q_counting;
									$correct_answer_Class = $_POST['correct_answer'.$i.','.$q_counting];
									
									$answers = $_POST[$answer_Class];

									foreach($answers as $v => $n ){
										if($count + 1 < count($correct_answer_Class))
										{
											if(($correct_answer_Class[$count ]) == '0' && $correct_answer_Class[$count +1 ] == '1')
											{
												$count += 2;
												//print $n . "\n";
												$str .= "<option text='".$n."' answer='true' /> \n";
											}
											else
											{
												$str .= "<option text='".$n."' answer='false' /> \n";
												$count += 1;
											}
										}
										else{
											$str .= "<option text='".$n."' answer='false' /> \n";
										
										
										}
									}
								}
								$i = $i + 1;
							}
							$j = $j + 1;	
							$str .= "</question> \n";
							
						}
					}
					else{
						$quiz_num = $quiz_num  +1; 
					
					}
				}
				
				$str .= "</quiz> \n";
			}
			
			$str .= "</quizzes> \n";

			$dataObj = new stdClass();
			$dataObj->id = $streamline -> id;
			$dataObj -> quiz_xml = $str;
			$table = 'streamline';
			$DB -> update_record($table,$dataObj );
			//fwrite($openF, $str);
			include 'BBB/stream_view.php';
		?>	

    </body>
</html>






