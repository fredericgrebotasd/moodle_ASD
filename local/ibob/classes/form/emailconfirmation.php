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

namespace local_ibob\form;

/**
 * User config form.
 *
 * @package    local_ibob
 * @copyright  2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

global $CFG;

require_once($CFG->libdir.'/formslib.php');

class userconfig extends \moodleform {

    /**
     * Defines forms elements
     */
    protected function definition() {
        global $CFG;

        $mform = $this->_form;
        $mform->addElement('html', '<div class="formheader"><h2>'.get_string('emaildescription','local_ibob').'</h2></div>');
        $mform->addElement('text', 'providerapikey', get_string('email'),array('size'=>'35'));
        $mform->setType('providerapikey', PARAM_EMAIL);
        $mform->addRule('providerapikey', get_string('invalidemail','local_ibob'), 'email', null, 'client');
        $this->add_action_buttons();
        $mform->addElement('hidden', 'hasprovider', null);
        $mform->setType('hasprovider', PARAM_INT);

        function validation($data, $files) {
            return array();
        }
    }
}
