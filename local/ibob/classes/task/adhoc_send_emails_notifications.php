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

class adhoc_send_emails_notifications  extends \core\task\adhoc_task {

    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG,$DB;

//        mtrace("Début de la tache adhoc");

        $data = $this->get_custom_data();
        $message = new \core\message\message();
        $message->component = 'local_ibob';
        $message->name = 'enrolcreatedupdated';
        $message->userfrom = \core_user::get_noreply_user();
        $message->subject = 'Nouveau cours disponible, accessible par vos Open Badge';
        $message->fullmessage = "test pour voir, le cours c'est le ".$data->courseid." et l'enrolment le ".$data->enrolid." ///// data complète = ".print_r($data);
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = "<h3>test pour voir, le cours c'est le ".$data->courseid." et l'enrolment le ".$data->enrolid." ///// data complète = ".print_r($data)."</h3>";
        $message->contexturl = $CFG->wwwroot.'/my';
        $message->courseid = $data->courseid;

        // Get the badge id from the enrolment method of the course created
        $obadgesid = SELF::get_badges_from_course($data->courseid);
//        $sql ="SELECT   customtext1
//                FROM    {enrol}
//                WHERE   enrol='ibobenrol' and courseid=:courseid";
//        $obadgesid = $DB->get_record_sql($sql,array("courseid"=>$data->courseid));

        // Select users who has at least one of the open badges defined in the enrolment method
        $ssearchbadges = str_replace('#',',',$obadgesid->customtext1);
        $auserslisttoenrol =  SELF::get_users_from_badges($ssearchbadges);
//        $sql ="SELECT   distinct(userid) as id
//                FROM    {local_ibob_badge_issued}
//                WHERE   badgeid in (:badgelist)";
//        $auserslisttoenrol = $DB->get_records_sql($sql,array("badgelist"=>$ssearchbadges));
        foreach($auserslisttoenrol as $ouser){
            $message->userto = \core_user::get_user($ouser->id);
//            mtrace("Message créé : ".print_r($message));
            $messageid = message_send($message);
//            mtrace("Message envoyé : ".print_r($messageid));
        }
//        mtrace("Fin de la tache adhoc");
    }

    function get_users_from_badges($ssearchbadges) {
        global $DB;
        $sql ="SELECT   distinct(userid) as id
                FROM    {local_ibob_badge_issued}
                WHERE   badgeid in (:badgelist)";
        return $DB->get_records_sql($sql,array("badgelist"=>$ssearchbadges));
    }

    function get_badges_from_course($courseid) {
        global $DB;
        $sql ="SELECT   customtext1
                FROM    {enrol}
                WHERE   enrol='ibobenrol' and courseid=:courseid";
        return $DB->get_record_sql($sql,array("courseid"=>$courseid));
    }
}