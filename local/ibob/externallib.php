<?php

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
 * External Web Service Template
 *
 * @package    localibob
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/externallib.php");

class mod_testtest_external extends external_api {

    public static function loadsettings_parameters() {
        return new external_function_parameters(
            array(
                'itemid' => new external_value(PARAM_INT, 'The item id to operate on'),
            )
        );
    }

    public static function loadsettings_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'content' => new external_value(PARAM_RAW, 'settings content text'),
                )
            )
        );
    }

    public static function loadsettings($itemid) {
        global $DB;
        //$params = self::validate_parameters(self::getExample_parameters(), array());
        $params = self::validate_parameters(self::loadsettings_parameters(),
            array('itemid'=>$itemid));

        $sql = 'SELECT server FROM {listeGlobale} WHERE id = ?';
        $paramsDB = $params; //array($itemid);
        $db_result = $DB->get_records_sql($sql,$paramsDB);

        return $db_result;
    }

    public static function updatesettings_parameters() {
        return new external_function_parameters(

            array(
                'itemid' => new external_value(PARAM_INT, 'The item id to operate on'),
                'data2update' => new external_value(PARAM_TEXT, 'Update data'))
        );
    }

    public static function updatesettings_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'content' => new external_value(PARAM_RAW, 'settings content text'),
                )
            )
        );
    }

    public static function updatesettings($itemid, $data2update) {
        global $DB;
        //$params = self::validate_parameters(self::getExample_parameters(), array());
        $params = self::validate_parameters(self::updatesettings_parameters(),
            array('itemid'=>$itemid, 'data2update'=>$data2update));

        $newdata = new stdClass();
        $newdata->id = $itemid;
        $newdata->url = "url de test";
        $newdata->server = $data2update;
        if ($DB->record_exists('listeGlobale', array('id' => $itemid))) {
            $DB->update_record('listeGlobale', $newdata);
        }


        $sql = 'SELECT server FROM {listeGlobale} WHERE id = ?';
        $paramsDB = array($itemid);
        $db_result = $DB->get_records_sql($sql,$paramsDB);

        return $db_result;
    }

}
