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

namespace mod_gmeet\external;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/externallib.php');

use external_function_parameters;
use external_single_structure;
use external_value;
use context_course;
use external_api;
use stdClass;

/**
 * Class delete_recording
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_recording extends external_api {
    /**
     * Function for retrieving recordings from gmeet_recordings table
     * @param int $id recordings id
     * @param int $courseid course id
     * @return object $recording recording'row
     */
    public static function delete_recording(int $id, int $courseid) {
        global $DB;

        $params = self::validate_parameters(self::delete_recording_parameters(), [
            'id' => $id,
            'courseid' => $courseid,
        ]);

        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('mod/gmeet:addinstance', $context);
        $id = $params['id'];

        $response = $DB->delete_records('gmeet_recordings', ['id' => $id]);

        $result = new stdClass;
        $result->responsecode = $response;
        return $result;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function delete_recording_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'recording \'s id', VALUE_REQUIRED),
            'courseid' => new external_value(PARAM_INT, 'course \'s id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns description of method result value
     *
     * @return external_function_returns
     * @since Moodle 3.0
     */
    public static function delete_recording_returns() {
        return new external_single_structure([
            'responsecode' => new external_value(PARAM_BOOL, 'Updated result'),
        ]);
    }
}


