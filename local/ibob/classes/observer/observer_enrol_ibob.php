<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 15/12/20
 * Time: 10:08
 */

namespace local_ibob\observers;

use local_ibob\task\adhoc_send_emails_notifications;

class observer_enrol_ibob {
    public static function enrol_instance_created(\core\event\enrol_instance_created $event) {
        $courseid = $event->courseid;
        $enrolid = $event->objectid;

        try {
            debugging('test observer created / courseid='.$courseid." / enrolid=".$enrolid);
            $task = new adhoc_send_emails_notifications();
            $task->set_custom_data(['courseid' => $courseid,'enrolid' => $enrolid]);
            \core\task\manager::queue_adhoc_task($task);
        } catch (Exception $e){
            debugging("course {$courseid} from method enrolment {$enrolid} problem " .
                $e->getMessage(), DEBUG_NORMAL, $e->getTrace());
        }
    }
    public static function enrol_instance_updated(\core\event\enrol_instance_updated $event) {
        $courseid = $event->courseid;
        $enrolid = $event->objectid;

        try {
            debugging('test observer updated / courseid='.$courseid." / enrolid=".$enrolid);
            $task = new adhoc_send_emails_notifications();
            $task->set_custom_data(['courseid' => $courseid,'enrolid' => $enrolid]);
            \core\task\manager::queue_adhoc_task($task);
        } catch (Exception $e){
            debugging("course {$courseid} from method enrolment {$enrolid} problem " .
                $e->getMessage(), DEBUG_NORMAL, $e->getTrace());
        }
    }
}