<?php

include_once __DIR__ . '/../../vendor/autoload.php';
use mod_gmeet\google\GHandler;
use Google\Apps\Meet\V2beta\Client\SpacesServiceClient;
use Google\Apps\Meet\V2beta\CreateSpaceRequest;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Auth\OAuth2;
use Google\Client;
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
 * This file allows for testing of login via configured oauth2 IDP poviders.
 *
 * @package mod_gmeet
 * @copyright 2021 Matt Porritt <mattp@catalyst-au.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

// Require_login is not needed here.
// phpcs:disable moodle.Files.RequireLogin.Missing
require_once('../../config.php');
$clientSecretJson = json_decode(
    file_get_contents('client_secret.json'),
    true
)['web'];
$clientId = $clientSecretJson['client_id'];
$clientSecret = $clientSecretJson['client_secret'];
$redirectURI = 'http://' . $_SERVER['HTTP_HOST'] . '/moodle401/' . new moodle_url('mod/gmeet/oauth2callback.php');
$scopes = "https://www.googleapis.com/auth/meetings.space.created";

$oauth2 = new OAuth2([
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
    'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
    // Where to return the user to if they accept your request to access their account.
    // You must authorize this URI in the Google API Console.
    'redirectUri' => $redirectURI,
    'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
    'scope' => $scopes,
]);

if (!isset($_SESSION['credentials']) || $_SESSION['credentials']->getLastReceivedToken()['expires_at'] < time()) {

    $authenticationUrl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
    header("Location: " . $authenticationUrl);
}
try {

    $spacesServiceClient = new SpacesServiceClient(['credentials' => $_SESSION['credentials']]);
    
    // Prepare the request message.
    $request = new CreateSpaceRequest();
    
    // Call the API and handle any network failures.
    
    /** @var Space $response */
    $response = $spacesServiceClient->createSpace($request);
    printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());


}catch (Exception $e) {
    error_log($e);
}

