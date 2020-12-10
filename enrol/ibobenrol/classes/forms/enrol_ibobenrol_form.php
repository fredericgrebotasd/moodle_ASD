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
 * The library file for badge enrolment plugin.
 *
 * @package    enrol_ibobenrol
 * @copyright  2015 onwards Matthias Schwabe {@link http://matthiasschwa.be}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_ibobenrol\forms;
defined('MOODLE_INTERNAL') || die();

class enrol_ibobenrol_form extends \moodleform {
    protected $instance;

    /**
     * Overriding this function to get unique form id for multiple self enrolments.
     *
     * @return string form identifier
     */
    protected function get_form_identifier() {
        $formid = $this->_customdata->id . '_' . get_class($this);
        return $formid;
    }

    public function definition() {
        global $USER, $OUTPUT, $CFG;

        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        $plugin = enrol_get_plugin('ibobenrol');

        $heading = $plugin->get_instance_name($instance);
        $mform->addElement('header', 'ibobenrol_header', $heading);

        $mform->addElement('static', 'access', '', get_string('accessgranted', 'enrol_ibobenrol'));

        $this->add_action_buttons(false, get_string('enrolme', 'enrol_ibobenrol'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
    }

    public function validation($data, $files) {

        $errors = parent::validation($data, $files);
        $instance = $this->instance;

        return $errors;
    }
}