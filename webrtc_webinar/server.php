<?php

function get_ids(){
    global $DB, $COURSE;

    $result = $DB->get_records_sql('SELECT u.username AS username, u.id AS userid
        FROM mdl_user u, mdl_course c, mdl_role_assignments ra, mdl_context cxt
        WHERE c.fullname = "' . $COURSE->fullname . '"
        AND cxt.contextlevel = 50
        AND cxt.instanceid = c.id
        AND ra.contextid = cxt.id
        AND ra.userid = u.id
        AND (roleid = 3)');

    $userids = array();
    foreach($result as $u){
        $userids[] = $u->userid;
    }

    return $userids;
}

/*
' . $coursename . '
$sql = "SELECT u.username, c.id AS id, u.lastname
        FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
        WHERE ra.userid = u.id
        AND ra.contextid = cxt.id
        AND cxt.contextlevel = 50
        AND cxt.instanceid = c.id
        AND c.fullname = 'WebRTC Webinar Test Course'
        AND (roleid = 3)";

$users = $DB->get_records_sql($sql);
*/

/*
get enroled people

SELECT c.id AS id, u.username, u.lastname
FROM mdl_role_assignments ra, mdl_user u, mdl_course c, mdl_context cxt
WHERE ra.userid = u.id
AND ra.contextid = cxt.id
AND cxt.contextlevel =50
AND cxt.instanceid = c.id
AND c.fullname ='<insert>'
AND (roleid = 5 OR roleid = 3 );

// 5 is for students 
// 3 is for lecturers

*/