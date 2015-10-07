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
 * This file keeps track of upgrades to the streamline module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod_streamline
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
/**
 * Execute streamline upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_streamline_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
    /*
     * And upgrade begins here. For each one, you'll need one
     * block of code similar to the next one. Please, delete
     * this comment lines once this file start handling proper
     * upgrade code.
     *
     * if ($oldversion < YYYYMMDD00) { //New version in version.php 20140516;
     * }
     *
     * Lines below (this included)  MUST BE DELETED once you get the first version
     * of your module ready to be installed. They are here only
     * for demonstrative purposes and to show how the streamline
     * iself has been upgraded.
     *
     * For each upgrade block, the file streamline/version.php
     * needs to be updated . Such change allows Moodle to know
     * that this file has to be processed.
     *
     * To know more about how to write correct DB upgrade scripts it's
     * highly recommended to read information available at:
     *   http://docs.moodle.org/en/Development:XMLDB_Documentation
     * and to play with the XMLDB Editor (in the admin menu) and its
     * PHP generation posibilities.
     *
     * First example, some fields were added to install.xml on 2007/04/01
     */
    if ($oldversion < 2012062720) {
        // Define field course to be added to streamline.
        $table = new xmldb_table('streamline');
        $field1 = new xmldb_field('moderatorpass', XMLDB_TYPE_CHAR, '225', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        $field2 = new xmldb_field('viewerpass', XMLDB_TYPE_CHAR, '225', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }
        $field3 = new xmldb_field('wait', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field3)) {
            $dbman->add_field($table, $field3);
        }
        $field4 = new xmldb_field('newwindow', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field4)) {
            $dbman->add_field($table, $field4);
        }
        $field5 = new xmldb_field('allmoderators', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field5)) {
            $dbman->add_field($table, $field5);
        }
        $field6 = new xmldb_field('record', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field6)) {
            $dbman->add_field($table, $field6);
        }
        $field7 = new xmldb_field('description', XMLDB_TYPE_CHAR, '225', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field7)) {
            $dbman->add_field($table, $field7);
        }
        $field8 = new xmldb_field('welcome', XMLDB_TYPE_CHAR, '225', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field8)) {
            $dbman->add_field($table, $field8);
        }
        $field9 = new xmldb_field('voicebridge', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, null, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field9)) {
            $dbman->add_field($table, $field9);
        }
        $field10 = new xmldb_field('timedue', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field10)) {
            $dbman->add_field($table, $field10);
        }
        $field11 = new xmldb_field('timeavailable', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field11)) {
            $dbman->add_field($table, $field11);
        }
        $field12 = new xmldb_field('timeduration', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field12)) {
            $dbman->add_field($table, $field12);
        }
        $field13 = new xmldb_field('meetingid', XMLDB_TYPE_CHAR, '225', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field13)) {
            $dbman->add_field($table, $field13);
        }
        $field14 = new xmldb_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field14)) {
            $dbman->add_field($table, $field14);
        }
        $field15 = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field15)) {
            $dbman->add_field($table, $field15);
        }
        $field16 = new xmldb_field('participants', XMLDB_TYPE_TEXT, XMLDB_UNSIGNED,null,'0',null,null);
       // Add field course.
        if (!$dbman->field_exists($table, $field16)) {
            $dbman->add_field($table, $field16);
        }
	$field17 = new xmldb_field('userlimit', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null, null);
        // Add field course.
        if (!$dbman->field_exists($table, $field17)) {
            $dbman->add_field($table, $field17);
        }
//-------------------------------------------------------------------------------------------------
// Creating the streamline log table Maruti
//-------------------------------------------------------------------------------------------------
	$table1 = new xmldb_table('streamline_log');
	$field1->set_attributes('id',XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, '0', null); 
        // Add field course.
        if (!$dbman->field_exists($table1, $field1)) {
            $dbman->add_field($table1, $field1);
        }
        $field2 = new xmldb_field('meetingid', XMLDB_TYPE_CHAR, '225', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
        // Add field course.
        if (!$dbman->field_exists($table1, $field2)) {
            $dbman->add_field($table1, $field2);
        }
        $field3 = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
        // Add field course.
        if (!$dbman->field_exists($table1, $field3)) {
            $dbman->add_field($table1, $field3);
        }
        $field4 = new xmldb_field('streamlineid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null,null);
        // Add field course.
        if (!$dbman->field_exists($table1, $field4)) {
            $dbman->add_field($table1, $field4);
        }
        $field5 = new xmldb_field('record', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0', null,null);
        // Add field course.
        if (!$dbman->field_exists($table1, $field5)) {
            $dbman->add_field($table1, $field5);
        }
        $field6 = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', null,null);
        // Add field course.
        if (!$dbman->field_exists($table1, $field6)) {
            $dbman->add_field($table1, $field6);
        }
        $field7 = new xmldb_field('event', XMLDB_TYPE_INTEGER, '32', XMLDB_NOTNULL, null, '0', null,null);
        // Add field course.
        if (!$dbman->field_exists($table1, $field7)) {
            $dbman->add_field($table1, $field7);
        }
	$status = $dbman->create_table($table1);
//-------------------------------------------------------------------------------------------------
        upgrade_mod_savepoint(true, 20120627016, 'streamline');
    }


    if ($oldversion < 20150627099) {
        //-------------------------------------------------------------------------------------------------
        // Creating the streamline  table
        //-------------------------------------------------------------------------------------------------
        
        $table2 = new xmldb_table('streamline_quiz');
        
        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null, null);
        
        // Add field course.
        if (!$dbman->field_exists($table2, $field)) {
            $dbman->add_field($table2, $field);
        }
        
        $field2 = new xmldb_field('quizid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null, null);
        
        // Add field course.
        if (!$dbman->field_exists($table2, $field2)) {
            $dbman->add_field($table2, $field2);
        }
        
        $field3 = new xmldb_field('streamlineid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null, null);
        
        // Add field course.
        if (!$dbman->field_exists($table2, $field3)) {
            $dbman->add_field($table2, $field3);
        }
        
        $field4 = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null, null);
        
        // Add field course.
        if (!$dbman->field_exists($table2, $field4)) {
            $dbman->add_field($table2, $field4);
        }
        
        
        $field5 = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null, null);
        
        // Add field course.
        if (!$dbman->field_exists($table2, $field5)) {
            $dbman->add_field($table2, $field5);
        }
        
        
        $field6 = new xmldb_field('answers', XMLDB_TYPE_TEXT, XMLDB_UNSIGNED, null, '0', null, null);
        
        // Add field course.
        if (!$dbman->field_exists($table2, $field6)) {
            $dbman->add_field($table2, $field6);
        }

		if (!$dbman->table_exists($table2)) {
           $dbman->create_table($table2);
        }
        //-------------------------------------------------------------------------------------------------
    }


    // Second example, some hours later, the same day 2007/04/01
    // ... two more fields and one index were added to install.xml (note the micro increment
    // ... "01" in the last two digits of the version).
    if ($oldversion < 2015100400) {
        // Define field timecreated to be added to streamline.
        $table = new xmldb_table('streamline');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'introformat');
        // Add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field timemodified to be added to streamline.
        $table = new xmldb_table('streamline');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'timecreated');
        // Add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define index course (not unique) to be added to streamline.
        $table = new xmldb_table('streamline');
        $index = new xmldb_index('courseindex', XMLDB_INDEX_NOTUNIQUE, array('course'));
        // Add index to course field.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
		
		//matts code
		$table = new xmldb_table('streamline');
		$field = new xmldb_field('meetingended', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, 0, null);
		  if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Another save point reached.
        upgrade_mod_savepoint(true, 2012062707, 'streamline');
    }
    // Third example, the next day, 2007/04/02 (with the trailing 00),
    // some actions were performed to install.php related with the module.
    if ($oldversion < 2007040200) {
        // Insert code here to perform some actions (same as in install.php).
        upgrade_mod_savepoint(true, 2012062709, 'streamline');
    }
	
	//TODO: A fix is required to add this field from within the if statement - Please remove the following code once the fix is made - Akshay & Matt
	$table = new xmldb_table('streamline');
	$field = new xmldb_field('meetingended', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, 0, null);
	  if (!$dbman->field_exists($table, $field)) {
		$dbman->add_field($table, $field);
	}
	
	//TODO: A fix is required to add this field from within the if statement - Please remove the following code once the fix is made - Akshay & Matt
	$table = new xmldb_table('streamline');
	$field = new xmldb_field('quiz_xml', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null);
	  if (!$dbman->field_exists($table, $field)) {
		$dbman->add_field($table, $field);
	}
	
    /*
     * And that's all. Please, examine and understand the 3 example blocks above. Also
     * it's interesting to look how other modules are using this script. Remember that
     * the basic idea is to have "blocks" of code (each one being executed only once,
     * when the module version (version.php) is updated.
     *
     * Lines above (this included) MUST BE DELETED once you get the first version of
     * yout module working. Each time you need to modify something in the module (DB
     * related, you'll raise the version and add one upgrade block here.
     *
     * Finally, return of upgrade result (true, all went good) to Moodle.
     */
    return true;
/*
        // Define field intro to be added to streamline.
        $table = new xmldb_table('streamline');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'name');
        // Add field intro.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field introformat to be added to streamline.
        $table = new xmldb_table('streamline');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'intro');
        // Add field introformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Once we reach this point, we can store the new version and consider the module
        // ... upgraded to the version 2007040100 so the next time this block is skipped.
*/
}
