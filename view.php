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
 * Prints an instance of mod_gmeet.
 *
 * @package     mod_gmeet
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_gmeet\google\handler;
use mod_gmeet\recordings_table;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once("$CFG->libdir/tablelib.php");

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$g = optional_param('g', 0, PARAM_INT);

$logout = optional_param('logout', 0, PARAM_BOOL);

$client = new handler();

// Recupera l'istanza dell'attività.
if ($id) {
    $cm = get_coursemodule_from_id('gmeet', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('gmeet', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('gmeet', ['id' => $g], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $moduleinstance->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('gmeet', $moduleinstance->id, $course->id, false, MUST_EXIST);
}
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

// Renderizzazione della pagina dell'attività.
$PAGE->set_url('/mod/gmeet/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->requires->js_call_amd('mod_gmeet/main', 'init');

if ($logout) {
    $client->logout();
}
// Impostazione url nella sessione.
$SESSION->redirecturl = $PAGE->url;
$ownership = false;
$loggedin = $client->check_login();

if (has_capability('mod/gmeet:addinstance', $modulecontext) && $loggedin) {
    $ownership = $client->getspace_request($moduleinstance->space_name);
}

$spaceinfo = [
    'instance_id' => $moduleinstance->id,
    'meeting_url' => $moduleinstance->google_url,
    'space_name' => $moduleinstance->space_name,
    'isowner' => $ownership,
];

echo $OUTPUT->header();

$output = $PAGE->get_renderer('mod_gmeet');

if (has_capability('mod/gmeet:addinstance', $modulecontext)) {

    if ($client->enabled && !$client->check_login()) {

        echo(html_writer::div(get_string('logintoyourgoogleaccount', 'gmeet') .
        $client->print_login_popup(), 'mdl-align alert alert-info googlemeet_loginbutton'));
    }

    // If is logged in, shows Google account information.
    if ($client->check_login()) {
        echo(html_writer::div($client->print_user_info(), 'mdl-align alert alert-success'));
    }
}

$renderable = new mod_gmeet\output\view($spaceinfo);

echo $output->render($renderable);

$table = new recordings_table('uniqueid');
// Work out the sql for the table.
$table->set_sql('id, name, description, date,file_id', "{gmeet_recordings}", "meet_id = $moduleinstance->id");
$table->define_baseurl($PAGE->url);

echo(html_writer::start_div('mt-5'));
$table->out(10, true);
echo(html_writer::end_div());

echo $OUTPUT->footer();
