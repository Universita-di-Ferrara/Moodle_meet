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

use external_function_parameters;
use external_single_structure;
use external_value;
use context_system;
use external_api;

defined('MOODLE_INTERNAL') || die;

/**
 * Class external
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_recording extends external_api {

    /**
     * Function for retrieving recordings from gmeet_recordings table
     * @param int $id recordings id
     * @return object $recording recording'row
     */
    public static function get_recording($id) {
        global $DB;

        $context = context_system::instance();
        require_capability('mod/gmeet:addinstance', $context);

        $params = self::validate_parameters(self::get_recording_parameters(), ['id' => $id]);
        $id = $params['id'];
        $recording = $DB->get_record('gmeet_recordings', ['id' => $id]);
        return $recording;

    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_recording_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'recording \'s id'),
            
        ]);
    }

    /**
     * Returns description of method result value
     *
     * @return external_function_returns
     * @since Moodle 3.0
     */
    public static function get_recording_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Id of the recording'),
            'name' => new external_value(PARAM_TEXT, 'Name of the recording'),
            'description' => new external_value(PARAM_TEXT, 'Description of the recording'),
            'meet_id' => new external_value(PARAM_INT, 'Foreign key of the gmeet table id field'),
            'file_id' => new external_value(PARAM_TEXT, 'Id of the recording file in drive'),
        ]);
    }
}
