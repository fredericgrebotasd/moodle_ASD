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
namespace \local_ibob\form;
/**
 * User config form.
 *
 * @package    local_ibob
 * @copyright  2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

require_once($CFG->libdir.'/formslib.php');
$PAGE->requires->jquery_plugin('ibob-emailverifier', 'local_ibob');
//require_once(__DIR__ . '/../renderer.php');

class ibob_userconfig_form extends moodleform {
    /**
     * @var int tiny badge image.
     */
    const BADGE_IMAGE_SIZE_TINY = 22;
    /**
     * @var int small badge image.
     */
    const BADGE_IMAGE_SIZE_SMALL = 32;
    /**
     * @var int normal badge image.
     */
    const BADGE_IMAGE_SIZE_NORMAL = 100;

    /**
     * Defines forms elements
     */
    protected function definition() {
        global $OUTPUT, $CFG, $USER, $DB;

        require_once($CFG->libdir . '/filelib.php');
        $user_id=$USER->id;

        // Get the user provider



//        SELECT DISTINCT
//            ' . $DB->sql_concat('r.plugintype', "'_'", 'r.pluginname', "'_'", 's.name')  . ' AS uniqueid,
//             s.name,
//             r.plugintype,
//             r.pluginname
//        FROM
//            {mnet_service} s
//       JOIN {mnet_remote_service2rpc} s2r ON s2r.serviceid = s.id
//       JOIN {mnet_remote_rpc} r ON r.id = s2r.rpcid'

//        $sql ="select P.apiurl,AK.key_field from mdl_local_ibob_user_apikey as AK inner join mdl_local_ibob_providers as P on P.id=AK.provider_id where AK.user_id=:user_id";
//        $oUserProvider = $DB->get_record_sql($sql,array("user_id"=>$user_id));
//
//        echo "oUserProvider = <pre>";
//        print_r($oUserProvider);
//        echo "</pre>";
//
//        if($oUserProvider){ // user has a provider
//            $url = $oUserProvider->apiurl;
//            $oApiKey=json_decode($oUserProvider->key_field);
//            $email = $oApiKey->email;
//        } else {
//            // default configuration
//            $url = 'https://openbadgepassport.com/displayer/';
//            $email = $USER->email;
//
//        }

//        $curl = new curl();
//        $fullurl = $url. 'convert/email';
//        $output = $curl->post($fullurl,
//            array('email' => $email));
//        $json = json_decode($output);
//        $code = $curl->info['http_code'];

//        if (is_null($json) && $code != 200) {
//            throw new Exception(get_string('testbackpackapiurlexception', 'local_ibob',
//                    (object)array('url' => $fullurl, 'errorcode' => $code))
//                , $code);
//        } else {
//            echo '<pre>';
//            print_r($json);
//            echo '</pre>';
//
//            if(!is_array($json->userId)){
//                $aJson=array($json->userId);
//            } else {
//                $aJson=$json->userId;
//            }
//            // liste de tous les badges publics (group=0)
//            $groupId=0;
//            $aBadges=array();
//            foreach ($aJson as $backpackItem){
//                echo "<br>-->$backpackItem";
//                $fullurl = $url. $backpackItem .'/group/'.$groupId.'.json';
//                $output = $curl->get($fullurl);
//
//                $aListBadgesUser=json_decode($output);
//
//                foreach($aListBadgesUser->badges as $aBadgeTemp){
//                    $aBadges[]=$aBadgeTemp;
//                }
//            }
//
//        }
        // faire insertion dans base si besoin

//        if(!empty($aBadges)){
//            // affichage des badges
//            $html='';
//            foreach ($aBadges as $aBadge){
//
//                $badge = $aBadge->assertion->badge;
//
//                $params = array("src" => $badge->image, "alt" => $badge->name, "width" => self::BADGE_IMAGE_SIZE_NORMAL);
//                $badgeimage = html_writer::empty_tag("img", $params);
//
//                $badgename = html_writer::tag('p', s($badge->name), array('class' => 'badgename'));
//                $badgedescription = html_writer::tag('p', s($badge->description), array('class' => 'description'));
//                $extra = '';
//                $divclass = array('ibob-badge');
////            if ($assertion->badge_has_expired()) {
////                $divclass .= ' expired-assertion';
////                $extra = html_writer::tag('div', get_string('expired', 'local_ibob'), array('class' => 'expired-info'));
////            }
////            if ($large) {
////                $divclass .= ' large';
////            }
//
////            $html .= html_writer::tag('div', $badgeimage . $badgename . $badgedescription, $divclass);
//                $html .= html_writer::tag('div', $badgeimage . $badgename , $divclass);
//
//            }
//            echo $html;
//        }







        $mform = $this->_form;
//        $backpacks = $this->_customdata['backpacks'];
//        echo '<hr>';
//        print_r($this->_customdata);
//        echo '<hr>';
        $userpreferences = $this->_customdata['userpreferences'];


//        $usersdisplaybadges = get_config('local_ibob', 'usersdisplaybadges');
//        if ($usersdisplaybadges != ibob_user_preferences::USERS_FORCED_TO_DISPLAY_BADGES &&
//            $usersdisplaybadges != ibob_user_preferences::USERS_NOT_ALLOWED_TO_DISPLAY_BADGES) {
//            // Users can manage displayment of badges
//            $mform->addElement('header', 'header_userprefeferences_fields',
//                get_string('userpreferences', 'local_ibob'));
//            $this->setExpanded($mform, 'header_userprefeferences_fields');
//
//            $mform->addElement('advcheckbox', 'badgesonprofile', get_string('showbadgesonmyprofile', 'local_ibob'));
//            $mform->setDefault('badgesonprofile', $userpreferences->get_preference('badgesonprofile'));
//        } else {
//            // Users cannot modify the value
//            $displaybadges = $usersdisplaybadges == ibob_user_preferences::USERS_FORCED_TO_DISPLAY_BADGES;
//            $mform->addElement('hidden', 'badgesonprofile', $displaybadges);
//            $mform->setType('badgesonprofile', PARAM_INT);
//        }
        

//        foreach ($backpacks as $backpack) {
//            $this->render_backpack_settings($mform, $backpack);
//        }

        $buttonarray = array();

        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'),
                array('class' => 'savegroups'));

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
    /**
     * Render preferences for a backpack provider.
     * @param MoodleQuickForm& $mform
     * @param ibob_backpack $backpack The backpack the settings should be rendered for.
     */
    private function render_backpack_settings(&$mform, ibob_backpack $backpack) {
        global $OUTPUT, $USER;
        $langkey = 'backpack' . (!$backpack->is_connected() ? 'dis' : '') . 'connected';
        $provider = $backpack->get_provider();
        $groupprefix = $backpack->get_providershortname() . 'backpackgroups';
        $providername = ibob_backpack::get_providerfullname_by_providerid($provider);

        $mform->addElement('header', 'header_'.$backpack->get_providershortname().'backpack_fields',
                    get_string('backpackprovidersettings', 'local_ibob', $providername));
        $this->setExpanded($mform, 'header_'.$backpack->get_providershortname().'backpack_fields', false);

        /*if ($provider == ibob_backpack::BACKPACK_PROVIDER_MOZILLA) {
            $mform->addElement('header', 'header_backpack_fields',
                    get_string('backpacksettings', 'local_ibob'));
            $this->setExpanded($mform, 'header_backpack_fields', false);
        } else if ($provider == ibob_backpack::BACKPACK_PROVIDER_OBP) {
            $mform->addElement('header', 'header_obpbackpack_fields',
                    get_string('obpbackpacksettings', 'local_ibob'));
            $this->setExpanded($mform, 'header_obpbackpack_fields', false);
        }*/

        $statustext = html_writer::tag('span', get_string($langkey, 'local_ibob'),
                        array('class' => $langkey));

        $mform->addElement('static', 'connectionstatus',
                get_string('connectionstatus', 'local_ibob'), $statustext);
        $email = $backpack->get_email();

        $mform->addElement('static', 'backpackemail', get_string('backpackemail', 'local_ibob'),
                    empty($email) ? '-' : s($email));

        $mform->addHelpButton('backpackemail', 'backpackemail', 'local_ibob');

        if ($backpack->is_connected()) {
            $groups = $backpack->get_groups();
//            echo '<hr>';print_r($groups);echo '<hr>';

            if (count($groups) === 0) {
                $mform->addElement('static', 'nogroups', get_string('backpackgroups', 'local_ibob'),
                        get_string('nobackpackgroups', 'local_ibob'));
            } else {
                $checkboxes = array();

                foreach ($groups as $group) {
                    $assertions = $backpack->get_group_assertions($group->groupId);
                    $grouphtml = s($group->name) . $OUTPUT->box($this->render_badge_group($assertions),
                                    'generalbox service ibob-userconfig-group');
                    $checkboxes[] = $mform->createElement('advcheckbox', $group->groupId, '',
                            $grouphtml);
                }

                $mform->addGroup($checkboxes, $groupprefix,
                        get_string('backpackgroups', 'local_ibob'), '<br  />', true);
                $mform->addHelpButton($groupprefix, 'backpackgroups', 'local_ibob');

                foreach ($backpack->get_group_ids() as $id) {
                    $mform->setDefault($groupprefix . '[' . $id . ']', true);
                }
            }
        }
        if (!$backpack->is_connected() && $backpack->requires_email_verification()) {
            $mform->addElement('button', 'backpack_submitbutton',
                    get_string('connect', 'local_ibob', $backpack->get_providerfullname()),
                            array('class' => 'verifyemail', 'data-provider' => $backpack->get_provider()));
        } else if (!$backpack->is_connected() && !$backpack->requires_email_verification()) {
            $params = new stdClass();
            $params->backpackprovidershortname = $backpack->get_providershortname();
            $params->backpackproviderfullname = $backpack->get_providerfullname();
            $params->backpackprovidersiteurl = $backpack->get_siteurl();
            $params->useremail = $USER->email;
            $externaladdhtml = get_string('backpackemailaddexternalbackpackprovider', 'local_ibob', $params);
            $mform->addElement('html', $OUTPUT->notification($externaladdhtml), 'notifyproblem');
        }

        if ($backpack->is_connected() && $backpack->requires_email_verification()) {
            $mform->addElement('cancel', 'cancelbackpack'.$backpack->get_providershortname(),
                    get_string('disconnect', 'local_ibob', $backpack->get_providerfullname()));
        }
    }
    /**
     * Render badge group.
     * @param ibob_assertion_collection $assertions
     * @return string HTML.
     */
    private function render_badge_group(ibob_assertion_collection $assertions) {
        global $PAGE;

        $items = array();
        $renderer = $PAGE->get_renderer('local_ibob');
        $size = -1;

        for ($i = 0; $i < count($assertions); $i++) {
            $assertion = $assertions->get_assertion($i);
            $badge = $assertion->get_badge();
            $items[] = local_ibob_html::div($renderer->render_single_simple_assertion($assertion, false) );
        }

        return html_writer::alist($items, array('class' => 'badgelist'));
    }

}
