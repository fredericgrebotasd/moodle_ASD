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
 * Page for handling user's email confirmation
 *
 * @package    local_ibob
 * @copyright  2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function delete_badges_user($userid) {
    global $DB;
    $DB->delete_records('local_ibob_badge_issued', array('userid'=>$userid));
}

function update_api_key_user($infoapiuser) {
    global $DB;
    $ouserapikey = new stdClass();
    $ouserapikey->id = $infoapiuser->id;
    $ouserapikey->timemodified = time();
    $ouserapikey->key_field = json_encode(array('email'=>$infoapiuser->confirmation_email_wanted));
    $ouserapikey->confirmation_needed = 0;
    $ouserapikey->confirmation_code = '';
    $ouserapikey->confirmation_expiration_date = null;
    $ouserapikey->confirmation_email_wanted = '';
    $apikeyuser = $DB->update_record('local_ibob_user_apikey', $ouserapikey);
    return $apikeyuser;
}

function get_info_user($code) {
    global $DB;
    $sql ="SELECT   *
                FROM    {local_ibob_user_apikey} 
                WHERE   confirmation_code=:confirmation_code";
    return $DB->get_record_sql($sql,array("confirmation_code"=>$code));
}

function generate_confirmation_code() {
    return mt_rand(1000,9999);
}

require_once(__DIR__ . '/../../config.php');

global $DB;
$content='';
$action='';
if(isloggedin()){
    $returnurl = optional_param('returnurl', '/user/profile.php?id='.$USER->id, PARAM_LOCALURL);
} else {
    $returnurl = optional_param('returnurl', '/', PARAM_LOCALURL);
}

$context = context_system::instance();
$url = new moodle_url('/local/ibob/emailconfirmation.php', array('action' => $action));
$urlnewcode = new moodle_url('/local/ibob/userconfig.php', array('action' => $action));

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

$mform = new \local_ibob\form\emailconfirmation(null,array('returnurl' => $returnurl));

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form
    $infoapiuser=get_info_user($fromform->emailconfirmationcode);
    $returnedhtml = html_writer::start_div();
    if($infoapiuser){
        if(time()>$infoapiuser->confirmation_expiration_date){ // valid code but expiration date reached
            $returnedhtml .= html_writer::tag('h1',get_string('emailconfirmationinvalidcode', 'local_ibob'));
            $returnedhtml .= html_writer::tag('p',get_string('emailconfirmationexpirationdatereached', 'local_ibob'));
            $returnlink = html_writer::link(new moodle_url($urlnewcode),get_string('emailconfirmationlinknewcode', 'local_ibob'));
            $returnedhtml .= html_writer::tag('p',$returnlink);
//
//            echo $OUTPUT->header();
//            $content .= $returnedhtml;
//            $content .= $OUTPUT->footer();
//            echo $content;
//            exit;
        } else { // code is valid
            update_api_key_user($infoapiuser);
            delete_badges_user($infoapiuser->user_id);
            redirect(new moodle_url($returnurl));
        }
    } else {  // code is invalid
        $returnedhtml .= html_writer::tag('h1',get_string('emailconfirmationinvalidcode', 'local_ibob'));
        $returnlink = html_writer::link(new moodle_url($url),get_string('emailconfirmationreturn', 'local_ibob'));
        $returnedhtml .= html_writer::tag('p',$returnlink);

    }
    $returnedhtml .= html_writer::end_div();

    echo $OUTPUT->header();
    $content .= $returnedhtml;
    $content .= $OUTPUT->footer();
    echo $content;
    exit;
}

//Set default data
$toform=array();
$mform->set_data($toform);
echo $OUTPUT->header();
$content .= $mform->render();
$content .= $OUTPUT->footer();
echo $content;
