<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Event observers used in enrol_ibobenrol plugin.
 *
 * @package    enrol_ibobenrol
 * @author     Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_ibobenrol_observer {

    /**
     * Observer function to handle the assessable_uploaded event in mod_assign.
     * @param \assignsubmission_file\event\assessable_uploaded $event
     */
    public static function badge_awarded(\core\event\badge_awarded $event) {
        global $DB;
        // Check to make sure this plugin is enabled.
        if (!enrol_is_enabled('ibobenrol')) {
            return;
        }
        $eventdata = $event->get_data();
        static::award_badge($eventdata['relateduserid'],
            empty($eventdata['other']['badgeissuedid'])?null:$eventdata['other']['badgeissuedid']
        );
    }
    /**
     * Observer function to handle the assessable_uploaded event in mod_assign.
     * @param \assignsubmission_file\event\assessable_uploaded $event
     */
    public static function award_badge($userid, $badgeissuedid = null) {
        global $DB;
        // Check to see if this badge should result in an enrolment.
        static $enrolmentplugins;
        // It would be better if the enrol plugin used it's own tables so we could search for plugins relevant
        // to this badge, instead we populate an array to use in case multiple badges are assigned at the same time.
        if (empty($enrolmentplugins)) {
            // Get all enrolment plugins.
            $enrolmentplugins = $DB->get_records('enrol', array('enrol' => 'ibobenrol', 'customint1' => 1));
        }
        foreach ($enrolmentplugins as $ep) {
            $badges = explode('#', $ep->customtext1);
            if (!empty($badgeissuedid)) {
                $badgeid = $DB->get_field('badge_issued', 'badgeid', array('id' => $badgeissuedid));
                if (in_array($badgeid, $badges)) {
                    if (count($badges) > 1) { // If more than one badge required, check user has all.
                        foreach ($badges as $badge) {
                            if ($badge == $badgeid) {
                                continue;
                            }
                            // Check the user has this badge - if not, prevent enrolment.
                            if (!$DB->record_exists('badge_issued', array('badgeid' => $badge,
                                'userid' => $userid))) {
                                return; // Stop here and don't enrol user, more badges required before enrolment can be given.
                            }
                        }
                    }
                    $plugin = enrol_get_plugin('ibobenrol');
                    $plugin->enrol_user($ep, $userid, $ep->roleid, time());
                }
            }
        }
    }
}