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
 * Ibob enrolment tests.
 *
 * @package    enrol_ibobenrol
 * @copyright  2015 onwards Matthias Schwabe {@link http://matthiasschwa.be}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Ibob enrolment tests.
 *
 * @package    enrol_ibobenrol
 * @copyright  2015 onwards Matthias Schwabe {@link http://matthiasschwa.be}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_ibobenrol_observer_testcase extends advanced_testcase {

    public function test_award_badge() {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $ibobenrol = enrol_get_plugin('ibobenrol');
        $id = $ibobenrol->add_instance($course, array('customtext1'=>'#1'));
        $instance3b = $DB->get_record('enrol', array('id'=>$id), '*', MUST_EXIST);
        $now = time();
        $issued = new stdClass();
        $issued->badgeid = 1;
        $issued->userid = $user->id;
        $issued->uniquehash = sha1(rand() .  $user->id . '1' . $now);
        $issued->dateissued = $now;
        $DB->insert_record('badge_issued', $issued, true);
        enrol_ibobenrol_observer::award_badge($user->id);
        $this->assertTrue(is_enrolled(context_course::instance($course->id)));
    }
}