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
 * Global settings for badge enrolment plugin
 *
 * @package    enrol_ibobenrol
 * @copyright  2015 onwards Matthias Schwabe {@link http://matthiasschwa.be}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $ADMIN->add('enrolments', new admin_category('enrol_ibobenrol_folder',
                get_string('pluginname', 'enrol_ibobenrol')));

    $ADMIN->add('enrol_ibobenrol_folder', new admin_externalpage('enrol_ibobenrol_about',
                get_string('about', 'enrol_ibobenrol'),
                new moodle_url('/enrol/ibobenrol/about.php')));

    // To prevent creation of separate settings page.
    $settings = null;
}
