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
 * Adds a new instance of enrol_ibobenrol to specified course
 * or edits current instance.
 *
 * @package    enrol_ibobenrol
 * @copyright  2015 onwards Matthias Schwabe {@link http://matthiasschwa.be}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_ibobenrol_edit_form extends moodleform {

    public function definition() {
        global $DB;

        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_ibobenrol'));

//        if ($badges = $DB->get_records('badge', array('type' => 1))) {
        if ($badges = \local_ibob\ibob_badges::get_records()) {

            $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
            $mform->setType('name', PARAM_TEXT);

            $badgeslist = array();
            foreach ($badges as $badge) {
//                if ($badge->status == 1 or $badge->status == 3) {
                    $badgeslist[$badge->get('id')] = $badge->get('name');
//                }
            }
            $select = $mform->addElement('select', 'badges', get_string('selectbadges', 'enrol_ibobenrol'),
                $badgeslist, array('size' => '12'));
            $select->setMultiple(true);

            $roles = get_assignable_roles($context, ROLENAME_BOTH);
            $studentid = $DB->get_field('role', 'id', array('shortname' => 'student'), IGNORE_MISSING);
            $mform->addElement('select', 'roleid', get_string('role', 'enrol_ibobenrol'), $roles);
            if ($studentid) {
                $mform->setDefault('roleid', $studentid);
            }

            $mform->addElement('checkbox', 'customint1', get_string('autoenrol', 'enrol_ibobenrol'));
            $mform->setType('customint1', PARAM_INT);

            $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        } else {
            $mform->addElement('static', 'nobadgesfound', '', get_string('nobadgesfound', 'enrol_ibobenrol'));
            $mform->addElement('cancel');
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->set_data($instance);
    }

    public function set_data($instance) {
        parent::set_data($instance);
        if (isset($instance->customtext1)) {
            $defaultvalues['badges'] = explode('#', $instance->customtext1);
            parent::set_data($defaultvalues);
        }
    }
}
