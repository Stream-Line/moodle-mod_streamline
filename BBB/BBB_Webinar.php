<?php
/**
 * Join a BigBlueButton room
 *
 * @package   mod_streamline
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(__FILE__)).'/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$b  = optional_param('n', 0, PARAM_INT);  // streamline instance ID
$group  = optional_param('group', 0, PARAM_INT);  // streamline group ID

if ($id) {
    $cm = get_coursemodule_from_id('streamline', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $streamline = $DB->get_record('streamline', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($b) {
    $streamline = $DB->get_record('streamline', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $streamline->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('streamline', $streamline->id, $course->id, false, MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

if ( $CFG->version < '2013111800' ) {
    //This is valid before v2.6
    $module = $DB->get_record('modules', array('name' => 'streamline'));
    $module_version = $module->version;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    //This is valid after v2.6
    $module_version = get_config('mod_streamline', 'version');
    $context = context_module::instance($cm->id);
}

if ( $CFG->version < '2014051200' ) {
    //This is valid before v2.7
    add_to_log($course->id, 'streamline', 'view', 'view.php?id=$cm->id', $streamline->name, $cm->id);
} else {
    //This is valid after v2.7 bigbluebuttonbn
    $event = \mod_streamline\event\bigbluebuttonbn_activity_viewed::create(
            array(
                    'context' => $context,
                    'objectid' => $streamline->id
            )
    );
    $event->trigger();
}

//User data
$bbbsession['username'] = get_string('fullnamedisplay', 'moodle', $USER);
$bbbsession['userID'] = $USER->id;
$bbbsession['roles'] = get_user_roles($context, $USER->id, true);

if( $streamline->participants == null || $streamline->participants == "[]" ){
    //The room that is being used comes from a previous version
    $moderator = has_capability('mod/streamline:moderate', $context);
} else {
    $moderator = bigbluebuttonbn_is_moderator($bbbsession['userID'], $bbbsession['roles'], $streamline->participants);
}
$administrator = has_capability('moodle/category:manage', $context);

//Validates if the BigBlueButton server is running 
//BigBlueButton server data
$bbbsession['salt'] = trim($CFG->BigBlueButtonSaltKey);
$bbbsession['url'] = trim(trim($CFG->ServerURLforBigBlueButton),'/').'/';

$serverVersion = bigbluebuttonbn_getServerVersion($bbbsession['url']); 
if ( !isset($serverVersion) ) { //Server is not working
    if ( $administrator )
        print_error( $bbbsession['url'] .'view_error_unable_join', 'streamline', $CFG->wwwroot.'/admin/settings.php?section=modsettingbigbluebuttonbn' );
    else if ( $moderator )
        print_error( 'view_error_unable_join_teacher', 'streamline', $CFG->wwwroot.'/course/view.php?id='.$streamline->course );
    else
        print_error( 'view_error_unable_join_student', 'streamline', $CFG->wwwroot.'/course/view.php?id='.$streamline->course );
} else {
    $xml = bigbluebuttonbn_wrap_simplexml_load_file( bigbluebuttonbn_getMeetingsURL( $bbbsession['url'], $bbbsession['salt'] ) );
    
	
	if ( !isset($xml) || !isset($xml->returncode) || $xml->returncode == 'FAILED' ){ // The salt is wrong
        if ( $administrator ) 
            print_error( 'view_error_unable_join', 'streamline', $CFG->wwwroot.'/admin/settings.php?section=modsettingbigbluebuttonbn' );
        else if ( $moderator )
            print_error( 'view_error_unable_join_teacher', 'streamline', $CFG->wwwroot.'/course/view.php?id='.$streamline->course );
        else
            print_error( 'view_error_unable_join_student', 'streamline', $CFG->wwwroot.'/course/view.php?id='.$streamline->course );
    }
}

//// BigBlueButton Setup Starts

//Server data
$bbbsession['modPW'] = $streamline->moderatorpass;
$bbbsession['viewerPW'] = $streamline->viewerpass;
//User roles
$bbbsession['flag']['moderator'] = $moderator;
$bbbsession['textflag']['moderator'] = $moderator? 'true': 'false';
$bbbsession['flag']['administrator'] = $administrator;
$bbbsession['textflag']['administrator'] = $administrator? 'true': 'false';

//Database info related to the activity
$bbbsession['welcome'] = $streamline->welcome;
if( !isset($bbbsession['welcome']) || $bbbsession['welcome'] == '') {
    $bbbsession['welcome'] = get_string('mod_form_field_welcome_default', 'streamline'); 
}

$bbbsession['userlimit'] = intval($streamline->userlimit);
$bbbsession['voicebridge'] = $streamline->voicebridge;
$bbbsession['description'] = $streamline->description;
$bbbsession['flag']['wait'] = $streamline->wait;
$bbbsession['flag']['allmoderators'] = $streamline->allmoderators;
$bbbsession['flag']['record'] = $streamline->record;
$bbbsession['textflag']['wait'] = $streamline->wait? 'true': 'false';
$bbbsession['textflag']['record'] = $streamline->record? 'true': 'false';
$bbbsession['textflag']['allmoderators'] = $streamline->allmoderators? 'true': 'false';
if( $streamline->record )
    $bbbsession['welcome'] .= '<br><br>'.get_string('bbbrecordwarning', 'streamline');

$bbbsession['timeavailable'] = $streamline->timeavailable;
$bbbsession['timedue'] = $streamline->timedue;
$bbbsession['timeduration'] = intval($streamline->timeduration / 60);
if( $bbbsession['timeduration'] > 0 )
    $bbbsession['welcome'] .= '<br><br>'.str_replace("%duration%", ''.$bbbsession['timeduration'], get_string('bbbdurationwarning', 'streamline'));

//Additional info related to the course
$bbbsession['coursename'] = $course->fullname;
$bbbsession['courseid'] = $course->id;
$bbbsession['cm'] = $cm;

//Operation URLs
$bbbsession['courseURL'] = $CFG->wwwroot.'/course/view.php?id='.$streamline->course;
$bbbsession['logoutURL'] = $CFG->wwwroot.'/course/view.php?id='.$course->id;

//Metadata
$bbbsession['origin'] = "Moodle";
$bbbsession['originVersion'] = $CFG->release;
$parsedUrl = parse_url($CFG->wwwroot);
$bbbsession['originServerName'] = $parsedUrl['host'];
$bbbsession['originServerUrl'] = $CFG->wwwroot;
$bbbsession['originServerCommonName'] = '';
$bbbsession['originTag'] = 'moodle-mod_streamline ('.$module_version.')';
$bbbsession['context'] = $course->fullname;
$bbbsession['contextActivity'] = $streamline->name;
$bbbsession['contextActivityDescription'] = $streamline->description;

//Matts variables - placeholder

$hasEnded = $streamline -> meetingended;

//// BigBlueButton Setup Ends

// Mark viewed by user (if required)
//$completion = new completion_info($course);
//$completion->set_module_viewed($cm);



// Validate if the user is in a role allowed to join
if ( !has_capability('mod/streamline:join', $context) ) {
    echo $OUTPUT->header();
    if (isguestuser()) {
        echo $OUTPUT->confirm('<p>'.get_string('view_noguests', 'streamline').'</p>'.get_string('liketologin'),
            get_login_url(), $CFG->wwwroot.'/course/view.php?id='.$course->id);
    } else { 
        echo $OUTPUT->confirm('<p>'.get_string('view_nojoin', 'streamline').'</p>'.get_string('liketologin'),
            get_login_url(), $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    echo $OUTPUT->footer();
    exit;
}


$bbbsession['streamlineid'] = $streamline->id;
/// find out current groups mode
if (groups_get_activity_groupmode($cm) == 0) {  //No groups mode
    $bbbsession['meetingid'] = $streamline->meetingid.'-'.$bbbsession['courseid'].'-'.$bbbsession['streamlineid'];
    $bbbsession['meetingname'] = $streamline->name;
} else {                                        // Separate groups mode
    //If doesnt have group
    $bbbsession['group'] = (!$group)?groups_get_activity_group($cm): $group;
    $bbbsession['meetingid'] = $streamline->meetingid.'-'.$bbbsession['courseid'].'-'.$bbbsession['streamlineid'].'['.$bbbsession['group'].']';
    if( $bbbsession['group'] > 0 )
        $group_name = groups_get_group_name($bbbsession['group']);
    else
        $group_name = get_string('allparticipants');
    $bbbsession['meetingname'] = $streamline->name.' ('.$group_name.')';
}

if( $bbbsession['flag']['administrator'] || $bbbsession['flag']['moderator'] || $bbbsession['flag']['allmoderators'] )
    $bbbsession['joinURL'] = bigbluebuttonbn_getJoinURL($bbbsession['meetingid'], $bbbsession['username'], $bbbsession['modPW'], $bbbsession['salt'], $bbbsession['url'], $bbbsession['userID']);
else
    $bbbsession['joinURL'] = bigbluebuttonbn_getJoinURL($bbbsession['meetingid'], $bbbsession['username'], $bbbsession['viewerPW'], $bbbsession['salt'], $bbbsession['url'], $bbbsession['userID']);


$joining = false;
$bigbluebuttonbn_view = '';

// Matt edited: hasEnded has been added to each if statement to catch when the meeting has ended and start the after view function.
if (!$streamline->timeavailable && $hasEnded == 0) {
    if (!$streamline->timedue || time() <= $streamline->timedue){
        //GO JOINING
        if( bigbluebuttonbn_is_user_limit_reached( $bbbsession ) ){
            if (!$streamline->newwindow) {
                print_error( 'view_error_userlimit_reached', 'streamline', $CFG->wwwroot.'/course/view.php?id='.$course->id );
            } else {
                print_error( 'view_error_userlimit_reached', 'streamline', $CFG->wwwroot.'/mod/streamline/view_end.php?id='.$cm->id );
            }
        } else {
            groups_print_activity_menu($cm, $CFG->wwwroot.'/mod/streamline/view.php?id='.$cm->id);
            $bigbluebuttonbn_view = 'join';
            $joining = bigbluebuttonbn_view_joining( $bbbsession, $context, $streamline );
        }

    } else {
        //CALLING AFTER
        $bigbluebuttonbn_view = 'after';
        //echo $OUTPUT->heading(get_string('bbbfinished', 'streamline'));
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');

        bigbluebuttonbn_view_after( $bbbsession );

        echo $OUTPUT->box_end();
    }

} else if ( time() < $streamline->timeavailable && $hasEnded == 0){
    //CALLING BEFORE
    $bigbluebuttonbn_view = 'before';
    echo $OUTPUT->heading(get_string('bbbnotavailableyet', 'streamline'));
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');

    bigbluebuttonbn_view_before( $bbbsession );

    echo $OUTPUT->box_end();

} else if ((!$streamline->timedue || time() <= $streamline->timedue) && $hasEnded == 0 ) {
    //GO JOINING
    if( bigbluebuttonbn_is_user_limit_reached( $bbbsession ) ){
        if (!$streamline->newwindow) {
            print_error( 'view_error_userlimit_reached', 'streamline', $CFG->wwwroot.'/course/view.php?id='.$course->id );
        } else {
            print_error( 'view_error_userlimit_reached', 'streamline', $CFG->wwwroot.'/mod/streamline/view_end.php?id='.$cm->id );
        }
    } else {
        groups_print_activity_menu($cm, $CFG->wwwroot.'/mod/streamline/view.php?id='.$cm->id);
        $bigbluebuttonbn_view = 'join';
        $joining = bigbluebuttonbn_view_joining( $bbbsession, $context, $streamline );
    }

} else {
    //CALLING AFTER
    $bigbluebuttonbn_view = 'after';
    //echo $OUTPUT->heading(get_string('bbbfinished', 'streamline'));
    //echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');

    bigbluebuttonbn_view_after( $bbbsession );

    //echo $OUTPUT->box_end();
}

//JavaScript variables
$jsVars = array(
        'waitformoderator' => $bbbsession['textflag']['wait'],
        'isadministrator' => $bbbsession['textflag']['administrator'],
        'ismoderator' => $bbbsession['textflag']['moderator'],
        'meetingid' => $bbbsession['meetingid'],
        'joinurl' => $bbbsession['joinURL'],
        'joining' => ($joining? 'true':'false'),
        'bigbluebuttonbn_view' => $bigbluebuttonbn_view,
        'bigbluebuttonbnid' => $bbbsession['streamlineid']
);

$jsmodule = array(
        'name'     => 'mod_streamline',
        'fullpath' => '/mod/streamline/module.js',
        'requires' => array('datasource-get', 'datasource-jsonschema', 'datasource-polling'),
);
$PAGE->requires->data_for_js('streamline', $jsVars);
$PAGE->requires->js_init_call('M.mod_streamline.init_view', array(), false, $jsmodule);


///////////////////iframe////////////////////////////
/*if($hasEnded == 0){
	$param = $bbbsession['joinURL'];
	echo '<div id="iframe_box"> <iframe id = "frame" src="';
	echo htmlspecialchars($param);
	echo '" style="position: absolute; width: 100%; height: 100%; border: none">\n';
	echo "</iframe> </div>";
}*/
/////////////////////////////////////////////////////





function bigbluebuttonbn_view_joining( $bbbsession, $context, $streamline ){
    global $CFG, $DB;

    $joining = false;

    // If user is administrator, moderator or if is viewer and no waiting is required
    if( $bbbsession['flag']['administrator'] || $bbbsession['flag']['moderator'] || !$bbbsession['flag']['wait'] ) {
        //
        // Join directly
        //
        $metadata = array("meta_origin" => $bbbsession['origin'],
                "meta_originVersion" => $bbbsession['originVersion'],
                "meta_originServerName" => $bbbsession['originServerName'],
                "meta_originServerCommonName" => $bbbsession['originServerCommonName'],
                "meta_originTag" => $bbbsession['originTag'],
                "meta_context" => $bbbsession['context'],
                "meta_contextActivity" => $bbbsession['contextActivity'],
                "meta_contextActivityDescription" => $bbbsession['contextActivityDescription'],
                "meta_recording" => $bbbsession['textflag']['record']);
        $response = bigbluebuttonbn_getCreateMeetingArray( $bbbsession['meetingname'], $bbbsession['meetingid'], $bbbsession['welcome'], $bbbsession['modPW'], $bbbsession['viewerPW'], $bbbsession['salt'], $bbbsession['url'], $bbbsession['logoutURL'], $bbbsession['textflag']['record'], $bbbsession['timeduration'], $bbbsession['voicebridge'], $metadata );

        if (!$response) {
            // If the server is unreachable, then prompts the user of the necessary action
            if ( $bbbsession['flag']['administrator'] ) {
                print_error( 'view_error_unable_join', 'streamline', $CFG->wwwroot.'/admin/settings.php?section=modsettingbigbluebuttonbn' );
            } else if ( $bbbsession['flag']['moderator'] ) {
                print_error( 'view_error_unable_join_teacher', 'streamline', $CFG->wwwroot.'/admin/settings.php?section=modsettingbigbluebuttonbn' );
            } else {
                print_error( 'view_error_unable_join_student', 'streamline', $CFG->wwwroot.'/admin/settings.php?section=modsettingbigbluebuttonbn' );
            }

        } else if( $response['returncode'] == "FAILED" ) {
            // The meeting was not created
            $error_key = bigbluebuttonbn_get_error_key( $response['messageKey'], 'view_error_create' );
            if( !$error_key ) {
                print_error( $response['message'], 'streamline' );
            } else {
                print_error( $error_key, 'streamline' );
            }

        } else if ($response['hasBeenForciblyEnded'] == "true"){
            print_error( get_string( 'index_error_forciblyended', 'streamline' ));

        } else { ///////////////Everything is ok /////////////////////
            /// Moodle event logger: Create an event for meeting created
            if ( $CFG->version < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($bbbsession['courseid'], 'streamline', 'meeting created', '', $streamline->name, $bbbsession['cm']->id);
            } else {
                //This is valid after v2.7
                $event = \mod_streamline\event\bigbluebuttonbn_meeting_created::create(
                        array(
                                'context' => $context,
                                'objectid' => $streamline->id
                        )
                );
                $event->trigger();
            }

            /// Internal logger: Instert a record with the meeting created
            bigbluebuttonbn_log($bbbsession, 'Create');

            if ( groups_get_activity_groupmode($bbbsession['cm']) > 0 && count(groups_get_activity_allowed_groups($bbbsession['cm'])) > 1 ){
                print "<br>".get_string('view_groups_selection', 'streamline' )."&nbsp;&nbsp;<input type='button' onClick='M.mod_streamline.joinURL()' value='".get_string('view_groups_selection_join', 'streamline' )."'>";
            } else {
                $joining = true;

               /*  if( $bbbsession['flag']['administrator'] || $bbbsession['flag']['moderator'] )
                    print "<br />".get_string('view_login_moderator', 'streamline' )."<br /><br />";
                else
                    print "<br />".get_string('view_login_viewer', 'streamline' )."<br /><br />";
                
                print "<center><img src='pix/loading.gif' /></center>"; */
            }

            /// Moodle event logger: Create an event for meeting joined
            if ( $CFG->version < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($bbbsession['courseid'], 'streamline', 'meeting joined', '', $streamline->name, $bbbsession['cm']->id);
            } else {
                //This is valid after v2.7
                $event = \mod_streamline\event\bigbluebuttonbn_meeting_joined::create(
                        array(
                                'context' => $context,
                                'objectid' => $streamline->id
                        )
                );
                $event->trigger();
            }
        }
    } else {
        //    
        // "Viewer" && Waiting for moderator is required;
        //
        $joining = true;

        print "<div align='center'>";
        if( bigbluebuttonbn_wrap_simplexml_load_file(bigbluebuttonbn_getIsMeetingRunningURL( $bbbsession['meetingid'], $bbbsession['url'], $bbbsession['salt'] )) == "true" ) {
            /// Since the meeting is already running, we just join the session
            print "<br />".get_string('view_login_viewer', 'streamline' )."<br /><br />";
            print "<center><img src='pix/loading.gif' /></center>";
            /// Moodle event logger: Create an event for meeting joined
            if ( $CFG->version < '2014051200' ) {
                //This is valid before v2.7
                add_to_log($bbbsession['courseid'], 'streamline', 'meeting joined', '', $streamline->name, $bbbsession['cm']->id);
            } else {
                //This is valid after v2.7
                $event = \mod_streamline\event\bigbluebuttonbn_meeting_joined::create(
                        array(
                                'context' => $context,
                                'objectid' => $streamline->id
                        )
                );
                $event->trigger();
            }
        } else {
            /// Since the meeting is not running, the spining wheel is shown
            print "<br />".get_string('view_wait', 'streamline' )."<br /><br />";
            print '<center><img src="pix/polling.gif"></center>';
        }
        print "</div>";
    }
    return $joining;
}

function bigbluebuttonbn_view_before( $bbbsession ){

    echo '<table>';
    if ($bbbsession['timeavailable']) {
        echo '<tr><td class="c0">'.get_string('mod_form_field_availabledate','streamline').':</td>';
        echo '    <td class="c1">'.userdate($bbbsession['timeavailable']).'</td></tr>';
    }
    if ($bbbsession['timedue']) {
        echo '<tr><td class="c0">'.get_string('mod_form_field_duedate','streamline').':</td>';
        echo '    <td class="c1">'.userdate($bbbsession['timedue']).'</td></tr>';
    }
    echo '</table>';
}

// Matt edited: Loads the BBB.php after the meeting has ended
function bigbluebuttonbn_view_after( $bbbsession ){

    global $DB, $CFG;
	$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
	$cm = get_coursemodule_from_id('streamline', $id, 0, false, MUST_EXIST);
	$streamline = $DB->get_record('streamline', array('id' => $cm->instance), '*', MUST_EXIST);
	
	include "streamline_view.php";
    $recordingsArray = bigbluebuttonbn_getRecordingsArray($bbbsession['meetingid'], $bbbsession['url'], $bbbsession['salt']);
	if($streamline -> meetingended == 0)
	{
		$dataObj = new stdClass();
		$dataObj->id = $streamline -> id;
		$dataObj -> meetingended = 1;
		$table = 'streamline';
		$DB -> update_record($table,$dataObj );
	}

	
}

function bigbluebuttonbn_is_user_limit_reached( $bbbsession ){
    if( $bbbsession['userlimit'] == 0 )
        return false;

    $meetingInfo = bigbluebuttonbn_getMeetingInfoArray( $bbbsession['meetingid'], $bbbsession['modPW'], $bbbsession['url'], $bbbsession['salt'] );
    if( $meetingInfo['returncode'] == 'FAILED' || $meetingInfo['participantCount'] < $bbbsession['userlimit'])
        return false;

    return true;
}


?>

<script type="text/javascript">
var scriptTag = "<script> function BBBRecordRequest(){alert('hello');}<\/script>";
$("#frame").contents().find("head").append(scriptTag);

/*ocument.getElementById('frame').onload = function BBBRecordRequest() {
		alert("Yo");	
		

}*/
</script>



