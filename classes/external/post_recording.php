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

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/lib/externallib.php');

use context_course;
use external_function_parameters;
use external_single_structure;
use external_value;
use external_api;
use moodle_exception;
use DateTime;
use stdClass;

/**
 * Class post_recording
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_recording extends external_api {

    /**
     * Function for retrieving recordings from gmeet_recordings table
     * @param int $fileid recording's drive file id
     * @param string $name recordings name
     * @param string $description recordings description
     * @param int $date recording's createdTime timestamp
     * @param int $courseid course id
     * @param int $instanceid gmeet activity
     * @return object $recordingid recording's id
     */
    public static function post_recording($fileid, $name , $description, $date, $courseid, $instanceid) {
        global $DB;
        $params = self::validate_parameters(self::post_recording_parameters(), [
            'fileid' => $fileid,
            'name' => $name,
            'description' => $description,
            'date' => $date,
            'courseid' => $courseid,
            'instanceid' => $instanceid,
        ]);

        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('mod/gmeet:manager', $context);

        $datatoformat = (new DateTime())->setTimestamp($params['date']);

        $datesql = $datatoformat->format("Y-m-d H:i:s");
        $recording = new stdClass;
        $recording->meet_id = $params['instanceid'];
        $recording->date = $datesql;
        $recording->file_id = $params['fileid'];
        $recording->name = $params['name'];
        if (isset($params['name'])) {
            $recording->name = $params['name'];
        }
        if (isset($params['description'])) {
            $recording->description = $params['description'];
        }

        $response = $DB->insert_record('gmeet_recordings', $recording);

        $result = new stdClass;
        $result->recordingid = $response;
        return $result;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function post_recording_parameters() {
        return new external_function_parameters([
            'fileid' => new external_value(PARAM_TEXT, 'recording \'s drive file ID', VALUE_REQUIRED),
            'name' => new external_value(PARAM_TEXT, 'recording \'s name', VALUE_DEFAULT, null),
            'description' => new external_value(PARAM_TEXT, 'recording \'s description', VALUE_DEFAULT, null),
            'date' => new external_value(PARAM_INT, 'recording \'s timestamp', VALUE_REQUIRED),
            'courseid' => new external_value(PARAM_INT, 'course \'s id', VALUE_REQUIRED),
            'instanceid' => new external_value(PARAM_INT, 'instance module \'s id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns description of method result value
     *
     * @return external_function_returns
     * @since Moodle 3.0
     */
    public static function post_recording_returns() {
        return new external_single_structure([
            'recordingid' => new external_value(PARAM_INT, 'New recording id'),
        ]);
    }
}



