<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 08/12/20
 * Time: 10:05
 */
namespace local_ibob;

class ibob_badges extends \core\persistent
{

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    public static function define_properties() {
        return array(
            'name' => array(
                'type' => PARAM_ALPHANUMEXT,
            ),
            'description' => array(
                'type' => PARAM_RAW,
            ),
            'issuername' => array(
                'type' => PARAM_TEXT,
            ),
            'issuerurl' => array(
                'type' => PARAM_RAW,
            ),
            'issuercontact' => array(
                'type' => PARAM_EMAIL,
            ),
            'expiredate' => array(
                'type' => PARAM_INT,
            ),
            'group' => array(
                'type' => PARAM_INT,
            ),
            'image' => array(
                'type' => PARAM_RAW,
            )
        );
    }

    const TABLE = 'local_ibob_badges';
}