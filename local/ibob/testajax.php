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
 * Displays the details of a single event from OBF.
 *
 * @package    local_obf
 * @copyright  2013-2015, Discendum Oy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');


require_login();


$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/ibob/textajax.php'));
$PAGE->set_title(get_string('ibob', 'local_ibob'));

$PAGE->requires->js_call_amd('local_ibob/ajaxcalls', 'init');
echo $OUTPUT->header();

echo \html_writer::link('', 'Click me', array('class'=>'ajaxcall'));

echo $OUTPUT->footer();