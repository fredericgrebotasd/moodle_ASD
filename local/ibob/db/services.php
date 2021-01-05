<?php

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localibob
 * @copyright  2020 Frédéric Grebot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'local_ibob_detail_badge_function' => array(
        'classname' => 'local_ibob_external',
        'methodname' => 'detail_badge_function',
        'classpath' => 'local/ibob/externallib.php',
        'description' => 'Print badge detail',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => '',
    ),
);

$services = array(
    'ibobwebservice' => array(
        'functions' => array ('local_ibob_detail_badge_function'),
        'requiredcapability' => '',
        'restrictedusers' =>0,
        'enabled'=>1,
        'shortname'=>'myibobwebservice'
    )
);