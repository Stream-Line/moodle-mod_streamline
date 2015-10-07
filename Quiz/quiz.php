<?php
	echo $streamline->quiz_xml;
?>

<!-- Quiz -->

<!-- Modal -->
<div class="modal fade" id="quizModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-header header-shadow">
			<button type="button" id="modal_close" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Quiz</h4>
		</div>
		<div class="modal-body">
		
			<form id="quizForm">
			</form>
			
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	  
	</div>
</div>

<script>

	function loadXMLDoc(filename)
	{
		if (window.XMLHttpRequest)
		{
			xhttp=new XMLHttpRequest();
		}
		else // code for IE5 and IE6
		{
			xhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xhttp.open("GET",filename,false);
		xhttp.send();
		return xhttp.responseXML;
	}
	
	$(".quiz_button").hover(function() {
		$( ".quiz_button" ).toggleClass( "open" )
		if($("#dropdownMenu1").attr("aria-expanded") == true) {
			$("#dropdownMenu1").attr("aria-expanded", false)		
		} else {
			$("#dropdownMenu1").attr("aria-expanded", true)
		}
	}, function() {
		$( ".quiz_button" ).toggleClass( "open" )
		if($("#dropdownMenu1").attr("aria-expanded") == true) {
			$("#dropdownMenu1").attr("aria-expanded", false)		
		} else {
			$("#dropdownMenu1").attr("aria-expanded", true)
		}
	});

	// XML string to JSON    
	var x2js = new X2JS(); 
	var xml = loadXMLDoc('Quiz/quiz_example.xml');
	var xmlText = new XMLSerializer().serializeToString(xml);
	xmlText = <?=json_encode($streamline->quiz_xml)?>;
	console.log("QUIZ");
	console.log(xmlText);
	var quizJSON = x2js.xml_str2json( xmlText );

	var data;
	if(quizJSON.quizzes.quiz instanceof Array) {
		number_of_quizzes = quizJSON.quizzes.quiz.length;
	} else {
		number_of_quizzes = 1;
	}
	var answerArray = new Array(number_of_quizzes);
	
	var currentQuizId = null;
	var quizCompleted = [];
	
	var sid = null;
	var cid = null;
	var uid = null ;
	var qid = null;
	
	if(quizJSON.quizzes.quiz instanceof Array) {
		for(i=0; i< quizJSON.quizzes.quiz.length; i++) {
			var quiz = "<li class='quizOption' data-toggle='modal' data-target='#quizModal' onclick='SubmissionCheck("+(i)+")'>Quiz "+(i+1)+"</li>";
			$("#quiz_menu").append(quiz);
			quizCompleted.push(false);
		}		
	} else {
		console.log("Added drop down");
		i = 0;
		var quiz = "<li class='quizOption' data-toggle='modal' data-target='#quizModal' onclick='SubmissionCheck("+(i)+")'>Quiz "+(i+1)+"</li>";
		$("#quiz_menu").append(quiz);
		quizCompleted.push(false);
	}
	
	//Determines if user has already submitted
	//id  - The index
	function SubmissionCheck(id) 
	{
		sid = <?=json_encode($streamline->id)?>;
		cid = <?=json_encode($COURSE->id)?>;
		uid = <?=json_encode($USER->id)?>;
		qid = id + 1;
				
		console.log("LOG: Checking for submission");
		console.log("LOG: Passing the following data");
		
		query_data={ "qid" : qid, "sid" : sid, "cid" : cid, "uid" : uid};
		console.log(query_data);

		$("#quizForm").empty();
		var loading_div = "<div class='quiz_loading'><img class='quiz_loading_img' src='Quiz/images/loading.gif' height='200px'></div>"
		$("#quizForm").append(loading_div);
			
		performCheck(query_data, id);
		
	}
	
	function performCheck(query_data, id) {
		
		$.post( "Quiz/quiz_submit_check.php", query_data, function( result_data ) {
			var answers = JSON.parse(result_data);
			if(answers == null || answers.length == 0) {
				console.log("LOG: Submission has not occured");
				populateQuiz(id);
			} else {
				console.log("LOG: Submission has occured");
				quizCompleted[id] = true;
				currentQuizId = id+1;
				answerArray[id] = answers;
				QuizSummary(id+1);
			}
			console.log(answers);
		});
	}
	
	window.setInterval(function(){
	  checkSummary();
	}, 5000);
		
	function checkSummary() {
		if(currentQuizId == null) {
			console.log("LOG: No Quiz has been selected");
			return;
		}
		
		if(quizCompleted[currentQuizId-1] == false) {
			console.log("LOG: Selected Quiz has not been completed");
			return;
		}
		
		if(sid == null) {
			sid = <?=json_encode($streamline->id)?>;
		}
		if(cid == null) {
			cid = <?=json_encode($COURSE->id)?>;
		}
		if(uid == null) {
			uid = <?=json_encode($USER->id)?>;
		}
				
		console.log("LOG: Updating Summary");
		console.log("LOG: Passing the following data");
		
		query_data={ "qid" : currentQuizId, "sid" : sid, "cid" : cid, "uid" : uid};
		console.log(query_data);
			
		performUpdate(query_data, currentQuizId-1);
	}
	
	function performUpdate(query_data, id) {
		
		$.post( "Quiz/quiz_submit_check.php", query_data, function( result_data ) {
			var answers = JSON.parse(result_data);
			if(answers == null || answers.length == 0) {
				console.log("ERROR: Issue with quiz entry for student");
			} else {
				updateSummary(id+1);
			}
		});
	}
	
	function updateSummary(id) {
	
		sid = <?=json_encode($streamline->id)?>;
		cid = <?=json_encode($COURSE->id)?>;
		var answers = obtainAnswers(id-1);
		var answering = ""; // have to make this a string to work for some reason
		for(var i = 0; i < answers.length; i++)
		{
			answering += answers[i] + ";";
		}
		
		data={ "qid" : id, "sid" : sid, "cid" : cid, "answer" : answering};
		console.log(data);
		
		obtainResultsData(data);
	}
	
	var right_id;
	var wrong_id;
	
	var newRightPercentage;
	var newWrongPercentage;
			
	function updateProgressBars(data) {
		console.log("LOG: Updating Progress Bars with following data");
		var number_of_questions = data.data.length;
		for(i=0; i<number_of_questions; i++) {
			right_percentage = data.data[i][1]*100;
			wrong_percentage = 100-right_percentage;
			right_id = "Question_correct"+(data.data[i][0]-1);
			wrong_id = "Question_wrong"+(data.data[i][0]-1);
			
			newRightPercentage = right_percentage+"%";
			newWrongPercentage = wrong_percentage+"%";
			
			console.log(right_id);
			console.log(wrong_id);
			
			console.log("New Right Percentage: " + newRightPercentage);
			console.log("New Wrong Percentage: " + newWrongPercentage);			
			
			$("#"+right_id+"").width(newRightPercentage);
			$("#"+right_id+"").text(newRightPercentage);

			$("#"+wrong_id+"").width(newWrongPercentage);
			$("#"+wrong_id+"").text(newWrongPercentage);

			//$("#"+class_div+"").append('<div class="progress"><div id="'+right_id+'" class="progress-bar progress-bar-striped progress-bar-success active" style="width: '+right_percentage+'%">'+right_percentage+'%</div><div id="'+wrong_id+'" class="progress-bar progress-bar-striped progress-bar-danger active" style="width: '+wrong_percentage+'%">'+wrong_percentage+'%</div></div>');
		}	
	}
	
	function obtainResultsData(data){
		$.post( "Quiz/quiz_results.php", data, function( data ) {
			summaryData = JSON.parse(data);
			updateProgressBars(summaryData);
	   });
	}
	
	function QuizSummary(id) {
	
		$('.modal-title').text("Quiz " + (id));
		$("#quizForm").empty();
		
		console.log("Obtaining Results");
		
		sid = <?=json_encode($streamline->id)?>;
		cid = <?=json_encode($COURSE->id)?>;
		var answers = obtainAnswers(id-1);
		var answering = ""; // have to make this a string to work for some reason
		for(var i = 0; i < answers.length; i++)
		{
			answering += answers[i] + ";";
		}
		
		console.log("This is the answers " + answering);
		data={ "qid" : id, "sid" : sid, "cid" : cid, "answer" : answering};
		console.log(data);
		obtainSummaryData();
		  function obtainSummaryData(){
			   $.post( "Quiz/quiz_results.php", data, function( data ) {
				console.log(data);
				summaryData = JSON.parse(data);
				displayQuizSummary(summaryData);
			   });
        }
		
	}
	
	var question_summary_height = -1;
	function displayQuizSummary(data) {
	
		var answers = obtainAnswers(currentQuizId-1);
		console.log(answers);
			
		//Obtain number of quizzes
		if(quizJSON.quizzes.quiz instanceof Array) {
			quiz = quizJSON.quizzes.quiz[currentQuizId-1];
		} else {
			quiz = quizJSON.quizzes.quiz;
		}
		
		//Obtain number of questions
		if(quiz.question instanceof Array) {
			number_of_questions = quiz.question.length;
		} else {
			number_of_questions = 1;
		}
		
		var percentage;
		var answer_index = 0;
		
		var tick = '<img class="tick" src="./Quiz/images/tick.png">';
		var cross = '<img class="cross" src="./Quiz/images/cross.png">';
	
		for(i=0; i<number_of_questions; i++) {

				summary_div = "summary_overview_q"+(i+1);		
				student_div = "student_overview_q"+(i+1);
				class_div = "class_overview_q"+(i+1);
		
				$("#quizForm").append('<div class="summary_overview" id="'+summary_div+'"></div>');
				
				$("#"+summary_div+"").append('<div class="student_overview" id="'+student_div+'"></div>');
				$("#"+summary_div+"").append('<div class="class_overview" id="'+class_div+'"></div>');
		
				question_div_id	= "question_" + (i+1);
		
				if(number_of_questions == 1) {
					$("#"+student_div+"").append("<div id="+question_div_id+"><b>Question " + (i+1) + "</b> : " + quiz.question._text + "</div>");
					question = quiz.question;
				} else {
					$("#"+student_div+"").append("<div id="+question_div_id+"><b>Question " + (i+1) + "</b> : " + quiz.question[i]._text + "</div>");
					question = quiz.question[i];
				}
				
				//Obtain number of options
				if(question.option instanceof Array) {
					number_of_options = question.option.length;
				} else {
					number_of_options = 1;
				}
				
				var isCorrect = true;
				for(j=0; j<number_of_options; j++) {
					if(number_of_options == 1) {
						var option = question.option._text;
					} else {
						var option = question.option[j]._text;
					}
					answer_div_id = (i+1)+"_"+(j+1);
					answer_id = (i+1)+"."+(j+1);

					option_text = option;

					if(answerArray[currentQuizId-1].indexOf(answer_id) != -1) {
						if(answers.indexOf(answer_id) != -1) {
							option_text=option+tick;
						} else {
							option_text=option+cross;
							isCorrect = false;
						}
					}	

					$("#"+student_div+"").append("<input type='checkbox' id='"+answer_div_id+"' name='quiz"+(id+1)+" value='"+option+"'/>"+option_text+"<br>");
					
					if(answerArray[currentQuizId-1].indexOf(answer_id) != -1) {
						$("#"+answer_div_id+"").prop('checked', true);					
					}
					
					if(answers.indexOf(answer_id) != -1) {
						$("#"+answer_div_id+"").append("test");
					}
					
					$("#"+answer_div_id+"").attr("disabled", true);
				}
				if(isCorrect) {
					$("#"+question_div_id+"").append("<span class='correct_answer'>Correct<br></div>");					
				} else {
					$("#"+question_div_id+"").append("<span class='incorrect_answer'>Incorrect<br></div>");	
				}
				$("#"+student_div+"").append("<br>");
				
				if(question_summary_height == -1) {
					question_summary_height = $("#"+student_div+"").height();
				}

				$("#"+class_div+"").height(question_summary_height);
				$("#"+class_div+"").append("<b>Question "+(i+1)+" - Class Summary</b><br>");				
				
				if(i == data.data[answer_index][0]-1) {
					right_percentage = data.data[answer_index][1]*100;
					wrong_percentage = 100-right_percentage;
					var right_id = "Question_correct"+i;
					var wrong_id = "Question_wrong"+i; 
					$("#"+class_div+"").append('<div class="progress"><div id="'+right_id+'" class="progress-bar progress-bar-striped progress-bar-success active" style="width: '+right_percentage+'%">'+right_percentage+'%</div><div id="'+wrong_id+'" class="progress-bar progress-bar-striped progress-bar-danger active" style="width: '+wrong_percentage+'%">'+wrong_percentage+'%</div></div>');
					answer_index += 1;
				} else {
					$("#"+class_div+"").append('<div>No Data Available</div>');
				}
		}
	}
	
	function obtainAnswers(id) {
	
		var answers = [];

		if(quizJSON.quizzes.quiz instanceof Array) {
			number_of_quizzes = quizJSON.quizzes.quiz.length;
			quiz = quizJSON.quizzes.quiz[id];
		} else {
			number_of_quizzes = 1;
			quiz = quizJSON.quizzes.quiz;
		}
		
		console.log(quiz);
		
		if(id <= number_of_quizzes) {
			
			//Obtain number of questions
			if(quiz.question instanceof Array) {
				number_of_questions = quiz.question.length;
			} else {
				number_of_questions = 1;
			}

			for(i=0; i<number_of_questions; i++) {
				if(number_of_questions == 1) {
					question = quiz.question;
				} else {
					question = quiz.question[i];
				}
				
				//Obtain number of options
				if(question.option instanceof Array) {
					number_of_options = question.option.length;
				} else {
					number_of_options = 1;
				}
				
				for(j=0; j<number_of_options; j++) {
					if(number_of_options == 1) {
						if(question.option._answer == "true") {
							answer = (i+1) + "." + (j+1)
							answers.push(answer);
						}
					} else {
						if(question.option[j]._answer == "true") {
							answer = (i+1) + "." + (j+1)
							answers.push(answer);
						}					
					}
				}
			}
		}
		return answers;
	}
	
	function populateQuiz(id) {

		currentQuizId = id+1;

		if(quizCompleted[id]) {
			QuizSummary(currentQuizId);
			return;
		}
			
		$('.modal-title').text("Quiz " + (id+1));
		
		//Check if a quiz with the specified ID exists
		
		//Obtain number of quizzes
		if(quizJSON.quizzes.quiz instanceof Array) {
			number_of_quizzes = quizJSON.quizzes.quiz.length;
			quiz = quizJSON.quizzes.quiz[id];
		} else {
			number_of_quizzes = 1;
			quiz = quizJSON.quizzes.quiz;
		}
				
		if(id <= number_of_quizzes) {
			$("#quizForm").empty();
			
			//Obtain number of questions
			if(quiz.question instanceof Array) {
				number_of_questions = quiz.question.length;
			} else {
				number_of_questions = 1;
			}

			for(i=0; i<number_of_questions; i++) {
				if(number_of_questions == 1) {
					$("#quizForm").append("<b>Question " + (i+1) + "</b> : " + quiz.question._text + "<br>");
					question = quiz.question;
				} else {
					$("#quizForm").append("<b>Question " + (i+1) + "</b> : " + quiz.question[i]._text + "<br>");
					question = quiz.question[i];
				}
				
				//Obtain number of options
				if(question.option instanceof Array) {
					number_of_options = question.option.length;
				} else {
					number_of_options = 1;
				}
				
				for(j=0; j<number_of_options; j++) {
					if(number_of_options == 1) {
						var option = question.option._text;
					} else {
						var option = question.option[j]._text;
					}
					$("#quizForm").append("<input type='checkbox' id='"+(i+1)+"."+(j+1)+"' name='quiz"+(id+1)+" value='"+option+"'/>"+option+"<br>");
				}
				$("#quizForm").append("<br>");
			}
			
			$("#quizForm").append('<input type="hidden" name="action" value="quiz_submit" /><input type="submit" value="Submit" id="submit"name="submit">');

		}
	}

	$('#quizForm').submit(function() {
		
		quizCompleted[currentQuizId-1] = true;
		
		var checkboxes = $('input:checkbox');
		
		answerArray[currentQuizId-1]=[];
		
		for(i=0;i<checkboxes.length;i++) {
			if(checkboxes[i].checked) {
				answerArray[currentQuizId-1].push(checkboxes[i].id);
			}
		}

		sid = <?=json_encode($streamline->id)?>;
		stuid = <?=json_encode($USER->id)?>;
		cid = <?=json_encode($COURSE->id)?>;
		qid = currentQuizId;
		
		data={ "qid" : qid, "sid" : sid, "cid" : cid, "stuid" : stuid, "answers" : answerArray[currentQuizId-1] };
		
		console.log(data);
		
		$("#quizForm").empty();
		var loading_div = "<div class='quiz_loading'><img class='quiz_loading_img' src='Quiz/images/loading.gif' height='200px'></div>"
		$("#quizForm").append(loading_div);
			
		$.post('Quiz/quiz_submit.php', data, function(data) {
		});
		
		QuizSummary(currentQuizId);
		
		//$('#quizModal').hide();
		//$('.modal-backdrop').hide();
		//$('body').removeClass( "modal-open" );

	  return false;
	});

</script>