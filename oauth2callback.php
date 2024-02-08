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
 * Oauth client instance callback script
 *
 * @package    mod_gmeet
 * @copyright  2024 Università degli Studi di Ferrara - Unife
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../config.php');

require_once(__DIR__ . '/lib.php');

use mod_gmeet\google\GHandler;
use Google\Auth\OAuth2;
use Google\Auth\Credentials\UserRefreshCredentials;
global $SESSION;

require_login();

$clientsecretjson = json_decode(
    file_get_contents('client_secret.json'),
    true
)['web'];
$clientid = $clientsecretjson['client_id'];
$clientsecret = $clientsecretjson['client_secret'];
$redirecturi = 'http://' . $_SERVER['HTTP_HOST'] . '/moodle401/' . new moodle_url('mod/gmeet/oauth2callback.php');
$scopes = "https://www.googleapis.com/auth/meetings.space.created";

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
$currenturlredirect = $SESSION->currentredirect;

if (!isset($_GET['code'])) {

    $authenticationurl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
    header("Location: " . $authenticationurl);
} else {

    // With the code returned by the OAuth flow, we can retrieve the refresh token.
    $oauth2->setCode($_GET['code']);
    $authtoken = $oauth2->fetchAuthToken();
    $refreshtoken = $authtoken['access_token'];

    // The UserRefreshCredentials will use the refresh token to 'refresh' the credentials when
    // they expire.
    $SESSION->credentials = new UserRefreshCredentials(
        $scopes,
        [
            'client_id' => $clientid,
            'client_secret' => $clientsecret,
            'refresh_token' => $refreshtoken,
        ]
    );
    header('Location: ' . filter_var($currenturlredirect, FILTER_SANITIZE_URL));
}
