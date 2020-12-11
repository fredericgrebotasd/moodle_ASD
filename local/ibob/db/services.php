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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localibob
 * @copyright  2020 Frédéric Grebot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'My service' => array(
        'functions' => array ('local_ibob_hello_world'),
        'restrictedusers' => 0,
        'enabled'=>1,
    )
);

// We defined the web service functions to install.
$functions = array(
        'local_ibob_hello_world' => array(
                'classname'   => 'local_ibob_external',
                'methodname'  => 'hello_world',
                'classpath'   => 'local/ibob/externallib.php',
                'description' => 'Return Hello World FIRSTNAME. Can change the text (Hello World) sending a new text as parameter',
                'type'        => 'read',
                'ajax' => true,
                'loginrequired' => true,
        )
);

