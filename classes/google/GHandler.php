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
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class GHandler
{


    public function __construct()
    {
    }



    public function createSpace($credentials)
    {
        $spacesServiceClient = new SpacesServiceClient(['credentials' => $credentials]);

        // Prepare the request message.
        $request = new CreateSpaceRequest();

        // Call the API and handle any network failures.
        // TO DO: limit access only to domain users
        /** @var Space $response */
        $response = $spacesServiceClient->createSpace($request);
        return json_decode($response->serializeToJsonString());
    }

    public function getSpace($credentials, $space_name)
    {
        $spacesServiceClient = new SpacesServiceClient(['credentials' => $credentials]);

        // Prepare the request message.
        $request = new GetSpaceRequest();
        $request->setName('spaces/' . $space_name);
        // Call the API and handle any network failures.

        /** @var Space $response */
        $response = $spacesServiceClient->getSpace($request);
        return ($response->serializeToJsonString());
    }
    public function listConference($credentials, $meetingCode,$timestamp)
    {



        $client = new ConferenceRecordsServiceClient(['credentials' => $credentials]);

        $request = new ListConferenceRecordsRequest();
        //is it bettter to use meeting_name? 
        error_log('space.meeting_code = "'.$meetingCode.'"'. " and start_time >= $timestamp");
        $request->setFilter("space.meeting_code = $meetingCode start_time >= $timestamp");
       
        $response = $client->listConferenceRecords($request);
        return ($response);
    }

    public function listRecordings($credentials, $conference_name)
    {

        $client = new ConferenceRecordsServiceClient(['credentials' => $credentials]);
        $request = new ListRecordingsRequest();
        $request->setParent($conference_name);

        $response = $client->listRecordings($request);
        return ($response);
    }

    public function listFileDrive($credentials)
    {
        $client = new Client(['credentials' => $credentials]);
        $client->setScopes(Drive::DRIVE);
        $service = new Drive($client);
        // Print the names and IDs for up to 10 files.
        $optParams = array(
            'pageSize' => 10,
            'q' => 'name = "Meet Recordings" and mimeType = "application/vnd.google-apps.folder"'
        );
        $results = $service->files->listFiles($optParams);

        if (count($results->getFiles()) == 0) {
            return false;
        } else {
            
            foreach ($results->getFiles() as $file) {
                $folder_id =  $file->getId();
                //condivido la cartella con tutto il dominio Unife

            }
        }
    }

    public function shareFile($fileid,$credentials){
        $client = new Client(['credentials' => $credentials]);
        $client->setScopes(Drive::DRIVE);
        $service = new Drive($client);
        $domain_permission = new Permission(array(
            'type'=>'domain',
            'role'=>'reader',
            'domain'=>'unife.it'
        ));
        $service->permissions->create($fileid,$domain_permission);
    }
}
