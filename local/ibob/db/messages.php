<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 15/12/20
 * Time: 11:23
 */
/**
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     block_todo
 * @category    upgrade
 * @copyright   2020 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$messageproviders = array(
    'courseobcreated' => array(
        'capability' => 'local/ibob:emailnotifycourseobcreated'
    ),
);