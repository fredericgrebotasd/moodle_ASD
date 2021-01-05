<?php

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
 * External Web Service Template
 *
 * @package    localibob
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir."/externallib.php");

class local_ibob_external extends external_api {

    function get_badge($badgeid) {
        global $DB;
        $DB->set_debug(true);
        return $DB->get_record_select('local_ibob_badges', 'id=:id', array('id'=>$badgeid), $fields='*', $strictness=IGNORE_MISSING);
    }

    public static function detail_badge_function($badgeid) {
        $params = self::validate_parameters(self::detail_badge_function_parameters(), array('badgeid'=>$badgeid));
        return self::get_badge($badgeid);
    }

    public static function detail_badge_function_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'badge id'),
                'name' => new external_value(PARAM_TEXT, 'badge name'),
                'description' => new external_value(PARAM_TEXT, 'badge description'),
                'issuername' => new external_value(PARAM_TEXT, 'badge issuer name'),
                'issuerurl' => new external_value(PARAM_TEXT, 'badge issuer url'),
                'issuercontact' => new external_value(PARAM_TEXT, 'badge issuer contact'),
                'group' => new external_value(PARAM_INT, 'badge group'),
                'image' => new external_value(PARAM_TEXT, 'badge image url'),
            )
        );
    }

    public static function detail_badge_function_parameters() {
        return new external_function_parameters(
            array(
                'badgeid' => new external_value(PARAM_INT, 'The badge id',VALUE_REQUIRED)
            )
        );
    }
}
