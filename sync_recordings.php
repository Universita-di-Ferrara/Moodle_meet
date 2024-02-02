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
 * TODO describe file sync_recordings.ajax
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_gmeet\google\GHandler;
use Google\Apps\Meet\V2beta\ListConferenceRecordsRequest;
use Google\Apps\Meet\V2beta\ListRecordingsRequest;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Apps\Meet\V2beta\Client\ConferenceRecordsServiceClient;
use Google\Protobuf\Timestamp;
include_once __DIR__ . '/../../vendor/autoload.php';

require('../../config.php');

require_login();
if($_POST){


    $meet_code = $_POST['meet_code'];
    $instance_id = $_POST['instance_id'];

    //prendo l'instanza dell'attività, visto che devo aggiornare il campo last_sync
    $moduleinstance = $DB->get_record('gmeet', array('id' => $instance_id), '*', MUST_EXIST);


    //default faccio il retrieve di tutte le conference
    
    $timestamp_google = new Timestamp();
    $timestamp_google = $timestamp_google->serializeToJsonString();
    //se il campo last_sync è valorizzato, prendo quello come timestamp
    if (isset($moduleinstance->last_sync)) $timestamp_google = $moduleinstance->last_sync;



    $meet_recordings_array = [];
    $Google_handler = new GHandler();
    $response_conferenceList = $Google_handler->listConference($SESSION->credentials,$moduleinstance->meeting_code,$timestamp_google);
    foreach ($response_conferenceList as $element) {
        $recordings  = $Google_handler->listRecordings($SESSION->credentials,$element->getName());
        foreach ($recordings as $record){
            //controllo che non esista già il record (es più sync al giorno)
            error_log($record->serializeToJsonString());
            $text_compare_file_id = $DB->sql_compare_text('file_id');
            $text_compare_file_id_placeholder = $DB->sql_compare_text(':file_id');
            if (!($DB->record_exists_sql("select * from {gmeet_recordings} where {$text_compare_file_id} = {$text_compare_file_id_placeholder}",
                [
                    'file_id'=>$record->getDriveDestination()->getFile(),
                ]))){
                // devo andare a salvare i valori in db dei recordings
                $dataobj = new stdClass();
                $dataobj->file_id = $record->getDriveDestination()->getFile();
                $dataobj->meet_id = $instance_id;
                $DB->insert_record('gmeet_recordings',$dataobj);
            }
           
        }
    }
    
    //dopo aver inserito le registrazioni in db, posso aggiornare il campo last_sync
    $timestamp_google = new Timestamp();
    $timestamp_google->setSeconds(strtotime(date("d-m-Y")));
    $moduleinstance->last_sync = $timestamp_google->serializeToJsonString();
    $DB->update_record('gmeet', $moduleinstance);

    header('location:'.$SESSION->redirect_url);
    exit();
}



