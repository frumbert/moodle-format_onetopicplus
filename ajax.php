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
 * Ajax handlers
 * Requires course logon & sesskey
 *
 * @package format_onetopicplus
 * @copyright  2022 tim st. clair (https://github.com/frumbert/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');
require_once('lib.php');

require_sesskey();

$action = required_param('action', PARAM_ALPHANUM);
$courseid = required_param('id', PARAM_INT);
$contextid = optional_param('context', 0, PARAM_INT);

$course = get_course($courseid);
$context = context_course::instance($course->id);

require_login($course, false, null, false, true);

header('content-type: application/json'); // because jquery expects it

if (!isloggedin()) {
    print_error('nopermission');
}

$response = new stdClass();

switch ($action) {

    // set the star state of the module
    case "star":
        $state = (optional_param('state', 0, PARAM_INT) !== 0);
        $cm = get_fast_modinfo($course)->get_cm($contextid);
        $stars = new format_onetopicplus_stars($cm);
        $stars->set_state($state);
        $response->state = $state;
        break;

    // toggle the star state of the module
    case "togglestar":
        $cm = get_fast_modinfo($course)->get_cm($contextid);
        $stars = new format_onetopicplus_stars($cm);
        $state = !$stars->get_state();
        $stars->set_state($state);
        $response->state = $state;
        break;

}

echo json_encode($response);
die();