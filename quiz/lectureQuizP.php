<html>

<style>
.QV{
    width:550px;
}
.Q{
    width:550px;
}
.C{
    width:550px;
}
</style>
    <body>

        <?php
            
            if(!is_file("Quiz.xml")){
                $openF = fopen("Quiz.xml", "a");
                $str = "<?xml version='1.0' encoding='utf-8'?>\n";
                $str .="<Quiz>\n</Quiz>";

                if(fwrite($openF, $str)){}
                else{
                        echo "write xml error";
                    }
            }
            $quantity = $_POST["quantity"];

            $openF = fopen("Quiz.xml", "r+");
            fseek($openF, -7, SEEK_END);
            $str = '<Q id="'.$_POST["class"].'">'."\n";
            echo '<div class = "QV"> Quiz '.$_POST["class"].'</div>';

            $str .='<Question>'.$_POST["Question"]."</Question>\n";
            echo '<div class = "Q"> Question: '.$_POST["Question"].'</div>';

            $str .= "<quantity>".$quantity."</quantity>\n";

            for($x = 1; $x <= $quantity; $x++){
                $str .= "<Choice".$x.">".$_POST["Choice".$x]."</Choice".$x.">\n";
                echo '<div class = "C"> Choice'.$x.': '.$_POST["Choice".$x].'</div>';
            }

            for($x = 1; $x <= $quantity; $x++){
                if ($_POST["C".$x]==1){
                    $str .= "<Answer".$x.">".$x."</Answer".$x.">\n";
                }
            }

            $str .= "</Q>\n";
            $str .= "</Quiz>";


            fwrite($openF, $str);

        ?><br />
    </body>
</html>