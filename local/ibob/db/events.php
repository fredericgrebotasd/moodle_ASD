<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 15/12/20
 * Time: 10:03
 */

defined('MOODLE_INTERNAL') or die();

$observers = array(
    array(
        'eventname' => '\core\event\enrol_instance_created',
        'callback' => '\local_ibob\observers\adhoc_task_enrol_ibob::enrol_instance_created',
    ),
//    array(
//        'eventname' => '\core\event\course_updated',
//        'callback' => '\local_ibob\observers\course::course_created',
//    ),
);