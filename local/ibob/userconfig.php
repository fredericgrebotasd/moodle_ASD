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

function insert_api_key_user($userid,$formdata) {
    global $DB;
    $ouserapikey = new stdClass();
    // pour l'instant, providerid=1 car 1 seulement obp pris en compte
    $ouserapikey->provider_id = 1;
    $ouserapikey->key_field = json_encode(array('email'=>$formdata->providerapikey));
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

function get_api_key_user($userid) {
    global $DB;
    return $DB->get_record_select('local_ibob_user_apikey', 'user_id=:user_id', array('user_id'=>$userid), $fields='key_field', $strictness=IGNORE_MISSING);
}

require_once(__DIR__ . '/../../config.php');

require_login();

global $USER,$DB;
$content='';
$action='';
$returnurl = optional_param('returnurl', '/user/preferences.php', PARAM_LOCALURL);

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
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    if($idapiuser=$DB->record_exists('local_ibob_user_apikey', array('user_id'=>$USER->id))){
        $DB->set_debug(true);
        $apikeyuser=update_api_key_user($idapiuser,$fromform);
        echo "email deja enregistré !";
    } else {
        $apikeyuser=insert_api_key_user($USER->id,$fromform);
        echo "email à insérer !";
    }
    echo "apikeyuser = ".$apikeyuser;
//    redirect(new moodle_url($returnurl));
    exit;
}
//Set default data (if any)
$toform=array();
$ojsonapikey=get_api_key_user($USER->id);
if($ojsonapikey->key_field!==''){
    $jsondecoded=json_decode($ojsonapikey->key_field);
    $toform=array('providerapikey'=>$jsondecoded->email);
}
$mform->set_data($toform);

echo $OUTPUT->header();
$content .= $mform->render();
$content .= $OUTPUT->footer();
echo $content;
