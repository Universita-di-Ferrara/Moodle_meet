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
 * Upgrade steps for google_meet
 *
 * Documentation: {@link https://moodledev.io/docs/guides/upgrade}
 *
 * @package    mod_gmeet
 * @category   upgrade
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute the plugin upgrade steps from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_gmeet_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024012902) {

        // Define field google_url to be added to gmeet.
        $table = new xmldb_table('gmeet');
        $field = new xmldb_field('meeting_code', XMLDB_TYPE_TEXT, null, null, null, null, null, 'introformat');

        // Conditionally launch add field google_url.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gmeet savepoint reached.
        upgrade_mod_savepoint(true, 2024012902, 'gmeet');
    }

    if ($oldversion < 2024020101) {

        // Define table gmeet_recordings to be created.
        $table = new xmldb_table('gmeet_recordings');

        // Adding fields to table gmeet_recordings.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('file_id', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('meet_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table gmeet_recordings.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_meet_id', XMLDB_KEY_FOREIGN, ['meet_id'], 'gmeet', ['id']);

        // Conditionally launch create table for gmeet_recordings.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gmeet savepoint reached.
        upgrade_mod_savepoint(true, 2024020101, 'gmeet');
    }

    if ($oldversion < 2024020201) {

        // Define field last_sync to be added to gmeet.
        $table = new xmldb_table('gmeet');
        $field = new xmldb_field('last_sync', XMLDB_TYPE_TEXT, null, null, null, null, null, 'meeting_code');

        // Conditionally launch add field last_sync.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gmeet savepoint reached.
        upgrade_mod_savepoint(true, 2024020201, 'gmeet');
    }

    if ($oldversion < 2024020801) {

        // Define field name to be added to gmeet_recordings.
        $table = new xmldb_table('gmeet_recordings');
        $field = new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null, 'meet_id');

        // Conditionally launch add field name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gmeet savepoint reached.
        upgrade_mod_savepoint(true, 2024020801, 'gmeet');
    }


}
