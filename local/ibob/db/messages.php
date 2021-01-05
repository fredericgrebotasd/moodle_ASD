<?php
/**
 * Plugin message provider.
 *
 * @package     local_ibob
 * @category    message
 * @copyright   2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$messageproviders = array(
    'enrolcreatedupdated' => array(
        'capability' => 'local/ibob:emailnotifyenrolibob',
        'defaults' => array(
            'popup' => MESSAGE_FORCED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_LOGGEDIN + MESSAGE_DEFAULT_LOGGEDOFF,
        ),
    ),
    'ibobemailchange' => array(
        'capability' => 'local/ibob:ibobemailchange',
        'defaults' => array(
            'popup' => MESSAGE_DISALLOWED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_LOGGEDIN + MESSAGE_DEFAULT_LOGGEDOFF,
        ),
    ),
//    'defaults' => [
//        'popup' => MESSAGE_DISALLOWED,
//        'email' => MESSAGE_PERMITTED
//    ],
);