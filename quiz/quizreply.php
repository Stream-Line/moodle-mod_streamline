<html>
<style>
.section{
    width: 300px;
}

</style>

<head>
    <script>
        function colorWrong(str) { 
            document.getElementById(str).style.background = "F11";
        }

        function colorGreen(str) { 
            document.getElementById(str).style.background = "1F1";
        }
    </script>
	</head>
<body>
	<?php
	    $quizId = $_POST["quizId"];

	    echo '<form action="test6.php" method="post">';

	    $data = simplexml_load_file("Quiz.xml")or die("Error: Cannot create object");

	    $t = 1;
	    foreach ($data->children() as $questions) {
        if ($questions["id"] == $quizId){

            echo '<div id="'.$t.'">'.$t.". ".$questions->Question.'</div>'."<br>";

            $quantity = $questions->quantity;
            $char_A = 'A';
            $flag = 0;
            for ($i=1; $i <= $quantity; $i++) { 
            	if($_POST[$quizId.'@'.$t.'@'.$i]==1){
            		echo '<input type="checkbox" checked="checked">'.$char_A++.'. ';
            		$name_str = "Answer".$i;
            		if($questions->$name_str != $i){
            			$flag = 1;
            		}
            		
            	}else{
            		echo '<input type="checkbox">'.$char_A++.'. ';
           		    $name_str = "Answer".$i;
            		if($questions->$name_str == $i){
            			$flag = 1;
            		}
            	}

            if ($flag == 1){
            	echo '<script type="text/javascript">colorWrong('.$t.');</script>';
            }else{
                echo '<script type="text/javascript">colorGreen('.$t.');</script>';
            }
              $str = "Choice".$i;
              echo $questions->$str."<br>";
            }
            echo "<br>";
            $t++;

        }


        }
        echo '</form>';

        echo '<a href="studentQuizH.html"><button onclick="reload("studentQuizH.html")> New Quiz</button> </a>';
        
	?>


  
	</body>
</html>