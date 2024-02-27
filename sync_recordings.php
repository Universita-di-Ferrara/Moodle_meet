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

require_once(__DIR__ . '/../../vendor/autoload.php');
require('../../config.php');


require_login();
if ($_POST) {

    $settings = (get_config('gmeet'));
    $domain = $settings->domain;
    $meetcode = $_POST['meet_code'];
    $instanceid = $_POST['instance_id'];
    
    // Prendo l'instanza dell'attività, visto che devo aggiornare il campo last_sync.
    $moduleinstance = $DB->get_record('gmeet', ['id' => $instanceid], '*', MUST_EXIST);

    // Default faccio il retrieve di tutte le conference.

    $timestampgoogle = new Timestamp();
    $timestampgoogle = $timestampgoogle->serializeToJsonString();

    // Se il campo last_sync è valorizzato, prendo quello come timestamp.
    if (isset($moduleinstance->last_sync)) {
        $timestampgoogle = $moduleinstance->last_sync;
    }

    $googlehandler = new GHandler();
    $responseconferencelist = $googlehandler->list_conference($SESSION->credentials, $moduleinstance->meeting_code, $timestampgoogle);
    foreach ($responseconferencelist as $element) {
        $recordings  = $googlehandler->list_recordings($SESSION->credentials, $element->getName());
        foreach ($recordings as $record) {
            // Controllo che non esista già il record (es più sync al giorno).
            $textcomparefileid = $DB->sql_compare_text('file_id');
            $textcomparefileidplaceholder = $DB->sql_compare_text(':file_id');
            $sql = "select * from {gmeet_recordings} where {$textcomparefileid} = {$textcomparefileidplaceholder}";
            $fileexist = $DB->record_exists_sql($sql, ['file_id' => $record->getDriveDestination()->getFile()]);
            if (!($fileexist)) {
                //giornata e orario della registrazione
                $datetime = $record->getStartTime()->toDateTime();
                $giorno = $datetime->format("d-m-Y");
                $orario = $datetime->format("G:i");
                // Devo andare a salvare i valori in db dei recordings.
                $dataobj = new stdClass();
                $dataobj->file_id = $record->getDriveDestination()->getFile();
                $dataobj->meet_id = $instanceid;
                $dataobj->name = "Registrazione del $giorno alle $orario";
                $DB->insert_record('gmeet_recordings', $dataobj);
                $googlehandler->share_file($record->getDriveDestination()->getFile(), $SESSION->credentials, $domain);
            }
        }
    }
    // Dopo aver inserito le registrazioni in db, posso aggiornare il campo last_sync.
    $timestampgoogle = new Timestamp();
    $timestampgoogle->setSeconds(strtotime(date("d-m-Y")));
    $moduleinstance->last_sync = $timestampgoogle->serializeToJsonString();
    $DB->update_record('gmeet', $moduleinstance);

    header('location:' . $SESSION->redirecturl);
    exit();
}
