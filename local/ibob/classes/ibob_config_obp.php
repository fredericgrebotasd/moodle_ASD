<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 08/12/20
 * Time: 10:05
 */
namespace local_ibob;

class ibob_config_obp extends \core\persistent
{

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    public static function define_properties() {
        return array(
            'provider_id' => array(
                'type' => PARAM_INT,
            ),
            'key_field' => array(
                'type' => PARAM_TEXT,
            ),
            'user_id' => array(
                'type' => PARAM_INT,
            ),
        );
    }

    const TABLE = 'local_ibob_user_apikey';
}