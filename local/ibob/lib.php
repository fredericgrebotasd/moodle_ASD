<?php
/**
 * Plugin libs steps are defined here.
 *
 * @package     local_ibob
 * @category    upgrade
 * @copyright   2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adds OB badges to profile pages.
 *
 * @param \core_user\output\myprofile\tree $tree
 * @param stdClass $user
 * @param bool $iscurrentuser
 * @param moodle_course $course
 */

defined('MOODLE_INTERNAL') or die();

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
const BADGE_IMAGE_SIZE_NORMAL = 50;

function get_user_providers($userid) {
    global $DB;
    $sql ="SELECT   {local_ibob_providers}.apiurl,{local_ibob_user_apikey}.key_field
                FROM    {local_ibob_user_apikey} JOIN {local_ibob_providers} ON {local_ibob_providers}.id={local_ibob_user_apikey}.provider_id 
                WHERE   {local_ibob_user_apikey}.user_id=:userid";
    return $DB->get_record_sql($sql,array("userid"=>$userid));
}

function get_user_provider_json($url,$email) {
    $curl = new curl();
    $fullurl = $url. 'convert/email';
    $output = $curl->post($fullurl,
        array('email' => $email));
    $json = json_decode($output);
    $code = $curl->info['http_code'];
    return array('json'=>$json,'code'=>$code,'fullurl'=>$fullurl,'curl'=>$curl);
}

function print_badge($imgsize,$img,$name,$description,$badgeuniqueid,$badgeid) {
    $params = array("src" => $img, "alt" => $name, "width" => $imgsize);
    $badgeimage = html_writer::empty_tag("img", $params);
    $badgename = html_writer::tag('p', s($name), array('class' => 'badgename'));
//    $badgedescription = html_writer::tag('p', s($description), array('class' => 'description'));
//    $extra = '';
//    $divclass = array('class'=>'ibob-badge');
//            if ($assertion->badge_has_expired()) {
//                $divclass .= ' expired-assertion';
//                $extra = html_writer::tag('div', get_string('expired', 'local_obf'), array('class' => 'expired-info'));
//            }
//            if ($large) {
//                $divclass .= ' large';
//            }
//    return html_writer::tag('div', $badgeimage . $badgename ,array('class'=>'ibob-badge','id'=>$badgeid));
    return html_writer::div($badgeimage . $badgename, "ibob-badge", array('id'=>$badgeuniqueid,'data-id'=>$badgeid));
}

function local_ibob_myprofile_navigation(\core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG, $USER, $PAGE, $DB;


    require_once($CFG->libdir . '/filelib.php');
    $userid=$USER->id;
//    $usersdisplaybadges = get_config('local_ibob', 'usersdisplaybadges');
//    if(
//        $usersdisplaybadges == ibob_user_preferences::USERS_FORCED_TO_DISPLAY_BADGES ||
//        $usersdisplaybadges != ibob_user_preferences::USERS_NOT_ALLOWED_TO_DISPLAY_BADGES &&
//        ibob_user_preferences::get_user_preference($user->id, 'badgesonprofile') == 1
//    ){
//        $show=1;
//    }
    $show=1; // a modifier plus tard si on rajoute la possibilité d'afficher ou pas les openbadges au niveau du profil
    if ($show) {
        $category = new core_user\output\myprofile\category('local_ibob/badges', get_string('profilebadgelist', 'local_ibob'), null);
        $tree->add_category($category);

        // Open Badges list construction
        $ouserprovider = get_user_providers($userid);
        $abadges=array();
        if($ouserprovider){ // user has a provider
            $url = $ouserprovider->apiurl;
            $oApiKey=json_decode($ouserprovider->key_field);
            $email = $oApiKey->email;
            $aCurl = get_user_provider_json($url,$email);
//            print_r($aCurl);
            if (is_null($aCurl['json']) && $aCurl['code'] != 200) {
                throw new Exception(get_string('testbackpackapiurlexception', 'local_ibob',
                        (object)array('url' => $aCurl['fullurl'], 'errorcode' => $aCurl['code']))
                    , $aCurl['code']);
            } else {
                if($aCurl['code'] === 200){
                    if(!is_array($aCurl['json']->userId)){
                        $aJson=array($aCurl['json']->userId);
                    } else {
                        $aJson=$aCurl['json']->userId;
                    }
                    // Global Public/social Openbadges List (public group : 0 and 1)
                    $agroupid=array(0,1);
                    foreach ($aJson as $backpackitem){
                        foreach ($agroupid as $groupid){
                            $fullurl = $url. $backpackitem .'/group/'.$groupid.'.json';
                            $output = $aCurl['curl']->get($fullurl);
                            $alistbadgesuser = json_decode($output, true);
                            // badge suppression if expiration date > now
                            if(count($alistbadgesuser) > 2){
                                foreach($alistbadgesuser['badges'] as $abadgetemp){
                                    if(isset($abadgetemp['assertion']['expires'])){
                                        if($abadgetemp['assertion']['expires']!==''){
                                            if($abadgetemp['assertion']['expires']>time()){
                                                $abadges[]=$abadgetemp;
                                            } elseif ($idbadge=$DB->get_record_select('local_ibob_badges', 'name=:name', array('name'=>$abadgetemp['name']), $fields='id', $strictness=IGNORE_MISSING)){
//                                                $DB->delete_records('local_ibob_badges', array('id'=>$idbadge));
                                                $DB->delete_records('local_ibob_badge_issued', array('badgeid'=>$idbadge,'userid'=>$userid));
                                            }
                                        } else {
                                            $abadges[]=$abadgetemp;
                                        }
                                    } else {
                                        $abadgetemp['assertion']['expires']='';
                                        $abadges[]=$abadgetemp;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!empty($abadges)){
            // Print badges
            $content='';
            foreach ($abadges as $abadge){
                $badge = $abadge['assertion']['badge'];
                // insert in database if not already present
                $badgeid = $DB->get_field_select('local_ibob_badges','id', 'name=:name', array('name'=>$badge['name']));
                if(!$badgeid){
                    $obadge = new stdClass();
                    $obadge->name = $badge['name'];
                    $obadge->description = $badge['description'];
                    $obadge->issuername = $badge['issuer']['name'];
                    $obadge->issuerurl = $badge['issuer']['url'];
                    $obadge->issuercontact = $badge['issuer']['email'];
                    $obadge->image = $badge['image'];
                    $obadge->usermodified = $userid;
                    $obadge->timecreated = time();
                    $obadge->timecreated = time();
//                    $obadge->group = $badge['issuer']['group'];
                    $badgeid = $DB->insert_record('local_ibob_badges', $obadge);
                }
                if(!$DB->get_record_select('local_ibob_badge_issued', 'userid=:userid and badgeid=:badgeid', array('userid'=>$userid,'badgeid'=>$badgeid), $fields='id', $strictness=IGNORE_MISSING)){
                    $obadgeissued = new stdClass();
                    $obadgeissued->userid = $userid;
                    $obadgeissued->badgeid = $badgeid;
                    if($abadge['assertion']['expires']){
                        $obadgeissued->expirationdate = $abadge['assertion']['expires'];
                    }
                    $DB->insert_record('local_ibob_badge_issued', $obadgeissued);
                }

                $badgeuniqueid='badge_'.$badgeid;
                $content.=print_badge(BADGE_IMAGE_SIZE_NORMAL,$badge['image'],$badge['name'],$badge['description'],$badgeuniqueid,$badgeid);
            }
            $PAGE->requires->js_call_amd('local_ibob/userbadgedisplayer', 'init');
        } else {
            $content = html_writer::tag('div', get_string('noBadgesFound', 'local_ibob'), array('class' => 'no-badges-found'));
        }

        $localnode = $mybadges = new core_user\output\myprofile\node('local_ibob/badges', 'ibobbadges',
            '', null, null, $content, null, 'local-ibob');
        $tree->add_node($localnode);
    }
}

/**
 * Adds the IBOB-links to Moodle's settings navigation.
 *
 * @param settings_navigation $navigation
 */
function local_ibob_extend_settings_navigation(settings_navigation $navigation) {
    if(isloggedin() and !isguestuser()){
        $branch = $navigation->find('usercurrentsettings', navigation_node::TYPE_CONTAINER);
        $ibobprefs = $branch->add(get_string('ibobprefs', 'local_ibob'), null, navigation_node::TYPE_CONTAINER, 'Ibob pref', 'ibobprefs');
        $node = navigation_node::create(get_string('ibobprefslink', 'local_ibob'),
            new moodle_url('/local/ibob/userconfig.php'));
        $ibobprefs->add_node($node);
    }
}