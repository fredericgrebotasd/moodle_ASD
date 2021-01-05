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

global $CFG,$DB;

require_once($CFG->libdir.'/formslib.php');

class userconfig extends \moodleform {

    /**
     * Defines forms elements
     */
    protected function definition(){
        global $CFG;

        $mform = $this->_form;
        $mform->addElement('html', '<div class="formheader"><h2>'.get_string('emaildescription','local_ibob').'</h2></div>');
        $mform->addElement('html', '<p>'.get_string('emailsequenceexplanation','local_ibob').'</p>');
        $mform->addElement('text', 'providerapikey', get_string('email'),array('size'=>'35'));
        $mform->setType('providerapikey', PARAM_EMAIL);
        $mform->addRule('providerapikey', get_string('invalidemail','local_ibob'), 'email', null, 'client');
        if(SELF::printvalidationtext()){
            $mform->addElement('html', '<div><p>'.get_string('emailvalidated','local_ibob').' : <strong>'.get_string(SELF::gettextvalidatedemail(),'local_ibob').'</strong> </p></div>');
            SELF::generatevalidationlink($mform);
        }
        $this->add_action_buttons();
        $mform->addElement('hidden', 'hasprovider', null);
        $mform->setType('hasprovider', PARAM_INT);

        function validation($data, $files) {
            return array();
        }
    }

    function gettextvalidatedemail(){
        global $DB,$USER;
        $confirmationneeded = $DB->get_record_select('local_ibob_user_apikey', 'user_id=:user_id', array('user_id'=>$USER->id), 'confirmation_needed', IGNORE_MISSING);
        $mystr='emailvalidatedno';
        if($confirmationneeded){
            if($confirmationneeded->confirmation_needed==0){
                $mystr='emailvalidatedyes';
            }
        }
        return $mystr;
    }

    function generatevalidationlink($mform){
        global $DB,$USER,$CFG;
        $confirmationneeded = $DB->get_record_select('local_ibob_user_apikey', 'user_id=:user_id', array('user_id'=>$USER->id), 'confirmation_needed', IGNORE_MISSING);
        if($confirmationneeded){
            if($confirmationneeded->confirmation_needed==1){
                $mform->addElement('static', 'staticlink', '', '<p><a href="'.$CFG->wwwroot.'/local/ibob/emailconfirmation.php" target="_self" title="'.get_string('addvalidationcodelink','local_ibob').'">'.get_string('addvalidationcodelink','local_ibob').'</a></p>');
            }
        }
    }

    function printvalidationtext(){
        global $DB, $USER;
        $hasprovider = $DB->get_record_select('local_ibob_user_apikey', 'user_id=:user_id', array('user_id' => $USER->id), 'id', IGNORE_MISSING);
        if ($hasprovider) {
            return true;
        }
    }
}
