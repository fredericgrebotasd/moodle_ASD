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
        'callback' => 'local_ibob\observer\observer_enrol_ibob::enrol_instance_created',
        'priority'    => 9999,
    ),
    array(
        'eventname' => '\core\event\enrol_instance_updated',
        'callback' => 'local_ibob\observer\observer_enrol_ibob::enrol_instance_updated',
        'priority'    => 9999,
    ),
);