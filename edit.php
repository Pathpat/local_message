<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_message
 * @copyright   2023 test testopoylos<you@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/message/classes/form/edit.php');

global $DB;

$context = context_system::instance();

$PAGE->set_url(new moodle_url(url:'/local/message/edit.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('plugintitleedit', 'local_message'));

//we want to display form
$mform = new edit();



if ($mform->is_cancelled()) {
    //go back to manage.php page
    redirect($CFG->wwwroot . '/local/message/manage.php' ,get_string('cancelmessageform' , 'local_message'));
} else if ($fromform = $mform->get_data()) {
    //Insert the data into our database table
    $recordtoinsert = new stdClass();
    $recordtoinsert->messagetext = $fromform->messagetext;
    $recordtoinsert->messagetype = $fromform->messagetype;
    
    $DB->insert_record('local_message', $recordtoinsert);
    redirect($CFG->wwwroot . '/local/message/manage.php' ,get_string('createmessageform', 'local_message').' '. $fromform->messagetext );
}

echo $OUTPUT->header();

 $mform->display();

echo $OUTPUT->footer();