<?php
/**
 * Plugin tasks.
 *
 * @package     local_ibob
 * @category    tasks
 * @copyright   2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ibob\task;

use core\task\adhoc_task;

class send_emails_notifications  extends \core\task\adhoc_task {

    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG;
        $data = $this->get_custom_data();
        $message = new \core\message\message();
        $message->component = 'local_ibob';
        $message->name = 'enrolmentobcreated';

        $message->userfrom = \core_user::get_noreply_user();
        $message->subject = 'Inscription open badge';
        $message->fullmessage = "test pour voir, le cours c'est le ".$data->courseid." et l'enrolment le ".$data->enrolid;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = "<h3>{test pour voir, le cours c'est le ".$data->courseid." et l'enrolment le ".$data->enrolid."}</h3>";
        $message->smallmessage = "message small : test pour voir, le cours c'est le ".$data->courseid." et l'enrolment le ".$data->enrolid;
        $message->contexturl = $CFG->wwwroot.'/my';
        $message->contexturlname = 'Context name';
        $message->replyto = "random@example.com";
        $message->courseid = $data->courseid;

        $message->userto = \core_user::get_user(5);
        $messageid = message_send($message);
    }

    function get_users_from_badges($userid) {
        global $DB;
        $sql ="SELECT   {local_ibob_providers}.apiurl,{local_ibob_user_apikey}.key_field 
                FROM    {local_ibob_user_apikey} JOIN {local_ibob_providers} ON {local_ibob_providers}.id={local_ibob_user_apikey}.provider_id 
                WHERE   {local_ibob_user_apikey}.user_id=:userid";
        return $DB->get_record_sql($sql,array("userid"=>$userid));
    }

    function get_badges_from_course($courseid) {
        global $DB;
        $sql ="SELECT   {local_ibob_providers}.apiurl,{local_ibob_user_apikey}.key_field 
                FROM    {local_ibob_user_apikey} JOIN {local_ibob_providers} ON {local_ibob_providers}.id={local_ibob_user_apikey}.provider_id 
                WHERE   {local_ibob_user_apikey}.user_id=:userid";
        return $DB->get_record_sql($sql,array("userid"=>$userid));
    }
}