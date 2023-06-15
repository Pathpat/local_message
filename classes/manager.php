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

namespace local_message;
//exception for any database problem
use dml_exception;
use stdClass;

class manager {
    /** Insert the data into our database table.
     * @param string $message_text
     * @param string $message_type
     * @return bool true if successful
     */
    public function create_message(string $message_text, string $messge_type):bool {
        global $DB;
         $recordtoinsert = new stdClass();
         $recordtoinsert->messagetext = $fromform->messagetext;
         $recordtoinsert->messagetype = $fromform->messagetype;
         try {
         return $DB->insert_record('local_message', $recordtoinsert, false);
        } catch (dml_exception $e){
            return false;
        }
    }
    
    /** Gets all messages that have not been read by this user
     * @param int $userid the user that we are getting messages for
     * @return array of messages
     */
    public function get_messages(int $userid):array {
        global $DB;
        
        $sql = "SELECT lm.id, lm.messagetext, lm.messagetype 
            FROM {local_message} lm 
            LEFT OUTER JOIN {local_message_read} lmr ON lm.id = lmr.messageid AND lmr.userid = :userid
            WHERE lmr.userid IS NULL ";
        $params =[
            'userid' => $userid,
        ];
        try {
            return $DB->get_records_sql($sql, $params);
        }catch (dml_exception $e){
            return [];
        }
    }

    /** Mark that a message was read by this user.
     * @param int $message_id the message to mark as read
     * @param int $userid the user that we are marking message read
     * @return bool true if successful
     */
    public function mark_message_read(int $message_id, int $userid): bool {
        global $DB;
        $read_record = new stdClass();
        $read_record->messageid = $message_id;
        $read_record->userid = $userid;
        $read_record->timeread = time();
        try{
          return $DB->insert_record('local_message_read', $read_record, false);
        }catch (dml_exception $e){
            return false;
        }
    }

    /** Get a single message from its id.
     * @param int $messageid the message we're trying to get.
     * @return object|false message data or false if not found.
     */
    public function get_message(int $messageid) {
        global $DB;
        return $DB->get_record('local_message', ['id' => $messageid]);
    }

    /** Update details for a single message.
     * @param int $messageid the message we're trying to get.
     * @param string $message_text the new text for the message.
     * @param string $message_type the new type for the message.
     * @return bool message data or false if not found.
     */
    public function update_message(int $messageid, string $message_text, string $message_type): bool {
        global $DB;
        $object = new stdClass();
        $object->id = $messageid;
        $object->messagetext = $message_text;
        $object->messagetype = $message_type;
        try{
            return $DB->update_record('local_message', $object, false);
        }catch (dml_exception $e){
            return false;
        }
    }
    /** Delete a message and all the read history.
     * @param $messageid
     * @return bool
     * @throws \dml_transaction_exception
     * @throws dml_exception
     */
    public function delete_message($messageid) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $deleteMessage = $DB->delete_records('local_message', ['id' => $messageid]);
        $deletedRead = $DB->delete_records('local_message_read', ['messageid' => $messageid]);
        if ($deleteMessage && $deletedRead) {
            $DB->commit_delegated_transaction($transaction);
        }
        return true;
    }
}
