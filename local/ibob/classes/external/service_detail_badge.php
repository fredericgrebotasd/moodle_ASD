<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 16/12/20
 * Time: 09:48
 */

namespace local_ibob\external;

/**
 * PLUGIN external file
 *
 * @package    component
 * @category   external
 * @copyright  20XX YOURSELF
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");

class service_detail_badge extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function ibob_detail_badge_webservice_parameters() {
        // FUNCTIONNAME_parameters() always return an external_function_parameters().
        // The external_function_parameters constructor expects an array of external_description.
        return new external_function_parameters(
        // a external_description can be: external_value, external_single_structure or external_multiple structure
            array('PARAM1' => new external_value(PARAM_TYPE, 'human description of PARAM1'))
        );
    }

    /**
     * The function itself
     * @return string welcome message
     */
    public static function FUNCTIONNAME($PARAM1) {

        //Parameters validation
        $params = self::validate_parameters(self::FUNCTIONNAME_parameters(),
            array('PARAM1' => $PARAM1));

        //Note: don't forget to validate the context and check capabilities

        return $returnedvalue;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function FUNCTIONNAME_returns() {
        return new external_value(PARAM_TYPE, 'human description of the returned value');
    }



}