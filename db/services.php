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
 * External functions and service declaration for Moodle Meet
 *
 * Documentation: {@link https://moodledev.io/docs/apis/subsystems/external/description}
 *
 * @package    mod_gmeet
 * @category   webservice
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_gmeet_get_recording' => [
        'classname' => 'mod_gmeet\external\get_recording',
        'methodname' => 'get_recording',
        'description' => 'Get recording by id',
        'type' => 'read',
        'ajax' => true,

    ],
    'mod_gmeet_add_recording' => [
        'classname' => 'mod_gmeet\external\post_recording',
        'methodname' => 'post_recording',
        'description' => 'Add new Recording',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_gmeet_update_recording' => [
        'classname' => 'mod_gmeet\external\put_recording',
        'methodname' => 'put_recording',
        'description' => 'Update Recording',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_gmeet_delete_recording' => [
        'classname' => 'mod_gmeet\external\delete_recording',
        'methodname' => 'delete_recording',
        'description' => 'Delete Recording',
        'type' => 'write',
        'ajax' => true,
    ],

];
