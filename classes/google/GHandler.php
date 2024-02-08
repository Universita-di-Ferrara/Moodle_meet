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

namespace mod_gmeet\google;

use DateTime;
use html_writer;
use moodle_url;
use dml_missing_record_exception;
use moodle_exception;
use stdClass;
use Google\Auth\OAuth2;
use Google\Apps\Meet\V2beta\Client\SpacesServiceClient;
use Google\Apps\Meet\V2beta\Client\ConferenceRecordsServiceClient;
use Google\Apps\Meet\V2beta\GetSpaceRequest;
use Google\Apps\Meet\V2beta\CreateSpaceRequest;
use Google\Apps\Meet\V2beta\ListConferenceRecordsRequest;
use Google\Apps\Meet\V2beta\ListRecordingsRequest;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Service\Drive;
use Google\Client;
use Google\Service\Drive\Permission;
use Google\Protobuf\Timestamp;
/**
 * Class GHandler
 *
 * @package    mod_gmeet
 * @copyright  2024 Università degli Studi di Ferrara - Unife
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class GHandler {

    /**
     * Create a new meeting space
     * @param Google\Auth\Credentials\UserRefreshCredentials $credentials user credentials
     * @return array json_decoded space
     */
    public function create_space($credentials) {
        $spacesserviceclient = new SpacesServiceClient(['credentials' => $credentials]);

        // Prepare the request message.
        $request = new CreateSpaceRequest();

        // Call the API and handle any network failures.
        /** @var Space $response */
        $response = $spacesserviceclient->createSpace($request);
        return json_decode($response->serializeToJsonString());
    }

    /**
     * Get space by his name
     *
     * @param Google\Auth\Credentials\UserRefreshCredentials $credentials user credentials
     * @param string $spacename space name
     * @return string space info 
     * 
     **/
    public function get_space($credentials, $spacename) {
        $spacesserviceclient = new SpacesServiceClient(['credentials' => $credentials]);

        // Prepare the request message.
        $request = new GetSpaceRequest();
        $request->setName('spaces/' . $spacename);
        // Call the API and handle any network failures.

        /** @var Space $response */
        $response = $spacesserviceclient->getSpace($request);
        return ($response->serializeToJsonString());
    }
    /**
     * List all conference filtered by $meetingcode and start_time >= $timestamp
     * 
     * @param Google\Auth\Credentials\UserRefreshCredentials $credentials user credentials
     * @param string $meetingcode meeting_code
     * @param Google\Protobuf\Timestamp $timestamp timestamp of day from which start filtering
     *  
     * @return array all conferences filtered
     */
    public function list_conference($credentials, $meetingcode, $timestamp) {

        $client = new ConferenceRecordsServiceClient(['credentials' => $credentials]);

        $request = new ListConferenceRecordsRequest();
        // Is it bettter to use meeting_name?
        $request->setFilter("space.meeting_code = $meetingcode start_time >= $timestamp");

        $response = $client->listConferenceRecords($request);
        return ($response);
    }

    /**
     * List all recordings filtered by $conferencename 
     * 
     * @param Google\Auth\Credentials\UserRefreshCredentials $credentials user credentials
     * @param string $conferencename conference name
     *  
     * @return array all recordings filtered
     */
    public function list_recordings($credentials, $conferencename) {

        $client = new ConferenceRecordsServiceClient(['credentials' => $credentials]);
        $request = new ListRecordingsRequest();
        $request->setParent($conferencename);

        $response = $client->listRecordings($request);
        return ($response);
    }

    /**
     * Share file with unife domain
     * 
     * @param string $fileid file id to share with domain
     * @param Google\Auth\Credentials\UserRefreshCredentials $credentials user credentials
     *  
     * @return void
     */
    public function share_file($fileid, $credentials) {
        $client = new Client(['credentials' => $credentials]);
        $client->setScopes(Drive::DRIVE);
        $service = new Drive($client);
        $domainpermission = new Permission([
            'type' => 'domain',
            'role' => 'reader',
            'domain' => 'unife.it',
        ]);
        $service->permissions->create($fileid, $domainpermission);
    }
}
