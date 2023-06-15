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
use local_message\manager;

function local_message_before_footer(){ 
   global $USER;
   
   if (!get_config('local_message', 'enabled')){
       return;
   }
   
   $manager = new manager();
   $messages = $manager->get_messages($USER->id);
   
  foreach ($messages as $message) {
      
    
//make a switch statement to get the right notifications messages depends the messagetype table
   switch($message->messagetype) {
     case 1:
     $type = \core\output\notification::NOTIFY_SUCCESS;
     break;
     case 2:
     $type = \core\output\notification::NOTIFY_WARNING;
     break;
     case 3:
     $type = \core\output\notification::NOTIFY_ERROR;
     break;
     default:
         $type = \core\output\notification::NOTIFY_INFO;
   }
         \core\notification::add($message->messagetext , $type);
         
         $manager->mark_message_read($message->id, $USER->id);
    }
    
}



