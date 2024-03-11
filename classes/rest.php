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
 * Google Rest API.
 *
 * @package     mod_gmeet
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_gmeet;
/**
 * Google Rest API.
 *
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rest extends \core\oauth2\rest {

    /**
     * Define the functions of the rest API.
     *
     * @return array
     */
    public function get_api_functions() {
        return [
            'createspace' => [
                'endpoint' => 'https://meet.googleapis.com/v2/spaces',
                'method' => 'post',
                'args' => [],
                'response' => 'json',
            ],
            'listconferences' => [
                'endpoint' => 'https://meet.googleapis.com/v2/conferenceRecords',
                'method' => 'get',
                'args' => [
                    'filter' => PARAM_TEXT,
                ],
                'response' => 'json',
            ],
            'listrecordings' => [
                'endpoint' => 'https://meet.googleapis.com/v2/{parent}/recordings',
                'method' => 'get',
                'args' => [
                    'parent' => PARAM_TEXT,
                ],
                'response' => 'json',
            ],
            'create_permission' => [
                'endpoint' => 'https://www.googleapis.com/drive/v3/files/{fileid}/permissions',
                'method' => 'post',
                'args' => [
                    'fileid' => PARAM_TEXT,
                ],
                'response' => 'json',
            ],
        ];
    }
}
