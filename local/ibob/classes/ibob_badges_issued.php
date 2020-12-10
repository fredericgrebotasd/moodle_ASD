<?php
/**
 * Created by PhpStorm.
 * User: fred
 * Date: 08/12/20
 * Time: 10:05
 */
namespace local_ibob;

class ibob_badges_issued extends \core\persistent
{

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    public static function define_properties() {
        return array(
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'badgeid' => array(
                'type' => PARAM_INT,
            ),
            'expirationdate' => array(
                'type' => PARAM_INT,
            ),
        );
    }

    const TABLE = 'local_ibob_badge_issued';
}