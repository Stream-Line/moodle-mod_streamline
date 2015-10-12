<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript" src="./javascript.js"></script>
<script
    src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCJnj2nWoM86eU8Bq2G4lSNz3udIkZT4YY&sensor=false">
</script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="Quiz/quizForm.css">

<?php
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
	require_once(dirname(dirname(__FILE__)).'/lib.php');

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

	$direct =  $CFG->wwwroot . '/mod/streamline/lectureQuizMatt.php' . '?id='. $course->id;







?>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>StreamLine: Quiz Creation</title>
</head>

<body>

	<form action="quizSubmit.php?id=<?php echo $id; ?>" method="post">
	
		<div class="quiz_page_title">Quiz</div>
		<div class="quiz_msg">Please use the form below to create the quiz</div>	
				
		<div id="quiz_menu" class="btn-group">
			<button onclick="showQuiz(1)" id="1" type="button" class="btn btn-default quiz_selection_button">Quiz 1</button>
			<button type="button" class="btn btn-default add_quiz_button">+</button>  
		</div>
					
		<div class="quizzes">
			<div class="quiz" id ="1">
			<input type='hidden' value='0' name="quiz[]">
			
				<div class="input_fields_wrap">
				
			
					<!--<button class="add_answer">Add More answers</button> -->
					<div class="question" id = "1">
						<div class="panel panel-default">
						  <div class="panel-heading">
							<div class="question_label">Question:</div>
							<input class="question_input" type="text" name="mytext_question1[]" placeholder="Please enter question here">
						  </div>
						  <div class="panel-body">
								<span class="options_tag"> Options </span> 
								<div class="answer">
									<input type='hidden' value='0' name="correct_answer1,1[]">
									<div class="options_checkbox"><input class="checkbox_answer" type="checkbox" name="correct_answer1,1[]" value="1"></div>
									<input class="answer_input"  type="text" name="mytext_answer1,1[]">
									<div href="#" class="remove_field"></div>
								</div>
								<button class="quiz_options add_answer btn btn-default">Add More Answers</button>
						  </div>
						</div>
					</div>
					<button class="add_question btn btn-default">Add More Questions</button>
				</div>
			</div>
		</div>
		
		<input class="btn btn-default quiz_submit_button" type="submit">

	</form>
	
	
	<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
	
	
	<script type="text/javascript">
		envs: {
        	browser: true
		}
		$(document).ready(function() {
		    var max_fields      = 5; //maximum input boxes allowed
		    var wrapper         = $("div.input_fields_wrap"); //Fields wrapper
		    var answer_field    = $(".answer"); //Fields wrapper
		    var add_button      = $(".add_question"); //Add button ID
		    var add_answer      = $(".add_answer"); //Add button ID
			
			var question_counter = []; //stores question count in each quiz
			question_counter.push(1);
			
		 	var quiz_count = 1; //Counts number of quizzes
			
		    $(document).on('click','.add_question',function(e){
		        e.preventDefault();
	
				var quiz_number = $(this).closest("div.quiz").attr("id");
	
		       if(question_counter[quiz_number-1] < max_fields)//max input box allowed
		       { 
		        	question_counter[quiz_number-1] += 1; //text box increment
					var x  = question_counter[quiz_number-1];
					
					// Applies an id to each question and creates an array of answers with that id appended on the end
		            $(this).parent('div.input_fields_wrap').append('<div class="question" id = '+ x + '><div class="panel panel-default"><div class="panel-heading"><div class="question_label">Question:</div><input class="question_input" type="text" name="mytext_question' + quiz_number + '[]"  placeholder="Please enter question here"/><div href="#" class="remove_question"></div></div><div class="panel-body"><br><span class="options_tag"> Options </span><div class="answer"><input type="hidden" name="correct_answer'+x+ ',' + quiz_number + '[]" value="0"><div class="options_checkbox"><input class="checkbox_answer" type="checkbox" name="correct_answer'+x+ ',' + quiz_number + '[]" value="1"></div><input class="answer_input" type="text" name="mytext_answer'+ x + ',' + quiz_number + '[]"><div href="#" class="remove_field"></div></div><button class="add_answer btn btn-default">Add More answers</button></div></div></div>'); //add input box
					$(this).parent('div.input_fields_wrap').append($('.add_question')[quiz_number-1]);
			   }
		    });
	
	
			$(document).on('click','.add_answer',function(e){
		        e.preventDefault();
				
				var quiz_number = $(this).closest("div.quiz").attr("id");
				
				var y =  $(this).closest("div.question").attr("id"); // gets the question id to assign the answer to the right id
				var q =  $(this).closest("div.quiz").attr("id"); // gets the question id to assign the answer to the right id
				// alert(y);
				console.log($(this).parent('div'));
				$(this).parent('div').append('<div class="answer"><input type="hidden" name="correct_answer'+y+ ',' + q + '[]" value="0"><div class="options_checkbox"><input class="checkbox_answer" type="checkbox" name="correct_answer'+ y + ',' + q +  '[]" value="1"></div><input class="answer_input" type="text" name="mytext_answer'+ y + ',' + q +  '[]"> <a href="#" class="remove_field"><img src="../images/close_light.png" height="15" width="15" style="visibility: hidden;"></a></div>'); //add input box
				$(this).parent('div').append($(this));
		    });
			
			$(document).on('click','.add_quiz_button',function(e){
		        e.preventDefault();
				quiz_count++;	
				hideQuizzes();
				$('div.quizzes').append('<div class="quiz" id ="' + quiz_count + '"> <input type="hidden" value="0" name="quiz[]"><div href="#" class="remove_quiz"><img src="./images/close.png" height="25px" width="25px">Remove Quiz</div> <div class="input_fields_wrap"><div class="question" id = "1"><div class="panel panel-default"><div class="panel-heading"><div class="question_label">Question:</div><input class="question_input" type="text" name="mytext_question' + quiz_count + '[]" placeholder="Please enter question here"></div><div class="panel-body"><span class="options_tag"> Options </span><div class="answer"><input type="hidden" value="0" name="correct_answer1' + ','  + quiz_count + '[]"><div class="options_checkbox"><input class="checkbox_answer" type="checkbox" name="correct_answer1' + ','  + quiz_count + '[]" value="1"></div><input class="answer_input" type="text" name="mytext_answer1' + ',' +  quiz_count + '[]"><div href="#" class="remove_field"></div></div><button class="quiz_options add_answer btn btn-default">Add More Answers</button></div></div></div><button class="add_question btn btn-default">Add More Questions</button></div></div>');
				showQuiz(quiz_count);
				
				question_counter.push(1);
				$(this).closest('div#quiz_menu').append('<button onclick="showQuiz('+quiz_count+')" id="'+quiz_count+'" type="button" class="btn btn-default quiz_selection_button">Quiz '+quiz_count+'</button>');
				$(this).closest('div#quiz_menu').append($(this));
			});
		 
		   
		    $(document).on("click",".remove_field", function(e){ //user click on remove text
		       e.preventDefault(); 
				
		       $(this).parent('div').remove(); 
	        
		   	});   
		    
		    
		    
			$(document).on('click','.remove_question',function(e){
				var y =  parseInt($(this).closest("div.question").attr("id")) + 1; // gets the question id to assign the answer to the right id
				var q =  parseInt($(this).closest("div.quiz").attr("id")) - 1; // gets the question id to assign the answer to the right id
		
				for(var i = y; i <= question_counter[q]; i++)
				{
					$('.question').eq(i-1).attr('id',i-1); 
		
					
					for (var j = 1; j <= document.getElementsByName("mytext_answer" + i + "," + (q+1) + "[]").length; j++) {
						document.getElementsByName("mytext_answer" + i + "," + (q+1) + "[]")[j-1].setAttribute("name",("mytext_answer" + (i-1) + "," + (q+1) + "[]"));
						document.getElementsByName("correct_answer" + i + "," + (q+1) + "[]")[j-1].setAttribute("name",("correct_answer" + (i-1) + "," + (q+1) + "[]"));
						document.getElementsByName("correct_answer" + i + "," + (q+1) + "[]")[j-1].setAttribute("name",("correct_answer" + (i-1) + "," + (q+1) + "[]"));
						
					}		
					
				}
				
				$(this).parent('div').parent('div').remove(); 
			
		
		
				question_counter[q] -= 1;
			
	
	
		    });
	
		   
			$(document).on("click",".remove_quiz", function(e){ //user click on remove text
		        e.preventDefault(); 
		       
				var id =  parseInt($(this).closest("div.quiz").attr("id"));
				$(".quiz_selection_button[id='"+id+"']").remove();

		        var q =  parseInt($(this).closest("div.quiz").attr("id")) - 1; // gets the question id to assign the answer to the right id
				var l = q+2; // look at every element after q 
				for(var i = l; i <= quiz_count; i++)
				{
					$('.quiz').eq(i-1).attr('id',i-1);
					$(".quiz_selection_button[id="+i+"]").text("Quiz " + (i-1));
					$(".quiz_selection_button[id="+i+"]").attr("onclick", "showQuiz("+(i-1)+")");
					$(".quiz_selection_button[id="+i+"]").attr('id',i-1);
					
					for (var j = 1; j <= document.getElementsByName("mytext_question" + i + "[]").length; j++) 
					{
	
						document.getElementsByName("mytext_question" + i + "[]")[j-1].setAttribute("name",("mytext_question" + (i-1) + "[]"));
						
						for (var k = 1; k <= document.getElementsByName("mytext_answer" + j + "," + i + "[]").length; k++) {
							document.getElementsByName("mytext_answer" + j + "," + i + "[]")[k-1].setAttribute("name",("mytext_answer" + (j) + "," + (i-1) + "[]"));
							document.getElementsByName("correct_answer" + j + "," + i + "[]")[k-1].setAttribute("name",("correct_answer" + (j) + "," + (i-1) + "[]"));
							document.getElementsByName("correct_answer" + j + "," + i + "[]")[k-1].setAttribute("name", ("correct_answer" + (j) + "," + (i-1) + "[]"));
							
						}	
					
					}	
					
				}
	
				question_counter.splice((q-1), 1);
				 
		        $(this).parent('div').remove(); 
		        quiz_count--;
				showQuiz(id-1);
			});   
		

		});
	
		function showQuiz(id) {
			for(i=1;i<=$("div.quiz").length;i++) {
				$("div.quiz[id='"+i+"']").fadeOut(1);
			}
			$("div.quiz[id='"+id+"']").fadeIn();
		}
		
		function hideQuizzes() {
			for(i=1;i<=$("div.quiz").length;i++) {
				$("div.quiz[id='"+i+"']").fadeOut(1);
			}
		}
	
	$('body').on('click', '.checkbox_answer', function() { 
		console.log("Changed");
		if(this.checked) {
			$(this).parent('div').css("background-color", "#C2FFC2");
			//Do stuff
		} else {
			$(this).parent('div').css("background-color", "#EEEEEE");
		}
	});
	//http://stackoverflow.com/questions/2105815/weird-behaviour-of-iframe-name-attribute-set-by-jquery-in-ie
	// explains why submitname appears in IE and why IE cannot be used to create a quiz
	
	</script>
	
</body>

</html>

