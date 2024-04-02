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
require_capability('mod/gmeet:addinstance', context_system::instance());

$instanceid = required_param('instance_id', PARAM_INT);
$spacename = required_param('space_name', PARAM_TEXT);

$settings = (get_config('gmeet'));
$domain = $settings->domain;

// Prendo l'instanza dell'attività, visto che devo aggiornare il campo last_sync.
$moduleinstance = $DB->get_record('gmeet', ['id' => $instanceid], '*', MUST_EXIST);

// Default date.
$timestamp = make_timestamp((date('Y') - 1));
debugging("Timestamp default date: $timestamp",DEBUG_DEVELOPER);
$data = str_replace('+00:00', 'Z', date('c', $timestamp));

// Se il campo last_sync è valorizzato, prendo quello come timestamp.
if (isset($moduleinstance->last_sync)) {
    $data = $moduleinstance->last_sync;
}

$googlehandler = new handler();
$googlehandler->check_login();
$nexttokenpage  = false;
$allconference = [];
do {
    $responseconferencelist = $googlehandler->list_conference_request($moduleinstance->space_name, $data, $nexttokenpage);
    if (isset($responseconferencelist->nextPageToken)) {
        $nexttokenpage = $responseconferencelist->nextPageToken;
    }
    if (isset($responseconferencelist->conferenceRecords)) {
        $allconference = array_merge($allconference, $responseconferencelist->conferenceRecords);
    }
} while ($nexttokenpage);
foreach ($allconference as $element) {
    $recordings  = $googlehandler->list_recordings_request($element->name);
    if (!(isset($recordings->recordings))) {
        continue;
    }
    foreach ($recordings->recordings as $record) {
        if (!($record->state == 'FILE_GENERATED')) {
            continue;
        }
        $file = $googlehandler->getfile_request($record->driveDestination->file);
        if (!($file) || $file->trashed) {
            continue;
        };
        // Controllo che non esista già il record (es più sync al giorno).
        $textcomparefileid = $DB->sql_compare_text('file_id');
        $textcomparefileidplaceholder = $DB->sql_compare_text(':file_id');
        $sql = "select * from {gmeet_recordings} where {$textcomparefileid} = {$textcomparefileidplaceholder}";
        $fileexist = $DB->record_exists_sql($sql, ['file_id' => $record->driveDestination->file]);
        if (!($fileexist)) {
            // Giornata e orario della registrazione.
            $datetime = new Datetime($record->startTime);
            $datetimerome = $datetime->setTimezone(new DateTimeZone('Europe/Rome'));
            $giorno = $datetimerome->format("d-m-Y");
            $orario = $datetimerome->format("G:i");
            $datetimesql = $datetimerome->format('Y-m-d H:i:s');
            // Devo andare a salvare i valori in db dei recordings.
            $dataobj = new stdClass();
            $dataobj->file_id = $record->driveDestination->file;
            $dataobj->meet_id = $instanceid;
            $dataobj->name = "Registrazione del $giorno alle $orario";
            $dataobj->date = $datetimesql;
            $dataobj->description = '';
            $DB->insert_record('gmeet_recordings', $dataobj);
            $googlehandler->sharefile_request($record->driveDestination->file, $domain);
        }
    }
}
// Dopo aver inserito le registrazioni in db, posso aggiornare il campo last_sync.
$todaytimestamp = make_timestamp(date('Y'), date('m'), date('d'));
debugging("Timestamp di aggiornamento attuale con make_timestamp: $todaytimestamp", DEBUG_DEVELOPER);
$timestampgoogle = str_replace('+00:00', 'Z', date('c', $todaytimestamp));
debugging("Timestamp di aggiornamento attuale con str_replace: $timestampgoogle", DEBUG_DEVELOPER);
$moduleinstance->last_sync = $timestampgoogle;
$DB->update_record('gmeet', $moduleinstance);

if (debugging('', DEBUG_DEVELOPER)) {
    debugging("Nome dello spazio: $spacename", DEBUG_DEVELOPER);
    debugging("Numero dell'istanza: $instanceid", DEBUG_DEVELOPER);
    debugging("Data di ultimo sync: $data", DEBUG_DEVELOPER);
    die();
};

header('location:' . $SESSION->redirecturl);
exit();
