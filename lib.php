<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_gmeet
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use mod_gmeet\google\handler;

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function gmeet_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_gmeet into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_gmeet_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function gmeet_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $client = new handler();
    $moduleinstance->timecreated = time();

    // Create meeting space.
    $space = $client->create_space_request();

    $moduleinstance->google_url = $space->meetingUri;
    $moduleinstance->space_name = $space->name;

    // Create last_sync.
    $todaytimestamp = make_timestamp(date('Y'), date('m'), date('d'));
    $offsetlocal = date('P', $todaytimestamp);
    $timestampgoogle = str_replace($offsetlocal, 'Z', date('c', $todaytimestamp));
    $moduleinstance->last_sync = $timestampgoogle;

    $id = $DB->insert_record('gmeet', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_gmeet in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_gmeet_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function gmeet_update_instance($moduleinstance, $mform = null) {
    global $DB;
    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('gmeet', $moduleinstance);
}

/**
 * Removes an instance of the mod_gmeet from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function gmeet_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('gmeet', ['id' => $id]);
    if (!$exists) {
        return false;
    }

    $DB->delete_records('gmeet', ['id' => $id]);

    return true;
}

