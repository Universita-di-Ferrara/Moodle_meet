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
require_once($CFG->dirroot . '/lib/externallib.php');

use external_function_parameters;
use external_single_structure;
use external_value;
use context_system;
use external_api;
use moodle_exception;
use stdClass;

/**
 * Class put_recording
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class put_recording extends external_api {

    /**
     * Function for retrieving recordings from gmeet_recordings table
     * @param int $id recordings id
     * @return object $recording recording'row
     */
    public static function put_recording($id, $name , $description) {
        global $DB;

        $context = context_system::instance();
        require_capability('mod/gmeet:addinstance', $context);

        $params = self::validate_parameters(self::put_recording_parameters(), [
            'id' => $id,
            'name' => $name,
            'description' => $description,    
        ]);

        if (!(isset($params['name']) && isset($params['description']))) {
            throw new moodle_exception('either name or recording name must be passed as a parameter');
        }

        $recording = new stdClass;
        $recording->id = $params['id'];
        $recording->name = $params['name'];
        $recording->description = $params['description'];
         
        $response = $DB->update_record('gmeet_recordings', $recording);

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
    public static function put_recording_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'recording \'s id', VALUE_REQUIRED),
            'name' => new external_value(PARAM_TEXT, 'recording \'s name',VALUE_DEFAULT, null),
            'description' => new external_value(PARAM_TEXT, 'recording \'s description', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Returns description of method result value
     *
     * @return external_function_returns
     * @since Moodle 3.0
     */
    public static function put_recording_returns() {
        return new external_single_structure([
            'responsecode' => new external_value(PARAM_BOOL, 'Updated result'),
        ]);
    }
}
