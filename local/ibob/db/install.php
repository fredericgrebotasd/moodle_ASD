<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     local_ibob
 * @category    upgrade
 * @copyright   2020 Frédéric Grebot <frederic.grebot@agrosupdijon.fr>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Install function.
 *
 * @return boolean
 **/
function xmldb_local_ibob_install() {
    global $CFG, $DB;

    // Set default backpack sources
    $oBackpackProvider = new stdClass();
    $oBackpackProvider->apiurl = 'https://openbadgepassport.com/displayer/';
    $oBackpackProvider->fullname = 'Open Badge Passport';
    $oBackpackProvider->shortname = 'obp';
    $oBackpackProvider->usermodified = '1';
    $oBackpackProvider->timecreated = time();
    $DB->insert_record('local_ibob_providers', $oBackpackProvider);
    unset($oBackpackProvider);

    // Set custom fields in user profile
    $oCustomField = new stdClass();
    $oCustomField->shortname = 'hasimportedfromexternalob';
    $oCustomField->name = 'Has imported from external source of badges';
    $oCustomField->datatype = 'text';
    $oCustomField->description = '<p>Has imported from external source of badges</p>';
    $oCustomField->descriptionformat = 1;
    $oCustomField->categoryid = 1;
    $oCustomField->sortorder = 1;
    $oCustomField->required = 0;
    $oCustomField->locked = 0;
    $oCustomField->visible = 0;
    $oCustomField->forceunique = 0;
    $oCustomField->signup = 0;
    $oCustomField->defaultdata = 0;
    $oCustomField->param1 = 1;
    $oCustomField->param2 = 1;
    $oCustomField->param3 = 1;
    $lastInsertedIdCustomFielsId = $DB->insert_record('user_info_field', $oCustomField, true);
    unset($oCustomField);

    // pour test ; l'admin (user_id=2) est automatiquement enregistré comme récupérant les obp (provider_id=1)
    $DB->delete_records('user_info_data', array('userid'=>2));
    $DB->delete_records('mdl_user_info_field', array('name'=>'Has imported from external source of badges'));

    $oUserApikey = new stdClass();
    $oUserApikey->usermodified = 2;
    $oUserApikey->timecreated = time();
    $oUserApikey->provider_id = 1;
    $oUserApikey->key_field = json_encode(array('email' => 'frederic.grebot@gmail.com'));
    $oUserApikey->user_id = 2;
    $DB->insert_record('local_ibob_user_apikey', $oUserApikey);
    unset($oUserApikey);

    $oUserCustomField = new stdClass();
    $oUserCustomField->userid = 2;
    $oUserCustomField->fieldid = $lastInsertedIdCustomFielsId;
    $oUserCustomField->data = '0';
    $DB->insert_record('user_info_data', $oUserCustomField);
    unset($oUserCustomField);

    // fin du test

    return true;
}
