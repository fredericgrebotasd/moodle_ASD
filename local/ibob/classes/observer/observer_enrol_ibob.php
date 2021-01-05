<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 15/12/20
 * Time: 10:08
 */

namespace local_ibob\observer;
use local_ibob\task\adhoc_send_emails_notifications;

class observer_enrol_ibob {
    public static function enrol_instance_created(\core\event\enrol_instance_created $event) {
//        mtrace("Observer : 1 cours avec une instance enrolment est créé");
        $courseid = $event->courseid;
        $enrolid = $event->objectid;
        $other = $event->other;
        if (!empty($other)) {
            $enrol=$other['enrol'];
            if ($enrol === 'ibobenrol') {
                try {
//                    debugging('test observer created / courseid='.$courseid." / enrolid=".$enrolid);
                    $task = new adhoc_send_emails_notifications();
                    $task->set_custom_data(['courseid' => $courseid,'enrolid' => $enrolid,'event' => $event]);
                    \core\task\manager::queue_adhoc_task($task);
                } catch (Exception $e){
                    debugging("course {$courseid} from method enrolment {$enrolid} problem " .
                        $e->getMessage(), DEBUG_NORMAL, $e->getTrace());
                }
            }
        }
    }
    public static function enrol_instance_updated(\core\event\enrol_instance_updated $event) {
        $courseid = $event->courseid;
        debugging('test observer updated / courseid='.$courseid);
        mtrace("Observer : 1 cours avec une instance enrolment est updaté");
//        $enrolid = $event->objectid;
//        $other = $event->other;
//        if (!empty($other)) {
//            $enrol=$other['enrol'];
//            if ($enrol === 'ibobenrol') {
//                try {
//                    debugging('test observer updated / courseid='.$courseid." / enrolid=".$enrolid);
//                    $task = new adhoc_send_emails_notifications();
//                    $task->set_custom_data(['courseid' => $courseid,'enrolid' => $enrolid]);
//                    \core\task\manager::queue_adhoc_task($task);
//                } catch (Exception $e){
//                    debugging("course {$courseid} from method enrolment {$enrolid} problem " .
//                        $e->getMessage(), DEBUG_NORMAL, $e->getTrace());
//                }
//            }
//        }
    }
}