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

use mod_gmeet\google\handler;


require('../../config.php');


require_login();

if ($_POST) {

    $settings = (get_config('gmeet'));
    $domain = $settings->domain;
    $meetcode = $_POST['meet_code'];
    $instanceid = $_POST['instance_id'];
    
    // Prendo l'instanza dell'attività, visto che devo aggiornare il campo last_sync.
    $moduleinstance = $DB->get_record('gmeet', ['id' => $instanceid], '*', MUST_EXIST);

    // Default date
    $timestamp = make_timestamp((date('Y')-1));
    $data = str_replace('+00:00', 'Z', date('c',$timestamp));
    $data = json_encode($data);
    
    // Se il campo last_sync è valorizzato, prendo quello come timestamp.
    if (isset($moduleinstance->last_sync)) {
        $data = $moduleinstance->last_sync;
    }

    $googlehandler = new handler();
    $googlehandler->check_login();
    $nexttokenpage  = false;
    $allconference = [];
    do {
        $responseconferencelist = $googlehandler->list_conference_request($moduleinstance->meeting_code, $data, $nexttokenpage);
        if (isset($responseconferencelist->nextPageToken)) {
            $nexttokenpage = $responseconferencelist->nextPageToken;
        }
        $allconference = array_merge($allconference,$responseconferencelist->conferenceRecords);
    }while ($nexttokenpage);
    error_log(print_r($allconference,true));
    foreach ($allconference as $element) {
        $recordings  = $googlehandler->list_recordings_request($element->name);
        if (!(isset($recordings->recordings))) continue;
        foreach ($recordings->recordings as $record) {
            error_log(print_r($record,true));
            if (!($record->state == 'FILE_GENERATED')) continue;
            // Controllo che non esista già il record (es più sync al giorno).
            $textcomparefileid = $DB->sql_compare_text('file_id');
            $textcomparefileidplaceholder = $DB->sql_compare_text(':file_id');
            $sql = "select * from {gmeet_recordings} where {$textcomparefileid} = {$textcomparefileidplaceholder}";
            $fileexist = $DB->record_exists_sql($sql, ['file_id' => $record->driveDestination->file]);
            if (!($fileexist)) {
                //giornata e orario della registrazione
                $datetime = new Datetime($record->startTime);
                
                $giorno = $datetime->format("d-m-Y");
                $orario = $datetime->format("G:i");
                // Devo andare a salvare i valori in db dei recordings.
                $dataobj = new stdClass();
                $dataobj->file_id = $record->driveDestination->file;
                $dataobj->meet_id = $instanceid;
                $dataobj->name = "Registrazione del $giorno alle $orario";
                $DB->insert_record('gmeet_recordings', $dataobj);
                $googlehandler->sharefile_request($record->driveDestination->file, $domain);
            }
        }
    }
    // Dopo aver inserito le registrazioni in db, posso aggiornare il campo last_sync.
    $todaytimestamp = make_timestamp(date('Y'),date('m'),date('d'));
    $timestampgoogle = str_replace('+00:00', 'Z', date('c',$todaytimestamp));
    $moduleinstance->last_sync = json_encode($timestampgoogle);
    $DB->update_record('gmeet', $moduleinstance);

    header('location:' . $SESSION->redirecturl);
    exit();
}
