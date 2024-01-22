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




//$redirect_uri = ''.new moodle_url('mod/gmeet/oauth2callback.php');
$oauth_credentials = "client_secret.json";
$clientSecretJson = json_decode(
    file_get_contents('client_secret.json'),
    true
)['web'];
$scopes = "https://www.googleapis.com/auth/meetings.space.created";
$clientId = $clientSecretJson['client_id'];
$clientSecret = $clientSecretJson['client_secret'];
$client = new Google\Client();
$client->setAuthConfig('client_secret.json');
$client->addScope($scopes);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $_SESSION['credentials'] = new UserRefreshCredentials(
        $scopes,
        [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $_SESSION['access_token']
        ]
    );
    $client->setAccessToken($_SESSION['access_token']);
    $spacesServiceClient = new SpacesServiceClient(['credentials'=>$_SESSION['credentials']]);
    $request = new CreateSpaceRequest();
    $response = $spacesServiceClient->createSpace($request);
    printf('Response data: %s' . PHP_EOL, $response->serializeToJsonString());
} else {
    $redirect_uri = '' . new moodle_url('oauth2callback.php');
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
