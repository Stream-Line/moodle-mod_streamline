<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of 	
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_streamline
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace streamline with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

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

$event = \mod_streamline\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $streamline);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/streamline/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($streamline->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('streamline-'.$somevar);
 */

// Output starts here.
//echo $OUTPUT->header();

/*
if you the moderator and meeting hasn't started
run the script else run the BBB/stream_view.php
store quiz variable where 0 means no quiz 1 means quiz has been selected and 2 means has been set

*/
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

/*if( $streamline->participants == null || $streamline->participants == "[]" ){
    //The room that is being used comes from a previous version
    $moderator = has_capability('mod/streamline:moderate', $context);
} else {
    $moderator = bigbluebuttonbn_is_moderator($bbbsession['userID'], $bbbsession['roles'], $streamline->participants);
}*/
$administrator = has_capability('moodle/category:manage', $context);
$moderator = has_capability('mod/streamline:moderate', $context);



    if ( $administrator && ($streamline -> quiz_xml) == null)
	{
		include "quiz/quizForm.php";
	}
    else if ( $moderator && ($streamline -> quiz_xml) == null)
	{	
		include "quiz/quizForm.php";
	}
	else
	{
		include 'BBB/stream_view.php';
	}
//include 'BBB/stream_view.php';
?>


<link rel="stylesheet" type="text/css" href="streamline.css">

<body>

	
</body>


<?php

// Finish the page.
//echo $OUTPUT->footer();

?>
