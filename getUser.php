<?php
    require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
    require_once(dirname(__FILE__).'/lib.php');
    
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
    
    require_login($course, true, $cm);
    
    if(isset($_GET["uid"]) && isset($_GET["param"])){
        $usr = $DB->get_record('user', array('id'=>$_GET["uid"]), '*', MUST_EXIST); // gets user from db
        switch($_GET["param"]){
            case "fullname":
                echo $usr->firstname . " " . $usr->lastname; // returns string
                break;
            case "displaypicture":
                echo $OUTPUT->user_picture($usr, array('size'=>100)); // returns html string
                break;
            case "user":
                echo json_encode($usr); // returns user object. NOTE: "json" must be set as the datatype in the ajax expression!!
                break;
            default:
                echo "no parameters matched"; break;
        }
        return;
    }
?>