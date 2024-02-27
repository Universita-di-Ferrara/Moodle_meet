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
// Richiama autoloader per le classi installate con composer
// Errore segnalato da Codechecker
require_once(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Classi Google
use Google\Apps\Meet\V2beta\Client\SpacesServiceClient;
use Google\Apps\Meet\V2beta\CreateSpaceRequest;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Auth\OAuth2;

// Classe plugin
use mod_gmeet\google\GHandler;

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$g = optional_param('g', 0, PARAM_INT);

// Recupera l'istanza dell'attività
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

$googlehandler = new GHandler();

// Renderizzazione della pagina dell'attività
$PAGE->set_url('/mod/gmeet/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

// Impostazione url nella sessione
$SESSION->redirecturl = $PAGE->url;

$settings = (get_config('gmeet'));

$domain = $settings->domain;
$oauth2 = \core\oauth2\api::get_issuer($settings->issuerid);

$clientid = $oauth2->get('clientid');
$clientsecret = $oauth2->get('clientsecret');

$redirecturi = (string) new moodle_url('/mod/gmeet/oauth2callback.php');
$currenturl = (string)new moodle_url('/mod/gmeet/view.php', ['id' => $cm->id]);
$SESSION->currentredirect = $currenturl;
$scopes = "https://www.googleapis.com/auth/meetings.space.created https://www.googleapis.com/auth/drive";

$oauth2 = new OAuth2([
    'clientId' => $clientid,
    'clientSecret' => $clientsecret,
    'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
    // Where to return the user to if they accept your request to access their account.
    // You must authorize this URI in the Google API Console.
    'redirectUri' => $redirecturi,
    'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
    'scope' => $scopes,
]);
if (!isset($SESSION->credentials)) {
    $authenticationurl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
    header("Location: " . $authenticationurl);
    die();
}

try {
    $SESSION->credentials->fetchAuthToken();
} catch (Exception $e) {
    $authenticationurl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
    header("Location: " . $authenticationurl);
    die();
}
if (!$moduleinstance->google_url) {


    $meetinfo = $googlehandler->create_space($SESSION->credentials);

    $moduleinstance->google_url = $meetinfo->meetingUri;
    $moduleinstance->meeting_code = $meetinfo->meetingCode;

    $DB->update_record('gmeet', $moduleinstance);
}


$meetrecordingsarray = [];
if ($datarecords = $DB->get_records('gmeet_recordings', ['meet_id' => $moduleinstance->id])) {
    $meetrecordingsarray = ['records' => []];

    foreach ($datarecords as $record) {
        $record = [
            "recordingsfileurl" => "https://drive.google.com/file/d/{$record->file_id}/view?usp=drive_web",
            "namerecording" => $record->name,

        ];

        array_push($meetrecordingsarray['records'], $record);
    }


}

$spaceinfo = [
    'instance_id' => $moduleinstance->id,
    'meeting_url' => $moduleinstance->google_url,
    'meeting_code' => $moduleinstance->meeting_code,
    'meeting_recordings' =>   $meetrecordingsarray,
];






echo $OUTPUT->header();

$output = $PAGE->get_renderer('mod_gmeet');
$renderable = new mod_gmeet\output\view($spaceinfo);

echo $output->render($renderable);


echo $OUTPUT->footer();
