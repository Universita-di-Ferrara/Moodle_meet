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
 * @copyright   2023 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
include_once __DIR__ . '/../../vendor/autoload.php';
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

use Google\Apps\Meet\V2beta\Client\SpacesServiceClient;
use Google\Apps\Meet\V2beta\CreateSpaceRequest;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Auth\OAuth2;

use mod_gmeet\google\GHandler;

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$g = optional_param('g', 0, PARAM_INT);


if ($id) {
    $cm = get_coursemodule_from_id('gmeet', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('gmeet', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('gmeet', array('id' => $g), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('gmeet', $moduleinstance->id, $course->id, false, MUST_EXIST);
}
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$Google_handler = new GHandler();

$PAGE->set_url('/mod/gmeet/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);


$SESSION->redirect_url = $PAGE->url;
//se non ho popolato il campo Google url nella tabella gmeet di questa istanza allora vado a creare un nuovo spazio


$clientSecretJson = json_decode(
    file_get_contents('client_secret.json'),
    true
)['web'];
$clientId = $clientSecretJson['client_id'];
$clientSecret = $clientSecretJson['client_secret'];
$redirectURI = 'http://' . $_SERVER['HTTP_HOST'] . '/moodle401/' . new moodle_url('mod/gmeet/oauth2callback.php');
$current_url = (string)new moodle_url('/mod/gmeet/view.php', array('id' => $cm->id));
$SESSION->current_redirect = $current_url;
$scopes = "https://www.googleapis.com/auth/meetings.space.created https://www.googleapis.com/auth/drive";

$oauth2 = new OAuth2([
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
    'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
    // Where to return the user to if they accept your request to access their account.
    // You must authorize this URI in the Google API Console.
    'redirectUri' => $redirectURI,
    'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
    'scope' => $scopes
]);
if (!isset($SESSION->credentials)) {
    $authenticationUrl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
    header("Location: " . $authenticationUrl);
    die();
}

try {
    $SESSION->credentials->fetchAuthToken();
} catch (Exception $e) {
    $authenticationUrl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
    header("Location: " . $authenticationUrl);
    die();
}
if (!$moduleinstance->google_url) {


    $meet_info = $Google_handler->createSpace($SESSION->credentials);

    $moduleinstance->google_url = $meet_info->meetingUri;
    $moduleinstance->meeting_code = $meet_info->meetingCode;

    $DB->update_record('gmeet', $moduleinstance);
}


$meet_recordings_array = [];

if ($data_records = $DB->get_records('gmeet_recordings',array('meet_id' => $moduleinstance->id))){

    foreach ($data_records as $record){
        $recordings_file_url = "https://drive.google.com/file/d/{$record->file_id}/view?usp=drive_web";
        array_push($meet_recordings_array,$recordings_file_url);
    }

}


$space_info = [
    'instance_id'=>$moduleinstance->id,
    'meeting_url'=>$moduleinstance->google_url,
    'meeting_code'=>$moduleinstance->meeting_code,
    'meeting_recordings' => $meet_recordings_array
];






echo $OUTPUT->header();

$output = $PAGE->get_renderer('mod_gmeet');
$renderable = new mod_gmeet\output\view($space_info);

echo $output->render($renderable);


echo $OUTPUT->footer();
