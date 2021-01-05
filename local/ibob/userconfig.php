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
 * Page for handling user's backpack settings.
 *
 * @package    local_ibob
 * @copyright  2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function delete_api_key_user($apiuserkey) {
    global $DB;
    $DB->delete_records('local_ibob_user_apikey', array('id'=>$apiuserkey));
}

function delete_badges_user($userid) {
    global $DB;
    $DB->delete_records('local_ibob_badge_issued', array('userid'=>$userid));
}

function insert_api_key_user($userid,$email) {
    global $DB;
    $ouserapikey = new stdClass();
    // pour l'instant, providerid=1 car 1 seulement obp pris en compte
    $ouserapikey->provider_id = 1;
    $ouserapikey->timecreated = time();
    $ouserapikey->key_field = json_encode(array('email'=>'waiting@validation.com'));
    $ouserapikey->confirmation_email_wanted = $email;
    $ouserapikey->user_id = $userid;
    $apikeyuser = $DB->insert_record('local_ibob_user_apikey', $ouserapikey);
    return $apikeyuser;
}

function update_api_key_user($userid,$formdata) {
    global $DB;
    $ouserapikey = new stdClass();
    $ouserapikey->id = $userid;
    $ouserapikey->key_field = json_encode(array('email'=>$formdata->providerapikey));
    $apikeyuser = $DB->update_record('local_ibob_user_apikey', $ouserapikey);
    return $apikeyuser;
}

function send_email_confirmation($userid,$newemail,$ouserapikey){
    global $CFG;
    $adateexpiration=getdate($ouserapikey->confirmation_expiration_date);
    $message = new \core\message\message();
    $message->component = 'local_ibob';
    $message->name = 'ibobemailchange';
    $message->userto = \core_user::get_user($userid);
    $message->userto->email = $newemail;
    $message->userfrom = \core_user::get_noreply_user();
    $message->subject = 'Confirmation de changement de mèl pour votre compte Open Badge Passport dans moodle';
    $message->fullmessage = "Bonjour\n\nVous avez initié un changement de mèl dans la plateforme moodlle '".$CFG->wwwroot."' pour votre compte Open Badge Passport.\n\n";
    $message->fullmessage .= "Si vous êtes à l'initiative de ce changement, pour qu'il soit effectif, vous devez cliquer sur le lien ".html_writer::link(
            new moodle_url($CFG->wwwroot.'/local/ibob/emailconfirmation.php'),
            "de validation de votre mèl"
        )." et saisir le code de confirmation suivant :\n\n";
    $message->fullmessage .= "Code de confirmation à saisir :\n\n";
    $message->fullmessage .= $ouserapikey->confirmation_code."\n\n";
    $message->fullmessage .= "Ce code sera valide jusqu'au ".$adateexpiration['mday']."/".$adateexpiration['mon']."/".$adateexpiration['year']." à ".$adateexpiration['hours']."h".$adateexpiration['minutes'].".\n\n";
    $message->fullmessage .= "Merci d'utiliser ".$CFG->wwwroot." et bon apprentissage !";
    $message->fullmessageformat = FORMAT_PLAIN;
    $message->fullmessagehtml = "<h1>Bonjour</h1><p>Vous avez initié un changement de mèl dans la plateforme moodlle '".$CFG->wwwroot."' pour votre compte Open Badge Passport.</p>";
    $message->fullmessagehtml .= "<p>Si vous êtes à l'initiative de ce changement, pour qu'il soit effectif, vous devez cliquer sur le lien ".html_writer::link(
            new moodle_url($CFG->wwwroot.'/local/ibob/emailconfirmation.php'),
            "de validation de votre mèl"
        ). " et saisir le code de confirmation ci-dessous :</p>";
    $message->fullmessagehtml .= "<p><strong>Code de confirmation à saisir : ".$ouserapikey->confirmation_code."</strong></p>";
    $message->fullmessagehtml .= "<p>Ce code sera valide 24h, soit jusqu'au ".$adateexpiration['mday']."/".$adateexpiration['mon']."/".$adateexpiration['year']." à ".$adateexpiration['hours']."h".$adateexpiration['minutes'].".</p>";
    $message->fullmessagehtml .= "<h2>Merci d'utiliser ".$CFG->wwwroot." et bon apprentissage !</h2>";

    message_send($message);
}

function update_confirmation_sequence_init($userid,$apikeyid,$newemail) {
    global $DB;
    $ouserapikey = new stdClass();
    $ouserapikey->id = $apikeyid;
    $ouserapikey->confirmation_needed = true;
    $expirationdate = time()+86400;
    $ouserapikey->confirmation_code = generate_confirmation_code();
    $ouserapikey->confirmation_expiration_date = $expirationdate;
    $ouserapikey->confirmation_email_wanted  = $newemail;

    send_email_confirmation($userid,$newemail,$ouserapikey);
    $apikeyuser = $DB->update_record('local_ibob_user_apikey', $ouserapikey);
    return $apikeyuser;
}

function get_info_user($userid) {
    global $DB;
    return $DB->get_record_select('local_ibob_user_apikey', 'user_id=:user_id', array('user_id'=>$userid), '*', IGNORE_MISSING);
}

function generate_confirmation_code() {
    return mt_rand(1000,9999);
}

require_once(__DIR__ . '/../../config.php');

require_login();

global $USER,$DB;
$content='';
$action='';
$returnurl = optional_param('returnurl', '/user/profile.php?id='.$USER->id, PARAM_LOCALURL);

$context = context_user::instance($USER->id);
$url = new moodle_url('/local/ibob/userconfig.php', array('action' => $action));

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

$mform = new \local_ibob\form\userconfig(null,array('returnurl' => $returnurl));

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form
    if($DB->record_exists('local_ibob_user_apikey', array('user_id'=>$USER->id))){
        $infoapiuser=get_info_user($USER->id);
        // Is the confirmation sequence initiated ?
        if($infoapiuser->confirmation_needed===0){
            // case 1 : verification : has the email changed ?
            if($infoapiuser->key_field!==''){ // email recorded
                $jsondecoded=json_decode($infoapiuser->key_field);
                if($jsondecoded->email!==$fromform->providerapikey){
                    if($fromform->providerapikey!==''){
                        // case 1 : email has changed, record the new email in database and initiate verification sequence
                        if(!update_confirmation_sequence_init($USER->id,$infoapiuser->id,$fromform->providerapikey)){
                            echo "Problème pendant l'update du provider, case 1<br>";exit;
                        }
                    } else {
                        delete_badges_user($USER->id);
                        delete_api_key_user($infoapiuser->id);
                    }
                }
            } else {
                // case 2 : no old email, record the new email in database and initiate verification sequence
                if(!update_confirmation_sequence_init($USER->id,$infoapiuser->id,$fromform->providerapikey)){
                    echo "Problème pendant l'update du provider, case 2<br>";exit;
                }
            }
        } else {
            // case 3 : waiting for the confirmation code
            if($fromform->providerapikey!==''){ // email wanted typed, re-doing the verfication sequence
                if(!update_confirmation_sequence_init($USER->id,$infoapiuser->id,$fromform->providerapikey)){
                    echo "Problème pendant l'update du provider, case 3<br>";exit;
                }
            } else {
                // email typed is empty, erasing provider and issued badges
                delete_badges_user($USER->id);
                delete_api_key_user($infoapiuser->id);
            }
        }
    } else {
        // case 4 : no provider yet for the user
        if($fromform->providerapikey!==''){
            //case 4 ; create provider for the user
            if($apikeyid=insert_api_key_user($USER->id,$fromform->providerapikey)){
                // case 4 : initiate verification sequence
                if(!update_confirmation_sequence_init($USER->id,$apikeyid,$fromform->providerapikey)){
                    echo "Problème pendant l'update du provider, case 4<br>";exit;
                }
            } else {
                echo "Problème pendant la creation du provider, case 4<br>";exit;
            }
        }
    }
    redirect(new moodle_url($returnurl));
}

//Set default data
$toform=array();
if($ojsonapikey=get_info_user($USER->id)){ // user has a provider
    if($ojsonapikey->key_field!==''){
        $jsondecoded=json_decode($ojsonapikey->key_field);
        if($ojsonapikey->confirmation_needed==1){ // waiting for confirmation
            $jsondecoded->email=$ojsonapikey->confirmation_email_wanted;
        }
        $toform=array('providerapikey'=>$jsondecoded->email,'hasprovider'=>$ojsonapikey->provider_id);
    }
} else {
    $toform=array('providerapikey'=>$USER->email,'hasprovider'=>'0');
}

$mform->set_data($toform);
echo $OUTPUT->header();
$content .= $mform->render();
$content .= $OUTPUT->footer();
echo $content;
