<html>
<style>
</style>
<body>
    <?php

    $quizId = $_POST["class"];


    $data = simplexml_load_file("Quiz.xml")or die("Error: Cannot create object");

    echo '<form action="quizreply.php" method="post">';

    echo '<input type="hidden" name="quizId" value="'.$quizId.'">';

    $t=1;

    foreach ($data->children() as $questions) {
        if ($questions["id"] == $quizId){

            echo '<div id="'.$t.'">'.$t.". ".$questions->Question.'</div>'."<br>";

            $quantity = $questions->quantity;
            $char_A = 'A';

            for ($i=1; $i <= $quantity; $i++) { 

              echo '<input type="checkbox" name="'.$quizId.'@'.$t.'@'.$i.'" value="1">'.$char_A++.'. ';
              $str = "Choice".$i;
              echo $questions->$str."<br>";
            }
            echo "<br>";
            $t++;

        }
        
    }

    echo '
        <input type="submit">
        </form>';
    ?>

</body>
</html>