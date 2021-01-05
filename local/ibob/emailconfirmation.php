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
    $message->name = 'enrolcreatedupdated';

    $message->userfrom = \core_user::get_noreply_user();
    $message->subject = 'Confirmation de changement de mèl pour votre compte Open Badge Passport dans moodle';
    $message->fullmessage = "Bonjour\n\nVous avez initié un changement de mèl dans la plateforme moodlle '".$CFG->wwwroot."' pour votre compte Open Badge Passport.\n\n";
    $message->fullmessage .= "Si vous êtes à l'initiative de ce changement, pour qu'il soit effectif, vous devez vous connecter sur ".$CFG->wwwroot.", et saisir le code de confirmation ci-dessous dans les paramètres de votre profil, Inscription par open badges (Ibob) -> Gérer votre configuration.\n\n";
    $message->fullmessage .= "Ce code sera valide jusqu'au ".$adateexpiration['mday']."/".$adateexpiration['mon']."/".$adateexpiration['year']." à ".$adateexpiration['hours']."-".$adateexpiration['minutes']."-".$adateexpiration['seconds']."\n\n";
    $message->fullmessage .= "Code de confirmation à saisir :\n\n";
    $message->fullmessage .= $ouserapikey->confirmation_code."\n\n";
    $message->fullmessage .= "Merci d'utiliser ".$CFG->wwwroot." et bon apprentissage !";
    $message->fullmessageformat = FORMAT_PLAIN;
    $message->fullmessagehtml = "<h1>Bonjour</h1><p>Vous avez initié un changement de mèl dans la plateforme moodlle '".$CFG->wwwroot."' pour votre compte Open Badge Passport.</p>";
    $message->fullmessagehtml .= "<p>Si vous êtes à l'initiative de ce changement, pour qu'il soit effectif, vous devez vous connecter sur ".$CFG->wwwroot.", et saisir le code de confirmation ci-dessous dans les paramètres de votre profil, Inscription par open badges (Ibob) -> Gérer votre configuration.</p>";
    $message->fullmessage .= "<p>Ce code sera valide 24h, soit jusqu'au ".$adateexpiration['mday']."/".$adateexpiration['mon']."/".$adateexpiration['year']." à ".$adateexpiration['hours']."-".$adateexpiration['minutes']."-".$adateexpiration['seconds']."</p>";
    $message->fullmessagehtml .= "<p>Code de confirmation à saisir :</p>";
    $message->fullmessagehtml .= "<p>".$ouserapikey->confirmation_code."</p>";
    $message->fullmessagehtml .= "<h2>Merci d'utiliser ".$CFG->wwwroot." et bon apprentissage !</h2>";
    $message->smallmessage = "Bonjour. Voici le code de confirmation valable 24h à saisir dans les préférences de votre profil pour valider le changement de mèl pour Open Badge Passport dans moodle : ".$ouserapikey->confirmation_code;
    $message->contexturl = $CFG->wwwroot.'/my';
    $message->contexturlname = 'Context name';
    $message->replyto = "random@example.com";

    $message->userto = \core_user::get_user($userid);
    $message->userto->email = $newemail;
    echo "email destinataire : ".$message->userto->email."<br>";
//    echo "<br>";print_r($message);echo "<br>";
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
//    if(send_email_confirmation($userid,$newemail,$ouserapikey)){
//        $apikeyuser = $DB->update_record('local_ibob_user_apikey', $ouserapikey);
//    } else {
//        echo "Erreur pendant l'envoi du mèl de confirmation...";exit;
//    }
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
    echo "<br><br><br><br><br><br><br><br><br>";
    //In this case you process validated data. $mform->get_data() returns data posted in form
//    $DB->set_debug(true);
    if($DB->record_exists('local_ibob_user_apikey', array('user_id'=>$USER->id))){
        echo "provider existe<br>";
        $infoapiuser=get_info_user($USER->id);
        // Is the confirmation sequence initiated ?
        if($infoapiuser->confirmation_needed===0){
            echo "confirmation sequence non initiée encore<br>";
            $initiatedConfirmation=true;
            // step 1 : verification : has the email changed ?
            if($ojsonapikey->key_field!==''){ // email recorded
                echo "le user a un email dans son provider<br>";
                $jsondecoded=json_decode($ojsonapikey->key_field);
                if($jsondecoded->email!==$fromform->providerapikey){
                    echo "l'email a changé<br>";
                    // step 2 : email has changed, record the new email in database and initiate verification sequence
                    if(!update_confirmation_sequence_init($USER->id,$infoapiuser->id,$fromform->providerapikey)){
                        echo "Problème pendant l'update du provider, step 2<br>";exit;
                    } else {
                        echo "confirmation sequence initiéee<br>";
                    }
                }
            } else {
                // step 1 : no old email, record the new email in database and initiate verification sequence
                if(!update_confirmation_sequence_init($USER->id,$infoapiuser->id,$fromform->providerapikey)){
                    echo "Problème pendant l'update du provider, step 1<br>";exit;
                } else {
                    echo "confirmation sequence initiéee<br>";
                }
            }
        } else {
            // step 3 : en attente du code de confirmation
//            $initiatedConfirmation=true;

            // affichege du form du code de verif

        }
    } else { // no provider yet for the user
        echo "provider n'existe pas<br>";
        if($fromform->providerapikey!==''){
            echo "formulaire provider non vide<br>";
            //step 2 ; create provider for the user
            if($apikeyid=insert_api_key_user($USER->id,$fromform->providerapikey)){
                echo "provider inséré<br>";
                // step 3 : initiate verification sequence
                if(!update_confirmation_sequence_init($USER->id,$apikeyid,$fromform->providerapikey)){
                    echo "Problème pendant l'update du provider, step 3<br>";exit;
                } else {
                    echo "confirmation sequence initiée<br>";
                }
            } else {
                echo "Problème pendant la creation du provider<br>";exit;
            }
        }
    }


//    if($DB->record_exists('local_ibob_user_apikey', array('user_id'=>$USER->id))){
//        $idapiuser=get_info_user($USER->id);
//        switch ($fromform->hasprovider) {
//            case 1:
//                $apikeyuser=delete_badges_user($USER->id);
//                if(!$fromform->providerapikey){
//                    $apikeyuser=delete_api_key_user($idapiuser->id);
//                } else {
//                    $apikeyuser=update_api_key_user($idapiuser->id,$fromform);
//                }
//                break;
//
//            case 0:
//                $apikeyuser=update_api_key_user($idapiuser->id,$fromform);
//                break;
//        }
//    } else {
//        $apikeyuser=insert_api_key_user($USER->id,$fromform);
//    }
//    redirect(new moodle_url($returnurl));
}
//Set default data

$toform=array();

//echo "<br><br><br><br><br><br><br><br><br><br>";
//print_r(get_info_user($USER->id));

if($ojsonapikey=get_info_user($USER->id)){ // user has a provider
    if($ojsonapikey->confirmation_needed===1){ // a validation code is expected
        $mform .= new \local_ibob\form\emailconfirmation(null,array('returnurl' => $returnurl));
    }



    if($ojsonapikey->key_field!==''){
        $jsondecoded=json_decode($ojsonapikey->key_field);
        $emailvalidatedyesorno="Oui";
        if($ojsonapikey->confirmation_needed==1){ // waiting for confirmation
            $jsondecoded->email=$ojsonapikey->confirmation_email_wanted;
            $emailvalidatedyesorno="Non";
        }
        $toform=array('providerapikey'=>$jsondecoded->email,'hasprovider'=>$ojsonapikey->provider_id);
    }
} else {
    $toform=array('providerapikey'=>$USER->email,'hasprovider'=>'0','emailvalidatedyesorno'=> $emailvalidatedyesorno);
}

$mform->set_data($toform);
echo $OUTPUT->header();
$content .= $mform->render();
$content .= $OUTPUT->footer();
echo $content;
